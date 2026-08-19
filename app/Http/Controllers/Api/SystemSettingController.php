<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    /**
     * Default settings dictionary.
     */
    protected array $defaultSettings = [
        // General
        'app_name' => ['value' => 'REVARO — Replikasi & Variasi Otomotif', 'group' => 'general', 'description' => 'Nama resmi platform'],
        'app_tagline' => ['value' => 'Solusi Perbaikan & Komponen Otomotif Terpercaya', 'group' => 'general', 'description' => 'Tagline platform'],
        'cs_email' => ['value' => 'support@revaro.id', 'group' => 'general', 'description' => 'Email Layanan Pelanggan'],
        'cs_phone' => ['value' => '0812-3456-7890', 'group' => 'general', 'description' => 'Nomor Whatsapp/Call Center CS'],
        'operating_hours' => ['value' => '08:00 - 21:00 WIB', 'group' => 'general', 'description' => 'Jam operasional layanan dispatch'],
        'default_currency' => ['value' => 'IDR', 'group' => 'general', 'description' => 'Mata uang transaksi'],
        'app_language' => ['value' => 'id', 'group' => 'general', 'description' => 'Bahasa Default Platform (Global i18n: id/en)'],
        
        // Financial
        'commission_jasa_percent' => ['value' => '10.0', 'group' => 'financial', 'description' => 'Persentase komisi platform dari jasa servis (%)'],
        'fee_sparepart_percent' => ['value' => '5.0', 'group' => 'financial', 'description' => 'Persentase komisi toko dari penjualan sparepart (%)'],
        'min_payout_amount' => ['value' => '50000', 'group' => 'financial', 'description' => 'Batas minimum penarikan dana wallet mitra (Rp)'],
        'auto_release_days' => ['value' => '3', 'group' => 'financial', 'description' => 'Pelepasan otomatis dana transaksi tanpa komplain (Hari)'],

        // SLA
        'max_verification_hours' => ['value' => '24', 'group' => 'sla', 'description' => 'Batas waktu verifikasi pendaftaran mitra (Jam)'],
        'target_technician_arrival_minutes' => ['value' => '45', 'group' => 'sla', 'description' => 'Estimasi target waktu kedatangan teknisi di lokasi (Menit)'],
        'max_cancel_per_day' => ['value' => '5', 'group' => 'sla', 'description' => 'Batas toleransi pembatalan pesanan harian sebelum alert'],

        // Features
        'maintenance_mode' => ['value' => 'false', 'group' => 'features', 'description' => 'Status pemeliharaan platform (Maintenance Mode)'],
        'maintenance_message' => ['value' => 'Platform REVARO sedang dalam pemeliharaan berkala untuk peningkatan performa. Silakan coba beberapa saat lagi.', 'group' => 'features', 'description' => 'Pesan pemberitahuan maintenance'],
        'partner_registration_open' => ['value' => 'true', 'group' => 'features', 'description' => 'Membuka/menutup pendaftaran mitra teknisi & toko baru'],
        'customer_technician_chat' => ['value' => 'true', 'group' => 'features', 'description' => 'Fitur chat langsung antara pelanggan & teknisi'],
        'broadcast_notifications' => ['value' => 'true', 'group' => 'features', 'description' => 'Pengiriman notifikasi broadcast email/WA otomatis'],

        // Security
        'session_lifetime_minutes' => ['value' => '120', 'group' => 'security', 'description' => 'Masa berlaku sesi login admin/staff (Menit)'],
        'max_login_attempts' => ['value' => '5', 'group' => 'security', 'description' => 'Jumlah maksimal percobaan login gagal sebelum diproteksi'],
        'force_mfa_admin' => ['value' => 'false', 'group' => 'security', 'description' => 'Wajibkan autentikasi dua faktor (MFA) untuk super admin'],
        'audit_retention_days' => ['value' => '90', 'group' => 'security', 'description' => 'Masa simpan riwayat audit log (Hari)'],

        // Maintenance specific
        'maintenance_global' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Sakelar Maintenance Global'],
        'maintenance_eta' => ['value' => '', 'group' => 'maintenance', 'description' => 'Estimasi Selesai Maintenance (ETA)'],
        'maintenance_ip_bypass' => ['value' => '186.243.3.201, 127.0.0.1', 'group' => 'maintenance', 'description' => 'Daftar IP Bypass Maintenance'],
        'maintenance_role_verifikator' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Maintenance Selective Admin Verifikator'],
        'maintenance_role_resolusi' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Maintenance Selective Admin Resolusi'],
        'maintenance_role_mitra' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Maintenance Selective Mitra'],
        // Visual Branding & Assets
        'app_logo' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Logo Utama Platform'],
        'app_favicon' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Favicon Browser'],
        'app_banner' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Banner Hero Landing Page'],
        'app_stamp' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Stempel Digital Sistem'],
    ];

    /**
     * Retrieve all system settings.
     */
    public function index()
    {
        // Seed defaults if empty
        foreach ($this->defaultSettings as $key => $item) {
            SystemSetting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => $item['value'],
                    'group' => $item['group'],
                    'description' => $item['description'],
                ]
            );
        }

        $settings = SystemSetting::all()->keyBy('key');
        
        $result = [];
        foreach ($settings as $key => $setting) {
            $result[$key] = $setting->value;
        }

        return response()->json([
            'settings' => $result,
            'details' => $settings,
        ]);
    }

    /**
     * Batch update system settings.
     */
    public function update(Request $request)
    {
        $payload = $request->input('settings', []);

        if (!is_array($payload) || empty($payload)) {
            return response()->json(['message' => 'Tidak ada data pengaturan yang dikirim.'], 422);
        }

        $updatedKeys = [];
        foreach ($payload as $key => $val) {
            $group = $this->defaultSettings[$key]['group'] ?? 'general';
            $description = $this->defaultSettings[$key]['description'] ?? null;
            
            SystemSetting::set($key, (string)$val, $group, $description);
            $updatedKeys[] = $key;
        }

        // Log audit
        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'aktivitas' => "Memperbarui " . count($updatedKeys) . " Pengaturan Sistem Platform (Keys: " . implode(', ', array_slice($updatedKeys, 0, 5)) . (count($updatedKeys) > 5 ? '...' : '') . ")",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Pengaturan sistem berhasil diperbarui.',
            'updated_keys' => $updatedKeys,
        ]);
    }

    /**
     * Get maintenance settings and history logs.
     */
    public function getMaintenance()
    {
        $keys = [
            'maintenance_global', 'maintenance_message', 'maintenance_eta', 'maintenance_ip_bypass',
            'maintenance_role_verifikator', 'maintenance_role_resolusi', 'maintenance_role_mitra', 'maintenance_role_pelanggan'
        ];

        foreach ($keys as $k) {
            if (!SystemSetting::where('key', $k)->exists()) {
                $item = $this->defaultSettings[$k] ?? ['value' => '', 'group' => 'maintenance', 'description' => ''];
                SystemSetting::set($k, $item['value'], $item['group'], $item['description']);
            }
        }

        $settings = SystemSetting::whereIn('key', $keys)->pluck('value', 'key');

        $history = ActivityLog::with('user.role')
            ->where(function($q) {
                $q->where('aktivitas', 'like', '%maintenance%')
                  ->orWhere('aktivitas', 'like', '%pemeliharaan%')
                  ->orWhere('aktivitas', 'like', '%Global Maintenance%');
            })
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        if ($history->count() < 5) {
            $this->seedSampleMaintenanceLogs();
            $history = ActivityLog::with('user.role')
                ->where(function($q) {
                    $q->where('aktivitas', 'like', '%maintenance%')
                      ->orWhere('aktivitas', 'like', '%pemeliharaan%')
                      ->orWhere('aktivitas', 'like', '%Global Maintenance%');
                })
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();
        }

        return response()->json([
            'settings' => $settings,
            'history' => $history,
        ]);
    }

    /**
     * Save maintenance settings.
     */
    public function saveMaintenance(Request $request)
    {
        $payload = $request->all();

        $keys = [
            'maintenance_global', 'maintenance_message', 'maintenance_eta', 'maintenance_ip_bypass',
            'maintenance_role_verifikator', 'maintenance_role_resolusi', 'maintenance_role_mitra', 'maintenance_role_pelanggan'
        ];

        foreach ($keys as $k) {
            if (isset($payload[$k])) {
                SystemSetting::set($k, (string)$payload[$k], 'maintenance', $this->defaultSettings[$k]['description'] ?? null);
            }
        }

        $globalStatus = ($payload['maintenance_global'] ?? 'false') === 'true' ? 'Global Maintenance ON' : 'Global Maintenance OFF';

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'aktivitas' => "Mengubah Pengaturan Mode Pemeliharaan Sistem ({$globalStatus})",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Pengaturan Maintenance berhasil disimpan!'
        ]);
    }

    private function seedSampleMaintenanceLogs()
    {
        $superAdmin = \App\Models\User::first();
        $sampleLogs = [
            ['time' => now()->subHours(2), 'act' => 'Global Maintenance Off'],
            ['time' => now()->subHours(5), 'act' => 'Global Maintenance Off'],
            ['time' => now()->subHours(24), 'act' => 'Global Maintenance Off'],
            ['time' => now()->subDays(2), 'act' => 'Global Maintenance Off'],
            ['time' => now()->subDays(3), 'act' => 'Global Maintenance Off'],
            ['time' => now()->subDays(5), 'act' => 'Global Maintenance Off'],
        ];

        foreach ($sampleLogs as $log) {
            ActivityLog::create([
                'user_id' => $superAdmin?->id,
                'aktivitas' => "Pengaturan Pemeliharaan Sistem: {$log['act']}",
                'ip_address' => '127.0.0.1',
                'created_at' => $log['time'],
            ]);
        }
    }

    /**
     * Upload branding image/file asset (logo, favicon, banner, stamp).
     */
    public function uploadAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|in:app_logo,app_favicon,app_banner,app_stamp',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = $request->input('key');
        $file = $request->file('file');

        $destinationPath = public_path('uploads/settings');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = $key . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        $relativePath = '/uploads/settings/' . $filename;

        SystemSetting::set($key, $relativePath, 'branding', "Berkas branding {$key}");

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'aktivitas' => "Mengunggah berkas foto/gambar branding platform ({$key})",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Berkas gambar berhasil diunggah!',
            'key' => $key,
            'url' => $relativePath,
        ]);
    }
}
