<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi REVARO</title>
</head>
<body style="font-family: sans-serif; background-color: #f1f5f9; padding: 20px;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #0F172A; margin-bottom: 20px;">Selamat Datang di REVARO!</h2>
        <p style="color: #475569; font-size: 16px;">Gunakan kode OTP berikut untuk memverifikasi akun Anda. Kode ini berlaku selama 10 menit.</p>
        <div style="background-color: #F97316; color: #ffffff; font-size: 32px; font-weight: bold; letter-spacing: 5px; padding: 15px; border-radius: 8px; margin: 30px 0;">
            {{ $otpCode }}
        </div>
        <p style="color: #94A3B8; font-size: 14px;">Jika Anda tidak mendaftar di REVARO, abaikan email ini.</p>
    </div>
</body>
</html>
