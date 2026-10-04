<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinkronisasi Akun REVARO...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl p-8 max-w-sm w-full text-center space-y-4 shadow-2xl">
        <div class="relative w-16 h-16 mx-auto flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-4 border-blue-500/20 animate-ping"></div>
            <div class="w-12 h-12 rounded-full border-4 border-blue-500 border-t-transparent animate-spin"></div>
        </div>
        <h3 class="text-lg font-bold">Menghubungkan Akun...</h3>
        <p class="text-xs text-slate-400">Harap tunggu sejenak, kami sedang menyiapkan sesi Anda ke sistem REVARO.</p>
    </div>

    <script>
        (function() {
            try {
                const token = @json($token);
                const user = @json($user);
                const profile = @json($profile);
                const redirectUrl = @json($redirectUrl);

                if (token) {
                    localStorage.setItem('token', token);
                }
                if (user) {
                    localStorage.setItem('user', JSON.stringify(user));
                }
                if (profile) {
                    localStorage.setItem('profile', JSON.stringify(profile));
                }

                // Redirect setelah menyimpan state ke localStorage
                setTimeout(() => {
                    window.location.href = redirectUrl || '/pelanggan/dashboard';
                }, 300);
            } catch (err) {
                console.error("Gagal sinkronisasi sesi:", err);
                window.location.href = '/pelanggan/dashboard';
            }
        })();
    </script>
</body>
</html>
