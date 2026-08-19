<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request and set application locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->cookie('app_language') ?: 'id';
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $lang = SystemSetting::get('app_language', $lang);
            }
        } catch (\Throwable $e) {
            // Keep default from cookie or fallback
        }

        app()->setLocale($lang);
        view()->share('platform_language', $lang);

        $response = $next($request);

        return $response;
    }
}
