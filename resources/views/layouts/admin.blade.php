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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0B132B; color: #F8FAFC; }
        .sidebar-bg { background-color: #1C2541; border-right: 1px solid rgba(255, 255, 255, 0.08); }
        .content-bg { background-color: #0B132B; }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex overflow-hidden">

    <!-- Left Sticky Sidebar -->
    <aside class="w-64 sidebar-bg flex flex-col justify-between h-screen sticky top-0 shrink-0">
        <div class="p-6 space-y-8 flex flex-col h-[calc(100vh-80px)]">
            <!-- Branding -->
            <a href="/superadmin/dashboard" class="flex items-center space-x-3 group">
                <div class="bg-brandOrange p-2 rounded-xl font-black text-xs tracking-wider text-white shadow-lg">
                    REV
                </div>
                <div>
                    <h1 class="text-sm font-black tracking-tight text-white">REVARO KONSOL</h1>
                    <p class="text-[8px] text-brandOrange font-bold uppercase tracking-widest">SISTEM ADMINISTRASI STAF</p>
                </div>
            </a>

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
