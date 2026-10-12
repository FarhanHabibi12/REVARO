<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\IpBlacklist;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AntiMaliciousScanMiddleware
{
    /**
     * Known malicious automated vulnerability scanner signatures in User-Agent.
     */
    protected array $scannerUserAgents = [
        'sqlmap',
        'nikto',
        'acunetix',
        'havij',
        'wpscan',
        'nmap',
        'masscan',
        'dirbuster',
        'gobuster',
        'hydra',
        'zgrab',
        'nessus',
        'openvas',
        'webinspect',
        'arachni',
        'appscan',
    ];

    /**
     * Regex patterns for common SQL Injection attack payloads.
     */
    protected array $sqliPatterns = [
        '/\b(union(\s+all)?\s+select)\b/i',
        '/\b(select\s+.*\s+from\s+information_schema)\b/i',
        '/\b(select\s+.*\s+from\s+mysql\.)\b/i',
        '/\b(benchmark\s*\(\s*\d+\s*,\s*.*\))/i',
        '/\b(sleep\s*\(\s*\d+\s*\))/i',
        '/\b(waitfor\s+delay\s+[\'"]\d+)/i',
        '/\b(pg_sleep\s*\(\s*\d+\s*\))/i',
        '/\b(extractvalue\s*\(|\bupdatexml\s*\()/i',
        '/\b(into\s+outfile|into\s+dumpfile)\b/i',
        '/\b(load_file\s*\()/i',
        '/(;\s*drop\s+table\s+)/i',
        '/(;\s*alter\s+table\s+)/i',
    ];

    /**
     * Regex patterns for Path Traversal / LFI attacks.
     */
    protected array $lfiPatterns = [
        '/(\.\.\/|\.\.\\|%2e%2e%2f|%2e%2e\/|\.\.%2f)/i',
        '/(\/etc\/passwd|\/etc\/shadow|\/proc\/self\/environ)/i',
        '/(php:\/\/filter|php:\/\/input|data:\/\/text\/plain)/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = strtolower($request->header('User-Agent', ''));

        // 1. Detect Scanner User-Agent
        foreach ($this->scannerUserAgents as $scanner) {
            if (str_contains($userAgent, $scanner)) {
                $this->blockAndLog($request, "Scanner otomatis terdeteksi ({$scanner})", $ip);
                return $this->respondBlocked('Aktivitas pemindaian otomatis diblokir oleh sistem.');
            }
        }

        // 2. Inspect URI and Query String
        $fullUri = urldecode($request->getRequestUri());
        $queryString = urldecode($request->getQueryString() ?? '');

        // Check SQL Injection patterns in URI/Query
        foreach ($this->sqliPatterns as $pattern) {
            if (preg_match($pattern, $fullUri) || preg_match($pattern, $queryString)) {
                $this->blockAndLog($request, "Percobaan SQL Injection pada URL/Query", $ip);
                return $this->respondBlocked('Percobaan injeksi SQL diblokir oleh sistem.');
            }
        }

        // Check LFI / Path Traversal patterns
        foreach ($this->lfiPatterns as $pattern) {
            // Allow legitimate asset or CSS/JS paths if needed, but block in query strings and API parameters
            if (preg_match($pattern, $queryString)) {
                $this->blockAndLog($request, "Percobaan Path Traversal / LFI pada Query Parameter", $ip);
                return $this->respondBlocked('Percobaan manipulasi path diblokir oleh sistem.');
            }
        }

        // 3. Inspect Form/JSON Body Inputs (excluding file binary contents)
        $inputs = $request->except(['foto', 'sertifikat', 'ktp', 'dokumen_izin', 'backup_file']);
        $payloadString = json_encode($inputs);

        if ($payloadString) {
            $decodedPayload = urldecode($payloadString);
            foreach ($this->sqliPatterns as $pattern) {
                // If restore route, backup_file is handled safely via separate controller
                if ($request->is('api/admin/database/restore')) {
                    break;
                }

                if (preg_match($pattern, $decodedPayload)) {
                    $this->blockAndLog($request, "Percobaan SQL Injection pada Body Payload", $ip);
                    return $this->respondBlocked('Payload berbahaya terdeteksi dan diblokir oleh sistem.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Log incident and register in IP Blacklist after repeated offenses.
     */
    protected function blockAndLog(Request $request, string $reason, ?string $ip): void
    {
        if (!$ip) return;

        try {
            // Log security incident
            ActivityLog::create([
                'user_id' => $request->user()?->id,
                'aktivitas' => "WAF SHIELD ALERT: {$reason} dari IP {$ip} pada {$request->path()}",
                'ip_address' => $ip,
            ]);

            // Auto-blacklist IP if critical attack signature detected
            if (!IpBlacklist::where('ip_address', $ip)->exists()) {
                IpBlacklist::create([
                    'ip_address' => $ip,
                    'alasan' => "Auto-ban WAF: {$reason}",
                    'banned_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently catch to avoid crashing response
        }
    }

    /**
     * Return consistent blocked response.
     */
    protected function respondBlocked(string $message): Response
    {
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak: ' . $message,
                'security_incident' => true,
            ], 403);
        }

        abort(403, 'Akses Ditolak: ' . $message . ' Insiden keamanan ini telah dicatat.');
    }
}
