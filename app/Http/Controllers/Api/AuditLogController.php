<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display a filtered list of audit logs.
     */
    public function index(Request $request)
    {
        $this->ensureSeedData();

        $query = ActivityLog::with(['user.role'])->orderBy('id', 'desc');

        // Search filter (Keyword in aktivitas, ip_address, or user name/email)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Module/Category filter
        if ($module = $request->input('module')) {
            switch (strtolower($module)) {
                case 'auth':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%login%')
                          ->orWhere('aktivitas', 'like', '%logout%')
                          ->orWhere('aktivitas', 'like', '%password%')
                          ->orWhere('aktivitas', 'like', '%sesi%');
                    });
                    break;
                case 'accounts':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%akun%')
                          ->orWhere('aktivitas', 'like', '%staf%')
                          ->orWhere('aktivitas', 'like', '%user%')
                          ->orWhere('aktivitas', 'like', '%role%');
                    });
                    break;
                case 'master':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%servis%')
                          ->orWhere('aktivitas', 'like', '%komisi%')
                          ->orWhere('aktivitas', 'like', '%wilayah%')
                          ->orWhere('aktivitas', 'like', '%kategori%');
                    });
                    break;
                case 'dispute':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%dispute%')
                          ->orWhere('aktivitas', 'like', '%resolusi%')
                          ->orWhere('aktivitas', 'like', '%sanksi%')
                          ->orWhere('aktivitas', 'like', '%eskalasi%');
                    });
                    break;
                case 'system':
                    $query->where(function($q) {
                        $q->where('aktivitas', 'like', '%backup%')
                          ->orWhere('aktivitas', 'like', '%restore%')
                          ->orWhere('aktivitas', 'like', '%pengaturan%')
                          ->orWhere('aktivitas', 'like', '%setting%')
                          ->orWhere('aktivitas', 'like', '%cache%');
                    });
                    break;
            }
        }

        // Date range filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // User filter
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        $logs = $query->paginate(25);

        // Stats summary
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', Carbon::today())->count();
        $uniqueUsers = ActivityLog::distinct('user_id')->whereNotNull('user_id')->count('user_id');
        $sensitiveActions = ActivityLog::where(function($q) {
            $q->where('aktivitas', 'like', '%memblokir%')
              ->orWhere('aktivitas', 'like', '%restore%')
              ->orWhere('aktivitas', 'like', '%pengaturan%')
              ->orWhere('aktivitas', 'like', '%sanksi%')
              ->orWhere('aktivitas', 'like', '%revisi%');
        })->count();

        return response()->json([
            'logs' => $logs,
            'stats' => [
                'total_logs' => $totalLogs,
                'today_logs' => $todayLogs,
                'unique_users' => $uniqueUsers,
                'sensitive_actions' => $sensitiveActions,
            ]
        ]);
    }

    /**
     * Export logs as CSV payload or JSON.
     */
    public function export(Request $request)
    {
        $logs = ActivityLog::with('user.role')->orderBy('id', 'desc')->limit(500)->get();

        $data = $logs->map(function ($log) {
            return [
                'ID' => $log->id,
                'Waktu' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                'User Name' => $log->user ? $log->user->nama : 'System',
                'User Email' => $log->user ? $log->user->email : '-',
                'User Role' => $log->user && $log->user->role ? $log->user->role->nama : 'System',
                'Aktivitas' => $log->aktivitas,
                'IP Address' => $log->ip_address ?: '127.0.0.1',
            ];
        });

        return response()->json([
            'filename' => 'audit_logs_revaro_' . date('Ymd_His') . '.csv',
            'data' => $data,
        ]);
    }

    /**
     * Ensure rich seed data exists for demonstration if table is empty.
     */
    private function ensureSeedData()
    {
        if (ActivityLog::count() < 10) {
            $superAdmin = User::whereHas('role', fn($q) => $q->where('nama', 'super-admin-pemilik'))->first() ?? User::first();
            $verifikator = User::whereHas('role', fn($q) => $q->where('nama', 'admin-verifikator'))->first();
            $resolusi = User::whereHas('role', fn($q) => $q->where('nama', 'admin-resolusi'))->first();

            $samples = [
                ['user' => $superAdmin, 'akt' => 'Login sukses ke Portal Super Admin Pemilik', 'ip' => '127.0.0.1', 'time' => now()->subMinutes(5)],
                ['user' => $superAdmin, 'akt' => 'Memperbarui Pengaturan Sistem Platform (Jam Operasional & Maintenance)', 'ip' => '127.0.0.1', 'time' => now()->subMinutes(18)],
                ['user' => $superAdmin, 'akt' => 'Melakukan pembersihan cache framework Laravel', 'ip' => '127.0.0.1', 'time' => now()->subHours(1)],
                ['user' => $verifikator, 'akt' => 'Menyetujui pendaftaran mitra teknisi #MT-2049 (Ahmad Subagja)', 'ip' => '114.122.34.12', 'time' => now()->subHours(2)],
                ['user' => $verifikator, 'akt' => 'Meminta revisi berkas pendaftaran mitra toko #TK-1088 (Toko Central Sparepart)', 'ip' => '114.122.34.12', 'time' => now()->subHours(3)],
                ['user' => $resolusi, 'akt' => 'Menyelesaikan dispute order #ORD-8821 dengan keputusaan 100% Refund ke Pelanggan', 'ip' => '180.252.12.89', 'time' => now()->subHours(4)],
                ['user' => $superAdmin, 'akt' => 'Mengubah tarif komisi platform jasa dari 10.0% menjadi 12.0%', 'ip' => '127.0.0.1', 'time' => now()->subHours(5)],
                ['user' => $superAdmin, 'akt' => 'Mengunduh berkas cadangan database SQL platform (backup_revaro_2026.sql)', 'ip' => '127.0.0.1', 'time' => now()->subHours(8)],
                ['user' => $superAdmin, 'akt' => 'Menambahkan wilayah operasional baru: Kota Bandung & Cimahi', 'ip' => '127.0.0.1', 'time' => now()->subDay()],
                ['user' => $verifikator, 'akt' => 'Menolak verifikasi mitra #MT-2030 karena foto KTP buram', 'ip' => '114.122.34.12', 'time' => now()->subDays(2)],
                ['user' => $superAdmin, 'akt' => 'Mengubah status akun admin verifikator (admin.verifikator@revaro.id) menjadi ACTIVE', 'ip' => '127.0.0.1', 'time' => now()->subDays(3)],
            ];

            foreach ($samples as $s) {
                ActivityLog::create([
                    'user_id' => $s['user']?->id,
                    'aktivitas' => $s['akt'],
                    'ip_address' => $s['ip'],
                    'created_at' => $s['time'],
                ]);
            }
        }
    }
}
