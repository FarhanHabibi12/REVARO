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

        $validator = Validator::make($request->all(), [
            'role_slug' => 'required|in:pelanggan,teknisi,toko-sparepart',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleName = Role::$slugMap[$request->role_slug];
        $role = Role::where('nama', $roleName)->first();

        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan.'], 500);
        }

        // Create user
        $user = User::create([
            'role_id' => $role->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $profile = null;

        // Create profile based on role
        if ($request->role_slug === 'pelanggan') {
            $profile = Pelanggan::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'hp' => $request->hp,
                'alamat' => $request->alamat,
                'saldo' => 0.00,
            ]);
        } elseif ($request->role_slug === 'teknisi') {
            $profile = Teknisi::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'tier' => 'Bronze',
                'hp' => $request->hp,
                'rating' => 0.00,
                'tersedia' => true,
                'status' => 'PENDING_REVIEW',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        } elseif ($request->role_slug === 'toko-sparepart') {
            $profile = Toko::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'izin_usaha' => $request->izin_usaha,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'status' => 'PENDING_REVIEW',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => $user,
            'role' => $user->role->nama,
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

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json(['message' => 'Akun Anda dinonaktifkan.'], 403);
        }

        $profile = null;

        // Check profile freeze or reject state
        if ($user->isTeknisi()) {
            $profile = $user->teknisi;
            if ($profile) {
                if ($profile->status === 'FROZEN') {
                    return response()->json(['message' => 'Akun Teknisi Anda dibekukan (FROZEN).'], 403);
                }
                if ($profile->status === 'REJECTED') {
                    return response()->json(['message' => 'Akun Teknisi Anda ditangguhkan.'], 403);
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
            }
        } elseif ($user->isPelanggan()) {
            $profile = $user->pelanggan;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $user,
            'role' => $user->role->nama,
            'profile' => $profile,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('role');
        $profile = null;

        if ($user->isPelanggan()) {
            $profile = $user->pelanggan;
        } elseif ($user->isTeknisi()) {
            $profile = $user->teknisi;
        } elseif ($user->isToko()) {
            $profile = $user->toko;
        }

        return response()->json([
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Logout and revoke active token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Resubmit documents for Technician or Shop with NEEDS_REVISION status.
     */
    public function resubmit(Request $request)
    {
        $user = $request->user();

        if ($user->isTeknisi()) {
            $profile = $user->teknisi;
            if (!$profile || $profile->status !== 'NEEDS_REVISION') {
                return response()->json(['message' => 'Hanya profil dengan status NEEDS_REVISION yang dapat mengajukan ulang.'], 400);
            }

            $validator = Validator::make($request->all(), [
                'hp' => 'required|string|max:20',
                'kategori' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $profile->update([
                'hp' => $request->hp,
                'kategori' => $request->kategori,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'PENDING_REVIEW',
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => 'Mengajukan ulang dokumen verifikasi teknisi',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Dokumen berhasil diajukan ulang.',
                'profile' => $profile,
            ]);

        } elseif ($user->isToko()) {
            $profile = $user->toko;
            if (!$profile || $profile->status !== 'NEEDS_REVISION') {
                return response()->json(['message' => 'Hanya profil dengan status NEEDS_REVISION yang dapat mengajukan ulang.'], 400);
            }

            $validator = Validator::make($request->all(), [
                'izin_usaha' => 'required|string',
                'alamat' => 'required|string',
                'telepon' => 'required|string|max:20',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $profile->update([
                'izin_usaha' => $request->izin_usaha,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'PENDING_REVIEW',
            ]);

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
}
