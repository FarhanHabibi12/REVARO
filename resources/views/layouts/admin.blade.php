<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal — REVARO')</title>
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
            if (res.status === 401) {
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                window.location.href = '/admin/login';
                return false;
            }
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

            const token = localStorage.getItem('admin_token');
            const userRaw = localStorage.getItem('admin_user');
            if (!token || !userRaw) {
                window.location.href = '/admin/login';
                return;
            }

            try {
                currentUser = JSON.parse(userRaw);
                const role = currentUser.role || '';

                document.getElementById('adminNavName').innerText = currentUser.nama;
                document.getElementById('adminNavInitial').innerText = currentUser.nama.charAt(0).toUpperCase();

                const roleDisplayNames = {
                    'admin-verifikator': 'Admin Verifikator',
                    'admin-resolusi': 'Admin Resolusi'
                };
                document.getElementById('adminNavRole').innerText = roleDisplayNames[role] || 'Staf';

                if (role !== 'admin-verifikator' && role !== 'admin-resolusi') {
                    let homeUrl = '/admin/login';
                    if (role === 'super-admin-pemilik') {
                        homeUrl = '/superadmin/pemilik/dashboard';
                    }
                    window.location.href = homeUrl;
                    return;
                }

                renderSidebarMenu(role);

                if (typeof initPage === 'function') {
                    initPage();
                }

            } catch (e) {
                console.error('Failed to verify token:', e);
                window.location.href = '/admin/login';
            }
        });

        function renderSidebarMenu(role) {
            const menuContainer = document.getElementById('adminSidebarMenu');
            const currentPath = window.location.pathname;

            let menuItems = [];

            if (role === 'admin-verifikator') {
                menuItems = [
                    { path: '/admin/verifikator/dashboard', label: 'Antrian Pending', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>` },
                    { path: '/admin/verifikator/resubmitted', label: 'Antrian Resubmit', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>` },
                    { path: '/admin/verifikator/reverifications', label: 'Re-Verifikasi Berkala', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>` },
                    { path: '/admin/verifikator/history', label: 'Riwayat Keputusan Saya', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>` },
                    { path: '/admin/verifikator/settings', label: 'Pengaturan Profil', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>` }
                ];
            } else if (role === 'admin-resolusi') {
                menuItems = [
                    { path: '/admin/resolusi/dashboard', label: 'Antrian Sengketa Saya', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>` },
                    { path: '/admin/resolusi/history', label: 'Riwayat Keputusan Saya', icon: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>` },
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
            const token = localStorage.getItem('admin_token');
            fetch('/api/auth/logout', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            }).finally(() => {
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                window.location.href = '/admin/login';
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
