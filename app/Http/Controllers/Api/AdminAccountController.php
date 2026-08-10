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
}
