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
                    <div style="font-size:1rem;font-weight:900;color:white;letter-spacing:-0.02em;">REVARO</div>
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
            <div style="display:flex;align-items:center;gap:10px;">
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

    // Auth guard + nav builder
    document.addEventListener('DOMContentLoaded', function() {
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
