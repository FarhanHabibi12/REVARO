<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SmtpConfigController extends Controller
{
    /**
     * Menampilkan form pengaturan SMTP.
     */
    public function edit()
    {
        $smtp = SmtpSetting::first();
        
        if (!$smtp) {
            $smtp = new SmtpSetting([
                'mail_mailer' => 'smtp',
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
                'mail_from_name' => 'REVARO Platform',
                'is_active' => true,
            ]);
        }

        return view('superadmin.smtp_settings', compact('smtp'));
    }

    /**
     * Memvalidasi dan memperbarui pengaturan SMTP di database.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string|max:50',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'required|string|max:255',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ], [
            'mail_host.required' => 'Host SMTP wajib diisi (contoh: smtp.gmail.com).',
            'mail_port.required' => 'Port SMTP wajib diisi (contoh: 587 atau 465).',
            'mail_username.required' => 'Email / Username SMTP wajib diisi.',
            'mail_from_address.required' => 'Email Pengirim (From Address) wajib diisi.',
            'mail_from_name.required' => 'Nama Pengirim (From Name) wajib diisi.',
        ]);

        $smtp = SmtpSetting::first();
        if (!$smtp) {
            $smtp = new SmtpSetting();
        }

        $smtp->mail_mailer = $validated['mail_mailer'];
        $smtp->mail_host = $validated['mail_host'];
        $smtp->mail_port = $validated['mail_port'];
        $smtp->mail_username = $validated['mail_username'];
        
        // Hanya update password jika diisi input baru
        if (!empty($validated['mail_password'])) {
            $smtp->mail_password = $validated['mail_password'];
        }
        
        $smtp->mail_encryption = $validated['mail_encryption'] === 'null' ? null : $validated['mail_encryption'];
        $smtp->mail_from_address = $validated['mail_from_address'];
        $smtp->mail_from_name = $validated['mail_from_name'];
        $smtp->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
        
        $smtp->save();

        // Terapkan konfigurasi ke runtime sesi saat ini
        Config::set('mail.default', $smtp->mail_mailer ?: 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $smtp->mail_host);
        Config::set('mail.mailers.smtp.port', (int) $smtp->mail_port);
        Config::set('mail.mailers.smtp.encryption', $smtp->mail_encryption);
        Config::set('mail.mailers.smtp.username', $smtp->mail_username);
        Config::set('mail.mailers.smtp.password', $smtp->mail_password);
        Config::set('mail.from.address', $smtp->mail_from_address);
        Config::set('mail.from.name', $smtp->mail_from_name);

        // Reset cache mailer Laravel agar konfigurasi baru langsung aktif
        if (app()->bound('mail.manager')) {
            app('mail.manager')->forgetMailers();
        }

        return redirect()->back()->with('success', 'Konfigurasi SMTP berhasil disimpan dan langsung aktif di sistem!');
    }

    /**
     * Menguji koneksi SMTP dengan mengirim email percobaan.
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ], [
            'test_email.required' => 'Masukkan alamat email tujuan uji coba.',
            'test_email.email' => 'Format alamat email tujuan tidak valid.',
        ]);

        $smtp = SmtpSetting::where('is_active', true)->first();

        if (!$smtp || empty($smtp->mail_host) || empty($smtp->mail_username) || empty($smtp->mail_password)) {
            return redirect()->back()->with('error', 'Setelan SMTP belum lengkap atau dinonaktifkan di database.');
        }

        try {
            // Terapkan config runtime
            Config::set('mail.default', $smtp->mail_mailer ?: 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $smtp->mail_host);
            Config::set('mail.mailers.smtp.port', (int) $smtp->mail_port);
            Config::set('mail.mailers.smtp.encryption', $smtp->mail_encryption);
            Config::set('mail.mailers.smtp.username', $smtp->mail_username);
            Config::set('mail.mailers.smtp.password', $smtp->mail_password);
            Config::set('mail.from.address', $smtp->mail_from_address);
            Config::set('mail.from.name', $smtp->mail_from_name);

            if (app()->bound('mail.manager')) {
                app('mail.manager')->forgetMailers();
            }

            $recipient = $request->test_email;

            Mail::raw("Halo!\n\nIni adalah email pengujian dari Sistem REVARO.\nKonfigurasi SMTP Anda (" . $smtp->mail_host . " / " . $smtp->mail_username . ") berhasil terhubung dan siap digunakan untuk mengirim OTP pendaftaran pengguna!\n\nWaktu: " . now()->format('d M Y H:i:s') . " WIB", function ($message) use ($recipient, $smtp) {
                $message->to($recipient)
                        ->subject('✅ Uji Coba Konfigurasi SMTP REVARO Berhasil');
            });

            return redirect()->back()->with('success', 'Email uji coba berhasil dikirim ke ' . $recipient . '! Silakan periksa kotak masuk/spam Anda.');
        } catch (\Exception $e) {
            Log::error('SMTP Test Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
