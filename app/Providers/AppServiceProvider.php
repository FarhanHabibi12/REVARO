<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Otomatis konfigurasi CA Cert bundle untuk Laragon / Windows jika belum terdaftar
        $caPath = 'C:/laragon/etc/ssl/cacert.pem';
        if (file_exists($caPath)) {
            if (!ini_get('curl.cainfo')) {
                ini_set('curl.cainfo', $caPath);
            }
            if (!ini_get('openssl.cafile')) {
                ini_set('openssl.cafile', $caPath);
            }
        }

        // Terapkan Dynamic SMTP Configuration dari Database jika tersedia
        $this->configureDynamicSmtp();
    }

    /**
     * Muat dan timpa konfigurasi email Laravel secara dinamis dari database.
     */
    protected function configureDynamicSmtp(): void
    {
        try {
            if (Schema::hasTable('smtp_settings')) {
                $smtp = SmtpSetting::where('is_active', true)->first();
                if ($smtp && !empty($smtp->mail_host)) {
                    Config::set('mail.default', $smtp->mail_mailer ?: 'smtp');
                    Config::set('mail.mailers.smtp.transport', 'smtp');
                    Config::set('mail.mailers.smtp.host', $smtp->mail_host);
                    Config::set('mail.mailers.smtp.port', (int) $smtp->mail_port);
                    Config::set('mail.mailers.smtp.encryption', (!empty($smtp->mail_encryption) && $smtp->mail_encryption !== 'null') ? $smtp->mail_encryption : null);
                    Config::set('mail.mailers.smtp.username', $smtp->mail_username);
                    Config::set('mail.mailers.smtp.password', $smtp->mail_password);
                    
                    if (!empty($smtp->mail_from_address)) {
                        Config::set('mail.from.address', $smtp->mail_from_address);
                    }
                    if (!empty($smtp->mail_from_name)) {
                        Config::set('mail.from.name', $smtp->mail_from_name);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Cegah kegagalan saat migrasi/setup awal database
        }
    }
}
