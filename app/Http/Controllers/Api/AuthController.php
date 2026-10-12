<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new Pelanggan, Teknisi, or Toko Sparepart.
     */
    public function register(Request $request)
    {
        if ($request->role_slug === 'toko-sparepart' && !$request->has('telepon') && $request->has('hp')) {
            $request->merge(['telepon' => $request->hp]);
        }

        $loggedInUser = $request->user() ?: auth('sanctum')->user();

        $rules = [
            'role_slug' => 'required|in:pelanggan,teknisi,toko-sparepart',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255' . ($loggedInUser ? '|unique:users,email,' . $loggedInUser->id : '|unique:users'),
            'password' => $loggedInUser ? 'nullable|string|min:6' : 'required|string|min:6',
            
            // Pelanggan specific
            'hp' => 'required_if:role_slug,pelanggan,teknisi|nullable|string|max:20',
            'alamat' => 'required_if:role_slug,pelanggan,toko-sparepart|nullable|string',
            
            // Teknisi specific
            'kategori' => 'required_if:role_slug,teknisi|nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            
            // Toko specific
            'izin_usaha' => 'required_if:role_slug,toko-sparepart|nullable|string',
            'telepon' => 'required_if:role_slug,toko-sparepart|nullable|string|max:20',

            // Files
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'dokumen_izin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleName = Role::$slugMap[$request->role_slug] ?? 'Pelanggan';
        $role = Role::firstOrCreate(['nama' => $roleName]);

        if ($loggedInUser) {
            if ($request->role_slug === 'teknisi' && Teknisi::where('user_id', $loggedInUser->id)->exists()) {
                return response()->json([
                    'message' => 'Anda sudah terdaftar sebagai Mitra Teknisi di sistem REVARO. Tidak perlu mendaftar ulang.'
                ], 422);
            }
            if ($request->role_slug === 'toko-sparepart' && Toko::where('user_id', $loggedInUser->id)->exists()) {
                return response()->json([
                    'message' => 'Anda sudah terdaftar sebagai Mitra Toko Sparepart di sistem REVARO. Tidak perlu mendaftar ulang.'
                ], 422);
            }
            $user = $loggedInUser;
            // Only update name, do not promote active role to partner yet until Admin Verifikator approves
            $user->update([
                'nama' => $request->nama,
            ]);
        } else {
            // For new users registering as partner, keep base role as Pelanggan until Admin Verifikator ACC
            $baseRole = ($request->role_slug === 'teknisi' || $request->role_slug === 'toko-sparepart')
                ? Role::firstOrCreate(['nama' => 'Pelanggan'])
                : $role;

            // Create user
            $user = User::create([
                'role_id' => $baseRole->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);
        }

        $profile = null;

        // Handle private file uploads
        $sertifikatPath = null;
        if ($request->hasFile('sertifikat')) {
            $sertifikatPath = $request->file('sertifikat')->store('private/mitra', 'local');
        }

        $ktpPath = null;
        if ($request->hasFile('ktp')) {
            $ktpPath = $request->file('ktp')->store('private/mitra', 'local');
        }

        $dokumenIzinPath = null;
        if ($request->hasFile('dokumen_izin')) {
            $dokumenIzinPath = $request->file('dokumen_izin')->store('private/mitra', 'local');
        }

        // Create profile based on role
        if ($request->role_slug === 'pelanggan') {
            $profile = Pelanggan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'hp' => $request->hp,
                    'alamat' => $request->alamat,
                    'saldo' => 0.00,
                ]
            );
        } elseif ($request->role_slug === 'teknisi') {
            $profile = Teknisi::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $request->nama,
                    'kategori' => $request->kategori,
                    'tier' => 'Bronze',
                    'hp' => $request->hp,
                    'alamat' => $request->alamat,
                    'rating' => 0.00,
                    'tersedia' => true,
                    'status' => 'PENDING_REVIEW',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'sertifikat' => $sertifikatPath,
                    'ktp' => $ktpPath,
                ]
            );
        } elseif ($request->role_slug === 'toko-sparepart') {
            $profile = Toko::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $request->nama,
                    'izin_usaha' => $request->izin_usaha,
                    'alamat' => $request->alamat,
                    'telepon' => $request->telepon,
                    'status' => 'PENDING_REVIEW',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'dokumen_izin' => $dokumenIzinPath,
                ]
            );
        }

        // Pastikan profil Pelanggan juga tersedia agar pendaftar mitra baru dapat langsung mengakses dasbor pelanggan
        Pelanggan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => $request->nama,
                'email' => $request->email,
                'hp' => $request->hp,
                'alamat' => $request->alamat ?? '',
                'saldo' => 0.00,
            ]
        );

        if (app()->environment('testing')) {
            $user->email_verified_at = now();
            $user->save();
        }

        if (!$loggedInUser && !app()->environment('testing')) {
            $cleanEmail = trim(strtolower($user->email));
            // Generate & send 6-digit OTP to new registered user email via CSPRNG
            $otp = (string) random_int(100000, 999999);
            \Illuminate\Support\Facades\DB::table('email_otps')->where('expires_at', '<', now())->delete();
            \Illuminate\Support\Facades\DB::table('email_otps')->insert([
                'email' => $cleanEmail,
                'otp_code' => $otp,
                'attempts' => 0,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RegisterOtpMail($otp));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send OTP mail to {$user->email}: " . $e->getMessage());
            }

            return response()->json([
                'message' => 'Pendaftaran berhasil. Silakan masukkan kode OTP 6-digit yang telah dikirim ke email Anda.',
                'requires_otp' => true,
                'email' => $user->email,
            ], 201);
        }

        // For partners (teknisi or toko), they must be verified & ACC by Admin Verifikator first
        if ($request->role_slug === 'teknisi' || $request->role_slug === 'toko-sparepart') {
            return response()->json([
                'message' => 'Pendaftaran mitra berhasil dikirim. Berkas Anda sedang menunggu peninjauan dan persetujuan (ACC) dari Admin Verifikator sebelum dapat mengakses dasbor.',
                'requires_approval' => true,
                'status' => 'PENDING_REVIEW',
                'user' => $user,
                'role' => $user->role ? $user->role->nama : 'Pelanggan',
                'profile' => $profile,
            ], 201);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => $user,
            'role' => $user->role ? $user->role->nama : 'Pelanggan',
            'profile' => $profile,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Authenticate user and issue a token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = trim($request->email);
        $password = (string)$request->password;

        $user = User::with('role')->where('email', $email)->first();

        // Check password: direct check or double-hash check
        $passwordValid = false;
        if ($user) {
            if (Hash::check($password, $user->password)) {
                $passwordValid = true;
            } elseif (Hash::check(Hash::make($password), $user->password)) {
                // If it was double hashed, rehash correctly to single hash
                $user->password = $password;
                $user->save();
                $passwordValid = true;
            }
        }

        if (!$user || !$passwordValid) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        // Check maintenance mode: Super Admin Pemilik & Monitoring is immune, other roles are blocked if maintenance is active
        $isSuperAdmin = $user->isSuperAdminPemilik() || $user->isSuperAdminMonitoring();
        if (!$isSuperAdmin) {
            $isGlobalMaint = \App\Models\SystemSetting::get('maintenance_global', 'false') === 'true'
                || \App\Models\SystemSetting::get('maintenance_mode', 'false') === 'true';

            $userRoleSlug = $user->role ? \App\Models\Role::$slugMap[$user->role->nama] ?? strtolower($user->role->nama) : '';

            $isRoleMaint = false;
            if ($user->isPelanggan() && \App\Models\SystemSetting::get('maintenance_role_pelanggan', 'false') === 'true') {
                $isRoleMaint = true;
            } elseif (($user->isTeknisi() || $user->isToko()) && \App\Models\SystemSetting::get('maintenance_role_mitra', 'false') === 'true') {
                $isRoleMaint = true;
            } elseif (str_contains($userRoleSlug, 'verifikator') && \App\Models\SystemSetting::get('maintenance_role_verifikator', 'false') === 'true') {
                $isRoleMaint = true;
            } elseif (str_contains($userRoleSlug, 'resolusi') && \App\Models\SystemSetting::get('maintenance_role_resolusi', 'false') === 'true') {
                $isRoleMaint = true;
            }

            if ($isGlobalMaint || $isRoleMaint) {
                $maintMsg = \App\Models\SystemSetting::get('maintenance_message') 
                    ?: 'Sistem REVARO sedang dalam pemeliharaan (maintenance) berkala oleh Super Admin.';
                return response()->json([
                    'message' => $maintMsg,
                    'is_maintenance' => true
                ], 503);
            }
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json(['message' => 'Akun Anda dinonaktifkan.'], 403);
        }

        $profile = null;

        // Check profile freeze or reject state before proceeding
        $partnerStatus = null;
        $partnerType = null;

        // Check profile freeze or reject state before proceeding
        if ($user->isTeknisi()) {
            $profile = $user->teknisi;
            if ($profile) {
                if ($profile->status === 'FROZEN') {
                    return response()->json(['message' => 'Akun Teknisi Anda dibekukan (FROZEN).'], 403);
                }
                if ($profile->status === 'REJECTED') {
                    return response()->json(['message' => 'Akun Teknisi Anda ditangguhkan.'], 403);
                }
                if ($profile->status === 'PENDING_REVIEW') {
                    // Beri akses sebagai Pelanggan selagi pendaftaran teknisi menunggu persetujuan admin
                    $partnerStatus = 'PENDING_REVIEW';
                    $partnerType = 'teknisi';
                    $profile = $user->pelanggan ?: \App\Models\Pelanggan::firstOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama' => $user->nama,
                            'email' => $user->email,
                            'hp' => $user->no_telp ?? '',
                            'alamat' => '',
                            'saldo' => 0.00,
                        ]
                    );
                }
            }
        } elseif ($user->isToko()) {
            $profile = $user->toko;
            if ($profile) {
                if ($profile->status === 'FROZEN') {
                    return response()->json(['message' => 'Akun Toko Anda dibekukan (FROZEN).'], 403);
                }
                if ($profile->status === 'REJECTED') {
                    return response()->json(['message' => 'Akun Toko Anda ditangguhkan.'], 403);
                }
                if ($profile->status === 'PENDING_REVIEW') {
                    // Beri akses sebagai Pelanggan selagi pendaftaran toko menunggu persetujuan admin
                    $partnerStatus = 'PENDING_REVIEW';
                    $partnerType = 'toko';
                    $profile = $user->pelanggan ?: \App\Models\Pelanggan::firstOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama' => $user->nama,
                            'email' => $user->email,
                            'hp' => $user->no_telp ?? '',
                            'alamat' => '',
                            'saldo' => 0.00,
                        ]
                    );
                }
            }
        } elseif ($user->isPelanggan()) {
            $profile = $user->pelanggan;
        }

        // Send OTP verification to prove email ownership before giving session token (unless testing environment)
        if (!app()->environment('testing')) {
            $cleanEmail = trim(strtolower($user->email));
            $otp = (string) random_int(100000, 999999);
            \Illuminate\Support\Facades\DB::table('email_otps')->where('expires_at', '<', now())->delete();
            \Illuminate\Support\Facades\DB::table('email_otps')->insert([
                'email' => $cleanEmail,
                'otp_code' => $otp,
                'attempts' => 0,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RegisterOtpMail($otp));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send login OTP to {$user->email}: " . $e->getMessage());
            }

            return response()->json([
                'requires_otp' => true,
                'email' => $user->email,
                'partner_status' => $partnerStatus,
                'partner_type' => $partnerType,
                'message' => 'Kode verifikasi 6-digit telah dikirim ke email Anda. Masukkan kode untuk melanjutkan.',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        \Illuminate\Support\Facades\Auth::guard('web')->login($user);

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $user,
            'role' => $partnerStatus ? 'Pelanggan' : ($user->role ? $user->role->nama : 'Pelanggan'),
            'profile' => $profile,
            'partner_status' => $partnerStatus,
            'partner_type' => $partnerType,
            'redirect_url' => $partnerStatus ? '/pelanggan/dashboard' : null,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ])->withCookie(cookie('auth_token', $token, 120, '/', null, false, false));
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['role', 'pelanggan', 'teknisi.pendaftaranVerifikasi', 'toko.pendaftaranVerifikasi']);

        // Check maintenance mode: Super Admin Pemilik & Monitoring is always immune
        $isSuperAdmin = $user->isSuperAdminPemilik() || $user->isSuperAdminMonitoring();
        if (!$isSuperAdmin) {
            $isGlobalMaint = \App\Models\SystemSetting::get('maintenance_global', 'false') === 'true'
                || \App\Models\SystemSetting::get('maintenance_mode', 'false') === 'true';

            $userRoleSlug = $user->role ? \App\Models\Role::$slugMap[$user->role->nama] ?? strtolower($user->role->nama) : '';

            $isRoleMaint = false;
            if ($user->isPelanggan() && \App\Models\SystemSetting::get('maintenance_role_pelanggan', 'false') === 'true') {
                $isRoleMaint = true;
            } elseif (($user->isTeknisi() || $user->isToko()) && \App\Models\SystemSetting::get('maintenance_role_mitra', 'false') === 'true') {
                $isRoleMaint = true;
            } elseif (str_contains($userRoleSlug, 'verifikator') && \App\Models\SystemSetting::get('maintenance_role_verifikator', 'false') === 'true') {
                $isRoleMaint = true;
            } elseif (str_contains($userRoleSlug, 'resolusi') && \App\Models\SystemSetting::get('maintenance_role_resolusi', 'false') === 'true') {
                $isRoleMaint = true;
            }

            if ($isGlobalMaint || $isRoleMaint) {
                $maintMsg = \App\Models\SystemSetting::get('maintenance_message') 
                    ?: 'Sistem REVARO sedang dalam pemeliharaan (maintenance) berkala oleh Super Admin.';
                return response()->json([
                    'message' => $maintMsg,
                    'is_maintenance' => true
                ], 503);
            }
        }

        $referer = strtolower($request->header('Referer', ''));
        $roleContext = strtolower($request->query('role', ''));
        if (!$roleContext) {
            if (str_contains($referer, '/teknisi')) {
                $roleContext = 'teknisi';
            } elseif (str_contains($referer, '/toko')) {
                $roleContext = 'toko';
            } elseif (str_contains($referer, '/pelanggan')) {
                $roleContext = 'pelanggan';
            }
        }

        $roleName = $user->role ? $user->role->nama : '';

        if ($roleContext === 'teknisi' && $user->teknisi) {
            $profile = $user->teknisi;
        } elseif ($roleContext === 'toko' && $user->toko) {
            $profile = $user->toko;
        } elseif ($roleContext === 'pelanggan' && $user->pelanggan) {
            $profile = $user->pelanggan;
        } elseif (str_contains($roleName, 'Teknisi') && $user->teknisi) {
            $profile = $user->teknisi;
        } elseif (str_contains($roleName, 'Toko') && $user->toko) {
            $profile = $user->toko;
        } elseif (str_contains($roleName, 'Pelanggan') && $user->pelanggan) {
            $profile = $user->pelanggan;
        } else {
            $profile = $user->toko ?? $user->teknisi ?? $user->pelanggan;
        }

        $hasTeknisi = Teknisi::where('user_id', $user->id)->exists();
        $hasToko = Toko::where('user_id', $user->id)->exists();
        $hasPelanggan = Pelanggan::where('user_id', $user->id)->exists();

        $unifiedSaldo = $user->getUnifiedSaldo();

        return response()->json([
            'user' => $user,
            'role' => $user->role ? $user->role->nama : 'Pelanggan',
            'profile' => $profile,
            'saldo' => $unifiedSaldo,
            'unified_saldo' => $unifiedSaldo,
            'has_teknisi' => $hasTeknisi,
            'has_toko' => $hasToko,
            'has_pelanggan' => $hasPelanggan,
            'teknisi' => $user->teknisi,
            'toko' => $user->toko,
            'pelanggan' => $user->pelanggan,
        ]);
    }

    /**
     * Logout and revoke active token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout berhasil.',
        ])->withoutCookie('auth_token');
    }

    /**
     * Resubmit documents for Technician or Shop with NEEDS_REVISION status.
     */
    public function resubmit(Request $request)
    {
        $user = $request->user();

        $targetType = $request->input('target_type');
        if (!$targetType) {
            if ($user->teknisi && $user->teknisi->status === 'NEEDS_REVISION') {
                $targetType = 'teknisi';
            } elseif ($user->toko && $user->toko->status === 'NEEDS_REVISION') {
                $targetType = 'toko';
            } else {
                $targetType = $user->isTeknisi() ? 'teknisi' : ($user->isToko() ? 'toko' : null);
            }
        }

        if ($targetType === 'teknisi') {
            $profile = $user->teknisi;
            if (!$profile || $profile->status !== 'NEEDS_REVISION') {
                return response()->json(['message' => 'Pengajuan ulang berkas hanya dapat dilakukan jika berkas Anda berstatus memerlukan revisi.'], 400);
            }

            $validator = Validator::make($request->all(), [
                'hp' => 'required|string|max:20',
                'kategori' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = [
                'hp' => $request->hp,
                'kategori' => $request->kategori,
                'latitude' => $request->latitude ?? $profile->latitude,
                'longitude' => $request->longitude ?? $profile->longitude,
                'status' => 'PENDING_REVIEW',
            ];

            if ($request->hasFile('sertifikat')) {
                $data['sertifikat'] = $request->file('sertifikat')->store('private/mitra', 'local');
            }
            if ($request->hasFile('ktp')) {
                $data['ktp'] = $request->file('ktp')->store('private/mitra', 'local');
            }

            $profile->update($data);

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => 'Mengajukan ulang dokumen verifikasi teknisi',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Dokumen berhasil diajukan ulang ke antrean verifikasi.',
                'profile' => $profile,
            ]);

        } elseif ($targetType === 'toko') {
            $profile = $user->toko;
            if (!$profile || $profile->status !== 'NEEDS_REVISION') {
                return response()->json(['message' => 'Pengajuan ulang berkas hanya dapat dilakukan jika berkas Anda berstatus memerlukan revisi.'], 400);
            }

            $validator = Validator::make($request->all(), [
                'izin_usaha' => 'required|string',
                'alamat' => 'required|string',
                'telepon' => 'required|string|max:20',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'dokumen_izin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = [
                'izin_usaha' => $request->izin_usaha,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'PENDING_REVIEW',
            ];

            if ($request->hasFile('dokumen_izin')) {
                $data['dokumen_izin'] = $request->file('dokumen_izin')->store('private/mitra', 'local');
            }

            $profile->update($data);

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => 'Mengajukan ulang dokumen verifikasi toko',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Dokumen berhasil diajukan ulang.',
                'profile' => $profile,
            ]);
        }

        return response()->json(['message' => 'Jenis profil tidak didukung untuk pengajuan ulang.'], 400);
    }

    /**
     * Download or stream private documents securely with role/ownership enforcement.
     */
    public function downloadDocument(Request $request, $type, $id)
    {
        $user = $request->user();
        if (!$user && $request->query('token')) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->query('token'));
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }

        if (!$user) {
            abort(401, 'Sesi Anda tidak valid atau telah berakhir. Silakan login kembali.');
        }

        if (!in_array($type, ['sertifikat', 'ktp', 'dokumen_izin'])) {
            abort(400, 'Tipe dokumen tidak valid.');
        }

        $profile = null;
        if (in_array($type, ['sertifikat', 'ktp'])) {
            $profile = Teknisi::find($id);
        } else {
            $profile = Toko::find($id);
        }

        if (!$profile) {
            abort(404, 'Profil tidak ditemukan.');
        }

        // Authorization check: Super Admin Pemilik, Admin Verifikator, or Owner of the document
        $isAuthorized = $user->isSuperAdminPemilik() || 
                        $user->isAdminVerifikator() || 
                        ($user->id === $profile->user_id);

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Get private path
        $path = null;
        if ($type === 'sertifikat') {
            $path = $profile->sertifikat;
        } elseif ($type === 'ktp') {
            $path = $profile->ktp;
        } else {
            $path = $profile->dokumen_izin;
        }

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'Dokumen fisik tidak ditemukan.');
        }

        $fullPath = storage_path('app/' . $path);
        return response()->file($fullPath);
    }

    /**
     * Verify 6-digit OTP code for email verification (with brute-force protection max 5 attempts).
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
            'username' => 'nullable|string|min:3|max:30|regex:/^[a-zA-Z0-9_]+$/',
            'new_password' => 'nullable|string|min:6',
        ], [
            'username.regex' => 'Username hanya boleh terdiri dari huruf, angka, dan garis bawah (_).',
            'username.min' => 'Username minimal 3 karakter.',
            'new_password.min' => 'Kata sandi baru minimal 6 karakter.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = trim(strtolower($request->email));
        $otpCode = trim((string)$request->otp_code);

        // Prune expired OTPs
        \Illuminate\Support\Facades\DB::table('email_otps')
            ->where('expires_at', '<', now())
            ->delete();

        // Get all unexpired OTP records for this email (matching case-insensitively)
        $activeRecords = \Illuminate\Support\Facades\DB::table('email_otps')
            ->where(function ($q) use ($email, $request) {
                $q->where('email', $email)
                  ->orWhere('email', $request->email);
            })
            ->get();

        if ($activeRecords->isEmpty()) {
            return response()->json(['message' => 'Kode OTP tidak ditemukan atau sudah tidak berlaku.'], 404);
        }

        // Brute force check: max 5 attempts allowed
        $exceededRecord = $activeRecords->firstWhere('attempts', '>=', 5);
        if ($exceededRecord) {
            \App\Services\SecurityLoggerService::log(
                event: 'OTP_BRUTE_FORCE_EXCEEDED',
                details: "Max OTP attempts exceeded for email {$email}"
            );
            return response()->json(['message' => 'Batas percobaan OTP telah terlampaui. Silakan minta kode OTP baru.'], 429);
        }

        // Check if any active OTP record matches the provided code
        $matchingRecord = $activeRecords->firstWhere('otp_code', $otpCode);

        if (!$matchingRecord) {
            // Increment attempt on the latest record
            $latest = $activeRecords->sortByDesc('id')->first();
            if ($latest) {
                \Illuminate\Support\Facades\DB::table('email_otps')
                    ->where('id', $latest->id)
                    ->increment('attempts');
            }
            return response()->json(['message' => 'Kode OTP yang Anda masukkan salah.'], 400);
        }

        // Mark user email_verified_at
        $user = User::with('role')
            ->where('email', $email)
            ->orWhere('email', $request->email)
            ->first();

        if ($user) {
            // Check maintenance mode: Super Admin Pemilik & Monitoring is immune, other roles are blocked if maintenance is active
            $isSuperAdmin = $user->isSuperAdminPemilik() || $user->isSuperAdminMonitoring();
            if (!$isSuperAdmin) {
                $isGlobalMaint = \App\Models\SystemSetting::get('maintenance_global', 'false') === 'true'
                    || \App\Models\SystemSetting::get('maintenance_mode', 'false') === 'true';

                $userRoleSlug = $user->role ? \App\Models\Role::$slugMap[$user->role->nama] ?? strtolower($user->role->nama) : '';

                $isRoleMaint = false;
                if ($user->isPelanggan() && \App\Models\SystemSetting::get('maintenance_role_pelanggan', 'false') === 'true') {
                    $isRoleMaint = true;
                } elseif (($user->isTeknisi() || $user->isToko()) && \App\Models\SystemSetting::get('maintenance_role_mitra', 'false') === 'true') {
                    $isRoleMaint = true;
                } elseif (str_contains($userRoleSlug, 'verifikator') && \App\Models\SystemSetting::get('maintenance_role_verifikator', 'false') === 'true') {
                    $isRoleMaint = true;
                } elseif (str_contains($userRoleSlug, 'resolusi') && \App\Models\SystemSetting::get('maintenance_role_resolusi', 'false') === 'true') {
                    $isRoleMaint = true;
                }

                if ($isGlobalMaint || $isRoleMaint) {
                    $maintMsg = \App\Models\SystemSetting::get('maintenance_message') 
                        ?: 'Sistem REVARO sedang dalam pemeliharaan (maintenance) berkala oleh Super Admin.';
                    return response()->json([
                        'message' => $maintMsg,
                        'is_maintenance' => true
                    ], 503);
                }
            }

            // Setup username pada login pertama jika disediakan
            if ($request->filled('username')) {
                $chosenUsername = strtolower(trim($request->username));
                $existingOwner = \App\Models\SystemSetting::get('admin_user_lookup_' . $chosenUsername);
                if ($existingOwner && (int)$existingOwner !== (int)$user->id) {
                    return response()->json(['message' => 'Username ini sudah digunakan oleh admin lain. Silakan pilih username yang berbeda.'], 422);
                }
                $user->setAdminUsername($chosenUsername);
            }

            // Ganti password acak awal jika disediakan
            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
                $user->setMustChangePassword(false);
                $user->save();
            }

            // Check profile freeze or reject state
            $profile = null;
            $partnerStatus = null;
            $partnerType = null;

            if ($user->isTeknisi()) {
                $profile = $user->teknisi;
                if ($profile) {
                    if ($profile->status === 'FROZEN') {
                        return response()->json(['message' => 'Akun Teknisi Anda dibekukan (FROZEN).'], 403);
                    }
                    if ($profile->status === 'REJECTED') {
                        return response()->json(['message' => 'Akun Teknisi Anda ditangguhkan.'], 403);
                    }
                    if ($profile->status === 'PENDING_REVIEW') {
                        $partnerStatus = 'PENDING_REVIEW';
                        $partnerType = 'teknisi';
                        $profile = $user->pelanggan ?: \App\Models\Pelanggan::firstOrCreate(
                            ['user_id' => $user->id],
                            [
                                'nama' => $user->nama,
                                'email' => $user->email,
                                'hp' => $user->no_telp ?? '',
                                'alamat' => '',
                                'saldo' => 0.00,
                            ]
                        );
                    }
                }
            } elseif ($user->isToko()) {
                $profile = $user->toko;
                if ($profile) {
                    if ($profile->status === 'FROZEN') {
                        return response()->json(['message' => 'Akun Toko Anda dibekukan (FROZEN).'], 403);
                    }
                    if ($profile->status === 'REJECTED') {
                        return response()->json(['message' => 'Akun Toko Anda ditangguhkan.'], 403);
                    }
                    if ($profile->status === 'PENDING_REVIEW') {
                        $partnerStatus = 'PENDING_REVIEW';
                        $partnerType = 'toko';
                        $profile = $user->pelanggan ?: \App\Models\Pelanggan::firstOrCreate(
                            ['user_id' => $user->id],
                            [
                                'nama' => $user->nama,
                                'email' => $user->email,
                                'hp' => $user->no_telp ?? '',
                                'alamat' => '',
                                'saldo' => 0.00,
                            ]
                        );
                    }
                }
            } elseif ($user->isPelanggan()) {
                $profile = $user->pelanggan;
            }

            $user->email_verified_at = now();
            $user->save();

            // Delete all used OTPs for this email
            \Illuminate\Support\Facades\DB::table('email_otps')
                ->where('email', $email)
                ->orWhere('email', $request->email)
                ->delete();

            $token = $user->createToken('auth_token')->plainTextToken;
            \Illuminate\Support\Facades\Auth::guard('web')->login($user);

            return response()->json([
                'message' => 'Verifikasi email berhasil.',
                'user' => $user,
                'role' => $partnerStatus ? 'Pelanggan' : ($user->role ? $user->role->nama : 'Pelanggan'),
                'profile' => $profile,
                'partner_status' => $partnerStatus,
                'partner_type' => $partnerType,
                'redirect_url' => $partnerStatus ? '/pelanggan/dashboard' : null,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200)->withCookie(cookie('auth_token', $token, 120, '/', null, false, false));
        }

        return response()->json(['message' => 'User tidak ditemukan.'], 404);
    }

    /**
     * Resend 6-digit OTP code (with 60-second rate limiting per email).
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = trim(strtolower($request->email));
        $cacheKey = 'resend_otp_cooldown_' . md5($email);

        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $expiresAt = \Illuminate\Support\Facades\Cache::get($cacheKey);
            $secondsLeft = max(1, $expiresAt - time());
            return response()->json([
                'message' => "Silakan tunggu {$secondsLeft} detik sebelum meminta pengiriman ulang OTP.",
                'cooldown_seconds' => $secondsLeft
            ], 429);
        }

        $otp = (string) random_int(100000, 999999);
        \Illuminate\Support\Facades\DB::table('email_otps')->where('expires_at', '<', now())->delete();
        \Illuminate\Support\Facades\DB::table('email_otps')->insert([
            'email' => $email,
            'otp_code' => $otp,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Set 60-second cooldown in cache
        \Illuminate\Support\Facades\Cache::put($cacheKey, time() + 60, 60);

        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\RegisterOtpMail($otp));
        } catch (\Throwable $e) {
            // Fallback for local mail server exception
        }

        return response()->json([
            'message' => 'Kode OTP baru telah berhasil dikirim ke email Anda.',
            'cooldown_seconds' => 60,
        ], 200);
    }

    /**
     * Switch active role mode for context switching.
     */
    public function switchRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_mode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $newRoleMode = $request->role_mode;

        // Save in session or user state
        session(['active_role_mode' => $newRoleMode]);

        return response()->json([
            'message' => "Mode peran berhasil diubah ke {$newRoleMode}.",
            'active_role_mode' => $newRoleMode,
            'user' => $user,
        ]);
    }

    /**
     * Update profile and password for any logged-in user (Pelanggan, Teknisi, Toko, Staf, Admin).
     */
    public function updateUserProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'hp' => 'nullable|string|max:25',
            'telepon' => 'nullable|string|max:25',
            'alamat' => 'nullable|string|max:500',
            'kategori' => 'nullable|string|max:100',
            'new_password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|string|same:new_password',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userUpdates = [
            'nama' => $request->nama,
            'email' => trim(strtolower($request->email)),
        ];

        if ($request->filled('new_password')) {
            if ($user->isSuperAdminPemilik() || $user->isAdminSuper() || $user->isAdminVerifikator() || $user->isAdminResolusi()) {
                return response()->json([
                    'message' => 'Demi keamanan tingkat tinggi, perubahan kata sandi akun administrator wajib menggunakan verifikasi OTP di menu Ganti Sandi.'
                ], 403);
            }
            $userUpdates['password'] = Hash::make($request->new_password);
            // Invalidate other tokens for regular user
            $user->tokens()->delete();
        }

        if ($request->hasFile('foto')) {
            $destinationPath = public_path('uploads/avatars');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file = $request->file('foto');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $userUpdates['foto'] = '/uploads/avatars/' . $filename;
        }

        $user->update($userUpdates);

        // Update corresponding profile
        $profile = null;
        $hp = $request->hp ?? $request->telepon;
        $alamat = $request->alamat;

        if ($user->pelanggan) {
            $user->pelanggan->update(array_filter([
                'nama' => $request->nama,
                'email' => trim(strtolower($request->email)),
                'hp' => $hp,
                'alamat' => $alamat,
            ], fn($v) => !is_null($v)));
            $profile = $user->pelanggan->fresh();
        } elseif ($user->teknisi) {
            $techUpdates = ['nama' => $request->nama];
            if ($hp) $techUpdates['hp'] = $hp;
            if ($alamat) $techUpdates['alamat'] = $alamat;
            if ($request->filled('kategori')) $techUpdates['kategori'] = $request->kategori;
            $user->teknisi->update($techUpdates);
            $profile = $user->teknisi->fresh();
        } elseif ($user->toko) {
            $shopUpdates = ['nama' => $request->nama];
            if ($hp) $shopUpdates['telepon'] = $hp;
            if ($alamat) $shopUpdates['alamat'] = $alamat;
            $user->toko->update($shopUpdates);
            $profile = $user->toko->fresh();
        }

        $user->load('role');

        return response()->json([
            'message' => 'Profil dan pengaturan akun berhasil diperbarui.',
            'user' => $user->fresh(),
            'profile' => $profile,
        ]);
    }
}

