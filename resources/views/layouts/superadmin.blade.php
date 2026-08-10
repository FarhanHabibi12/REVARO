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
    <style>
        /* Custom Scrollbar Styles */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(249, 115, 22, 0.45); }
        html { scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.2) transparent; }

        :root { --sidebar-width: 260px; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0D1117; color: #E2E8F0; }
        .sidebar { width: var(--sidebar-width); height: 100vh; background: rgba(15,20,30,0.97); border-right: 1px solid rgba(248,113,22,0.12); position: fixed; top: 0; left: 0; z-index: 50; display: flex; flex-direction: column; overflow-y: auto; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; padding: 2rem; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 9px 16px; border-radius: 10px; font-size: 0.78rem; font-weight: 600; color: #94A3B8; transition: all 0.2s; text-decoration: none; margin: 2px 0; }
        .nav-link:hover { background: rgba(248,113,22,0.1); color: #F97316; }
        .nav-link.active { background: rgba(248,113,22,0.15); color: #F97316; border-left: 3px solid #F97316; }
        .nav-group-label { font-size: 0.65rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; padding: 16px 16px 6px; }
        .card { background: rgba(30,41,59,0.5); border: 1px solid rgba(71,85,105,0.3); border-radius: 16px; }
        .btn-primary { background: linear-gradient(135deg, #F97316, #EA580C); color: white; font-weight: 700; padding: 8px 18px; border-radius: 10px; border: none; cursor: pointer; font-size: 0.8rem; transition: all 0.2s; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(249,115,22,0.4); }
        .btn-outline { background: transparent; color: #94A3B8; font-weight: 600; padding: 8px 18px; border-radius: 10px; border: 1px solid rgba(71,85,105,0.4); cursor: pointer; font-size: 0.8rem; transition: all 0.2s; }
        .btn-outline:hover { border-color: #F97316; color: #F97316; }
        .badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-green { background: rgba(16,185,129,0.15); color: #10B981; }
        .badge-red { background: rgba(239,68,68,0.15); color: #EF4444; }
        .badge-yellow { background: rgba(234,179,8,0.15); color: #EAB308; }
        .badge-blue { background: rgba(59,130,246,0.15); color: #3B82F6; }
        .badge-gray { background: rgba(100,116,139,0.15); color: #64748B; }
        .input-field { width: 100%; background: rgba(15,23,42,0.8); border: 1px solid rgba(71,85,105,0.4); border-radius: 10px; padding: 9px 14px; color: #E2E8F0; font-size: 0.82rem; transition: all 0.2s; outline: none; }
        .input-field:focus { border-color: #F97316; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
        .input-field::placeholder { color: #475569; }
        select.input-field option { background: #1E293B; }
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #64748B; padding: 10px 14px; text-align: left; border-bottom: 1px solid rgba(71,85,105,0.3); }
        table.data-table td { padding: 12px 14px; font-size: 0.8rem; border-bottom: 1px solid rgba(30,41,59,0.8); vertical-align: middle; }
        table.data-table tr:hover td { background: rgba(248,113,22,0.04); }

        /* Light Mode CSS Overrides */
        body.light-mode { background: #FAF9F6; color: #0F172A; }
        body.light-mode .sidebar { background: #FFFFFF; border-right: 1px solid rgba(249,115,22,0.15); }
        body.light-mode #sidebarUserName { color: #0F172A !important; }
        body.light-mode #sidebarRoleLabel { color: #EA580C !important; }
        body.light-mode .nav-link { color: #475569; }
        body.light-mode .nav-link:hover { background: rgba(249,115,22,0.08); color: #EA580C; }
        body.light-mode .nav-link.active { background: rgba(249,115,22,0.12); color: #EA580C; border-left: 3px solid #EA580C; }
        body.light-mode .nav-group-label { color: #94A3B8; }
        body.light-mode .card { background: #FFFFFF; border: 1px solid rgba(71,85,105,0.12); box-shadow: 0 4px 18px rgba(0,0,0,0.03); }
        body.light-mode h1, body.light-mode h2, body.light-mode h3, body.light-mode th { color: #0F172A !important; }
        body.light-mode td { color: #334155 !important; border-bottom: 1px solid rgba(0,0,0,0.04); }
        body.light-mode table.data-table tr:hover td { background: rgba(249,115,22,0.03); }
        body.light-mode .input-field { background: #F1F5F9; border: 1px solid #CBD5E1; color: #0F172A; }
        body.light-mode .input-field:focus { border-color: #EA580C; background: #FFFFFF; }
        body.light-mode .input-field::placeholder { color: #94A3B8; }
        body.light-mode select.input-field option { background: #FFFFFF; color: #0F172A; }
        
        /* Tailwind classes overrides for light-mode */
        body.light-mode .text-white { color: #0F172A !important; }
        body.light-mode .text-slate-100 { color: #0F172A !important; }
        body.light-mode .text-slate-200 { color: #1E293B !important; }
        body.light-mode .text-slate-300 { color: #334155 !important; }
        body.light-mode .text-slate-400 { color: #475569 !important; }
        body.light-mode .text-slate-500 { color: #64748B !important; }
        body.light-mode .bg-slate-900\/40 { background-color: #FFFFFF !important; border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .bg-slate-950 { background-color: #F8FAFC !important; }
        body.light-mode .border-slate-800 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .border-slate-900 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .bg-slate-900 { background-color: #FFFFFF !important; }
        body.light-mode .divide-slate-800\/50 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(71,85,105,0.1) !important; }
        body.light-mode .divide-y { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .divide-slate-800 { border-color: rgba(71,85,105,0.12) !important; }
        body.light-mode .bg-brandDark { background-color: #FAF9F6 !important; }
        body.light-mode .brand-text { color: #0F172A !important; }

        /* Day/Night switch styles */
        .theme-switch-container {
            display: inline-block;
            position: relative;
            width: 90px;
            height: 50px;
            transform: scale(0.65);
            transform-origin: right center;
            margin-right: -10px;
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
            { icon: '⚙️', label: 'Kebijakan Komisi', url: '/superadmin/pemilik/commission' },
            { icon: '🗺️', label: 'Wilayah Operasional', url: '/superadmin/pemilik/regions' },
            { icon: '✅', label: 'Verifikasi Mitra Baru', url: '/superadmin/pemilik/verifications' },
            { icon: '💾', label: 'Backup & Restore DB', url: '/superadmin/pemilik/backup' },
            { icon: '🛡️', label: 'Anti-Hack & Keamanan', url: '/superadmin/pemilik/security' },
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

        const role = user.role || '';
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
