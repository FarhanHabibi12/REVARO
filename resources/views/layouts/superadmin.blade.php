<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin — REVARO')</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/images/revaro-icon-transparent.png">
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
    // Intercept native fetch to handle explicit session expiry / maintenance
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        const response = await originalFetch(...args);
        // If system is in maintenance
        if (response.status === 503) {
            try {
                const data = await response.clone().json();
                if (data.is_maintenance) {
                    Swal.fire({
                        title: 'Pemeliharaan Sistem',
                        text: data.message || 'Sistem sedang dalam mode pemeliharaan oleh Super Admin.',
                        icon: 'info',
                        background: '#1E293B',
                        color: '#E2E8F0',
                        confirmButtonColor: '#F97316'
                    });
                }
            } catch(e) {}
        }
        return response;
    };

    const SVG_ICONS = {
        dashboard: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>`,
        accounts: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`,
        security: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`,
        audit: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
        flag: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>`,
        cleaner: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`,
        services: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
        commission: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        regions: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
        verifications: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        database: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>`,
        maintenance: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>`,
        smtp: `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`,
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
            { icon: SVG_ICONS.flag, label: 'Instruksi & Peringatan', url: '/superadmin/pemilik/flags' },
        ]},
        { group: 'MANAJEMEN MASTER', items: [
            { icon: SVG_ICONS.services, label: 'Kategori Servis', url: '/superadmin/pemilik/services' },
            { icon: SVG_ICONS.commission, label: 'Kebijakan Komisi', url: '/superadmin/pemilik/commission' },
            { icon: SVG_ICONS.regions, label: 'Wilayah Operasional', url: '/superadmin/pemilik/regions' },
            { icon: SVG_ICONS.verifications, label: 'Verifikasi Mitra', url: '/superadmin/pemilik/verifications' },
        ]},
        { group: 'SISTEM & SERVER', items: [
            { icon: SVG_ICONS.database, label: 'Basis Data', url: '/superadmin/pemilik/backup' },
            { icon: SVG_ICONS.regions, label: 'Kunci API Maps (GOAPI)', url: '/superadmin/pemilik/maps-api' },
            { icon: SVG_ICONS.maintenance, label: 'Maintenance Mode', url: '/superadmin/pemilik/maintenance' },
            { icon: SVG_ICONS.smtp, label: 'Setelan SMTP', url: '/superadmin/pemilik/smtp' },
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
        if (typeof Swal !== 'undefined') {
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
                    performLogout();
                }
            });
        } else {
            if (confirm('Keluar dari Portal? Sesi Anda akan diakhiri.')) {
                performLogout();
            }
        }
    }

    function performLogout() {
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

    // Interceptor global agar setiap 401 Unauthorized (misal database di-migrate ulang) otomatis membersihkan sesi basi
    (function setupAuthInterceptor() {
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            try {
                const response = await originalFetch(...args);
                if (response.status === 401) {
                    const url = typeof args[0] === 'string' ? args[0] : (args[0] ? args[0].url : '');
                    if (!url.includes('/api/auth/login') && !url.includes('/api/auth/login-admin')) {
                        console.warn('Sesi tidak valid / kedaluwarsa (401). Mengalihkan ke session-expired...');
                        localStorage.removeItem('admin_token');
                        localStorage.removeItem('admin_user');
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                        localStorage.removeItem('pending_first_time_setup');
                        sessionStorage.clear();
                        window.location.href = '/session-expired';
                    }
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

    function toggleGenericPassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
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

        const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
        const userRaw = localStorage.getItem('admin_user') || localStorage.getItem('user');
        if (!token || !userRaw) { 
            window.location.href = '/login-admin'; 
            return; 
        }

        let user;
        try {
            user = JSON.parse(userRaw);
        } catch(e) {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/login-admin';
            return;
        }

        localStorage.setItem('admin_token', token);
        localStorage.setItem('admin_user', JSON.stringify(user));

        const nameElem = document.getElementById('sidebarUserName');
        if (nameElem) nameElem.textContent = user.nama || '—';

        let role = user.role || '';
        if (typeof role === 'object' && role !== null) {
            role = role.nama || role.name || '';
        }
        const lowerRole = role.toLowerCase();

        if (lowerRole.includes('pemilik') || lowerRole === 'super-admin-pemilik' || lowerRole === 'super admin (pemilik sistem)' || lowerRole === 'super admin pemilik') {
            const roleElem = document.getElementById('sidebarRoleLabel');
            if (roleElem) roleElem.textContent = 'SUPER ADMIN — PEMILIK';
            buildNav(MENU_PEMILIK);

            // Cek jika akun Super Admin pertama kali masuk (belum punya username)
            if (localStorage.getItem('pending_first_time_setup') === 'true' || !user.admin_username) {
                const ftModal = document.getElementById('firstTimeSetupModal');
                if (ftModal) {
                    ftModal.classList.remove('hidden');
                }
            }

            // Verifikasi token aktif ke server
            fetch('/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            }).then(res => {
                if (res.status === 401) {
                    localStorage.removeItem('admin_token');
                    localStorage.removeItem('admin_user');
                    window.location.href = '/session-expired';
                } else if (res.ok) {
                    res.json().then(fresh => {
                        if (fresh && fresh.user) {
                            localStorage.setItem('admin_user', JSON.stringify(fresh.user));
                            if (nameElem) nameElem.textContent = fresh.user.nama || '—';
                        }
                    }).catch(() => {});
                }
            }).catch(() => {});
        } else {
            let homeUrl = '/login-admin';
            if (lowerRole.includes('verifikator')) homeUrl = '/admin/verifikator/dashboard';
            else if (lowerRole.includes('resolusi')) homeUrl = '/admin/resolusi/dashboard';
            else if (lowerRole.includes('monitoring')) homeUrl = '/superadmin/monitoring/scorecard-verifikator';
            else if (lowerRole.includes('teknisi')) homeUrl = '/teknisi/dashboard';
            else if (lowerRole.includes('toko')) homeUrl = '/toko/dashboard';
            else homeUrl = '/pelanggan/dashboard';
            window.location.href = homeUrl;
        }
    });

    async function submitFirstTimeSetup() {
        const uInput = document.getElementById('ftUsernameInput').value.trim();
        const pInput = document.getElementById('ftPasswordInput') ? document.getElementById('ftPasswordInput').value : '';
        const pConfirm = document.getElementById('ftPasswordConfirmInput') ? document.getElementById('ftPasswordConfirmInput').value : '';

        if (!uInput || uInput.length < 3) {
            Swal.fire({
                title: 'Username Wajib Diisi',
                text: 'Silakan masukkan username minimal 3 karakter untuk akun Anda.',
                icon: 'warning',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        if (!/^[a-zA-Z0-9_]+$/.test(uInput)) {
            Swal.fire({
                title: 'Format Tidak Valid',
                text: 'Username hanya boleh terdiri dari huruf, angka, dan underscore (_).',
                icon: 'warning',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        const user = JSON.parse(localStorage.getItem('admin_user') || '{}');
        if (user.must_change_password && (!pInput || pInput.length < 6)) {
            Swal.fire({
                title: 'Password Baru Wajib',
                text: 'Sebagai admin, Anda wajib mengganti password awal dengan password baru Anda (minimal 6 karakter).',
                icon: 'warning',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        if (pInput && pInput !== pConfirm) {
            Swal.fire({
                title: 'Konfirmasi Password Tidak Cocok',
                text: 'Kata sandi baru dan konfirmasi kata sandi tidak sama.',
                icon: 'warning',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        const token = localStorage.getItem('admin_token');
        const submitBtn = document.getElementById('ftSubmitBtn');
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

                document.getElementById('firstTimeSetupModal').classList.add('hidden');

                Swal.fire({
                    title: 'Setup Akun Selesai!',
                    html: `
                        <p class="text-sm text-slate-300">
                            Username <b>${uInput}</b> berhasil diaktifkan.<br>
                            Untuk login selanjutnya, gunakan <b>Email</b>, <b>Username</b>, <b>Password</b>, dan <b>OTP</b>.
                        </p>
                    `,
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
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
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch(e) {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Simpan & Aktifkan Akun';
            Swal.fire({
                title: 'Error',
                text: 'Tidak dapat menghubungi server.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    }
    </script>

    <!-- Modal Setup First Time Super Admin -->
    <div id="firstTimeSetupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
        <div class="w-full max-w-lg bg-slate-900 border border-orange-500/40 rounded-3xl p-7 space-y-5 shadow-2xl relative">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-brandOrange to-amber-500 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-orange-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-black text-white tracking-wide">Pengaturan Awal Akun Super Admin</h3>
                <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                    Selamat datang di REVARO! Sebagai Pemilik Sistem, silakan tentukan Username Anda dan perbarui kata sandi awal jika diinginkan.
                </p>
            </div>

            <div class="space-y-4">
                <!-- Field Input Username -->
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-1.5">
                    <label class="block text-[11px] font-bold text-orange-400 uppercase tracking-wider">
                        1. Tentukan Username Resmi Anda *
                    </label>
                    <input type="text" id="ftUsernameInput" placeholder="cth. srz_senn atau salman_admin"
                           class="w-full text-sm bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 font-mono transition placeholder:text-slate-600">
                    <p class="text-[10px] text-slate-500">
                        Hanya huruf, angka, dan underscore (_). Minimal 3 karakter. Username ini akan diminta saat Anda login ke depannya.
                    </p>
                </div>

                <!-- Field Ganti Password Baru (Opsional / Wajib jika must_change) -->
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-3">
                    <label class="block text-[11px] font-bold text-orange-400 uppercase tracking-wider">
                        2. Ganti Kata Sandi Baru <span class="text-slate-500 text-[10px] font-normal">(opsional jika kata sandi sudah aman)</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="ftPasswordInput" placeholder="Kata Sandi Baru (min. 6 karakter)"
                               class="w-full text-sm bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 pr-10 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-600">
                        <button type="button" onclick="toggleGenericPassword('ftPasswordInput', 'ftEyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="ftEyeIcon1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" id="ftPasswordConfirmInput" placeholder="Konfirmasi Kata Sandi Baru"
                               class="w-full text-sm bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 pr-10 outline-none text-white focus:border-brandOrange focus:ring-2 focus:ring-brandOrange/20 transition placeholder:text-slate-600">
                        <button type="button" onclick="toggleGenericPassword('ftPasswordConfirmInput', 'ftEyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="ftEyeIcon2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Info Setup OTP & Dynamic SMTP -->
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 space-y-2 text-xs text-slate-300">
                    <label class="block text-[11px] font-bold text-orange-400 uppercase tracking-wider">
                        3. Verifikasi OTP (Login Selanjutnya)
                    </label>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Akun Anda dilindungi autentikasi dua faktor (OTP 6-digit). Untuk memastikan kode OTP terkirim ke Gmail Anda:
                    </p>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900 border border-slate-800">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-[11px] font-bold text-white">Dynamic SMTP Email</span>
                        </div>
                        <a href="/superadmin/pemilik/smtp" target="_blank" class="text-[10px] bg-orange-500/20 text-orange-400 hover:bg-orange-500 hover:text-white px-2.5 py-1 rounded-lg font-bold transition">
                            Atur SMTP Gmail &rarr;
                        </a>
                    </div>
                </div>

                <button type="button" id="ftSubmitBtn" onclick="submitFirstTimeSetup()"
                    class="w-full bg-brandOrange hover:bg-orange-600 text-white font-extrabold text-xs py-3.5 rounded-xl transition shadow-lg shadow-orange-600/30 active:scale-[0.99] flex items-center justify-center space-x-2">
                    <span>Simpan &amp; Aktifkan Akun</span>
                </button>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
