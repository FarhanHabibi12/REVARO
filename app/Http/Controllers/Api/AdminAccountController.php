<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAccountController extends Controller
{
    /**
     * Display a listing of the admin accounts.
     */
    public function index()
    {
        $adminRoles = Role::whereIn('nama', [
            'Super Admin (Pemilik Sistem)',
            'Super Admin (Monitoring Kinerja)',
            'Admin Resolusi',
            'Admin Verifikator'
        ])->pluck('id');

        $users = User::with('role')
            ->whereIn('role_id', $adminRoles)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($u) {
                // Map display role name back to slug for front-end roleLabel function
                $slug = array_search($u->role->nama, Role::$slugMap) ?: $u->role->nama;
                $arr = $u->toArray();
                $arr['role'] = $slug;
                return $arr;
            });

        return response()->json($users);
    }

    /**
     * Store a new internal administrator account.
     */
    public function store(Request $request)
    {
        if ($request->has('role') && !$request->has('role_slug')) {
            $request->merge(['role_slug' => $request->role]);
        }

        $validator = Validator::make($request->all(), [
            'role_slug' => 'required|in:admin-verifikator,admin-resolusi',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:6',
        ], [
            'role_slug.in' => 'Role yang dapat didaftarkan hanya Admin Verifikator dan Admin Resolusi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleName = Role::$slugMap[$request->role_slug] ?? null;
        if (!$roleName) {
            return response()->json(['message' => 'Role slug tidak valid.'], 400);
        }

        $role = Role::where('nama', $roleName)->first();
        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan di database.'], 500);
        }

        // Full Sistem: Gunakan password dari form jika diisi, atau generate acak otomatis
        $rawPassword = $request->filled('password')
            ? (string) $request->password 
            : \Illuminate\Support\Str::random(10);

        $user = User::create([
            'role_id' => $role->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($rawPassword),
            'is_active' => true,
        ]);

        // Tandai bahwa admin baru wajib ganti password awal & belum memiliki username
        $user->setMustChangePassword(true);

        // Catat jejak audit aktivitas pembuatan admin
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id() ?? $user->id,
            'aktivitas' => 'Mendaftarkan akun administrator baru: ' . $user->nama . ' (' . $roleName . ')',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Akun internal admin berhasil dibuat.',
            'user' => $user,
            'temporary_password' => $rawPassword,
        ], 201);
    }

    /**
     * Reset password of an admin account by Super Admin Pemilik.
     */
    public function resetAdminPassword(Request $request, $id)
    {
        $user = User::with('role')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Akun administrator tidak ditemukan.'], 404);
        }

        if ($user->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Kata sandi akun Super Admin Pemilik tidak dapat direset dari menu ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newPassword = $request->filled('password')
            ? (string) $request->password
            : \Illuminate\Support\Str::random(10);

        $user->password = Hash::make($newPassword);
        $user->setMustChangePassword(true);
        $user->save();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id() ?? $user->id,
            'aktivitas' => 'Mereset kata sandi akun administrator: ' . $user->nama . ' (' . ($user->role->nama ?? 'Admin') . ')',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Kata sandi akun admin berhasil direset.',
            'user' => $user,
            'temporary_password' => $newPassword,
        ], 200);
    }

    /**
     * Toggle active state.
     */
    public function toggleActive($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Akun administrator tidak ditemukan.'], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.'], 400);
        }

        if ($user->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Anda tidak bisa menonaktifkan akun Super Admin Pemilik.'], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => 'Status keaktifan administrator berhasil diperbarui.',
            'user' => $user
        ], 200);
    }

    /**
     * Get recent audit/security logs.
     */
    public function getAuditLogs()
    {
        $logs = \App\Models\ActivityLog::with('user.role')
            ->orderBy('id', 'desc')
            ->take(50)
            ->get();
            
        return response()->json($logs);
    }

    /**
     * Update current logged in admin personal profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
        ];

        if ($request->hasFile('foto')) {
            $destinationPath = public_path('uploads/avatars');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file = $request->file('foto');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $data['foto'] = '/uploads/avatars/' . $filename;
        }

        $user->update($data);
        $user->load('role');

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memperbarui profil akun pribadi administrator',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Profil pribadi berhasil diperbarui.',
            'user' => $user,
        ]);
    }

    /**
     * Update current logged in admin personal password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Kata sandi saat ini tidak cocok.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengubah kata sandi akun pribadi administrator',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Kata sandi pribadi berhasil diperbarui.',
        ]);
    }

    /**
     * Setup username and first-time credentials after first login.
     */
    public function setupFirstTime(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Support backward compatibility jika dikirim dengan field 'password'
        if ($request->filled('password') && !$request->filled('new_password')) {
            $request->merge(['new_password' => $request->password]);
            if ($request->filled('password_confirmation') && !$request->filled('new_password_confirmation')) {
                $request->merge(['new_password_confirmation' => $request->password_confirmation]);
            }
        }

        $rules = [
            'username' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9_]+$/',
        ];

        // Jika akun ditandai wajib ubah kata sandi, maka new_password wajib diisi dan confirmed
        if ($user->must_change_password) {
            $rules['new_password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['new_password'] = 'nullable|string|min:6|confirmed';
        }

        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.regex' => 'Username hanya boleh terdiri dari huruf, angka, dan underscore (_).',
            'new_password.required' => 'Sebagai staf admin, Anda wajib mengganti kata sandi awal dengan kata sandi baru pilihan Anda.',
            'new_password.min' => 'Kata sandi minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chosenUsername = strtolower(trim($request->username));
        $existingOwner = \App\Models\SystemSetting::get('admin_user_lookup_' . $chosenUsername);
        if ($existingOwner && (int)$existingOwner !== (int)$user->id) {
            return response()->json(['message' => 'Username ini sudah digunakan oleh admin lain. Silakan pilih username yang berbeda.'], 422);
        }

        $user->setAdminUsername($chosenUsername);

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
            $user->setMustChangePassword(false);
            $user->save();
        }

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'aktivitas' => 'Menyelesaikan setup akun administrator (username & pembaruan kata sandi)',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Username dan kata sandi baru berhasil disimpan.',
            'user' => $user->fresh(),
        ], 200);
    }
}
