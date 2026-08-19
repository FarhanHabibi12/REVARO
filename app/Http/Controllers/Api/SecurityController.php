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

    /**
     * List all active online visitors (all roles & guests).
     */
    public function listActiveVisitors(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Remove guest entries for this IP if user is authenticated
            \App\Models\ActiveVisitor::where('ip_address', $request->ip())
                ->whereNull('user_id')
                ->delete();

            $rName = $user->role?->nama ?? '';
            $roleSlug = array_search($rName, \App\Models\Role::$slugMap) ?: $rName;
            $roleName = 'SUPER ADMIN PLATFORM';
            if ($roleSlug === 'admin-verifikator' || $rName === 'Admin Verifikator') $roleName = 'ADMIN VERIFIKATOR';
            elseif ($roleSlug === 'admin-resolusi' || $rName === 'Admin Resolusi') $roleName = 'ADMIN RESOLUSI';
            elseif ($roleSlug === 'toko' || $rName === 'Toko Sparepart') $roleName = 'MITRA TOKO SPAREPART';
            elseif ($roleSlug === 'teknisi' || $rName === 'Teknisi') $roleName = 'MITRA TEKNISI';
            elseif ($roleSlug === 'pelanggan' || $rName === 'Pelanggan') $roleName = 'PELANGGAN';

            \App\Models\ActiveVisitor::updateOrCreate(
                ['ip_address' => $request->ip(), 'user_id' => $user->id],
                [
                    'nama' => $user->nama,
                    'role' => $roleName,
                    'endpoint' => '/superadmin/keamanan',
                    'user_agent' => substr($request->userAgent() ?? 'Mozilla/5.0', 0, 255),
                    'last_seen_at' => now(),
                ]
            );
        }

        $visitors = \App\Models\ActiveVisitor::with('user.role')
            ->orderBy('last_seen_at', 'desc')
            ->get()
            ->map(function ($v) {
                if ($v->user) {
                    $v->nama = $v->user->nama;
                    $rName = $v->user->role?->nama ?? '';
                    $roleSlug = array_search($rName, \App\Models\Role::$slugMap) ?: $rName;
                    
                    switch ($roleSlug) {
                        case 'super-admin-pemilik':
                        case 'Super Admin (Pemilik Sistem)':
                            $v->role = 'SUPER ADMIN PLATFORM';
                            break;
                        case 'admin-verifikator':
                        case 'Admin Verifikator':
                            $v->role = 'ADMIN VERIFIKATOR';
                            break;
                        case 'admin-resolusi':
                        case 'Admin Resolusi':
                            $v->role = 'ADMIN RESOLUSI';
                            break;
                        case 'toko':
                        case 'Toko Sparepart':
                            $v->role = 'MITRA TOKO SPAREPART';
                            break;
                        case 'teknisi':
                        case 'Teknisi':
                            $v->role = 'MITRA TEKNISI';
                            break;
                        case 'pelanggan':
                        case 'Pelanggan':
                            $v->role = 'PELANGGAN';
                            break;
                        default:
                            $v->role = strtoupper($rName ?: 'REGISTERED USER');
                    }
                }
                return $v;
            });

        if ($visitors->count() === 0) {
            $u = $user ?: \App\Models\User::first();
            $visitors = collect([
                [
                    'id' => 1,
                    'ip_address' => $request->ip() ?: '127.0.0.1',
                    'nama' => $u?->nama ?: 'Salman',
                    'role' => 'SUPER ADMIN PLATFORM',
                    'endpoint' => '/superadmin/keamanan',
                    'user_agent' => $request->userAgent() ?: 'Mozilla/5.0 (Windows NT 10.0)',
                    'last_seen_at' => now(),
                ]
            ]);
        }

        return response()->json($visitors);
    }
}
