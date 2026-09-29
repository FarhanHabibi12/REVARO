<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionTrackerService
{
    /**
     * Get active login sessions parsed with OS & Browser details.
     */
    public function getActiveSessions(): array
    {
        $sessions = [];

        if (Schema::hasTable('sessions')) {
            $rawSessions = DB::table('sessions')
                ->whereNotNull('user_id')
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($rawSessions as $s) {
                $user = User::find($s->user_id);
                $agent = $s->user_agent ?? '';

                $sessions[] = [
                    'session_id' => $s->id,
                    'user_id' => $s->user_id,
                    'user_nama' => $user->nama ?? 'Unknown',
                    'user_email' => $user->email ?? 'Unknown',
                    'ip_address' => $s->ip_address,
                    'os' => $this->parseOS($agent),
                    'browser' => $this->parseBrowser($agent),
                    'user_agent' => $agent,
                    'last_activity' => date('Y-m-d H:i:s', $s->last_activity),
                    'last_activity_url' => $s->payload ? 'Active' : 'N/A',
                ];
            }
        }

        // Fallback or addition from active_visitors table if present
        if (empty($sessions) && Schema::hasTable('active_visitors')) {
            $rawVisitors = DB::table('active_visitors')
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($rawVisitors as $v) {
                $user = $v->user_id ? User::find($v->user_id) : null;
                $agent = $v->user_agent ?? '';

                $sessions[] = [
                    'session_id' => $v->id,
                    'user_id' => $v->user_id,
                    'user_nama' => $user->nama ?? 'Guest',
                    'user_email' => $user->email ?? 'N/A',
                    'ip_address' => $v->ip_address,
                    'os' => $this->parseOS($agent),
                    'browser' => $this->parseBrowser($agent),
                    'user_agent' => $agent,
                    'last_activity' => $v->last_activity,
                    'last_activity_url' => $v->url ?? 'N/A',
                ];
            }
        }

        return $sessions;
    }

    /**
     * Kick a user's session and revoke personal access tokens.
     */
    public function kickSession($userId, ?string $sessionId = null): bool
    {
        $user = User::find($userId);

        if ($user) {
            // Revoke Sanctum tokens
            $user->tokens()->delete();

            // Delete database session if exists
            if (Schema::hasTable('sessions')) {
                $query = DB::table('sessions')->where('user_id', $userId);
                if ($sessionId) {
                    $query->where('id', $sessionId);
                }
                $query->delete();
            }

            // Log security incident
            SecurityLoggerService::log(
                event: 'SESSION_KICK',
                details: "Session kicked for user ID {$userId} ({$user->email})",
                userId: auth()->id()
            );

            return true;
        }

        return false;
    }

    /**
     * Extract OS from User-Agent string.
     */
    public function parseOS(?string $userAgent): string
    {
        if (empty($userAgent)) return 'Unknown OS';

        if (preg_match('/windows|win32/i', $userAgent)) return 'Windows';
        if (preg_match('/macintosh|mac os x/i', $userAgent)) return 'macOS';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/iphone|ipad|ipod/i', $userAgent)) return 'iOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';

        return 'Unknown OS';
    }

    /**
     * Extract Browser from User-Agent string.
     */
    public function parseBrowser(?string $userAgent): string
    {
        if (empty($userAgent)) return 'Unknown Browser';

        if (preg_match('/edg/i', $userAgent)) return 'Edge';
        if (preg_match('/chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/safari/i', $userAgent)) return 'Safari';
        if (preg_match('/opera|opr/i', $userAgent)) return 'Opera';

        return 'Unknown Browser';
    }
}
