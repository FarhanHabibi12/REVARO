<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden text-slate-100">

    <!-- Ambient Glow Effects -->
    <div class="absolute w-[500px] h-[500px] rounded-full bg-blue-600/15 blur-[140px] -top-32 -left-32 pointer-events-none"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-orange-600/15 blur-[140px] -bottom-32 -right-32 pointer-events-none"></div>

    <div class="w-full max-w-md glass-card rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative z-10">
        
        <!-- Header -->
        <div class="text-center space-y-3">
            <a href="/" class="inline-flex items-center space-x-3 group">
                <div class="bg-brandOrange p-2.5 rounded-2xl font-black text-sm tracking-widest text-white shadow-lg shadow-orange-500/30 group-hover:scale-105 transition-transform">
                    REV
                </div>
                <div class="text-left">
                    <h1 class="text-xl font-black tracking-tight text-white">REVARO</h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Internal Admin Portal</p>
                </div>
            </a>
            <div class="pt-2">
                <h2 class="text-lg font-bold text-white">Selamat Datang, Administrator</h2>
                <p class="text-xs text-slate-400 mt-1">Silakan masuk menggunakan akun kredensial manajemen Anda.</p>
            </div>
        </div>

        <!-- Login Form -->
        <form onsubmit="handleAdminLogin(event)" class="space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email Admin</label>
                <input required type="email" id="adminEmail" class="w-full text-sm bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-500" placeholder="pemilik@revaro.com">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input required type="password" id="adminPassword" class="w-full text-sm bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3 pr-11 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-500" placeholder="••••••••">
                    <button type="button" onclick="toggleAdminPwd()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition">
                        <span id="adminEye">👁️</span>
                    </button>
                </div>
            </div>


            <div class="bg-slate-900/40 p-3 rounded-xl border border-slate-800/80 text-[10px] text-slate-400 leading-relaxed flex items-start space-x-2">
                <span class="text-sm leading-none">🔒</span>
                <span><strong>Catatan Keamanan:</strong> Portal ini dilindungi oleh pembatasan middleware IP, enkripsi token JWT, dan audit trail log.</span>
            </div>

            <button type="submit" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-lg shadow-orange-600/20 active:scale-[0.99]">
                Masuk ke Konsol Dashboard
            </button>
        </form>
    </div>

    <!-- Client-Side Script -->
    <script>
        function toggleAdminPwd() {
            const inp = document.getElementById('adminPassword');
            const ico = document.getElementById('adminEye');
            if (inp.type === 'password') {
                inp.type = 'text';
                ico.innerText = '🙈';
            } else {
                inp.type = 'password';
                ico.innerText = '👁️';
            }
        }

        function fillAdminCreds(email, pwd) {
            document.getElementById('adminEmail').value = email;
            document.getElementById('adminPassword').value = pwd;
        }

        function getRoleSlug(user) {
            if (!user) return '';
            let r = user.role;
            if (typeof r === 'object' && r !== null) {
                r = r.nama || r.name || '';
            }
            const map = {
                'Super Admin (Pemilik Sistem)':     'super-admin-pemilik',
                'super-admin-pemilik':              'super-admin-pemilik',
                'Super Admin (Monitoring Kinerja)': 'super-admin-monitoring',
                'super-admin-monitoring':           'super-admin-monitoring',
                'Admin Verifikator':                 'admin-verifikator',
                'admin-verifikator':                 'admin-verifikator',
                'Admin Resolusi':                    'admin-resolusi',
                'admin-resolusi':                    'admin-resolusi',
            };
            return map[r] || (typeof r === 'string' ? r : '');
        }

        // Redirect if already logged in as admin
        window.addEventListener('load', () => {
            const token = localStorage.getItem('admin_token');
            const user = JSON.parse(localStorage.getItem('admin_user') || '{}');
            const role = getRoleSlug(user);

            if (token && role) {
                if (role === 'super-admin-pemilik') window.location.href = '/superadmin/pemilik/dashboard';
                else if (role === 'super-admin-monitoring') window.location.href = '/superadmin/monitoring/scorecard-verifikator';
                else if (role === 'admin-verifikator') window.location.href = '/admin/verifikator/dashboard';
                else if (role === 'admin-resolusi') window.location.href = '/admin/resolusi/dashboard';
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Masuk Gagal',
                        text: result.message || "Kombinasi email dan kata sandi salah.",
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6A00'
                    });
                    return;
                }

                let roleNama = result.role;
                if (typeof roleNama === 'object' && roleNama !== null) {
                    roleNama = roleNama.nama || '';
                }
                if (!roleNama && result.user && result.user.role) {
                    roleNama = typeof result.user.role === 'object' ? result.user.role.nama : result.user.role;
                }

                // Map DB role names → slugs used throughout the admin portal
                const roleSlugMap = {
                    'Super Admin (Pemilik Sistem)':     'super-admin-pemilik',
                    'super-admin-pemilik':              'super-admin-pemilik',
                    'Super Admin (Monitoring Kinerja)': 'super-admin-monitoring',
                    'super-admin-monitoring':           'super-admin-monitoring',
                    'Admin Verifikator':                 'admin-verifikator',
                    'admin-verifikator':                 'admin-verifikator',
                    'Admin Resolusi':                    'admin-resolusi',
                    'admin-resolusi':                    'admin-resolusi',
                };

                const role = roleSlugMap[roleNama] || null;

                // Block non-admin roles
                if (!role) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Ditolak',
                        text: 'Kredensial Anda bukan milik akun Administrator internal.',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#F97316'
                    });
                    return;
                }

                // Save credentials with consistent keys
                localStorage.setItem('admin_token', result.access_token);
                localStorage.setItem('admin_user', JSON.stringify({ ...result.user, role }));

                // Role-based redirect
                let redirectUrl = '/superadmin/pemilik/dashboard';
                if (role === 'super-admin-monitoring') redirectUrl = '/superadmin/monitoring/scorecard-verifikator';
                else if (role === 'admin-verifikator')  redirectUrl = '/admin/verifikator/dashboard';
                else if (role === 'admin-resolusi')     redirectUrl = '/admin/resolusi/dashboard';

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Masuk!',
                    text: `Selamat datang, ${result.user.nama}!`,
                    showConfirmButton: false,
                    timer: 1400,
                    background: '#0F172A',
                    color: '#E2E8F0'
                }).then(() => {
                    window.location.href = redirectUrl;
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Error',
                    text: 'Gagal menghubungkan ke server otentikasi.',
                    background: '#0F172A',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        }
    </script>

</body>
</html>
