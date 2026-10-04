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

        $pendingTechs = Teknisi::where('status', 'PENDING_REVIEW')->count();
        $pendingShops = Toko::where('status', 'PENDING_REVIEW')->count();
        $totalPending = $pendingTechs + $pendingShops;

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
            'rejected_onboarding_count' => $totalRejected,
            'pending_onboarding_count' => $totalPending,
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

    /**
     * Get security flags/alerts sent to Super Admin Pemilik.
     */
    public function getFlags(Request $request)
    {
        $user = $request->user();

        if (!$user->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $flags = Notifikasi::where('user_id', $user->id)
            ->where('judul', 'like', 'FLAG ALERT%')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($flags);
    }

    /**
     * Get financial summary (GMV, net commission, active transactions)
     */
    public function getFinancialSummary()
    {
        $policy = \App\Models\KebijakanKomisi::first() ?? (object)['persen_komisi_jasa' => 10, 'persen_fee_sparepart' => 5];
        $jasaPct = $policy->persen_komisi_jasa / 100;
        $partPct = $policy->persen_fee_sparepart / 100;

        $transactions = \App\Models\Transaksi::where('status_pembayaran', 'PAID')->get();
        $gmv = $transactions->sum('total');
        $komisi = $transactions->sum(function($t) use ($jasaPct, $partPct) {
            return ($t->biaya_jasa * $jasaPct) + ($t->biaya_part * $partPct);
        });
        $activeOrders = \App\Models\Order::whereNotIn('status', ['COMPLETED', 'CANCELLED'])->count();

        return response()->json([
            'gmv' => $gmv,
            'komisi_bersih' => $komisi,
            'transaksi_aktif' => $activeOrders,
        ]);
    }

    /**
     * Get monthly trends (GMV and net commission)
     */
    public function getMonthlyTrends()
    {
        $trends = [];
        $policy = \App\Models\KebijakanKomisi::first() ?? (object)['persen_komisi_jasa' => 10, 'persen_fee_sparepart' => 5];
        $jasaPct = $policy->persen_komisi_jasa / 100;
        $partPct = $policy->persen_fee_sparepart / 100;

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $start = $date->startOfMonth()->toDateTimeString();
            $end = $date->endOfMonth()->toDateTimeString();

            $txs = \App\Models\Transaksi::where('status_pembayaran', 'PAID')
                ->whereBetween('paid_at', [$start, $end])
                ->get();

            $mGmv = $txs->sum('total');
            $mKomisi = $txs->sum(function($t) use ($jasaPct, $partPct) {
                return ($t->biaya_jasa * $jasaPct) + ($t->biaya_part * $partPct);
            });

            $trends[] = [
                'bulan' => $monthName,
                'gmv' => $mGmv,
                'komisi' => $mKomisi,
            ];
        }

        return response()->json($trends);
    }

    /**
     * Get verification and dispute summary metrics.
     */
    public function getVerificationSummary()
    {
        $approved = \App\Models\PendaftaranVerifikasi::where('status', 'APPROVED')->count();
        $rejected = \App\Models\PendaftaranVerifikasi::where('status', 'REJECTED')->count();
        $pending = \App\Models\Teknisi::where('status', 'PENDING_REVIEW')->count() + \App\Models\Toko::where('status', 'PENDING_REVIEW')->count();

        $disputeOpen = \App\Models\Dispute::where('status', 'OPEN')->count();
        $disputeInvestigating = \App\Models\Dispute::whereIn('status', ['INVESTIGATING', 'ESCALATED'])->count();
        $disputeResolved = \App\Models\Dispute::where('status', 'RESOLVED')->count();

        return response()->json([
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'dispute_open' => $disputeOpen,
            'dispute_investigating' => $disputeInvestigating,
            'dispute_resolved' => $disputeResolved,
        ]);
    }

    /**
     * Scorecard Admin Verifikator from real database records.
     */
    public function scorecardVerifikator(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $since = Carbon::now()->subDays($days);

        $verifikators = User::where('role_id', 4)
            ->orWhereHas('role', fn($q) => $q->where('nama', 'like', '%Verifikator%'))
            ->get();

        $scorecard = [];

        foreach ($verifikators as $admin) {
            // Get decisions made by this admin
            $decisions = \App\Models\PendaftaranVerifikasi::where('admin_id', $admin->id)
                ->where('updated_at', '>=', $since)
                ->get();

            $approvedCount = $decisions->where('status', 'APPROVED')->count();
            $rejectedCount = $decisions->where('status', 'REJECTED')->count();
            $revisionCount = $decisions->where('status', 'NEEDS_REVISION')->count();
            $totalProcessed = $approvedCount + $rejectedCount + $revisionCount;

            // Calculate average response time in hours
            $totalHours = 0;
            $under24h = 0;
            foreach ($decisions as $d) {
                $hours = Carbon::parse($d->created_at)->diffInHours($d->updated_at);
                $totalHours += $hours;
                if ($hours <= 24) {
                    $under24h++;
                }
            }
            $avgHours = $totalProcessed > 0 ? round($totalHours / $totalProcessed, 1) : 0;
            $slaPct = $totalProcessed > 0 ? round(($under24h / $totalProcessed) * 100, 1) : 100;

            // Rating / Performance badge
            $perfRating = 'Sangat Baik';
            if ($slaPct < 80) $perfRating = 'Perlu Peningkatan';
            elseif ($slaPct < 90) $perfRating = 'Sesuai Target';

            $scorecard[] = [
                'id' => $admin->id,
                'nama' => $admin->nama,
                'email' => $admin->email,
                'total_proses' => $totalProcessed,
                'total_diproses' => $totalProcessed,
                'approved' => $approvedCount,
                'approved_count' => $approvedCount,
                'rejected' => $rejectedCount,
                'rejected_count' => $rejectedCount,
                'revisi' => $revisionCount,
                'avg_waktu_jam' => $avgHours,
                'avg_response_jam' => $avgHours,
                'skor_kinerja' => $slaPct,
                'sla_compliance_pct' => $slaPct,
                'performance_rating' => $perfRating,
                'status' => $admin->status ?? 'ACTIVE',
                'last_active' => $decisions->max('updated_at') ? Carbon::parse($decisions->max('updated_at'))->translatedFormat('d M Y, H:i') : 'Belum Ada',
            ];
        }

        return response()->json($scorecard);
    }

    /**
     * Scorecard Admin Resolusi from real database records.
     */
    public function scorecardResolusi(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $since = Carbon::now()->subDays($days);

        $resolusis = User::where('role_id', 5)
            ->orWhereHas('role', fn($q) => $q->where('nama', 'like', '%Resolusi%'))
            ->get();

        $scorecard = [];

        foreach ($resolusis as $admin) {
            $disputes = Dispute::where('admin_id', $admin->id)
                ->where('created_at', '>=', $since)
                ->get();

            $totalDisputes = $disputes->count();
            $resolvedCount = $disputes->where('status', 'RESOLVED')->count();
            $activeCount = $disputes->whereIn('status', ['OPEN', 'INVESTIGATING', 'ESCALATED'])->count();
            $escalatedCount = $disputes->where('status', 'ESCALATED')->count();

            $totalHours = 0;
            $breachedCount = 0;
            $onTimeCount = 0;
            foreach ($disputes as $disp) {
                if ($disp->status === 'RESOLVED' && $disp->resolved_at) {
                    $hours = Carbon::parse($disp->created_at)->diffInHours($disp->resolved_at);
                    $totalHours += $hours;
                    if ($disp->sla_deadline && Carbon::parse($disp->resolved_at)->gt(Carbon::parse($disp->sla_deadline))) {
                        $breachedCount++;
                    } else {
                        $onTimeCount++;
                    }
                } elseif ($disp->sla_deadline && Carbon::now()->gt(Carbon::parse($disp->sla_deadline))) {
                    $breachedCount++;
                }
            }
            $avgHours = $resolvedCount > 0 ? round($totalHours / $resolvedCount, 1) : 0;
            $slaPct = $totalDisputes > 0 ? round((max(0, $totalDisputes - $breachedCount) / $totalDisputes) * 100, 1) : 100;

            $perfRating = 'Sangat Baik';
            if ($slaPct < 75) $perfRating = 'Perlu Akselerasi';
            elseif ($slaPct < 90) $perfRating = 'Optimal';

            $scorecard[] = [
                'id' => $admin->id,
                'nama' => $admin->nama,
                'email' => $admin->email,
                'total_ditangani' => $totalDisputes,
                'total_dispute' => $totalDisputes,
                'resolved' => $resolvedCount,
                'resolved_count' => $resolvedCount,
                'eskalasi' => $escalatedCount,
                'active_count' => $activeCount,
                'sla_breached' => $breachedCount,
                'avg_waktu_jam' => $avgHours,
                'avg_resolution_jam' => $avgHours,
                'skor_kinerja' => $slaPct,
                'sla_compliance_pct' => $slaPct,
                'performance_rating' => $perfRating,
                'status' => $admin->status ?? 'ACTIVE',
                'last_active' => $disputes->max('updated_at') ? Carbon::parse($disputes->max('updated_at'))->translatedFormat('d M Y, H:i') : 'Belum Ada',
            ];
        }

        return response()->json($scorecard);
    }

    /**
     * Trends & Analytics aggregated from real database records.
     */
    public function getTrends(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $since = Carbon::now()->subDays($days);

        $newTechCount = Teknisi::where('created_at', '>=', $since)->count();
        $newShopCount = Toko::where('created_at', '>=', $since)->count();
        $newDisputeCount = Dispute::where('created_at', '>=', $since)->count();
        $newViolationCount = \App\Models\PelanggaranLog::where('created_at', '>=', $since)->count();

        // Interval buckets for charts
        $intervalDays = max(1, (int) ceil($days / 7));
        $labels = [];
        $techSeries = [];
        $shopSeries = [];
        $disputeSeries = [];
        $violationSeries = [];

        for ($i = 6; $i >= 0; $i--) {
            $bucketStart = Carbon::now()->subDays(($i + 1) * $intervalDays);
            $bucketEnd = Carbon::now()->subDays($i * $intervalDays);

            $labels[] = $bucketEnd->format('d M');
            $techSeries[] = Teknisi::whereBetween('created_at', [$bucketStart, $bucketEnd])->count();
            $shopSeries[] = Toko::whereBetween('created_at', [$bucketStart, $bucketEnd])->count();
            $disputeSeries[] = Dispute::whereBetween('created_at', [$bucketStart, $bucketEnd])->count();
            $violationSeries[] = \App\Models\PelanggaranLog::whereBetween('created_at', [$bucketStart, $bucketEnd])->count();
        }

        return response()->json([
            'teknisi_baru' => $newTechCount,
            'toko_baru' => $newShopCount,
            'dispute_baru' => $newDisputeCount,
            'pelanggaran_baru' => $newViolationCount,
            'labels' => $labels,
            'teknisi_series' => $techSeries,
            'toko_series' => $shopSeries,
            'dispute_series' => $disputeSeries,
            'pelanggaran_series' => $violationSeries,
        ]);
    }

    /**
     * SLA Alerts & Anomalies from real database records.
     */
    public function getSlaAlerts(Request $request)
    {
        $alerts = collect();

        // 1. Overdue or urgent disputes
        $disputes = Dispute::with(['admin', 'order'])
            ->whereIn('status', ['OPEN', 'INVESTIGATING', 'ESCALATED'])
            ->orderBy('sla_deadline', 'asc')
            ->get();

        foreach ($disputes as $d) {
            $deadline = $d->sla_deadline ? Carbon::parse($d->sla_deadline) : Carbon::parse($d->created_at)->addHours(48);
            $isBreached = $deadline->lt(Carbon::now());
            $hoursLeft = round(Carbon::now()->diffInHours($deadline, false));

            $alerts->push([
                'id' => $d->id,
                'order_id' => $d->order_id,
                'alasan' => $d->alasan ?? 'Komplain pesanan memerlukan investigasi',
                'status' => $d->status,
                'sla_deadline' => $deadline->toIso8601String(),
                'is_breached' => $isBreached,
                'hours_left' => $hoursLeft,
                'admin' => $d->admin ? ['id' => $d->admin->id, 'nama' => $d->admin->nama] : null,
                'source' => 'DISPUTE_SLA',
            ]);
        }

        // 2. Pending verifications exceeding 24 hours
        $overdueTechs = Teknisi::with('user')
            ->where('status', 'PENDING_REVIEW')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        foreach ($overdueTechs as $tech) {
            $deadline = Carbon::parse($tech->created_at)->addHours(24);
            $alerts->push([
                'id' => $tech->id,
                'order_id' => '-',
                'alasan' => "Pendaftaran Teknisi ({$tech->user?->nama}) menunggu verifikasi berkas melebihi SLA 24 jam.",
                'status' => 'PENDING_REVIEW',
                'sla_deadline' => $deadline->toIso8601String(),
                'is_breached' => true,
                'hours_left' => round(Carbon::now()->diffInHours($deadline, false)),
                'admin' => null,
                'source' => 'VERIFIKASI_SLA',
            ]);
        }

        return response()->json($alerts->values());
    }
}
