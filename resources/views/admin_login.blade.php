<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#0A4C8A',
                        brandOrange: '#FF6A00',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient Glow Effects -->
    <div class="absolute w-96 h-96 rounded-full bg-blue-900/20 blur-[120px] top-10 left-10"></div>
    <div class="absolute w-96 h-96 rounded-full bg-orange-950/20 blur-[120px] bottom-10 right-10"></div>

    <div class="w-full max-w-md glass-card text-white rounded-3xl p-8 space-y-6 shadow-2xl relative z-10">
        
        <div class="text-center space-y-2.5">
            <a href="/" class="inline-flex items-center space-x-2.5">
                <div class="bg-brandOrange p-2 rounded-xl font-extrabold text-sm tracking-widest text-white shadow-md">
                    REV
                </div>
                <div class="text-left">
                    <h1 class="text-lg font-black tracking-tight text-white">REVARO</h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Internal Admin Portal</p>
                </div>
            </a>
            <h2 class="text-lg font-bold mt-4">Selamat Datang, Administrator</h2>
            <p class="text-xs text-slate-400">Silakan masuk menggunakan akun kredensial manajemen Anda.</p>
        </div>

        <!-- Login Form -->
        <form onsubmit="handleAdminLogin(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Alamat Email Admin</label>
                <input required type="email" id="adminEmail" class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl p-3 outline-none text-white focus:border-brandOrange focus:bg-slate-900/60 transition" placeholder="admin@revaro.com">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Kata Sandi</label>
                <input required type="password" id="adminPassword" class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl p-3 outline-none text-white focus:border-brandOrange focus:bg-slate-900/60 transition" placeholder="••••••••">
            </div>

            <div class="bg-slate-900 p-3.5 rounded-xl border border-slate-800 text-[10px] text-slate-400 leading-normal">
                🔐 <strong>Catatan Keamanan:</strong> Portal ini dilindungi oleh pembatasan middleware IP, enkripsi token JWT, dan rate limiter dinamis. Segala aktivitas login terekam secara permanen di audit trail log.
            </div>

            <button type="submit" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3.5 rounded-xl transition shadow shadow-orange-700/20">
                Masuk ke Konsol Dashboard
            </button>
        </form>
    </div>

    <!-- Client-Side Script -->
    <script>
        // Redirect if already logged in as admin
        window.addEventListener('load', () => {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));
            const role = localStorage.getItem('role');

            if (token && user && role && role !== 'Pelanggan' && role !== 'Teknisi' && role !== 'Toko Sparepart') {
                window.location.href = '/admin/dashboard';
            }
        });

        async function handleAdminLogin(e) {
            e.preventDefault();

            const email = document.getElementById('adminEmail').value;
            const password = document.getElementById('adminPassword').value;

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || "Gagal masuk.");
                    return;
                }

                const role = result.role;
                
                // Validate if it is an admin role
                const adminRoles = [
                    'Super Admin (Pemilik Sistem)', 
                    'Admin Verifikator', 
                    'Admin Resolusi', 
                    'Super Admin (Monitoring Kinerja)'
                ];

                if (!adminRoles.includes(role)) {
                    alert("Akses ditolak! Kredensial Anda bukan milik akun Administrator. Silakan login di portal biasa.");
                    return;
                }

                // Save credentials
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                localStorage.setItem('role', role);

                alert(`Selamat Datang, ${result.user.nama}!\nLogin berhasil sebagai ${role}.`);
                window.location.href = '/admin/dashboard';

            } catch (err) {
                console.error(err);
                alert("Gagal menghubungkan ke server otentikasi.");
            }
        }
    </script>

</body>
</html>
