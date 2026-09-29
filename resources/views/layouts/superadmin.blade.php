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
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="toggleMobileSidebar(false)" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Brand -->
        <div style="padding: 24px 20px 20px; border-bottom: 1px solid rgba(248,113,22,0.1);">
            <div style="display:flex;align-items:center;justify-between;gap:10px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#F97316,#EA580C);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:900;letter-spacing:-0.02em;" class="brand-text text-white">REVARO</div>
                        <div style="font-size:0.6rem;color:#F97316;font-weight:700;letter-spacing:0.05em;" id="sidebarRoleLabel">LOADING...</div>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar(false)" class="lg:hidden ml-auto text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top bar -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:12px;">
                <button onclick="toggleMobileSidebar(true)" class="lg:hidden p-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition" title="Buka Menu Navigasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 style="font-size:1.25rem;font-weight:900;color:white;margin:0;letter-spacing:-0.02em;">@yield('page-title', 'Dashboard')</h1>
                    <p style="font-size:0.72rem;color:#64748B;margin:4px 0 0;font-weight:500;">@yield('page-subtitle', '')</p>
                </div>
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
                <div style="width:32px;height:32px;background:linear-gradient(135deg,#F97316,#EA580C);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    <script>
    function toggleMobileSidebar(open) {
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
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

    const SVG_ICONS = {
        dashboard: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>`,
        accounts: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`,
        security: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`,
        audit: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
        cleaner: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`,
        services: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
        commission: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        regions: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
        verifications: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        database: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>`,
        maintenance: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>`,
        settings: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
        scorecardVerif: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>`,
        scorecardRes: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 0h-2m2 0h2"/></svg>`,
        trends: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>`,
        alerts: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>`,
        disputes: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
    };

    const MENU_PEMILIK = [
        { group: 'KENDALI UTAMA', items: [
            { icon: SVG_ICONS.dashboard, label: 'Dasbor', url: '/superadmin/pemilik/dashboard' },
            { icon: SVG_ICONS.accounts, label: 'Akun Staf', url: '/superadmin/pemilik/accounts' },
        ]},
        { group: 'KEAMANAN & AUDIT', items: [
            { icon: SVG_ICONS.security, label: 'Keamanan', url: '/superadmin/pemilik/audit-logs' },
            { icon: SVG_ICONS.audit, label: 'Jejak Audit', url: '/superadmin/pemilik/jejak-audit' },
            { icon: SVG_ICONS.cleaner, label: 'Pembersih Berkas', url: '/superadmin/pemilik/flags' },
        ]},
        { group: 'MANAJEMEN MASTER', items: [
            { icon: SVG_ICONS.services, label: 'Kategori Servis', url: '/superadmin/pemilik/services' },
            { icon: SVG_ICONS.commission, label: 'Kebijakan Komisi', url: '/superadmin/pemilik/commission' },
            { icon: SVG_ICONS.regions, label: 'Wilayah Operasional', url: '/superadmin/pemilik/regions' },
            { icon: SVG_ICONS.verifications, label: 'Verifikasi Mitra', url: '/superadmin/pemilik/verifications' },
        ]},
        { group: 'SISTEM & SERVER', items: [
            { icon: SVG_ICONS.database, label: 'Basis Data', url: '/superadmin/pemilik/backup' },
            { icon: SVG_ICONS.maintenance, label: 'Maintenance Mode', url: '/superadmin/pemilik/maintenance' },
            { icon: SVG_ICONS.settings, label: 'Pengaturan', url: '/superadmin/pemilik/settings' },
        ]},
        { group: 'MONITORING PLATFORM', items: [
            { icon: SVG_ICONS.scorecardVerif, label: 'Scorecard Verifikator', url: '/superadmin/monitoring/scorecard-verifikator' },
            { icon: SVG_ICONS.scorecardRes, label: 'Scorecard Resolusi', url: '/superadmin/monitoring/scorecard-resolusi' },
            { icon: SVG_ICONS.trends, label: 'Tren & Analitik', url: '/superadmin/monitoring/trends' },
            { icon: SVG_ICONS.alerts, label: 'Peringatan SLA', url: '/superadmin/monitoring/alerts' },
            { icon: SVG_ICONS.disputes, label: 'Eskalasi Dispute', url: '/superadmin/pemilik/disputes' },
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
                html += `<a href="${item.url}" onclick="toggleMobileSidebar(false)" class="nav-link${isActive ? ' active' : ''}">${item.icon} <span>${item.label}</span></a>`;
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
