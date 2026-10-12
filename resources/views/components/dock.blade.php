<!-- REVARO Responsive Brutalist Dock Navbar Component -->
<style>
  /* ===== Revaro Responsive Dock ===== */
  .revaro-dock-outer {
    position: fixed !important;
    z-index: 999999 !important;
    display: flex !important;
    transition: all 0.25s ease;
  }

  /* Desktop / Laptop: Vertical on Left (All Roles) */
  @media (min-width: 768px) {
    .revaro-dock-outer {
      top: 50% !important;
      left: 18px !important;
      transform: translateY(-50%) !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: flex-start !important;
      bottom: auto !important;
      right: auto !important;
      pointer-events: none !important;
    }
    .revaro-dock-panel {
      flex-direction: column !important;
      align-items: center !important;
      padding: 14px 10px !important;
      gap: 14px !important;
      height: fit-content !important;
      margin-left: 0 !important;
      pointer-events: auto !important;
      display: flex;
      background: #ffffff;
      border: 2px solid #1e4d9b;
      border-radius: 16px;
      box-shadow: 4px 4px 0 #1e4d9b;
    }
    .revaro-dock-item {
      position: relative !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 46px !important;
      height: 46px !important;
      background: #ffffff;
      border: 2px solid #1e4d9b;
      border-radius: 12px;
      box-shadow: 3px 3px 0 #1e4d9b;
      color: #1e4d9b;
      cursor: pointer !important;
      pointer-events: auto !important;
      outline: none;
      flex-shrink: 0;
      text-decoration: none;
      transition: transform 0.15s ease, box-shadow 0.15s ease, color 0.15s ease, border-color 0.15s ease, background 0.15s ease;
    }
    .revaro-dock-mobile-text {
      display: none !important; /* Hide direct text on desktop */
    }
    .revaro-dock-label {
      left: calc(100% + 12px) !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      position: absolute;
      white-space: nowrap;
      pointer-events: none !important;
      background: #1e4d9b;
      color: #ffffff;
      font-weight: 800;
      font-size: 11px;
      letter-spacing: 0.02em;
      padding: 4px 10px;
      border-radius: 8px;
      opacity: 0;
      box-shadow: 2px 2px 0 rgba(0, 0, 0, 0.2);
      transition: opacity 0.15s ease, transform 0.15s ease;
      z-index: 1000000 !important;
    }
    @media (hover: hover) {
      .revaro-dock-item:hover {
        transform: scale(1.15) translateX(2px) !important;
        box-shadow: 2px 2px 0 #FF7F00 !important;
        border-color: #FF7F00 !important;
        color: #FF7F00 !important;
      }
      .revaro-dock-item:hover .revaro-dock-label {
        opacity: 1 !important;
        transform: translateY(-50%) translateX(2px) !important;
      }
    }
    .revaro-dock-item.active {
      background: #0B4A8F !important;
      color: #ffffff !important;
      border-color: #0B4A8F !important;
      box-shadow: 3px 3px 0 #FF7F00 !important;
    }
  }

  /* Crucial: SVGs must not capture pointer events */
  .revaro-dock-item svg,
  .revaro-dock-item svg * {
    pointer-events: none !important;
    width: 22px;
    height: 22px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  /* ===== Mobile / Phone: Khusus Pelanggan, Teknisi, Toko (Ada Tulisan Langsung, Bukan Hover) ===== */
  @media (max-width: 767px) {
    /* Mode Bottom Bar dengan Tulisan Langsung untuk Pelanggan, Teknisi, Toko */
    .revaro-dock-outer.mobile-bottom-bar {
      left: 0 !important;
      right: 0 !important;
      bottom: 10px !important;
      top: auto !important;
      width: 100% !important;
      transform: none !important;
      pointer-events: none !important;
      z-index: 999999 !important;
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      display: flex !important;
      justify-content: center !important;
      align-items: flex-end !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-panel {
      pointer-events: auto !important;
      width: fit-content !important;
      max-width: calc(100vw - 20px) !important;
      border: 2.5px solid #1e4d9b !important;
      box-shadow: 4px 4px 0 #1e4d9b !important;
      border-radius: 18px !important;
      background: #ffffff !important;
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 6px 10px !important;
      gap: 8px !important;
      margin: 0 auto !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item {
      position: relative !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      min-width: 54px !important;
      padding: 6px 8px !important;
      background: #ffffff !important;
      border: 2px solid transparent !important;
      border-radius: 12px !important;
      color: #1e4d9b !important;
      text-decoration: none !important;
      transform: none !important;
      transition: all 0.15s ease !important;
      cursor: pointer !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item svg {
      width: 22px !important;
      height: 22px !important;
      margin-bottom: 2px !important;
      stroke: currentColor !important;
      stroke-width: 2.2 !important;
    }
    /* TULISAN NAVBAR KHUSUS HP (BUKAN HOVER) */
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-mobile-text {
      display: block !important;
      font-size: 10px !important;
      font-weight: 800 !important;
      letter-spacing: -0.01em !important;
      line-height: 1.1 !important;
      text-align: center !important;
      color: inherit !important;
      white-space: nowrap !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-label {
      display: none !important; /* Matikan tooltip melayang di HP */
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.active {
      color: #ffffff !important;
      background: #0B4A8F !important;
      border: 2px solid #1e4d9b !important;
      box-shadow: 2px 2px 0 #1e4d9b !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.active svg {
      stroke: #ffffff !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.active .revaro-dock-mobile-text {
      color: #ffffff !important;
      font-weight: 900 !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.is-pesan {
      color: #FF7F00 !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.is-pesan.active {
      background: #FF7F00 !important;
      color: #ffffff !important;
      border-color: #D96800 !important;
      box-shadow: 2px 2px 0 #D96800 !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.is-pesan.active svg {
      stroke: #ffffff !important;
    }
    .revaro-dock-outer.mobile-bottom-bar .revaro-dock-item.is-pesan.active .revaro-dock-mobile-text {
      color: #ffffff !important;
    }

    /* Mode Standar / Default untuk SuperAdmin dan Admin (Tetap Melayang Ikon Kecil) */
    .revaro-dock-outer:not(.mobile-bottom-bar) {
      left: 0 !important;
      right: 0 !important;
      bottom: 12px !important;
      top: auto !important;
      transform: none !important;
      justify-content: center !important;
      align-items: flex-end !important;
      pointer-events: none !important;
    }
    .revaro-dock-outer:not(.mobile-bottom-bar) .revaro-dock-panel {
      flex-direction: row !important;
      align-items: center !important;
      padding: 8px 14px !important;
      gap: 12px !important;
      width: fit-content !important;
      max-width: calc(100vw - 24px) !important;
      overflow-x: auto !important;
      border: 2px solid #1e4d9b !important;
      border-radius: 16px !important;
      box-shadow: 4px 4px 0 #1e4d9b !important;
      background: #ffffff !important;
      pointer-events: auto !important;
      display: flex;
    }
    .revaro-dock-outer:not(.mobile-bottom-bar) .revaro-dock-item {
      width: 44px !important;
      height: 44px !important;
      background: #ffffff !important;
      border: 2px solid #1e4d9b !important;
      border-radius: 12px !important;
      box-shadow: 2px 2px 0 #1e4d9b !important;
      color: #1e4d9b !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
    }
    .revaro-dock-outer:not(.mobile-bottom-bar) .revaro-dock-mobile-text {
      display: none !important;
    }
    .revaro-dock-outer:not(.mobile-bottom-bar) .revaro-dock-label {
      display: none !important;
    }
    .revaro-dock-outer:not(.mobile-bottom-bar) .revaro-dock-item.active {
      background: #0B4A8F !important;
      color: #ffffff !important;
      box-shadow: 2px 2px 0 #FF7F00 !important;
    }
  }
</style>

<div class="revaro-dock-outer" id="revaroDockOuter" role="navigation" aria-label="Quick Dock Navigation">
  <div class="revaro-dock-panel" id="revaroDockPanel">
    <!-- Items dynamically injected or rendered based on active role -->
  </div>
</div>

<script>
(function() {
  function renderRevaroDock() {
    const dockOuter = document.getElementById('revaroDockOuter');
    const dockPanel = document.getElementById('revaroDockPanel');
    if (!dockPanel || !dockOuter) return;

    const currentPath = window.location.pathname;
    let role = 'pelanggan';

    // Auto-detect role from path or storage
    if (currentPath.includes('/toko')) role = 'toko';
    else if (currentPath.includes('/teknisi')) role = 'teknisi';
    else if (currentPath.includes('/admin/resolusi')) role = 'admin-resolusi';
    else if (currentPath.includes('/admin/verifikator')) role = 'admin-verifikator';
    else if (currentPath.includes('/superadmin')) role = 'superadmin';
    else if (currentPath.includes('/pelanggan')) role = 'pelanggan';
    else {
      try {
        const u = JSON.parse(localStorage.getItem('user') || '{}');
        const r = u.role ? (u.role.nama || u.role) : '';
        if (r.toLowerCase().includes('toko')) role = 'toko';
        else if (r.toLowerCase().includes('teknisi')) role = 'teknisi';
        else if (r.toLowerCase().includes('resolusi')) role = 'admin-resolusi';
        else if (r.toLowerCase().includes('verifikator')) role = 'admin-verifikator';
      } catch(e) {}
    }

    // Role non-admin mendapatkan bottom bar dengan teks di HP (sesuai instruksi user)
    const isUserRole = (role === 'pelanggan' || role === 'teknisi' || role === 'toko');
    if (isUserRole) {
      dockOuter.classList.add('mobile-bottom-bar');
    } else {
      dockOuter.classList.remove('mobile-bottom-bar');
    }

    const svgIcons = {
      home: '<svg viewBox="0 0 24 24"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>',
      pesanServis: '<svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
      katalog: '<svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
      keuangan: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><line x1="12" y1="6" x2="12" y2="18"/></svg>',
      riwayat: '<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
      wallet: '<svg viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M22 10H18a2 2 0 0 0-2 2v0a2 2 0 0 0 2 2h4"/><circle cx="18" cy="12" r="1"/></svg>',
      settings: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9L7 7M17 17l2.1 2.1M19.1 4.9L17 7M7 17l-2.1 2.1"/></svg>',
      partner: '<svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>',
      dispute: '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="4" rx="1"/><path d="M5 8v12h14V8"/><path d="M10 12h4"/></svg>',
      evaluation: '<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
      history: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
      staff: '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
    };

    let items = [];

    if (role === 'pelanggan') {
      items = [
        { icon: svgIcons.home, label: 'Beranda SOS', mobileLabel: 'Beranda', url: '/pelanggan/dashboard' },
        { 
          icon: svgIcons.pesanServis, 
          label: 'Pesan Servis', 
          mobileLabel: 'Pesan',
          url: '/pelanggan/dashboard?action=pesan',
          isPesan: true
        },
        { icon: svgIcons.riwayat, label: 'Riwayat Servis', mobileLabel: 'Riwayat', url: '/pelanggan/riwayat' },
        { icon: svgIcons.wallet, label: 'Dompet Saldo', mobileLabel: 'Dompet', url: '/pelanggan/dompet' },
        { icon: svgIcons.settings, label: 'Pengaturan Profil', mobileLabel: 'Profil', url: '/pelanggan/settings' }
      ];
    } else if (role === 'teknisi') {
      items = [
        { icon: svgIcons.home, label: 'Beranda & SOS', mobileLabel: 'Beranda', url: '/teknisi/dashboard' },
        { icon: svgIcons.riwayat, label: 'Riwayat Kerja', mobileLabel: 'Riwayat', url: '/teknisi/riwayat' },
        { icon: svgIcons.wallet, label: 'Dompet Saldo', mobileLabel: 'Dompet', url: '/teknisi/dompet' },
        { icon: svgIcons.settings, label: 'Pengaturan Akun', mobileLabel: 'Profil', url: '/teknisi/settings' }
      ];
    } else if (role === 'toko') {
      items = [
        { icon: svgIcons.katalog, label: 'Katalog Sparepart', mobileLabel: 'Katalog', url: '/toko/dashboard' },
        { icon: svgIcons.keuangan, label: 'Pembukuan Keuangan', mobileLabel: 'Keuangan', url: '/toko/keuangan' },
        { icon: svgIcons.settings, label: 'Pengaturan Toko', mobileLabel: 'Profil', url: '/toko/settings' }
      ];
    } else if (role === 'admin-resolusi') {
      items = [
        { icon: svgIcons.dispute, label: 'Sengketa Transaksi', mobileLabel: 'Sengketa', url: '/admin/resolusi/dashboard' },
        { icon: svgIcons.evaluation, label: 'Evaluasi Rating & Disiplin', mobileLabel: 'Evaluasi', url: '/admin/resolusi/evaluasi-rating' },
        { icon: svgIcons.history, label: 'Riwayat Keputusan', mobileLabel: 'Riwayat', url: '/admin/resolusi/history' },
        { icon: svgIcons.settings, label: 'Pengaturan Profil', mobileLabel: 'Profil', url: '/admin/resolusi/settings' }
      ];
    } else if (role === 'admin-verifikator') {
      items = [
        { icon: svgIcons.dispute, label: 'Antrian Pending', mobileLabel: 'Verifikasi', url: '/admin/verifikator/dashboard' },
        { icon: svgIcons.history, label: 'Riwayat Keputusan', mobileLabel: 'Riwayat', url: '/admin/verifikator/history' },
        { icon: svgIcons.settings, label: 'Pengaturan Profil', mobileLabel: 'Profil', url: '/admin/verifikator/settings' }
      ];
    } else if (role === 'superadmin') {
      items = [
        { icon: svgIcons.home, label: 'Konsol Pemilik', mobileLabel: 'Konsol', url: '/superadmin/pemilik/dashboard' },
        { icon: svgIcons.staff, label: 'Manajemen Staf', mobileLabel: 'Staf', url: '/superadmin/pemilik/accounts' },
        { icon: svgIcons.settings, label: 'Pengaturan Sistem', mobileLabel: 'Sistem', url: '/superadmin/pemilik/settings' }
      ];
    }

    dockPanel.innerHTML = items.map(item => {
      const cleanItemUrl = item.url.split('?')[0];
      const isPathActive = currentPath === cleanItemUrl && !item.isPesan;
      const pesanClass = item.isPesan ? 'is-pesan text-secondary' : '';

      return `
        <a href="${item.url}" class="revaro-dock-item ${isPathActive ? 'active' : ''} ${pesanClass}" title="${item.label}">
          ${item.icon}
          <span class="revaro-dock-mobile-text">${item.mobileLabel || item.label}</span>
          <span class="revaro-dock-label ${item.isPesan ? 'bg-secondary' : ''}">${item.label}</span>
        </a>
      `;
    }).join('');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderRevaroDock);
  } else {
    renderRevaroDock();
  }
})();

// Universal 401 Session Interceptor & Auto-Redirect to /session-expired
(function() {
  if (window.__revaroSessionInterceptorInstalled) return;
  window.__revaroSessionInterceptorInstalled = true;

  const originalFetch = window.fetch;
  window.fetch = async function(...args) {
    const response = await originalFetch.apply(this, args);
    if (response.status === 401) {
      const url = typeof args[0] === 'string' ? args[0] : (args[0]?.url || '');
      const isAuthAction = url.includes('/api/auth/login') ||
                           url.includes('/api/auth/verify-otp') ||
                           url.includes('/api/auth/register') ||
                           url.includes('/api/partner/register');
      if (!isAuthAction) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        localStorage.removeItem('profile');
        sessionStorage.clear();
        document.cookie = "auth_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
        window.location.href = '/session-expired';
      }
    }
    if (response.status === 429) {
      const url = typeof args[0] === 'string' ? args[0] : (args[0]?.url || '');
      const isAuthAction = url.includes('/api/auth/login') ||
                           url.includes('/api/auth/verify-otp') ||
                           url.includes('/api/auth/register');
      if (isAuthAction) {
        window.location.href = '/error/429';
      } else {
        console.warn('[REVARO] Rate limit (429) pada background request:', url);
      }
    }
    return response;
  };
})();
</script>
