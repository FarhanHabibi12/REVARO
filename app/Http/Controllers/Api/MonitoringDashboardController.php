<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dispute;
use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MonitoringDashboardController extends Controller
{
    /**
     * Get aggregate performance metrics for the monitoring dashboard.
     * Accessible by super-admin-monitoring and super-admin-pemilik.
     */
    public function getMetrics(Request $request)
    {
        $user = $request->user();

        // Security check: only super-admin-monitoring and super-admin-pemilik
        if (!$user->isSuperAdminPemilik() && !$user->isSuperAdminMonitoring()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // 1. Total active partners
        $totalActiveTechs = Teknisi::where('status', 'ACTIVE')->count();
        $totalActiveShops = Toko::where('status', 'ACTIVE')->count();

        // 2. Dispute status counts
        $openDisputesCount = Dispute::where('status', '!=', 'RESOLVED')->count();
        $resolvedDisputesCount = Dispute::where('status', 'RESOLVED')->count();

        // 3. Average dispute resolution time (in hours)
        $resolvedDisputes = Dispute::where('status', 'RESOLVED')
            ->whereNotNull('resolved_at')
            ->get();

        $totalResolutionHours = 0;
        $resolvedCount = $resolvedDisputes->count();
        foreach ($resolvedDisputes as $dispute) {
            $totalResolutionHours += Carbon::parse($dispute->created_at)->diffInHours($dispute->resolved_at);
        }
        $avgResolutionTimeHours = $resolvedCount > 0 ? round($totalResolutionHours / $resolvedCount, 2) : 0.00;

        // 4. Onboarding rejection rate vs Operational freeze rate
        $totalTechProfiles = Teknisi::count();
        $totalShopProfiles = Toko::count();
        $totalProfiles = $totalTechProfiles + $totalShopProfiles;

        $rejectedTechs = Teknisi::where('status', 'REJECTED')->count();
        $rejectedShops = Toko::where('status', 'REJECTED')->count();
        $totalRejected = $rejectedTechs + $rejectedShops;

        $frozenTechs = Teknisi::where('status', 'FROZEN')->count();
        $frozenShops = Toko::where('status', 'FROZEN')->count();
        $totalFrozen = $frozenTechs + $frozenShops;

        $onboardingRejectionRate = $totalProfiles > 0 ? round(($totalRejected / $totalProfiles) * 100, 2) : 0.00;
        $operationalFreezeRate = $totalProfiles > 0 ? round(($totalFrozen / $totalProfiles) * 100, 2) : 0.00;

        // 5. SLA breaches count (> 48 hours deadline)
        $slaBreachCount = Dispute::where('status', '!=', 'RESOLVED')
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', Carbon::now())
            ->count();

        return response()->json([
            'total_active_technicians' => $totalActiveTechs,
            'total_active_shops' => $totalActiveShops,
            'open_disputes' => $openDisputesCount,
            'resolved_disputes' => $resolvedDisputesCount,
            'average_resolution_time_hours' => $avgResolutionTimeHours,
            'onboarding_rejection_rate_percent' => $onboardingRejectionRate,
            'operational_freeze_rate_percent' => $operationalFreezeRate,
            'sla_breach_count' => $slaBreachCount,
        ]);
    }

    /**
     * Send performance warning flag alerts from Monitoring Kinerja to Super Admin Pemilik.
     * Accessible by super-admin-monitoring.
     */
    public function storeFlag(Request $request)
    {
        $user = $request->user();

        // Only super-admin-monitoring can send flags
        if (!$user->isSuperAdminMonitoring()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Backward compatibility for Stage 2 test payload format
        if ($request->has('target_admin_id') && $request->has('alasan')) {
            $request->merge([
                'judul' => 'Laporan Kinerja Verifikator',
                'pesan' => 'Peringatan kinerja untuk Verifikator ID ' . $request->target_admin_id . '. Alasan: ' . $request->alasan,
                'urgency' => 'HIGH',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'urgency' => 'required|in:LOW,MEDIUM,HIGH',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch all Super Admin Pemilik (System Owners)
        $ownerRole = Role::where('nama', 'Super Admin (Pemilik Sistem)')->first();
        $owners = $ownerRole ? User::where('role_id', $ownerRole->id)->get() : collect();

        foreach ($owners as $owner) {
            Notifikasi::create([
                'user_id' => $owner->id,
                'judul' => "FLAG ALERT [{$request->urgency}]: {$request->judul}",
                'pesan' => "Laporan Kinerja dari Administrator Monitoring: {$request->pesan}",
                'dibaca' => false,
            ]);
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'aktivitas' => "Monitoring Kinerja mengirimkan flag alert '{$request->judul}' dengan tingkat urgensi {$request->urgency} kepada Owner",
        ]);

        return response()->json([
            'message' => 'Flag berhasil dikirim ke Super Admin Pemilik.'
        ], 200);
    }
}
