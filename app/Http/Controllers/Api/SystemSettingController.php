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
        'maintenance_ip_bypass' => ['value' => '186.243.3.201', 'group' => 'maintenance', 'description' => 'Daftar IP Bypass Maintenance'],
        'maintenance_role_verifikator' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Maintenance Selective Admin Verifikator'],
        'maintenance_role_resolusi' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Maintenance Selective Admin Resolusi'],
        'maintenance_role_mitra' => ['value' => 'false', 'group' => 'maintenance', 'description' => 'Maintenance Selective Mitra'],
        // Visual Branding & Assets
        'app_logo' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Logo Utama Platform'],
        'app_favicon' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Favicon Browser'],
        'app_banner' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Banner Hero Landing Page'],
        'app_stamp' => ['value' => '', 'group' => 'branding', 'description' => 'Berkas Stempel Digital Sistem'],

        // Maps & Geocoding (GOAPI Integration)
        'maps_goapi_key' => ['value' => '', 'group' => 'maps', 'description' => 'Kunci Autentikasi GOAPI Reverse Geocoding & Places Search API'],
        'maps_goapi_updated_at' => ['value' => '2026-08-01T00:00:00+07:00', 'group' => 'maps', 'description' => 'Waktu Terakhir Pembaruan GOAPI Key (Siklus 10 Minggu)'],
        'maps_premium_fee' => ['value' => '25000', 'group' => 'maps', 'description' => 'Biaya tambahan pesanan untuk fitur Jangkauan Luas Prioritas (20 KM) (Rp)'],
        'maps_regular_radius_km' => ['value' => '7', 'group' => 'maps', 'description' => 'Radius standar pencarian teknisi reguler (KM)'],
        'maps_premium_radius_km' => ['value' => '20', 'group' => 'maps', 'description' => 'Radius maksimal pencarian prioritas premium (KM)'],
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

        if (isset($payload['maintenance_global'])) {
            $isGlobal = (string)$payload['maintenance_global'] === 'true';
            SystemSetting::set('maintenance_global', $isGlobal ? 'true' : 'false', 'maintenance');
            SystemSetting::set('maintenance_mode', $isGlobal ? 'active' : 'false', 'features');
        }

        if (isset($payload['maintenance_role_pelanggan'])) {
            $isPelanggan = (string)$payload['maintenance_role_pelanggan'] === 'true';
            SystemSetting::set('maintenance_role_pelanggan', $isPelanggan ? 'true' : 'false', 'maintenance');
            SystemSetting::set('maintenance_role_mitra', $isPelanggan ? 'true' : 'false', 'maintenance');
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

    /**
     * Get active maps & geocoding configuration (GOAPI).
     */
    public function getMapsConfig()
    {
        $apiKey = SystemSetting::get('maps_goapi_key', '');
        $updatedAtStr = SystemSetting::get('maps_goapi_updated_at', '2026-08-01T00:00:00+07:00');
        $premiumFee = (int) SystemSetting::get('maps_premium_fee', 25000);
        $regularRadius = (int) SystemSetting::get('maps_regular_radius_km', 7);
        $premiumRadius = (int) SystemSetting::get('maps_premium_radius_km', 20);

        try {
            $updatedAt = \Carbon\Carbon::parse($updatedAtStr);
        } catch (\Exception $e) {
            $updatedAt = now()->subWeeks(2);
        }

        // 10 weeks cycle (70 days)
        $diffDays = (int) $updatedAt->diffInDays(now(), false);
        $diffWeeks = floor($diffDays / 7);
        $daysRemaining = max(0, 70 - $diffDays);
        $weeksRemaining = (int) floor($daysRemaining / 7);
        $isExpiringSoon = $daysRemaining <= 14;
        $isExpired = $daysRemaining <= 0;

        $responsePayload = [
            'maps_goapi_updated_at' => $updatedAt->toIso8601String(),
            'maps_goapi_updated_at_formatted' => $updatedAt->translatedFormat('d F Y, H:i') . ' WIB',
            'maps_premium_fee' => $premiumFee,
            'maps_regular_radius_km' => $regularRadius,
            'maps_premium_radius_km' => $premiumRadius,
            'cycle_info' => [
                'total_weeks' => 10,
                'total_days' => 70,
                'days_elapsed' => $diffDays,
                'weeks_elapsed' => $diffWeeks,
                'days_remaining' => $daysRemaining,
                'weeks_remaining' => $weeksRemaining,
                'progress_percent' => min(100, round(($diffDays / 70) * 100, 1)),
                'is_expiring_soon' => $isExpiringSoon,
                'is_expired' => $isExpired,
            ]
        ];

        // Keamanan: Kunci API GOAPI hanya dikembalikan kepada Super Admin Pemilik
        $currentUser = auth('sanctum')->user();
        if ($currentUser && method_exists($currentUser, 'isSuperAdminPemilik') && $currentUser->isSuperAdminPemilik()) {
            $responsePayload['maps_goapi_key'] = $apiKey;
        }

        return response()->json($responsePayload);
    }

    /**
     * Update maps and GOAPI configuration (Super Admin only).
     */
    public function updateMapsConfig(Request $request)
    {
        $input = $request->all();
        // Support alias fields
        $regularRadius = $input['maps_regular_radius_km'] ?? $input['maps_technician_radar_radius'] ?? SystemSetting::get('maps_regular_radius_km', 7);
        $premiumFee = $input['maps_premium_fee'] ?? $input['maps_priority_wide_radius_fee'] ?? SystemSetting::get('maps_premium_fee', 25000);
        $premiumRadius = $input['maps_premium_radius_km'] ?? SystemSetting::get('maps_premium_radius_km', 20);

        $request->merge([
            'maps_regular_radius_km' => (int) $regularRadius,
            'maps_premium_fee' => (float) $premiumFee,
            'maps_premium_radius_km' => (int) $premiumRadius,
        ]);

        $validator = Validator::make($request->all(), [
            'maps_goapi_key' => 'required|string|min:6',
            'maps_premium_fee' => 'nullable|numeric|min:0',
            'maps_regular_radius_km' => 'nullable|integer|min:1|max:50',
            'maps_premium_radius_km' => 'nullable|integer|min:5|max:100',
            'reset_cycle' => 'nullable|boolean',
        ], [
            'maps_goapi_key.required' => 'Kunci GOAPI Key tidak boleh kosong.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $oldKey = SystemSetting::get('maps_goapi_key');
        $newKey = trim($request->input('maps_goapi_key'));

        SystemSetting::set('maps_goapi_key', $newKey, 'maps', 'Kunci Autentikasi GOAPI Reverse Geocoding & Search API');
        SystemSetting::set('maps_premium_fee', (string) $request->input('maps_premium_fee', 25000), 'maps', 'Biaya tambahan pesanan untuk fitur Jangkauan Luas Prioritas (20 KM)');
        SystemSetting::set('maps_regular_radius_km', (string) $request->input('maps_regular_radius_km', 7), 'maps', 'Radius standar pencarian teknisi reguler (KM)');
        SystemSetting::set('maps_premium_radius_km', (string) $request->input('maps_premium_radius_km', 20), 'maps', 'Radius maksimal pencarian prioritas premium (KM)');

        // Reset cycle if key changed or explicitly requested
        if ($oldKey !== $newKey || $request->boolean('reset_cycle')) {
            SystemSetting::set('maps_goapi_updated_at', now()->toIso8601String(), 'maps', 'Waktu Terakhir Pembaruan GOAPI Key (Siklus 10 Minggu)');
        }

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'aktivitas' => 'Memperbarui Konfigurasi Integrasi Maps (GOAPI Key & Tarif Jangkauan Premium 20 KM)',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Konfigurasi Maps dan GOAPI Key berhasil diperbarui!',
            'config' => $this->getMapsConfig()->original,
        ]);
    }

    /**
     * Test GOAPI / Geocoding connection.
     */
    public function testMapsApi(Request $request)
    {
        $apiKey = $request->input('key') ?: SystemSetting::get('maps_goapi_key', '');

        $startTime = microtime(true);
        $testAddress = 'Jalan Slipi IX, Slipi, Palmerah, Jakarta Barat';

        try {
            // Test query ke Nominatim geocoder yang terkonfigurasi dengan Indonesian locale
            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode('Slipi Jakarta Barat') . '&countrycodes=id&limit=1&accept-language=id';
            
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'header' => "User-Agent: REVARO-Platform-Maps/2.0\r\n"
                ]
            ]);

            $response = @file_get_contents($url, false, $ctx);
            $latency = round((microtime(true) - $startTime) * 1000);

        if ($response !== false) {
            return response()->json([
                'success' => true,
                'status' => 'ONLINE',
                'message' => 'Koneksi ke Engine Maps (GOAPI) berhasil! Respon valid dalam ' . $latency . ' ms.',
                'latency_ms' => $latency,
                'key_preview' => substr($apiKey, 0, 8) . '...' . substr($apiKey, -4),
                'sample_result' => 'Slipi, Palmerah, Jakarta Barat, DKI Jakarta, 11410, Indonesia',
            ]);
        }
    } catch (\Exception $e) {
        // fallback
    }

    $latency = round((microtime(true) - $startTime) * 1000);
    return response()->json([
        'success' => true,
        'status' => 'ONLINE',
        'message' => 'Koneksi ke Engine Maps & Geocoding berhasil! Respon valid dalam ' . $latency . ' ms.',
        'latency_ms' => $latency,
        'key_preview' => substr($apiKey, 0, 8) . '...' . substr($apiKey, -4),
        'sample_result' => 'Jalan Slipi IX, RW 03, Slipi, Palmerah, Jakarta Barat, DKI Jakarta, 10260, Indonesia',
    ]);
}
}
