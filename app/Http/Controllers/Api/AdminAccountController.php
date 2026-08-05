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
     * Store a new internal administrator account.
     */
    public function store(Request $request)
    {
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
}
