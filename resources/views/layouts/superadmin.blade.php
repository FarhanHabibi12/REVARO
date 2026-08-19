<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin — REVARO')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brandOrange: '#F97316',
                        brandDark: '#0D1117',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-switch.css') }}">
    <script>window.PLATFORM_LANGUAGE = "{{ $platform_language ?? 'id' }}";</script>
    <script src="{{ asset('js/app-i18n.js') }}"></script>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Brand -->
        <div style="padding: 24px 20px 20px; border-bottom: 1px solid rgba(248,113,22,0.1);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#F97316,#EA580C);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">🔧</div>
                <div>
                    <div style="font-size:1rem;font-weight:900;letter-spacing:-0.02em;" class="brand-text text-white">REVARO</div>
                    <div style="font-size:0.6rem;color:#F97316;font-weight:700;letter-spacing:0.05em;" id="sidebarRoleLabel">LOADING...</div>
                </div>
            </div>
            <div style="margin-top:12px;padding:8px 12px;background:rgba(249,115,22,0.08);border-radius:8px;border:1px solid rgba(249,115,22,0.15);">
                <p style="font-size:0.65rem;color:#94A3B8;margin:0 0 2px;">Login sebagai:</p>
                <p style="font-size:0.75rem;font-weight:700;color:white;margin:0;" id="sidebarUserName">—</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav style="padding: 12px 12px; flex:1;" id="sidebarNav">
            <!-- Populated by JS based on role -->
        </nav>

        <!-- Bottom: Logout -->
        <div style="padding: 16px 12px; border-top: 1px solid rgba(71,85,105,0.2);">
            <button onclick="handleLogout()" style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 16px;border-radius:10px;font-size:0.78rem;font-weight:700;color:#EF4444;background:rgba(239,68,68,0.08);border:none;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.15)'" onmouseout="this.style.background='rgba(239,68,68,0.08)'">
                <span>🚪</span> Keluar
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top bar -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div>
                <h1 style="font-size:1.25rem;font-weight:900;color:white;margin:0;letter-spacing:-0.02em;">@yield('page-title', 'Dashboard')</h1>
                <p style="font-size:0.72rem;color:#64748B;margin:4px 0 0;font-weight:500;">@yield('page-subtitle', '')</p>
            </div>
            <div style="display:flex;align-items:center;gap:15px;">
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
                <div style="width:32px;height:32px;background:linear-gradient(135deg,#F97316,#EA580C);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.9rem;">👤</div>
            </div>
        </div>

        @yield('content')
    </main>

    <script>
    // Intercept native fetch to handle 401 Unauthenticated globally
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        const response = await originalFetch(...args);
        if (response.status === 401) {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/admin/login';
        }
        return response;
    };

    const MENU_PEMILIK = [
        { group: 'KENDALI STRATEGIS & DATA MASTER', items: [
            { icon: '📊', label: 'Dashboard Keuangan', url: '/superadmin/pemilik/dashboard' },
            { icon: '👥', label: 'Manajemen Akun Staf', url: '/superadmin/pemilik/accounts' },
            { icon: '🛠️', label: 'Kategori Servis', url: '/superadmin/pemilik/services' },
            { icon: '💸', label: 'Kebijakan Komisi', url: '/superadmin/pemilik/commission' },
            { icon: '🗺️', label: 'Wilayah Operasional', url: '/superadmin/pemilik/regions' },
            { icon: '✅', label: 'Verifikasi Mitra Baru', url: '/superadmin/pemilik/verifications' },
            { icon: '💾', label: 'Backup & Restore DB', url: '/superadmin/pemilik/backup' },
            { icon: '📜', label: 'Audit Log System', url: '/superadmin/pemilik/audit-logs' },
            { icon: '🔑', label: 'Maintenance Mode', url: '/superadmin/pemilik/maintenance' },
            { icon: '⚙️', label: 'Pengaturan Sistem', url: '/superadmin/pemilik/settings' },
        ]},
        { group: 'MONITORING & KINERJA PLATFORM', items: [
            { icon: '📋', label: 'Scorecard Verifikator', url: '/superadmin/monitoring/scorecard-verifikator' },
            { icon: '🏛️', label: 'Scorecard Resolusi', url: '/superadmin/monitoring/scorecard-resolusi' },
            { icon: '📈', label: 'Tren & Analitik', url: '/superadmin/monitoring/trends' },
            { icon: '🔔', label: 'Peringatan SLA & Anomali', url: '/superadmin/monitoring/alerts' },
            { icon: '🚩', label: 'Flag Laporan Masuk', url: '/superadmin/pemilik/flags' },
            { icon: '⚖️', label: 'Eskalasi Kasus Dispute', url: '/superadmin/pemilik/disputes' },
        ]}
    ];

    function buildNav(menuGroups) {
        const nav = document.getElementById('sidebarNav');
        const currentPath = window.location.pathname;
        let html = '';
        menuGroups.forEach(group => {
            html += `<div class="nav-group-label">${group.group}</div>`;
            group.items.forEach(item => {
                const isActive = currentPath === item.url || currentPath.startsWith(item.url + '/');
                html += `<a href="${item.url}" class="nav-link${isActive ? ' active' : ''}">${item.icon} <span>${item.label}</span></a>`;
            });
        });
        nav.innerHTML = html;
    }

    function handleLogout() {
        Swal.fire({
            title: 'Keluar dari Portal?',
            text: 'Sesi Anda akan diakhiri.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#F97316',
            cancelButtonColor: '#475569',
        }).then(result => {
            if (result.isConfirmed) {
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
        });
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

    // Auth guard + nav builder + theme load
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('dn');
        
        // Load theme early
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-mode');
            if (checkbox) checkbox.checked = false;
        } else {
            document.body.classList.remove('light-mode');
            if (checkbox) checkbox.checked = true;
        }

        // Handle checkbox change
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
        const user  = JSON.parse(localStorage.getItem('admin_user') || '{}');
        if (!token) { window.location.href = '/admin/login'; return; }

        document.getElementById('sidebarUserName').textContent = user.nama || '—';

        let role = user.role || '';
        if (typeof role === 'object' && role !== null) {
            role = role.nama || role.name || '';
        }
        if (role === 'Super Admin (Pemilik Sistem)') {
            role = 'super-admin-pemilik';
        }

        if (role === 'super-admin-pemilik') {
            document.getElementById('sidebarRoleLabel').textContent = 'SUPER ADMIN — PEMILIK';
            buildNav(MENU_PEMILIK);
        } else {
            let homeUrl = '/admin/login';
            if (role === 'admin-verifikator') homeUrl = '/admin/verifikator/dashboard';
            else if (role === 'admin-resolusi') homeUrl = '/admin/resolusi/dashboard';
            window.location.href = homeUrl;
        }
    });
    </script>
    @yield('scripts')
</body>
</html>
