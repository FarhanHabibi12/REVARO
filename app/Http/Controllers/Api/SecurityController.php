<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\IpBlacklist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{
    /**
     * List all blacklisted IPs.
     */
    public function listBlacklist()
    {
        $blacklist = IpBlacklist::orderBy('id', 'desc')->get();
        return response()->json($blacklist);
    }

    /**
     * Ban a new IP.
     */
    public function banIp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
            'alasan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $blacklist = IpBlacklist::create([
            'ip_address' => $request->ip_address,
            'alasan' => $request->alasan ?: 'Aktivitas mencurigakan / hacking',
            'banned_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'aktivitas' => "Memblokir IP address: {$request->ip_address}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'IP address berhasil diblokir.',
            'blacklist' => $blacklist
        ], 201);
    }

    /**
     * Unban an IP.
     */
    public function unbanIp(Request $request, $id)
    {
        $blacklist = IpBlacklist::find($id);
        if (!$blacklist) {
            return response()->json(['message' => 'IP tidak ditemukan di daftar blokir.'], 404);
        }

        $ip = $blacklist->ip_address;
        $blacklist->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'aktivitas' => "Membuka blokir IP address: {$ip}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'IP address berhasil dilepas dari daftar blokir.'
        ]);
    }

    /**
     * List user accounts.
     */
    public function listUsers(Request $request)
    {
        $users = User::with('role')
            ->where('id', '!=', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($users);
    }

    /**
     * Toggle ban/activation status of user account.
     */
    public function toggleUserBan(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Anda tidak dapat memblokir akun Anda sendiri.'], 400);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'mengaktifkan' : 'memblokir';

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'aktivitas' => "Mengubah status akun {$user->email} menjadi " . ($user->is_active ? 'ACTIVE' : 'BANNED'),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => "Akun user berhasil di-{$action}.",
            'user' => $user
        ]);
    }

    /**
     * Get security activity logs.
     */
    public function getSecurityLogs()
    {
        $logs = ActivityLog::with('user')
            ->where(function($query) {
                $query->where('aktivitas', 'like', '%blokir%')
                      ->orWhere('aktivitas', 'like', '%BANNED%')
                      ->orWhere('aktivitas', 'like', '%hack%')
                      ->orWhere('aktivitas', 'like', '%keamanan%')
                      ->orWhere('aktivitas', 'like', '%login%')
                      ->orWhere('aktivitas', 'like', '%revis%')
                      ->orWhere('aktivitas', 'like', '%tolak%');
            })
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json($logs);
    }
}
