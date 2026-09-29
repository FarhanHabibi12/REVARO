<?php

namespace App\Services;

use App\Models\SecurityIncidentLog;
use Illuminate\Support\Facades\Request;

class SecurityLoggerService
{
    /**
     * Log a security incident to database.
     */
    public static function log(string $event, ?string $details = null, ?int $userId = null): SecurityIncidentLog
    {
        return SecurityIncidentLog::create([
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'event_type' => $event,
            'method' => Request::method() ?? 'GET',
            'url' => Request::fullUrl() ?? 'N/A',
            'details' => $details,
            'user_id' => $userId ?? auth()->id(),
            'created_at' => now(),
        ]);
    }

    /**
     * Purge security incident logs.
     */
    public static function purge(?int $days = null): int
    {
        if ($days !== null) {
            return SecurityIncidentLog::where('created_at', '<', now()->subDays($days))->delete();
        }

        // Truncate all logs if days is null
        SecurityIncidentLog::truncate();
        return 0;
    }
}
