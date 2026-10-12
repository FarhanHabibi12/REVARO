<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator Portal — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#002B49',
                        brandOrange: '#FF6B00',
                        adminDark: '#0B132B'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .admin-glass {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient Lighting Effects -->
    <div class="absolute w-96 h-96 rounded-full bg-orange-600/10 blur-[120px] -top-20 -left-20 pointer-events-none"></div>
    <div class="absolute w-96 h-96 rounded-full bg-blue-600/15 blur-[120px] -bottom-20 -right-20 pointer-events-none"></div>

    <div class="w-full max-w-md admin-glass rounded-3xl p-8 space-y-6 shadow-2xl relative z-10">
        
        <!-- Header & Branding -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center space-x-2">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-brandOrange to-amber-500 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-orange-500/20">
                    R
                </div>
                <span class="text-2xl font-black text-white tracking-wide">REVARO</span>
            </div>
            
            <div class="pt-2">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-orange-500/15 text-orange-400 border border-orange-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Portal Khusus Administrator
                </span>
                <h2 class="text-lg font-bold text-white mt-2">Masuk ke Konsol Manajemen</h2>
                <p class="text-xs text-slate-400">Gunakan akun kredensial internal platform Anda.</p>
            </div>
        </div>

        <!-- Form Login Admin -->
        <form onsubmit="handleAdminLogin(event)" class="space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                    Alamat Email Admin *
                </label>
                <input required type="email" id="adminEmail"
                       class="w-full text-xs bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3.5 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-500 font-mono"
                       placeholder="admin@revaro.com">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                    Kata Sandi *
                </label>
                <div class="relative">
                    <input required type="password" id="adminPassword"
                           class="w-full text-xs bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3.5 pr-11 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-500 font-mono"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition">
                        <span id="eyeIcon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </span>
                    </button>
                </div>
              <div class="bg-slate-900/60 p-3.5 rounded-2xl border border-slate-800 text-[11px] text-slate-400 flex items-start space-x-2.5">
                <svg class="w-4 h-4 text-orange-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <p class="leading-relaxed">
                    Halaman ini khusus pengelola sistem. Sesi dilindungi enkripsi JWT dan jejak audit otomatis.
                </p>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-lg shadow-orange-600/25 active:scale-[0.99] flex items-center justify-center space-x-2">
                <span id="btnText">Masuk ke Dashboard Admin</span>
                <svg id="btnSpinner" class="hidden w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <!-- Back to main portal -->
        <div class="text-center pt-2">
            <a href="/" class="text-xs text-slate-400 hover:text-white transition inline-flex items-center gap-1.5">
                <span>←</span>
                <span>Bukan Administrator? Ke Halaman Utama</span>
            </a>
        </div>
    </div>

    <!-- Modal Verifikasi OTP Admin -->
    <div id="adminOtpModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="w-full max-w-md admin-glass rounded-3xl p-7 space-y-5 shadow-2xl relative">
            <div class="text-center space-y-1.5">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-orange-500/20 text-orange-400 border border-orange-500/30 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-white">Verifikasi Keamanan Akun</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Kode verifikasi 6-digit telah dikirimkan ke <br>
                    <span id="adminOtpTargetEmail" class="text-orange-400 font-mono font-semibold">admin@revaro.com</span>
                </p>
            </div>

            <form onsubmit="handleAdminOtpSubmit(event)" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 text-center">
                        Masukkan 6-Digit Kode OTP
                    </label>
                    <input type="text" id="adminOtpCodeInput" maxlength="6" required autocomplete="one-time-code"
                        placeholder="••••••"
                        class="w-full text-center text-2xl tracking-[0.4em] font-mono font-extrabold bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-orange-400 focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 outline-none transition">
                </div>

                <button type="submit" id="adminOtpSubmitBtn"
                    class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-lg shadow-orange-600/25 active:scale-[0.99] flex items-center justify-center space-x-2">
                    <span id="adminOtpBtnText">Verifikasi & Masuk Portal</span>
                </button>
                <div class="text-center pt-1 space-y-1.5">
                    <p class="text-[11px] text-slate-500">
                        Tidak menerima kode verifikasi?
                    </p>
                    <button type="button" id="adminResendOtpBtn" onclick="handleAdminResendOtp()"
                        class="text-xs text-orange-400 hover:text-orange-300 font-semibold transition disabled:text-slate-600 disabled:cursor-not-allowed">
                        Kirim Ulang Kode (<span id="adminResendTimer">60</span>s)
                    </button>
                </div>
            </form>

            <button onclick="closeAdminOtpModal()" class="w-full text-center text-xs text-slate-500 hover:text-slate-300 transition pt-1">
                ← Kembali ke Form Login
            </button>
        </div>
    </div>

    <script>
        let currentAdminEmail = '';
        let adminResendTimerInterval = null;

        function togglePasswordVisibility() {
            const pwd = document.getElementById('adminPassword');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.innerHTML = `<svg class="w-5 h-5 text-brandOrange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>`;
            } else {
                pwd.type = 'password';
                icon.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
            }
        }

        // Cek jika sudah login sebagai admin, langsung redirect
        window.addEventListener('DOMContentLoaded', () => {
            const adminToken = localStorage.getItem('admin_token');
            const adminUserRaw = localStorage.getItem('admin_user');
            if (adminToken && adminUserRaw) {
                try {
                    const user = JSON.parse(adminUserRaw);
                    const dest = getAdminDashboardUrl(user.role);
                    if (dest) {
                        window.location.href = dest;
                    }
                } catch (e) {}
            }
        });

        function getAdminDashboardUrl(roleData) {
            let roleName = '';
            if (typeof roleData === 'object' && roleData !== null) {
                roleName = roleData.nama || roleData.name || '';
            } else if (typeof roleData === 'string') {
                roleName = roleData;
            }

            const lower = roleName.toLowerCase();
            if (lower.includes('pemilik') || lower === 'super-admin-pemilik' || lower.includes('super admin (pemilik')) {
                return '/superadmin/pemilik/dashboard';
            }
            if (lower.includes('monitoring') || lower === 'super-admin-monitoring') {
                return '/superadmin/monitoring/scorecard-verifikator';
            }
            if (lower.includes('verifikator') || lower === 'admin-verifikator') {
                return '/admin/verifikator/dashboard';
            }
            if (lower.includes('resolusi') || lower === 'admin-resolusi') {
                return '/admin/resolusi/dashboard';
            }
            return null;
        }

        function getRoleSlug(roleData) {
            const url = getAdminDashboardUrl(roleData);
            if (!url) return null;
            if (url.includes('pemilik')) return 'super-admin-pemilik';
            if (url.includes('monitoring')) return 'super-admin-monitoring';
            if (url.includes('verifikator')) return 'admin-verifikator';
            if (url.includes('resolusi')) return 'admin-resolusi';
            return null;
        }

        function openAdminOtpModal(email) {
            currentAdminEmail = email;
            document.getElementById('adminOtpTargetEmail').innerText = email;
            document.getElementById('adminOtpCodeInput').value = '';
            document.getElementById('adminOtpModal').classList.remove('hidden');
            startAdminResendTimer(60);
        }

        function closeAdminOtpModal() {
            document.getElementById('adminOtpModal').classList.add('hidden');
            if (adminResendTimerInterval) clearInterval(adminResendTimerInterval);
        }

        function startAdminResendTimer(seconds) {
            const btn = document.getElementById('adminResendOtpBtn');
            const timerSpan = document.getElementById('adminResendTimer');
            btn.disabled = true;
            let left = seconds;
            timerSpan.innerText = left;

            if (adminResendTimerInterval) clearInterval(adminResendTimerInterval);
            adminResendTimerInterval = setInterval(() => {
                left--;
                timerSpan.innerText = left;
                if (left <= 0) {
                    clearInterval(adminResendTimerInterval);
                    btn.disabled = false;
                    btn.innerText = 'Kirim Ulang Kode';
                }
            }, 1000);
        }

        async function handleAdminResendOtp() {
            if (!currentAdminEmail) return;
            try {
                const res = await fetch('/api/auth/resend-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentAdminEmail })
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: d.message || 'Kode verifikasi baru telah dikirim!',
                        showConfirmButton: false,
                        timer: 2500,
                        background: '#0F172A',
                        color: '#E2E8F0'
                    });
                    startAdminResendTimer(d.cooldown_seconds || 60);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: d.message || 'Gagal mengirim ulang kode.',
                        background: '#0F172A',
                        color: '#E2E8F0'
                    });
                }
            } catch(e) {
                console.error(e);
            }
        }

        async function handleAdminOtpSubmit(e) {
            e.preventDefault();
            const otp_code = document.getElementById('adminOtpCodeInput').value.trim();
            if (otp_code.length !== 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kode Wajib 6 Digit',
                    text: 'Silakan masukkan 6 angka kode verifikasi email Anda.',
                    background: '#0F172A',
                    color: '#E2E8F0',
                    confirmButtonColor: '#FF6B00'
                });
                return;
            }

            const btnText = document.getElementById('adminOtpBtnText');
            btnText.innerText = 'Memverifikasi...';

            try {
                const res = await fetch('/api/auth/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentAdminEmail, otp_code })
                });

                const result = await res.json();
                btnText.innerText = 'Verifikasi & Masuk Portal';

                if (!res.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Verifikasi Gagal',
                        text: result.message || 'Kode verifikasi salah atau kadaluarsa.',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6B00'
                    });
                    return;
                }

                const roleData = result.role || (result.user.role ? (result.user.role.nama || result.user.role) : '');
                const targetUrl = getAdminDashboardUrl(roleData);
                const roleSlug = getRoleSlug(roleData);

                if (!targetUrl || !roleSlug) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Ditolak',
                        text: 'Kredensial ini bukan akun Administrator.',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6B00'
                    });
                    return;
                }

                localStorage.setItem('admin_token', result.access_token);
                localStorage.setItem('admin_user', JSON.stringify({ ...result.user, role: roleSlug }));
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                document.cookie = "auth_token=" + result.access_token + "; path=/; max-age=86400; SameSite=Lax";

                // Tandai jika akun membutuhkan setup awal username / ganti password acak sistem
                if (!result.user.admin_username || result.user.must_change_password) {
                    localStorage.setItem('pending_first_time_setup', 'true');
                }

                closeAdminOtpModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Autentikasi Berhasil!',
                    text: `Selamat datang kembali, ${result.user.nama}!`,
                    showConfirmButton: false,
                    timer: 1200,
                    background: '#0F172A',
                    color: '#E2E8F0'
                }).then(() => {
                    window.location.href = targetUrl;
                });
            } catch (err) {
                console.error(err);
                btnText.innerText = 'Verifikasi & Masuk Portal';
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi masalah jaringan atau server.',
                    background: '#0F172A',
                    color: '#E2E8F0'
                });
            }
        }

        async function handleAdminLogin(e) {
            e.preventDefault();

            const email = document.getElementById('adminEmail').value.trim();
            const password = document.getElementById('adminPassword').value;

            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const submitBtn = document.getElementById('submitBtn');

            btnText.innerText = "Memverifikasi...";
            btnSpinner.classList.remove('hidden');
            submitBtn.disabled = true;

            const payload = { email, password };

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (response.status === 429) {
                    window.location.href = '/error/429';
                    return;
                }

                const result = await response.json();

                if (!response.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Masuk Gagal',
                        text: result.message || 'Kombinasi email atau kata sandi tidak cocok.',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6B00'
                    });
                    resetButton();
                    return;
                }

                // Cek jika butuh verifikasi OTP
                if (result.requires_otp) {
                    resetButton();
                    openAdminOtpModal(result.email || email);
                    Swal.fire({
                        icon: 'info',
                        title: 'Kode Verifikasi Terkirim',
                        text: result.message || 'Silakan cek kotak masuk email Anda.',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6B00'
                    });
                    return;
                }

                // Cek apakah akun memiliki hak akses Administrator
                const roleData = result.role || (result.user.role ? (result.user.role.nama || result.user.role) : '');
                const targetUrl = getAdminDashboardUrl(roleData);
                const roleSlug = getRoleSlug(roleData);

                if (!targetUrl || !roleSlug) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Ditolak',
                        text: 'Kredensial ini bukan akun Administrator.',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6B00'
                    });
                    resetButton();
                    return;
                }

                // Simpan kredensial admin dan token universal
                localStorage.setItem('admin_token', result.access_token);
                localStorage.setItem('admin_user', JSON.stringify({ ...result.user, role: roleSlug }));
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                document.cookie = "auth_token=" + result.access_token + "; path=/; max-age=86400; SameSite=Lax";

                // Tandai jika akun membutuhkan setup awal (belum punya username atau wajib ubah kata sandi)
                if (result.requires_first_time_setup || !result.user.admin_username || result.user.must_change_password) {
                    localStorage.setItem('pending_first_time_setup', 'true');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Autentikasi Berhasil!',
                    text: `Selamat datang, ${result.user.nama}!`,
                    showConfirmButton: false,
                    timer: 1200,
                    background: '#0F172A',
                    color: '#E2E8F0'
                }).then(() => {
                    window.location.href = targetUrl;
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal menghubungi server.',
                    background: '#0F172A',
                    color: '#E2E8F0',
                    confirmButtonColor: '#FF6B00'
                });
                resetButton();
            }

            function resetButton() {
                btnText.innerText = "Masuk ke Dashboard Admin";
                btnSpinner.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>
