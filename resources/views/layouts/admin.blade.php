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
    <style>
        /* Custom Scrollbar Styles */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(249, 115, 22, 0.45); }
        html { scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.2) transparent; }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0B132B; color: #F8FAFC; }
        .sidebar-bg { background-color: #1C2541; border-right: 1px solid rgba(255, 255, 255, 0.08); }
        .content-bg { background-color: #0B132B; }

        /* Light Mode CSS Overrides */
        body.light-mode { background-color: #FAF9F6; color: #0F172A; }
        body.light-mode .content-bg { background-color: #FAF9F6; }
        body.light-mode .sidebar-bg { background-color: #FFFFFF; border-right: 1px solid rgba(249, 115, 22, 0.15); }
        body.light-mode #adminSidebarMenu a { color: #475569; }
        body.light-mode #adminSidebarMenu a:hover { background-color: rgba(249, 115, 22, 0.08); color: #EA580C; }
        body.light-mode #adminSidebarMenu a.bg-brandOrange { background-color: #F97316; color: #FFFFFF; }
        body.light-mode #adminNavName { color: #0F172A !important; }
        body.light-mode #adminNavRole { color: #EA580C !important; }
        body.light-mode #adminNavInitial { background-color: #F1F5F9; color: #475569; border-color: #CBD5E1; }
        body.light-mode .bg-slate-900\/60 { background-color: #FFFFFF !important; border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .border-slate-800 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .border-slate-700\/30 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .bg-slate-900\/40 { background-color: #FFFFFF !important; border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .bg-slate-950 { background-color: #F8FAFC !important; }
        body.light-mode .text-white { color: #0F172A !important; }
        body.light-mode .text-slate-100 { color: #0F172A !important; }
        body.light-mode .text-slate-200 { color: #1E293B !important; }
        body.light-mode .text-slate-300 { color: #334155 !important; }
        body.light-mode .text-slate-400 { color: #475569 !important; }
        body.light-mode .text-slate-500 { color: #64748B !important; }
        body.light-mode .divide-slate-800\/50 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(71,85,105,0.1) !important; }
        body.light-mode .divide-y { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .divide-slate-800 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .card { background: #FFFFFF; border: 1px solid rgba(71,85,105,0.12); box-shadow: 0 4px 18px rgba(0,0,0,0.03); }
        body.light-mode footer { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .p-4.border-t.border-slate-800 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode #themeToggleBtn { color: #0F172A; }
        body.light-mode .brand-text { color: #0F172A !important; }

        /* Day/Night switch styles */
        .theme-switch-container {
            display: inline-block;
            position: relative;
            width: 90px;
            height: 50px;
            transform: scale(0.55);
            transform-origin: right center;
            margin-right: -12px;
        }
        .theme-switch-container .dn-input {
            display: none !important;
        }
        .dn-toggle {
            cursor: pointer;
            display: inline-block;
            position: relative;
            width: 90px;
            height: 50px;
            background-color: #83d8ff;
            border-radius: 84px;
            transition: background-color 200ms cubic-bezier(0.445, 0.05, 0.55, 0.95);
        }
        .dn-toggle-handler {
            display: inline-block;
            position: relative;
            z-index: 1;
            top: 3px;
            left: 3px;
            width: 44px;
            height: 44px;
            background-color: #ffcf96;
            border-radius: 50px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            transition: all 400ms cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform: rotate(-45deg);
        }
        .dn-toggle-handler .dn-crater {
            position: absolute;
            background-color: #e8cda5;
            opacity: 0;
            transition: opacity 200ms ease-in-out;
            border-radius: 100%;
        }
        .dn-toggle-handler .dn-crater--1 { top: 18px; left: 10px; width: 4px; height: 4px; }
        .dn-toggle-handler .dn-crater--2 { top: 28px; left: 22px; width: 6px; height: 6px; }
        .dn-toggle-handler .dn-crater--3 { top: 10px; left: 25px; width: 8px; height: 8px; }
        .dn-star {
            position: absolute;
            background-color: #fff;
            transition: all 300ms cubic-bezier(0.445, 0.05, 0.55, 0.95);
            border-radius: 50%;
        }
        .dn-star--1 { top: 10px; left: 35px; z-index: 0; width: 30px; height: 3px; }
        .dn-star--2 { top: 18px; left: 28px; z-index: 1; width: 30px; height: 3px; }
        .dn-star--3 { top: 27px; left: 40px; z-index: 0; width: 30px; height: 3px; }
        .dn-star--4, .dn-star--5, .dn-star--6 {
            opacity: 0;
            transition: all 300ms 0 cubic-bezier(0.445, 0.05, 0.55, 0.95);
        }
        .dn-star--4 { top: 16px; left: 11px; z-index: 0; width: 2px; height: 2px; transform: translate3d(3px, 0, 0); }
        .dn-star--5 { top: 32px; left: 17px; z-index: 0; width: 3px; height: 3px; transform: translate3d(3px, 0, 0); }
        .dn-star--6 { top: 36px; left: 28px; z-index: 0; width: 2px; height: 2px; transform: translate3d(3px, 0, 0); }
        .dn-input:checked + .dn-toggle { background-color: #749dd6; }
        .dn-input:checked + .dn-toggle .dn-toggle-handler {
            background-color: #ffe5b5;
            transform: translate3d(40px, 0, 0) rotate(0);
        }
        .dn-input:checked + .dn-toggle .dn-toggle-handler .dn-crater { opacity: 1; }
        .dn-input:checked + .dn-toggle .dn-star--1 { width: 2px; height: 2px; }
        .dn-input:checked + .dn-toggle .dn-star--2 { width: 4px; height: 4px; transform: translate3d(-5px, 0, 0); }
        .dn-input:checked + .dn-toggle .dn-star--3 { width: 2px; height: 2px; transform: translate3d(-7px, 0, 0); }
        .dn-input:checked + .dn-toggle .dn-star--4,
        .dn-input:checked + .dn-toggle .dn-star--5,
        .dn-input:checked + .dn-toggle .dn-star--6 {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
        .dn-input:checked + .dn-toggle .dn-star--4 { transition: all 300ms 200ms cubic-bezier(0.445, 0.05, 0.55, 0.95); }
        .dn-input:checked + .dn-toggle .dn-star--5 { transition: all 300ms 300ms cubic-bezier(0.445, 0.05, 0.55, 0.95); }
        .dn-input:checked + .dn-toggle .dn-star--6 { transition: all 300ms 400ms cubic-bezier(0.445, 0.05, 0.55, 0.95); }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex overflow-hidden">

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
                <span>🚪</span> Keluar (Logout)
            </button>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col h-screen overflow-y-auto content-bg">
        <div class="p-8 max-w-7xl w-full mx-auto space-y-6">
            @yield('content')
        </div>
        <footer class="mt-auto py-6 border-t border-slate-800 text-center text-[10px] text-slate-500 font-medium">
            © 2026 REVARO — Konsol Administrasi Bersih.
        </footer>
    </main>

    <!-- Global Layout Auth & Navigation Script -->
    <script>
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
                    { path: '/admin/verifikator/dashboard', label: '🆕 Antrian Pending', icon: '⏳' },
                    { path: '/admin/verifikator/resubmitted', label: '📝 Antrian Resubmit', icon: '🔄' },
                    { path: '/admin/verifikator/reverifications', label: '🕐 Re-Verifikasi Berkala', icon: '🕐' },
                    { path: '/admin/verifikator/history', label: '📜 Riwayat Keputusan Saya', icon: '📜' }
                ];
            } else if (role === 'admin-resolusi') {
                menuItems = [
                    { path: '/admin/resolusi/dashboard', label: '⚖️ Antrian Sengketa Saya', icon: '⚖️' },
                    { path: '/admin/resolusi/history', label: '📜 Riwayat Keputusan Saya', icon: '📜' }
                ];
            }

            menuContainer.innerHTML = menuItems.map(item => {
                const isActive = currentPath === item.path;
                const activeClasses = 'bg-brandOrange text-white font-bold shadow-sm';
                const inactiveClasses = 'text-slate-400 hover:text-white hover:bg-slate-800/50';

                return `
                    <a href="${item.path}" class="w-full text-left px-4 py-2.5 rounded-xl transition whitespace-nowrap flex items-center gap-3 text-xs ${isActive ? activeClasses : inactiveClasses}">
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
