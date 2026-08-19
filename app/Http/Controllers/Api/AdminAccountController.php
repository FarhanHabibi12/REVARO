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
            'role_slug' => 'required|in:admin-verifikator,admin-resolusi,super-admin-monitoring',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
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

        $user = User::create([
            'role_id' => $role->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Akun internal admin berhasil dibuat.',
            'user' => $user,
        ], 201);
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
}
