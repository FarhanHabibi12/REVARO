@extends('layouts.superadmin')

@section('title', 'Keamanan & Audit Log Platform — REVARO')
@section('page-title', 'Keamanan & Audit Log Platform')
@section('page-subtitle', 'Pemantauan sesi login aktif secara realtime, deteksi OS & peramban akurat, proteksi honeypot, dan pencegahan ancaman siber.')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action & Breadcrumb Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800">
        <div>
            <div class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest flex items-center gap-2 mb-1.5">
                <span>REV</span> / <span>SUPERADMIN</span> / <span class="text-brandOrange">KEAMANAN</span>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Keamanan <span class="italic font-normal text-slate-400">& Audit Log Platform</span></h2>
            <p class="text-xs text-slate-400 mt-1">Pemantauan sesi login aktif secara realtime, deteksi OS & peramban akurat, proteksi honeypot, dan pencegahan ancaman siber.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-2 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Intrusion Trap: Aktif
            </span>
        </div>
    </div>

    <!-- 4 Summary Metric Cards (DesaKita Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Pengguna Aktif Login -->
        <div class="card p-5 border-l-4 border-l-emerald-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">PENGGUNA AKTIF LOGIN</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl font-black text-white" id="statActiveUsers">1</h3>
                <span class="text-[10px] bg-emerald-500/20 text-emerald-400 font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Live
                </span>
            </div>
            <p class="text-[11px] text-slate-400">Sesi Terkoneksi Realtime</p>
        </div>

        <!-- Card 2: Total Insiden Tercatat -->
        <div class="card p-5 border-l-4 border-l-orange-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">TOTAL INSIDEN TERCATAT</p>
            <h3 class="text-3xl font-black text-white" id="statTotalIncidents">0</h3>
            <p class="text-[11px] text-slate-400">Riwayat Akses Terpantau</p>
        </div>

        <!-- Card 3: IP Terblokir -->
        <div class="card p-5 border-l-4 border-l-red-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">IP TERBLOKIR</p>
            <h3 class="text-3xl font-black text-white" id="statBlockedIpCount">0</h3>
            <p class="text-[11px] text-slate-400">Alamat Terisolasi</p>
        </div>

        <!-- Card 4: Status Honeypot Trap -->
        <div class="card p-5 border-l-4 border-l-blue-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">STATUS HONEYPOT TRAP</p>
            <h3 class="text-lg font-extrabold text-emerald-400 leading-tight">AKTIF & MELINDUNGI</h3>
            <p class="text-[11px] text-slate-400">Perlindungan Otomatis</p>
        </div>
    </div>

    <!-- Section 1: Sesi Pengguna Aktif & Kontrol Akses -->
    <div class="card p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-black text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sesi Pengguna Aktif & Kontrol Akses</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Daftar akun yang sedang aktif login di website. Anda dapat memantau jenis perangkat (Windows/Mobile), browser yang digunakan, serta memutus sesi (kick) pengguna kapan saja.</p>
            </div>
            <div class="flex items-center gap-3 self-end sm:self-auto">
                <span id="connectedAccountCount" class="text-xs bg-slate-800 text-slate-300 font-extrabold px-3 py-1.5 rounded-xl border border-slate-700">1 Akun Terhubung</span>
                <button onclick="fetchActiveVisitors()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs px-4 py-2 rounded-xl transition flex items-center gap-2 shadow shadow-orange-600/20">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="w-64">PENGGUNA / AKUN</th>
                        <th class="w-48">PERANGKAT & SISTEM OPERASI</th>
                        <th class="w-48">PERAMBAN (BROWSER)</th>
                        <th>ALAMAT IP & LOKASI AKSES</th>
                        <th class="w-36 text-right">KONTROL KEPUSAN / AKSI</th>
                    </tr>
                </thead>
                <tbody id="visitorsTableBody" class="text-xs divide-y divide-slate-800/60">
                    <!-- Populated dynamically via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Log Insiden & Blokir IP (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Log Insiden & Jejak Audit (Span 2) -->
        <div class="lg:col-span-2 card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-base font-black text-white">Log Insiden & Jejak Audit</h3>
                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest mt-0.5">PEMANTAUAN WAKTU NYATA</p>
                </div>
                <button onclick="purgeLogs()" class="border border-red-500/40 text-red-400 hover:bg-red-500/10 px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Bersihkan Log</span>
                </button>
            </div>

            <div id="incidentLogsList" class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                <!-- Log Cards Rendered Dynamically -->
            </div>
        </div>

        <!-- Right Column: IP Management (Span 1) -->
        <div class="space-y-6">
            
            <!-- Card 1: Blokir IP Manual -->
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-black text-white">Blokir IP Manual</h3>
                
                <form onsubmit="handleBanIpSubmit(event)" class="space-y-3.5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">ALAMAT IP <span class="text-red-400">*</span></label>
                        <input required type="text" id="banIpAddress" class="w-full text-xs bg-slate-900 border border-slate-700/80 rounded-xl p-3 text-white outline-none focus:border-brandOrange transition placeholder:text-slate-600" placeholder="Cth: 192.168.1.100">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">ALASAN PEMBLOKIRAN</label>
                        <input type="text" id="banIpReason" class="w-full text-xs bg-slate-900 border border-slate-700/80 rounded-xl p-3 text-white outline-none focus:border-brandOrange transition placeholder:text-slate-600" placeholder="Cth: Percobaan brute force login">
                    </div>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-3 rounded-xl border border-slate-700 transition shadow">
                        Tambahkan ke Daftar Blokir
                    </button>
                </form>
            </div>

            <!-- Card 2: IP Terblokir List -->
            <div class="card p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <h3 class="text-sm font-black text-white">IP Terblokir (<span id="blockedIpHeaderCount">0</span>)</h3>
                </div>

                <div id="blockedIpList" class="space-y-2.5 max-h-[300px] overflow-y-auto">
                    <!-- Blocked IP list items rendered dynamically -->
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetchActiveVisitors();
        fetchSecurityLogs();
        fetchBlockedIps();

        // Auto refresh every 15 seconds
        setInterval(() => {
            fetchActiveVisitors();
        }, 15000);
    });

    async function fetchActiveVisitors() {
        const token = localStorage.getItem('admin_token');
        try {
            const res = await fetch('/api/admin/security/active-visitors', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            renderActiveVisitors(data);
        } catch (err) {
            console.error(err);
        }
    }

    function parseBrowserInfo(agent) {
        if (!agent) return { name: 'Google Chrome', code: 'chrome' };
        if (agent.includes('Edg')) return { name: 'Microsoft Edge', code: 'edge' };
        if (agent.includes('Firefox')) return { name: 'Mozilla Firefox', code: 'firefox' };
        if (agent.includes('OPR') || agent.includes('Opera')) return { name: 'Opera', code: 'opera' };
        if (agent.includes('Brave')) return { name: 'Brave', code: 'brave' };
        if (agent.includes('Safari') && !agent.includes('Chrome')) return { name: 'Apple Safari', code: 'safari' };
        if (agent.includes('Chrome')) return { name: 'Google Chrome', code: 'chrome' };
        return { name: 'Browser', code: 'generic' };
    }

    function getBrowserBadgeHtml(agent) {
        const b = parseBrowserInfo(agent);
        let logoSvg = '';
        let badgeClass = 'bg-slate-800/80 text-slate-300 border-slate-700';

        if (b.code === 'chrome') {
            badgeClass = 'bg-blue-500/10 text-blue-300 border-blue-500/20';
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 2.98.97 4.29L8 10h11.93C18.6 5.23 15.65 2 12 2z"/><path fill="#FBBC05" d="M2.97 16.29A9.957 9.957 0 0 0 12 22c4.42 0 8.16-2.86 9.47-6.85L15 8.78c-.78 1.94-2.7 3.22-4.86 3.22H4.42l-1.45 4.29z"/><path fill="#34A853" d="M12 7c-2.76 0-5 2.24-5 5 0 .7.15 1.36.42 1.96L2.97 16.29C2.36 14.98 2 13.54 2 12c0-5.52 4.48-10 10-10l5.47 5.47H12z"/><circle cx="12" cy="12" r="4" fill="#FFFFFF"/><circle cx="12" cy="12" r="3" fill="#4285F4"/></svg>`;
        } else if (b.code === 'firefox') {
            badgeClass = 'bg-orange-500/10 text-orange-300 border-orange-500/20';
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24"><path fill="#FF9400" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.5c-2.48 0-4.5-2.02-4.5-4.5S10.52 7.5 13 7.5s4.5 2.02 4.5 4.5-2.02 4.5-4.5 4.5z"/><path fill="#E66000" d="M16.5 12c0 1.93-1.57 3.5-3.5 3.5S9.5 13.93 9.5 12s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5z"/></svg>`;
        } else if (b.code === 'edge') {
            badgeClass = 'bg-sky-500/10 text-sky-300 border-sky-500/20';
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24"><path fill="#0078D7" d="M12 2C6.48 2 2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H7v-3h3V9.5C10 7.57 11.57 6 13.5 6H17v3h-3c-.55 0-1 .45-1 1V12h4l-1 3h-3v6.8c4.56-.93 8-4.96 8-9.8 0-5.52-4.48-10-10-10z"/></svg>`;
        } else if (b.code === 'safari') {
            badgeClass = 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20';
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#0066CC"/><polygon points="12,5 14,11 19,12 14,13 12,19 10,13 5,12 10,11" fill="#FFFFFF"/></svg>`;
        } else if (b.code === 'opera') {
            badgeClass = 'bg-rose-500/10 text-rose-300 border-rose-500/20';
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24"><path fill="#FF1B2D" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>`;
        } else if (b.code === 'brave') {
            badgeClass = 'bg-amber-500/10 text-amber-300 border-amber-500/20';
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24"><polygon points="12,2 22,7 19,18 12,22 5,18 2,7" fill="#FF5000"/><circle cx="12" cy="12" r="4" fill="#FFFFFF"/></svg>`;
        } else {
            logoSvg = `<svg class="w-4 h-4 flex-shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`;
        }

        return `<span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-[11px] font-bold border ${badgeClass}">
            ${logoSvg} <span>${b.name}</span>
        </span>`;
    }

    function getOSBadgeHtml(agent) {
        let osName = 'Windows 10 / 11';
        let osIcon = `<svg class="w-4 h-4 text-sky-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-13.051-1.8"/></svg>`;
        let subtitle = 'Desktop (PC/Laptop)';

        if (agent) {
            if (agent.includes('Windows')) {
                osName = 'Windows 10 / 11';
            } else if (agent.includes('Macintosh') || agent.includes('Mac OS')) {
                osName = 'macOS';
                subtitle = 'Apple Desktop / Mac';
                osIcon = `<svg class="w-4 h-4 text-slate-200 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.35c.66-.8 1.11-1.92.99-3.04-.96.04-2.13.64-2.82 1.44-.61.71-1.14 1.86-1 2.97 1.08.08 2.17-.57 2.83-1.37z"/></svg>`;
            } else if (agent.includes('Android')) {
                osName = 'Android OS';
                subtitle = 'Mobile Smartphone';
                osIcon = `<svg class="w-4 h-4 text-emerald-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18c0 .55.45 1 1 1h1v3c0 .55.45 1 1 1s1-.45 1-1v-3h4v3c0 .55.45 1 1 1s1-.45 1-1v-3h1c.55 0 1-.45 1-1V8H6v10zM3.5 8C2.67 8 2 8.67 2 9.5v7c0 .83.67 1.5 1.5 1.5S5 17.33 5 16.5v-7C5 8.67 4.33 8 3.5 8zm17 0c-.83 0-1.5.67-1.5 1.5v7c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5v-7c0-.83-.67-1.5-1.5-1.5zM15.53 2.16l1.3-1.3c.2-.2.2-.51 0-.71-.2-.2-.51-.2-.71 0l-1.48 1.48C13.85 1.23 12.95 1 12 1c-.95 0-1.85.23-2.64.63L7.88.15c-.2-.2-.51-.2-.71 0-.2.2-.2.51 0 .71l1.3 1.3C6.84 3.39 5.5 5.54 5.5 8h13c0-2.46-1.34-4.61-2.97-5.84zM9 5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>`;
            } else if (agent.includes('iPhone') || agent.includes('iPad')) {
                osName = 'Apple iOS';
                subtitle = 'Mobile iPhone / iPad';
                osIcon = `<svg class="w-4 h-4 text-slate-200 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>`;
            } else if (agent.includes('Linux')) {
                osName = 'Linux OS';
                subtitle = 'Desktop / Workstation';
                osIcon = `<svg class="w-4 h-4 text-amber-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12.002 0c-2.42 0-4.382 1.96-4.382 4.38 0 .8.22 1.55.6 2.19-.07.41-.12.83-.12 1.26 0 1.92.83 3.65 2.15 4.86-.4.7-.65 1.5-.65 2.36 0 2.21 1.79 4 4 4s4-1.79 4-4c0-.86-.25-1.66-.65-2.36 1.32-1.21 2.15-2.94 2.15-4.86 0-.43-.05-.85-.12-1.26.38-.64.6-1.39.6-2.19C16.384 1.96 14.422 0 12.002 0z"/></svg>`;
            }
        }

        return `<div class="flex items-center gap-2.5">
            ${osIcon}
            <div>
                <div class="text-xs text-white font-bold">${osName}</div>
                <div class="text-[9px] text-slate-400 font-medium">${subtitle}</div>
            </div>
        </div>`;
    }

    function formatWibTime(dateStr) {
        if (!dateStr) return 'Aktif';
        try {
            if (typeof dateStr === 'string' && dateStr.includes(' ')) {
                const parts = dateStr.split(' ');
                if (parts[1]) return parts[1].substring(0, 8) + ' WIB';
            }
            const d = new Date(dateStr);
            if (!isNaN(d.getTime())) {
                return d.toLocaleTimeString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).replace(/\./g, ':') + ' WIB';
            }
            return dateStr.substring(11, 19) + ' WIB';
        } catch(e) {
            return (dateStr.substring(11, 19) || 'Aktif') + ' WIB';
        }
    }

    function formatWibDateTime(dateStr) {
        if (!dateStr) return 'Baru saja';
        try {
            const d = new Date(dateStr);
            if (!isNaN(d.getTime())) {
                return d.toLocaleString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).replace(/\./g, ':');
            }
            return dateStr.substring(0, 19).replace('T', ' ');
        } catch(e) {
            return dateStr.substring(0, 19).replace('T', ' ');
        }
    }

    function renderActiveVisitors(visitors) {
        const tbody = document.getElementById('visitorsTableBody');
        const countSpan = document.getElementById('connectedAccountCount');
        const statActive = document.getElementById('statActiveUsers');

        statActive.innerText = visitors.length;
        countSpan.innerText = `${visitors.length} Akun Terhubung`;

        if (!visitors || visitors.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-slate-500 font-medium">Tidak ada pengguna aktif.</td></tr>`;
            return;
        }

        const currentUser = JSON.parse(localStorage.getItem('admin_user') || '{}');

        tbody.innerHTML = visitors.map(v => {
            const osBadge = getOSBadgeHtml(v.user_agent);
            const browserBadge = getBrowserBadgeHtml(v.user_agent);
            const isSelf = currentUser && (currentUser.id === v.user_id || currentUser.email === v.email);
            
            let roleBadgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
            if (v.role?.includes('SUPER ADMIN')) roleBadgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
            else if (v.role?.includes('TEKNISI')) roleBadgeClass = 'bg-blue-500/20 text-blue-300 border-blue-500/30';
            else if (v.role?.includes('PELANGGAN')) roleBadgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';

            return `
                <tr class="hover:bg-slate-800/40 transition">
                    <td class="py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center font-black text-white text-sm">
                                ${v.nama ? v.nama.charAt(0).toUpperCase() : 'S'}
                            </div>
                            <div>
                                <div class="font-extrabold text-white text-xs flex items-center gap-1.5">
                                    <span>${v.nama || 'Super Admin Platform'}</span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-md font-extrabold border ${roleBadgeClass}">${v.role || 'SUPER ADMIN PLATFORM'}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">${v.user?.email || v.email || 'superadmin@revaro.com'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5">
                        ${osBadge}
                    </td>
                    <td class="py-3.5">
                        ${browserBadge}
                    </td>
                    <td class="py-3.5">
                        <div class="font-mono text-xs text-white font-bold">${v.ip_address || '127.0.0.1'}</div>
                        <div class="text-[10px] text-slate-400 flex items-center gap-2 mt-0.5">
                            <span>${v.endpoint || '/superadmin/keamanan'}</span>
                            <span class="text-slate-600">•</span>
                            <span class="text-emerald-400 font-bold">${formatWibTime(v.last_seen_at)}</span>
                        </div>
                    </td>
                    <td class="py-3.5 text-right">
                        ${isSelf ? `
                            <span class="inline-flex items-center gap-1.5 text-[10px] bg-emerald-500/10 text-emerald-400 font-bold px-3 py-1 rounded-xl border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Sesi Anda (Aktif)
                            </span>
                        ` : `
                            <button onclick="kickUserSession(${v.user_id})" class="bg-red-500/15 hover:bg-red-500/30 text-red-400 font-extrabold text-[11px] px-3.5 py-1.5 rounded-xl border border-red-500/30 transition">
                                Putus Sesi (Kick)
                            </button>
                        `}
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function kickUserSession(userId) {
        const token = localStorage.getItem('admin_token');
        const confirm = await Swal.fire({
            title: 'Putus Sesi Pengguna?',
            text: 'Pengguna akan secara paksa dikeluarkan dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Putus Sesi',
            cancelButtonText: 'Batal',
            background: '#0F172A',
            color: '#E2E8F0',
            confirmButtonColor: '#EF4444'
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fetch('/api/admin/security/sessions/kick', {
                method: 'POST',
                headers: { 
                    'Authorization': 'Bearer ' + token, 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ user_id: userId })
            });
            const data = await res.json();
            if (res.ok) {
                Swal.fire({ title: 'Berhasil', text: data.message, icon: 'success', timer: 1500, showConfirmButton: false, background: '#0F172A', color: '#E2E8F0' });
                fetchActiveVisitors();
                fetchSecurityLogs();
            } else {
                Swal.fire({ title: 'Gagal', text: data.message || 'Gagal memutus sesi', icon: 'error', background: '#0F172A', color: '#E2E8F0' });
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function fetchSecurityLogs() {
        const token = localStorage.getItem('admin_token');
        try {
            const res = await fetch('/api/admin/security/logs', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            renderSecurityLogs(data);
        } catch (err) {
            console.error(err);
        }
    }

    function renderSecurityLogs(logs) {
        const container = document.getElementById('incidentLogsList');
        const statIncidents = document.getElementById('statTotalIncidents');
        statIncidents.innerText = logs.length;

        if (!logs || logs.length === 0) {
            container.innerHTML = `<div class="text-center py-8 text-slate-500 font-medium text-xs">Belum ada log insiden keamanan tercatat.</div>`;
            return;
        }

        container.innerHTML = logs.map(l => `
            <div class="bg-slate-900/90 border border-slate-800 p-4 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-3 hover:border-slate-700 transition">
                <div class="flex items-start gap-3">
                    <span class="font-mono text-xs font-bold text-slate-300 bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-700">${l.ip_address || '127.0.0.1'}</span>
                    <div>
                        <div class="text-xs font-black text-white">${l.event_type || 'Pemutusan Sesi Paksa (Kick Session)'}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-2">
                            <span class="bg-slate-800 text-amber-400 font-bold px-1.5 py-0.5 rounded uppercase text-[9px]">${l.method || 'POST'}</span>
                            <span>${l.url || 'https://revaro.my.id/superadmin/keamanan'}</span>
                        </div>
                        <div class="text-[11px] text-slate-300 mt-1 font-medium text-emerald-400">
                            • ${l.details || l.aktivitas || 'Aktivitas keamanan tercatat'}
                        </div>
                    </div>
                </div>
                <div class="text-[10px] text-slate-500 font-mono self-end md:self-auto">
                    ${formatWibDateTime(l.created_at)}
                </div>
            </div>
        `).join('');
    }

    async function purgeLogs() {
        const token = localStorage.getItem('admin_token');
        const confirm = await Swal.fire({
            title: 'Bersihkan Log Insiden?',
            text: 'Semua catatan jejak insiden keamanan akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bersihkan',
            cancelButtonText: 'Batal',
            background: '#0F172A',
            color: '#E2E8F0',
            confirmButtonColor: '#EF4444'
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fetch('/api/admin/security/logs/purge', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (res.ok) {
                Swal.fire({ title: 'Berhasil', text: 'Log insiden berhasil dibersihkan.', icon: 'success', timer: 1400, showConfirmButton: false, background: '#0F172A', color: '#E2E8F0' });
                fetchSecurityLogs();
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function fetchBlockedIps() {
        const token = localStorage.getItem('admin_token');
        try {
            const res = await fetch('/api/admin/security/blacklist', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            renderBlockedIps(data);
        } catch (err) {
            console.error(err);
        }
    }

    function renderBlockedIps(list) {
        const container = document.getElementById('blockedIpList');
        const countHeader = document.getElementById('blockedIpHeaderCount');
        const statBlocked = document.getElementById('statBlockedIpCount');

        const count = list ? list.length : 0;
        countHeader.innerText = count;
        statBlocked.innerText = count;

        if (!list || list.length === 0) {
            container.innerHTML = `<div class="text-center py-6 text-slate-500 text-xs font-medium">Tidak ada IP yang sedang diblokir.</div>`;
            return;
        }

        container.innerHTML = list.map(item => `
            <div class="bg-slate-900 border border-slate-800 p-3 rounded-xl flex items-center justify-between text-xs">
                <div>
                    <div class="font-mono font-bold text-white">${item.ip_address}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">${item.alasan || 'Percobaan mencurigakan'}</div>
                </div>
                <button onclick="unbanIp(${item.id})" class="text-[10px] text-red-400 hover:text-red-300 font-bold bg-red-500/10 hover:bg-red-500/20 px-2.5 py-1 rounded-lg transition">
                    Buka Blokir
                </button>
            </div>
        `).join('');
    }

    async function handleBanIpSubmit(e) {
        e.preventDefault();
        const token = localStorage.getItem('admin_token');
        const ip_address = document.getElementById('banIpAddress').value;
        const alasan = document.getElementById('banIpReason').value;

        try {
            const res = await fetch('/api/admin/security/blacklist', {
                method: 'POST',
                headers: { 
                    'Authorization': 'Bearer ' + token, 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ ip_address, alasan })
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire({ title: 'Berhasil', text: 'IP address berhasil diblokir.', icon: 'success', timer: 1400, showConfirmButton: false, background: '#0F172A', color: '#E2E8F0' });
                document.getElementById('banIpAddress').value = '';
                document.getElementById('banIpReason').value = '';
                fetchBlockedIps();
            } else {
                Swal.fire({ title: 'Gagal', text: data.message || 'Gagal memblokir IP', icon: 'error', background: '#0F172A', color: '#E2E8F0' });
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function unbanIp(id) {
        const token = localStorage.getItem('admin_token');
        try {
            const res = await fetch(`/api/admin/security/blacklist/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (res.ok) {
                Swal.fire({ title: 'Berhasil', text: 'IP address berhasil dibuka blokirnya.', icon: 'success', timer: 1400, showConfirmButton: false, background: '#0F172A', color: '#E2E8F0' });
                fetchBlockedIps();
            }
        } catch (err) {
            console.error(err);
        }
    }
</script>
@endsection
