<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal — REVARO')</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/images/revaro-icon-transparent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#0A4C8A',
                        brandOrange: '#FF6A00',
                        darkNavy: '#0F172A',
                        warmBg: '#FBF9F5',
                        accentBrown: '#C85A32',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-switch.css') }}">
    <script>window.PLATFORM_LANGUAGE = "{{ $platform_language ?? 'id' }}";</script>
    <script src="{{ asset('js/app-i18n.js') }}"></script>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col lg:flex-row overflow-x-hidden">

    <!-- Mobile Backdrop Overlay -->
    <div id="adminSidebarBackdrop" onclick="toggleAdminMobileSidebar(false)" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Left Sticky Sidebar -->
    <aside class="w-64 sidebar-bg flex flex-col justify-between h-screen sticky top-0 shrink-0">
        <div class="p-6 space-y-8 flex flex-col h-[calc(100vh-80px)]">
            <!-- Branding + Toggle -->
            <div class="flex items-center justify-between">
                <a href="/superadmin/dashboard" class="flex items-center space-x-3 group">
                    <div class="bg-brandOrange p-2 rounded-xl font-black text-xs tracking-wider text-white shadow-lg">
                        REV
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-tight text-white brand-text">REVARO KONSOL</h1>
                        <p class="text-[8px] text-brandOrange font-bold uppercase tracking-widest text-[7px]">SISTEM STAF</p>
                    </div>
                </a>
                <div class="flex items-center gap-2">
                    <div class="theme-switch-container">
                        <input class="dn-input" id="dn" type="checkbox" checked />
                        <label class="dn-toggle" for="dn">
                            <span class="dn-toggle-handler">
                                <span class="dn-crater dn-crater--1"></span>
                                <span class="dn-crater dn-crater--2"></span>
                                <span class="dn-crater dn-crater--3"></span>
                            </span>
                            <span class="dn-star dn-star--1"></span>
                            <span class="dn-star dn-star--2"></span>
                            <span class="dn-star dn-star--3"></span>
                            <span class="dn-star dn-star--4"></span>
                            <span class="dn-star dn-star--5"></span>
                            <span class="dn-star dn-star--6"></span>
                        </label>
                    </div>
                    <button onclick="toggleAdminMobileSidebar(false)" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Admin Profile Card -->
            <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-700/30 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-slate-700 text-white font-bold flex items-center justify-center text-sm border border-slate-600 shadow" id="adminNavInitial">
                    A
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-black text-white truncate" id="adminNavName">Memuat...</p>
                    <p class="text-[9px] text-brandOrange font-bold uppercase tracking-widest truncate" id="adminNavRole">Staff</p>
                </div>
            </div>

            <!-- Dynamic Navigation Links -->
            <nav class="flex-1 overflow-y-auto space-y-1.5 pr-1 scrollbar-thin scrollbar-thumb-slate-800" id="adminSidebarMenu">
                <!-- Dynamic Content Loaded via JS -->
            </nav>
        </div>

        <!-- Logout Action Area -->
        <div class="p-4 border-t border-slate-800">
            <button onclick="handleAdminLogout()" class="w-full bg-red-950 hover:bg-red-900 text-red-400 hover:text-white font-bold text-xs py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 border border-red-900/30">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar (Logout)</span>
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col h-screen overflow-y-auto content-bg w-full">
        <!-- Mobile Header Bar -->
        <div class="lg:hidden flex items-center justify-between p-4 bg-slate-900/90 border-b border-slate-800 sticky top-0 z-30">
            <button onclick="toggleAdminMobileSidebar(true)" class="p-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition" title="Buka Navigasi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="font-extrabold text-white text-xs flex items-center gap-2">
                <span class="bg-brandOrange text-white font-black px-2 py-0.5 rounded-lg text-[10px]">REV</span>
                <span>REVARO KONSOL</span>
            </div>
        </div>

        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
            @yield('content')
        </div>
        <footer class="mt-auto py-6 border-t border-slate-800 text-center text-[10px] text-slate-500 font-medium">
            © 2026 REVARO — Konsol Administrasi Bersih.
        </footer>
    </main>

    <!-- Global Layout Auth & Navigation Script -->
    <script>
        function toggleAdminMobileSidebar(open) {
            const sidebar = document.querySelector('aside.sidebar-bg');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (sidebar && backdrop) {
                if (open) {
                    sidebar.classList.add('mobile-open');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.remove('mobile-open');
                    backdrop.classList.add('hidden');
                }
            }
        }

        function toggleTheme() {
            const body = document.body;
            if (body.classList.contains('light-mode')) {
                body.classList.remove('light-mode');
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.add('light-mode');
                localStorage.setItem('theme', 'light');
            }
        }

        // Early theme load
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-mode');
        } else {
            document.body.classList.remove('light-mode');
        }

        function checkAuthResponse(res) {
            // Do not immediately kick out to avoid abrupt session drops
            return res.ok;
        }

        let currentUser = null;

        window.addEventListener('load', async () => {
            const checkbox = document.getElementById('dn');
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                if (checkbox) checkbox.checked = false;
            } else {
                if (checkbox) checkbox.checked = true;
            }

            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    const body = document.body;
                    if (this.checked) {
                        body.classList.remove('light-mode');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        body.classList.add('light-mode');
                        localStorage.setItem('theme', 'light');
                    }
                });
            }

            // Interceptor global agar setiap 401 Unauthorized (misal database di-migrate ulang) otomatis membersihkan sesi basi
            (function setupAuthInterceptor() {
                const originalFetch = window.fetch;
                window.fetch = async function(...args) {
                    try {
                        const response = await originalFetch(...args);
                        if (response.status === 401) {
                            const url = typeof args[0] === 'string' ? args[0] : (args[0] ? args[0].url : '');
                            if (!url.includes('/api/auth/login') && !url.includes('/api/auth/login-admin')) {
                                console.warn('Sesi staf tidak valid / kedaluwarsa (401). Mengalihkan ke session expired...');
                                localStorage.removeItem('admin_token');
                                localStorage.removeItem('admin_user');
                                localStorage.removeItem('token');
                                localStorage.removeItem('user');
                                window.location.href = '/session-expired';
                            }
                        }
                        if (response.status === 503) {
                            try {
                                const clone = response.clone();
                                const data = await clone.json();
                                if (data && data.is_maintenance) {
                                    window.location.reload();
                                    return response;
                                }
                            } catch(e) {}
                        }
                        if (response.status === 429) {
                            window.location.href = '/error/429';
                            return response;
                        }
                        return response;
                    } catch (err) {
                        throw err;
                    }
                };
            })();

            // Realtime Maintenance Heartbeat for Admin
            setInterval(async () => {
                try {
                    const t = localStorage.getItem('admin_token') || localStorage.getItem('token');
                    if (t) {
                        const ping = await fetch('/api/auth/me', {
                            headers: { 'Authorization': `Bearer ${t}` }
                        });
                        if (ping.status === 401) {
                            localStorage.removeItem('admin_token');
                            localStorage.removeItem('admin_user');
                            window.location.href = '/session-expired';
                            return;
                        }
                        if (ping.status === 503) {
                            window.location.reload();
                        }
                    }
                } catch(e) {}
            }, 10000);

            const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
            const userRaw = localStorage.getItem('admin_user') || localStorage.getItem('user');
            if (!token || !userRaw) {
                window.location.href = '/login-admin';
                return;
            }

            try {
                currentUser = JSON.parse(userRaw);
            } catch(e) {
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                window.location.href = '/login-admin';
                return;
            }

            let role = currentUser.role || '';
            if (typeof role === 'object' && role !== null) {
                role = role.nama || role.name || '';
            }
            const lowerRole = role.toLowerCase();
            let roleSlug = role;
            if (lowerRole.includes('verifikator')) roleSlug = 'admin-verifikator';
            else if (lowerRole.includes('resolusi')) roleSlug = 'admin-resolusi';
            else if (lowerRole.includes('pemilik')) roleSlug = 'super-admin-pemilik';

            localStorage.setItem('admin_token', token);
            localStorage.setItem('admin_user', JSON.stringify({ ...currentUser, role: roleSlug }));

            const nameElem = document.getElementById('adminNavName');
            const initialElem = document.getElementById('adminNavInitial');
            if (nameElem && currentUser.nama) nameElem.innerText = currentUser.nama;
            if (initialElem && currentUser.nama) initialElem.innerText = currentUser.nama.charAt(0).toUpperCase();

            const roleDisplayNames = {
                'admin-verifikator': 'Admin Verifikator',
                'admin-resolusi': 'Admin Resolusi'
            };
            const roleElem = document.getElementById('adminNavRole');
            if (roleElem) roleElem.innerText = roleDisplayNames[roleSlug] || 'Staf';

            if (roleSlug !== 'admin-verifikator' && roleSlug !== 'admin-resolusi') {
                let homeUrl = '/login-admin';
                if (roleSlug === 'super-admin-pemilik') {
                    homeUrl = '/superadmin/pemilik/dashboard';
                }
                window.location.href = homeUrl;
                return;
            }

            renderSidebarMenu(roleSlug);

            // Verifikasi token aktif ke server
            fetch('/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            }).then(res => {
                if (res.status === 401) {
                    localStorage.removeItem('admin_token');
                    localStorage.removeItem('admin_user');
                    window.location.href = '/session-expired';
                }
            }).catch(() => {});

            // Cek jika staf admin pertama kali login (belum punya username atau wajib ganti password awal sistem)
            if (localStorage.getItem('pending_first_time_setup') === 'true' || !currentUser.admin_username || currentUser.must_change_password) {
                const ftModal = document.getElementById('adminFirstTimeSetupModal');
                if (ftModal) {
                    ftModal.classList.remove('hidden');
                    if (currentUser.must_change_password) {
                        const pwdNotice = document.getElementById('adminFtPwdNotice');
                        if (pwdNotice) pwdNotice.classList.remove('hidden');
                    }
                }
            }

            if (typeof initPage === 'function') {
                initPage();
            }
        });

        function toggleGenericPassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
            }
        }

        async function submitAdminFirstTimeSetup() {
            const uInput = document.getElementById('adminFtUsernameInput').value.trim();
            const pInput = document.getElementById('adminFtPasswordInput').value;
            const pConfirm = document.getElementById('adminFtPasswordConfirmInput').value;

            if (!uInput || uInput.length < 3) {
                Swal.fire({
                    title: 'Username Wajib Diisi',
                    text: 'Silakan masukkan username minimal 3 karakter untuk akun staf Anda.',
                    icon: 'warning',
                    background: '#0F172A',
                    color: '#E2E8F0',
                    confirmButtonColor: '#FF6A00'
                });
                return;
            }

            if (!/^[a-zA-Z0-9_]+$/.test(uInput)) {
                Swal.fire({
                    title: 'Format Username Tidak Valid',
                    text: 'Username hanya boleh terdiri dari huruf, angka, dan garis bawah (_).',
                    icon: 'warning',
                    background: '#0F172A',
                    color: '#E2E8F0',
                    confirmButtonColor: '#FF6A00'
                });
                return;
            }

            const user = JSON.parse(localStorage.getItem('admin_user') || '{}');
            if (user.must_change_password || pInput) {
                if (!pInput || pInput.length < 6) {
                    Swal.fire({
                        title: 'Kata Sandi Baru Wajib',
                        text: 'Sebagai staf admin baru, Anda wajib mengganti password acak sistem dengan kata sandi baru pribadi Anda (minimal 6 karakter).',
                        icon: 'warning',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6A00'
                    });
                    return;
                }

                if (pInput !== pConfirm) {
                    Swal.fire({
                        title: 'Konfirmasi Kata Sandi Tidak Cocok',
                        text: 'Kata sandi baru dan konfirmasi kata sandi tidak sama.',
                        icon: 'warning',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6A00'
                    });
                    return;
                }
            }

            const token = localStorage.getItem('admin_token');
            const submitBtn = document.getElementById('adminFtSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Menyimpan...';

            const payload = { username: uInput };
            if (pInput) {
                payload.new_password = pInput;
                payload.new_password_confirmation = pConfirm;
            }

            try {
                const r = await fetch('/api/admin/setup-first-time', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const d = await r.json();
                submitBtn.disabled = false;
                submitBtn.innerText = 'Simpan & Aktifkan Akun';

                if (r.ok) {
                    localStorage.removeItem('pending_first_time_setup');
                    const updatedUser = JSON.parse(localStorage.getItem('admin_user') || '{}');
                    updatedUser.admin_username = uInput;
                    updatedUser.must_change_password = false;
                    localStorage.setItem('admin_user', JSON.stringify(updatedUser));

                    document.getElementById('adminFirstTimeSetupModal').classList.add('hidden');

                    Swal.fire({
                        title: 'Setup Akun Selesai!',
                        html: `
                            <p class="text-sm text-slate-300">
                                Username resmi <b>${uInput}</b> dan kata sandi baru Anda berhasil diaktifkan.<br>
                                Gunakan kredensial ini untuk login ke portal staf REVARO selanjutnya.
                            </p>
                        `,
                        icon: 'success',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6A00'
                    });
                } else {
                    let errText = d.message || 'Terjadi kesalahan saat menyimpan pengaturan.';
                    if (d.errors) {
                        errText = Object.values(d.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        title: 'Gagal Menyimpan',
                        html: errText,
                        icon: 'error',
                        background: '#0F172A',
                        color: '#E2E8F0',
                        confirmButtonColor: '#FF6A00'
                    });
                }
            } catch(e) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Simpan & Aktifkan Akun';
                Swal.fire({
                    title: 'Error Jaringan',
                    text: 'Tidak dapat menghubungi server.',
                    icon: 'error',
                    background: '#0F172A',
                    color: '#E2E8F0',
                    confirmButtonColor: '#FF6A00'
                });
            }
        }

        function renderSidebarMenu(role) {
            const menuContainer = document.getElementById('adminSidebarMenu');
            const currentPath = window.location.pathname;

            let menuItems = [];

            const flagIcon = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>`;

            if (role === 'admin-verifikator') {
                menuItems = [
                    { path: '/admin/verifikator/dashboard', label: 'Antrian Pending', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>` },
                    { path: '/admin/verifikator/resubmitted', label: 'Antrian Resubmit', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>` },
                    { path: '/admin/verifikator/reverifications', label: 'Re-Verifikasi Berkala', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>` },
                    { path: '/admin/verifikator/history', label: 'Riwayat Keputusan Saya', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>` },
                    { path: '/admin/verifikator/instruksi', label: 'Instruksi Pemilik', icon: flagIcon },
                    { path: '/admin/verifikator/settings', label: 'Pengaturan Profil', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>` }
                ];
            } else if (role === 'admin-resolusi') {
                menuItems = [
                    { path: '/admin/resolusi/dashboard', label: 'Antrian Sengketa Transaksi', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>` },
                    { path: '/admin/resolusi/evaluasi-rating', label: 'Evaluasi Rating & Disiplin', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>` },
                    { path: '/admin/resolusi/history', label: 'Riwayat Keputusan Saya', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>` },
                    { path: '/admin/resolusi/instruksi', label: 'Instruksi Pemilik', icon: flagIcon },
                    { path: '/admin/resolusi/settings', label: 'Pengaturan Profil', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>` }
                ];
            }

            menuContainer.innerHTML = menuItems.map(item => {
                const isActive = currentPath === item.path;
                const activeClasses = 'bg-brandOrange text-white font-bold shadow-sm';
                const inactiveClasses = 'text-slate-400 hover:text-white hover:bg-slate-800/50';

                return `
                    <a href="${item.path}" onclick="toggleAdminMobileSidebar(false)" class="w-full text-left px-4 py-2.5 rounded-xl transition whitespace-nowrap flex items-center gap-3 text-xs ${isActive ? activeClasses : inactiveClasses}">
                        <span>${item.icon}</span>
                        <span>${item.label}</span>
                    </a>
                `;
            }).join('');
        }

        function handleAdminLogout() {
            const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
            const clearLocalSession = () => {
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                localStorage.removeItem('pending_first_time_setup');
                sessionStorage.clear();
                window.location.href = '/login-admin';
            };

            if (token) {
                fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                }).catch(() => {}).finally(() => {
                    clearLocalSession();
                });
            } else {
                clearLocalSession();
            }
        }
    </script>

    <!-- Modal Setup First Time Staf Admin (Verifikator & Resolusi) -->
    <div id="adminFirstTimeSetupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="w-full max-w-lg bg-slate-900 border border-brandOrange/40 rounded-3xl p-7 space-y-5 shadow-2xl relative">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-brandOrange to-amber-500 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-orange-500/20">
                    🔐
                </div>
                <h3 class="text-lg font-black text-white tracking-wide">Pengaturan Awal Akun Administrator</h3>
                <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                    Selamat datang di konsol staf REVARO. Demi keamanan platform, Anda wajib menentukan Username resmi dan memperbarui kata sandi awal akun Anda.
                </p>
            </div>

            <div class="space-y-4">
                <!-- Field Input Username -->
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-1.5">
                    <label class="block text-[11px] font-bold text-orange-400 uppercase tracking-wider">
                        1. Tentukan Username Resmi Anda *
                    </label>
                    <input type="text" id="adminFtUsernameInput" placeholder="cth. farhan_verif atau ibnu_resolusi"
                           class="w-full text-sm bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 font-mono transition placeholder:text-slate-600">
                    <p class="text-[10px] text-slate-500">
                        Hanya huruf, angka, dan garis bawah (_). Minimal 3 karakter. Username ini akan menjadi identitas resmi Anda.
                    </p>
                </div>

                <!-- Field Ganti Password Baru -->
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-orange-400 uppercase tracking-wider">
                            2. Kata Sandi Baru Pribadi *
                        </label>
                        <span id="adminFtPwdNotice" class="hidden text-[10px] text-amber-400 font-bold">Wajib Diganti (Password Sistem)</span>
                    </div>
                    <div class="relative">
                        <input type="password" id="adminFtPasswordInput" placeholder="Kata Sandi Baru (min. 6 karakter)"
                               class="w-full text-sm bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 pr-10 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-600">
                        <button type="button" onclick="toggleGenericPassword('adminFtPasswordInput', 'adminFtEyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="adminFtEyeIcon1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" id="adminFtPasswordConfirmInput" placeholder="Konfirmasi Kata Sandi Baru"
                               class="w-full text-sm bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 pr-10 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-600">
                        <button type="button" onclick="toggleGenericPassword('adminFtPasswordConfirmInput', 'adminFtEyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="adminFtEyeIcon2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <button type="button" id="adminFtSubmitBtn" onclick="submitAdminFirstTimeSetup()"
                    class="w-full bg-brandOrange hover:bg-orange-600 text-white font-extrabold text-xs py-3.5 rounded-xl transition shadow-lg shadow-orange-600/30 active:scale-[0.99] flex items-center justify-center space-x-2">
                    <span>Simpan & Aktifkan Akun</span>
                </button>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
