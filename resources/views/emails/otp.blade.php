<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi REVARO</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #0F172A; padding: 40px 15px; margin: 0;">
    <div style="max-width: 480px; margin: 0 auto; background-color: #ffffff; padding: 36px 28px; border-radius: 20px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        
        <!-- Logo REVARO -->
        <div style="margin-bottom: 24px;">
            <div style="display: inline-block; width: 48px; height: 48px; line-height: 48px; background: linear-gradient(135deg, #FF6B00 0%, #EA580C 100%); color: #ffffff; border-radius: 14px; font-weight: 900; font-size: 22px;">
                R
            </div>
            <h2 style="color: #002B49; font-size: 20px; font-weight: 800; margin: 12px 0 4px 0; letter-spacing: 0.5px;">REVARO PLATFORM</h2>
            <p style="color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin: 0; font-weight: 700;">Verifikasi Keamanan Akun</p>
        </div>

        <div style="border-top: 1px solid #F1F5F9; border-bottom: 1px solid #F1F5F9; padding: 20px 0; margin-bottom: 24px;">
            <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0 0 12px 0;">
                Halo! Kami mendeteksi permintaan masuk atau pendaftaran akun Anda di <strong>REVARO</strong>.
            </p>
            <p style="color: #64748B; font-size: 13px; line-height: 1.5; margin: 0;">
                Gunakan 6-digit kode verifikasi di bawah ini untuk membuktikan kepemilikan email Anda:
            </p>
            
            <!-- Kode OTP -->
            <div style="background-color: #FFF7ED; border: 2px dashed #FF6B00; color: #EA580C; font-size: 36px; font-weight: 900; letter-spacing: 8px; padding: 16px; border-radius: 12px; margin: 24px auto 16px auto; max-width: 280px; font-family: monospace;">
                {{ $otpCode }}
            </div>

            <p style="color: #94A3B8; font-size: 11px; margin: 0;">
                ⏱️ Kode ini berlaku selama <strong>10 menit</strong>. Jangan berikan kode ini kepada siapapun!
            </p>
        </div>

        <p style="color: #94A3B8; font-size: 11px; line-height: 1.5; margin: 0;">
            Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman.<br>
            &copy; {{ date('Y') }} REVARO. Hak cipta dilindungi.
        </p>
    </div>
</body>
</html>
