<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Dapatkan instance Socialite Google driver yang dikonfigurasi dengan aman.
     */
    protected function getGoogleDriver()
    {
        $driver = Socialite::driver('google')->stateless();

        // Konfigurasi sertifikat CA bundle untuk lingkungan Windows Laragon
        $caPath = 'C:/laragon/etc/ssl/cacert.pem';
        $verify = file_exists($caPath) ? $caPath : true;

        if (method_exists($driver, 'setHttpClient')) {
            $driver->setHttpClient(new \GuzzleHttp\Client([
                'verify' => $verify,
                'timeout' => 15,
            ]));
        }

        return $driver;
    }

    /**
     * Mengarahkan pengguna ke halaman OAuth Google.
     */
    public function redirectToGoogle()
    {
        return $this->getGoogleDriver()->redirect();
    }

    /**
     * Menangani callback yang dikembalikan oleh Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        // 1. Cek jika user membatalkan login di Google
        if ($request->has('error')) {
            return redirect('/')->with('error', 'Login dengan Google dibatalkan atau terjadi kesalahan.');
        }

        try {
            // Gunakan driver terkonfigurasi untuk mengambil profil pengguna Google
            $googleUser = $this->getGoogleDriver()->user();
        } catch (\Exception $e) {
            Log::error('Google Auth Callback Error: ' . $e->getMessage(), ['exception' => $e]);
            $msg = $e->getMessage() ?: 'Sesi autentikasi kedaluwarsa. Silakan coba kembali.';
            return redirect('/')->with('error', 'Gagal memproses autentikasi Google: ' . $msg);
        }

        // 2. Cari apakah user sudah ada berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // 3. Jika user sudah ada
        if ($user) {
            // Cek apakah akun aktif
            if (!$user->is_active) {
                return redirect('/')->with('error', 'Akun Anda dinonaktifkan atau diblokir.');
            }

            // Proteksi: Larang akun staf/admin login melalui Google OAuth
            $adminRoleNames = [
                'Super Admin (Pemilik Sistem)',
                'Super Admin (Monitoring Kinerja)',
                'Admin Verifikator',
                'Admin Resolusi'
            ];
            if ($user->role && in_array($user->role->nama, $adminRoleNames)) {
                return redirect('/')->with('error', 'Akun administrator/staf wajib masuk menggunakan email & password melalui portal resmi.');
            }

            // Cek mode maintenance sistem
            if (\App\Models\SystemSetting::get('maintenance_mode', 'false') === 'true') {
                return redirect('/')->with('error', 'Sistem REVARO sedang dalam pemeliharaan (maintenance).');
            }

            // Update google_id & avatar jika belum tersimpan
            $updateData = [];
            if (!$user->google_id) {
                $updateData['google_id'] = $googleUser->getId();
            }
            if (!$user->avatar && $googleUser->getAvatar()) {
                $updateData['avatar'] = $googleUser->getAvatar();
            }
            if (!$user->email_verified_at) {
                $updateData['email_verified_at'] = now();
            }
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Muat relasi
            $user->load(['role', 'pelanggan', 'teknisi', 'toko']);

            // Cek status profil jika ada
            if ($user->teknisi && $user->teknisi->status === 'FROZEN') {
                return redirect('/')->with('error', 'Akun Teknisi Anda sedang dibekukan (FROZEN).');
            }
            if ($user->toko && $user->toko->status === 'FROZEN') {
                return redirect('/')->with('error', 'Akun Toko Anda sedang dibekukan (FROZEN).');
            }

            // Pastikan jika mitra PENDING_REVIEW memiliki profil pelanggan untuk berbelanja/order
            $pelanggan = $user->pelanggan;
            if (!$pelanggan) {
                $pelanggan = Pelanggan::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama' => $user->nama ?: $googleUser->getName(),
                        'email' => $user->email,
                        'hp' => $user->no_telp ?? '',
                        'alamat' => '',
                        'saldo' => 0.00,
                    ]
                );
            }

            // Cek kelengkapan biodata
            $hasCompleteProfile = false;
            if ($user->role && in_array($user->role->nama, ['Teknisi', 'Toko Sparepart'])) {
                $hasCompleteProfile = true;
            } elseif ($pelanggan && !empty($pelanggan->hp) && !empty($pelanggan->alamat)) {
                $hasCompleteProfile = true;
            }

            if ($hasCompleteProfile) {
                // Loginkan user secara manual & buat token Sanctum
                Auth::login($user, true);
                $token = $user->createToken('auth-token')->plainTextToken;

                $redirectUrl = '/pelanggan/dashboard';
                if ($user->role && $user->role->nama === 'Teknisi' && $user->teknisi?->status === 'ACTIVE') {
                    $redirectUrl = '/teknisi/dashboard';
                } elseif ($user->role && $user->role->nama === 'Toko Sparepart' && $user->toko?->status === 'ACTIVE') {
                    $redirectUrl = '/toko/dashboard';
                }

                $profile = ($redirectUrl === '/pelanggan/dashboard') ? $pelanggan : ($user->teknisi ?? $user->toko ?? $pelanggan);

                // Render view transisi untuk sinkronisasi ke localStorage
                return view('auth.google_callback_success', [
                    'token' => $token,
                    'user' => $user,
                    'profile' => $profile,
                    'redirectUrl' => $redirectUrl,
                ]);
            }

            // Jika akun ada tapi biodata pelanggan (HP / Alamat) belum lengkap:
            session([
                'google_auth' => [
                    'user_id' => $user->id,
                    'google_id' => $googleUser->getId(),
                    'email' => $googleUser->getEmail(),
                    'nama' => $user->nama ?: $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                ]
            ]);

            return redirect()->route('auth.google.complete');
        }

        // 4. Jika user BELUM ADA di database (User Baru mendaftar via Google)
        // Simpan data Google ke session dan arahkan ke halaman pengisian biodata
        session([
            'google_auth' => [
                'user_id' => null,
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'nama' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
            ]
        ]);

        return redirect()->route('auth.google.complete');
    }

    /**
     * Menampilkan form pengisian biodata wajib (HP & Alamat) untuk pengguna Google.
     */
    public function showCompleteProfile()
    {
        $googleData = session('google_auth');

        if (!$googleData || empty($googleData['email'])) {
            return redirect('/')->with('error', 'Sesi Google telah kedaluwarsa. Silakan coba login kembali.');
        }

        return view('auth.google_complete_profile', [
            'googleData' => $googleData
        ]);
    }

    /**
     * Menyimpan biodata lengkap dan membuat akun user & profil pelanggan.
     */
    public function saveCompleteProfile(Request $request)
    {
        $googleData = session('google_auth');

        if (!$googleData || empty($googleData['email'])) {
            return redirect('/')->with('error', 'Sesi Google telah kedaluwarsa. Silakan coba login kembali.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:1000',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'hp.required' => 'Nomor WhatsApp / HP aktif wajib diisi.',
            'alamat.required' => 'Alamat rumah lengkap wajib diisi untuk penjemputan/servis.',
        ]);

        $rolePelanggan = Role::where('nama', 'Pelanggan')->first();
        if (!$rolePelanggan) {
            $rolePelanggan = Role::create(['nama' => 'Pelanggan', 'deskripsi' => 'Pengguna jasa']);
        }

        $userId = $googleData['user_id'] ?? null;
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'nama' => $request->nama,
                    'google_id' => $googleData['google_id'] ?? $user->google_id,
                    'avatar' => $googleData['avatar'] ?? $user->avatar,
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Buat User Baru
            $user = User::create([
                'role_id' => $rolePelanggan->id,
                'nama' => $request->nama,
                'email' => $googleData['email'],
                'google_id' => $googleData['google_id'],
                'avatar' => $googleData['avatar'] ?? null,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Buat atau perbarui profil Pelanggan
        $profile = Pelanggan::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => $user->nama,
                'email' => $user->email,
                'hp' => $request->hp,
                'alamat' => $request->alamat,
                'saldo' => 0.00,
            ]
        );

        // Hapus data sesi Google
        session()->forget('google_auth');

        // Autentikasi user
        Auth::login($user, true);
        $token = $user->createToken('auth-token')->plainTextToken;
        $user->load('role');

        return view('auth.google_callback_success', [
            'token' => $token,
            'user' => $user,
            'profile' => $profile,
            'redirectUrl' => '/pelanggan/dashboard',
        ]);
    }
}
