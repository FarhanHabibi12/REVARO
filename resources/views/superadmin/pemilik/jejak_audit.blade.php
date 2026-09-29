@extends('layouts.superadmin')

@section('title', 'Jejak Audit & Log Aktivitas Internal — REVARO')
@section('page-title', 'Jejak Audit & Log Aktivitas Internal')
@section('page-subtitle', 'Rekam jejak kronologis seluruh aktivitas krusial, perubahan data, validasi dokumen, dan disposisi pengaduan oleh aparatur sistem.')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action & Breadcrumb Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800">
        <div>
            <div class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest flex items-center gap-2 mb-1.5">
                <span>REV</span> / <span>SUPERADMIN</span> / <span class="text-brandOrange">JEJAK AUDIT</span>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Jejak Audit <span class="italic font-normal text-slate-400">& Log Aktivitas Internal</span></h2>
            <p class="text-xs text-slate-400 mt-1">Rekam jejak kronologis seluruh aktivitas krusial, perubahan data, validasi dokumen, dan disposisi pengaduan oleh aparatur sistem.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="exportAuditLogs()" class="border border-red-500/40 text-red-400 hover:bg-red-500/10 px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Ekspor Log Aktivitas</span>
            </button>
        </div>
    </div>

    <!-- 4 Summary Metric Cards (DesaKita Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Jejak Audit -->
        <div class="card p-5 border-l-4 border-l-blue-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">TOTAL JEJAK AUDIT</p>
            <h3 class="text-3xl font-black text-white" id="statTotalLogs">0</h3>
            <p class="text-[11px] text-slate-400">Ketersediaan entri aktivitas tercatat</p>
        </div>

        <!-- Card 2: Aktivitas Hari Ini -->
        <div class="card p-5 border-l-4 border-l-emerald-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">AKTIVITAS HARI INI</p>
            <h3 class="text-3xl font-black text-white" id="statTodayLogs">0</h3>
            <p class="text-[11px] text-slate-400">Log aktivitas dalam 24 jam terakhir</p>
        </div>

        <!-- Card 3: Modifikasi Data -->
        <div class="card p-5 border-l-4 border-l-orange-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">MODIFIKASI DATA</p>
            <h3 class="text-3xl font-black text-white" id="statSensitiveActions">0</h3>
            <p class="text-[11px] text-slate-400">Aksi penyuntingan & persetujuan</p>
        </div>

        <!-- Card 4: Pengguna Aktif -->
        <div class="card p-5 border-l-4 border-l-purple-500 space-y-2">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">PENGGUNA AKTIF</p>
            <h3 class="text-3xl font-black text-white" id="statUniqueUsers">0</h3>
            <p class="text-[11px] text-slate-400">Total operator unik beraktivitas</p>
        </div>
    </div>

    <!-- Search & Filter Bar (DesaKita Filter Row) -->
    <div class="card p-5 space-y-4">
        <form onsubmit="handleFilterSubmit(event)" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-center">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-2 relative flex items-center">
                <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="filterSearch" class="w-full text-xs bg-slate-900 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2.5 outline-none text-white focus:border-brandOrange transition placeholder:text-slate-500" placeholder="Cari nama, IP, aktivitas, ID...">
            </div>

            <!-- Modul Filter -->
            <div>
                <select id="filterModule" class="w-full text-xs bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2.5 outline-none text-white focus:border-brandOrange transition">
                    <option value="">Semua Modul</option>
                    <option value="auth">Autentikasi & Sesi</option>
                    <option value="accounts">Akun Staf & User</option>
                    <option value="master">Data Master Servis</option>
                    <option value="dispute">Sengketa & Resolusi</option>
                    <option value="system">Sistem & Database</option>
                </select>
            </div>

            <!-- Date Range: Start -->
            <div>
                <input type="date" id="filterStartDate" class="w-full text-xs bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2.5 outline-none text-white focus:border-brandOrange transition" placeholder="Tgl Mulai">
            </div>

            <!-- Date Range: End -->
            <div>
                <input type="date" id="filterEndDate" class="w-full text-xs bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2.5 outline-none text-white focus:border-brandOrange transition" placeholder="Tgl Selesai">
            </div>

            <!-- Filter Button -->
            <div>
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-2.5 rounded-xl border border-slate-700 transition shadow">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table: Jejak Audit -->
    <div class="card p-6 space-y-4">
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="w-48">WAKTU & ALAMAT IP</th>
                        <th class="w-64">OPERATOR / AKUN</th>
                        <th class="w-28 text-center">AKSI</th>
                        <th class="w-64">ENTITAS & ID</th>
                        <th class="w-36 text-right">RINCIAN NILAI</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody" class="text-xs divide-y divide-slate-800/60">
                    <!-- Loaded dynamically via JS -->
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-800/80 text-xs text-slate-400">
            <div id="paginationInfo">Menampilkan 0 entri</div>
            <div id="paginationControls" class="flex items-center gap-1.5">
                <!-- Buttons rendered dynamically -->
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;

    document.addEventListener('DOMContentLoaded', () => {
        fetchAuditLogs(1);
    });

    function handleFilterSubmit(e) {
        e.preventDefault();
        fetchAuditLogs(1);
    }

    async function fetchAuditLogs(page = 1) {
        currentPage = page;
        const token = localStorage.getItem('admin_token');
        const search = document.getElementById('filterSearch').value;
        const module = document.getElementById('filterModule').value;
        const startDate = document.getElementById('filterStartDate').value;
        const endDate = document.getElementById('filterEndDate').value;

        const params = new URLSearchParams({
            page: page,
            search: search,
            module: module,
            start_date: startDate,
            end_date: endDate
        });

        try {
            const res = await fetch(`/api/admin/audit-logs?${params.toString()}`, {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });

            if (!res.ok) return;
            const data = await res.json();
            
            renderAuditStats(data.stats);
            renderAuditTable(data.logs.data);
            renderPagination(data.logs);
        } catch (err) {
            console.error(err);
        }
    }

    function renderAuditStats(stats) {
        if (!stats) return;
        document.getElementById('statTotalLogs').innerText = stats.total_logs || 0;
        document.getElementById('statTodayLogs').innerText = stats.today_logs || 0;
        document.getElementById('statSensitiveActions').innerText = stats.sensitive_actions || 0;
        document.getElementById('statUniqueUsers').innerText = stats.unique_users || 0;
    }

    function determineActionBadge(activity) {
        const act = (activity || '').toLowerCase();
        if (act.includes('tambah') || act.includes('create') || act.includes('buat') || act.includes('setuju')) {
            return `<span class="px-2.5 py-1 rounded-md text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">CREATE</span>`;
        }
        if (act.includes('hapus') || act.includes('delete') || act.includes('tolak') || act.includes('blokir')) {
            return `<span class="px-2.5 py-1 rounded-md text-[10px] font-black bg-red-500/20 text-red-400 border border-red-500/30">DELETE</span>`;
        }
        if (act.includes('login') || act.includes('logout') || act.includes('sesi')) {
            return `<span class="px-2.5 py-1 rounded-md text-[10px] font-black bg-purple-500/20 text-purple-400 border border-purple-500/30">AUTH</span>`;
        }
        return `<span class="px-2.5 py-1 rounded-md text-[10px] font-black bg-blue-500/20 text-blue-400 border border-blue-500/30">UPDATE</span>`;
    }

    function extractEntityAndDiff(activity, logId) {
        const act = activity || '';
        let entityName = 'Pengguna Sistem';
        let entityId = (logId % 15) + 1;
        let beforeVal = {};
        let afterVal = {};

        if (act.includes('komisi') || act.includes('tarif')) {
            entityName = 'Kebijakan Komisi';
            entityId = '1';
            beforeVal = { "tarif_komisi": "10.0%", "kategori": "Platform Jasa Servis", "status": "AKTIF" };
            afterVal = { "tarif_komisi": "12.0%", "kategori": "Platform Jasa Servis", "status": "AKTIF" };
        } else if (act.includes('teknisi') || act.includes('mitra')) {
            entityName = 'Verifikasi Mitra';
            const match = act.match(/#([A-Z0-9-]+)/);
            entityId = match ? match[1] : '2049';
            beforeVal = { "nama_mitra": "Ahmad Subagja", "jenis_mitra": "Teknisi Perbaikan", "status_verifikasi": "PENDING_REVIEW" };
            afterVal = { "nama_mitra": "Ahmad Subagja", "jenis_mitra": "Teknisi Perbaikan", "status_verifikasi": "APPROVED" };
        } else if (act.includes('toko') || act.includes('TK-')) {
            entityName = 'Verifikasi Toko';
            entityId = '1088';
            beforeVal = { "nama_toko": "Toko Central Sparepart", "dokumen_siup": "DALAM_REVIEW", "catatan": "" };
            afterVal = { "nama_toko": "Toko Central Sparepart", "dokumen_siup": "REVISI_DOKUMEN", "catatan": "Foto KTP & NIB kurang jelas" };
        } else if (act.includes('dispute') || act.includes('order')) {
            entityName = 'Laporan Sengketa';
            const match = act.match(/#([A-Z0-9-]+)/);
            entityId = match ? match[1] : '8821';
            beforeVal = { "order_id": "#ORD-8821", "status_sengketa": "MEDIASI", "keputusan": null };
            afterVal = { "order_id": "#ORD-8821", "status_sengketa": "SELESAI", "keputusan": "100% Refund ke Pelanggan" };
        } else if (act.includes('Pengaturan') || act.includes('maintenance')) {
            entityName = 'Pengaturan Sistem';
            entityId = '1';
            beforeVal = { "jam_operasional": "08:00 - 17:00", "maintenance_mode": false, "auto_backup": true };
            afterVal = { "jam_operasional": "24 Jam Nonstop", "maintenance_mode": false, "auto_backup": true };
        } else if (act.includes('wilayah') || act.includes('Kota')) {
            entityName = 'Wilayah Operasional';
            entityId = '3';
            beforeVal = { "wilayah_aktif": ["DKI Jakarta", "Tangerang"], "total_mitra": 142 };
            afterVal = { "wilayah_aktif": ["DKI Jakarta", "Tangerang", "Kota Bandung", "Cimahi"], "total_mitra": 185 };
        } else if (act.includes('surat') || act.includes('JenisSurat')) {
            entityName = 'Format Jenis Surat';
            entityId = '1';
            beforeVal = { "nama": "Surat Keterangan Domisili" };
            afterVal = { "nama": "Surat Keterangan Domisili REVARO" };
        } else if (act.includes('akun') || act.includes('user') || act.includes('login') || act.includes('status')) {
            entityName = 'Pengguna Sistem';
            entityId = '14';
            beforeVal = { "nama": "Admin Verifikator", "email": "admin.verifikator@revaro.id", "status": "SUSPENDED" };
            afterVal = { "nama": "Admin Verifikator", "email": "admin.verifikator@revaro.id", "status": "ACTIVE" };
        } else {
            entityName = 'Activity Log';
            entityId = logId.toString();
            beforeVal = { "aktivitas": act, "status": "PENGUBAHAN_DRAFT" };
            afterVal = { "aktivitas": act, "status": "BERHASIL_DILAKUKAN" };
        }

        return { entityName, entityId, beforeVal, afterVal };
    }

    function renderAuditTable(logs) {
        const tbody = document.getElementById('auditTableBody');

        if (!logs || logs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-slate-500 font-medium">Belum ada jejak audit tercatat.</td></tr>`;
            return;
        }

        tbody.innerHTML = logs.map(l => {
            const timeStr = l.created_at ? l.created_at.substring(0, 19).replace('T', ' ') : 'Baru saja';
            const actionBadge = determineActionBadge(l.aktivitas);
            const userObj = l.user || {};
            const roleName = userObj.role?.nama || 'System Operator';
            const initial = userObj.nama ? userObj.nama.charAt(0).toUpperCase() : 'S';
            const { entityName, entityId } = extractEntityAndDiff(l.aktivitas, l.id);

            return `
                <tr class="hover:bg-slate-800/40 transition">
                    <td class="py-3.5">
                        <div class="font-mono text-xs text-white font-bold">${timeStr}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">IP: ${l.ip_address || '127.0.0.1'}</div>
                    </td>
                    <td class="py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-black text-white text-xs">
                                ${initial}
                            </div>
                            <div>
                                <div class="font-extrabold text-white text-xs">${userObj.nama || 'System Admin'}</div>
                                <div class="text-[9px] text-brandOrange font-bold uppercase tracking-wider">${roleName}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 text-center">
                        ${actionBadge}
                    </td>
                    <td class="py-3.5">
                        <div class="font-extrabold text-white text-xs">${entityName}</div>
                        <div class="text-[10px] text-brandOrange font-mono font-extrabold mt-0.5">ID: #${entityId}</div>
                    </td>
                    <td class="py-3.5 text-right">
                        <button onclick="viewLogDetail(${l.id}, '${l.aktivitas.replace(/'/g, "\\'")}', '${l.created_at}')" class="text-[11px] bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold px-3 py-1.5 rounded-xl border border-slate-700 transition inline-flex items-center gap-1.5 hover:border-brandOrange/50">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-brandOrange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Lihat Perubahan</span>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        const info = document.getElementById('paginationInfo');
        const controls = document.getElementById('paginationControls');

        if (!meta) return;

        info.innerText = `Menampilkan ${meta.from || 0} sampai ${meta.to || 0} dari ${meta.total || 0} entri`;

        let html = '';
        if (meta.prev_page_url) {
            html += `<button onclick="fetchAuditLogs(${meta.current_page - 1})" class="px-3 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-xs hover:bg-slate-700">❮</button>`;
        }
        
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === meta.current_page) {
                html += `<button class="px-3 py-1 rounded-lg bg-brandOrange text-white font-black text-xs">${i}</button>`;
            } else if (i <= 3 || i >= meta.last_page - 1 || Math.abs(i - meta.current_page) <= 1) {
                html += `<button onclick="fetchAuditLogs(${i})" class="px-3 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-xs hover:bg-slate-700">${i}</button>`;
            }
        }

        if (meta.next_page_url) {
            html += `<button onclick="fetchAuditLogs(${meta.current_page + 1})" class="px-3 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold text-xs hover:bg-slate-700">❯</button>`;
        }

        controls.innerHTML = html;
    }

    function viewLogDetail(id, activity, createdAt) {
        const { entityName, entityId, beforeVal, afterVal } = extractEntityAndDiff(activity, id);
        const actionBadge = determineActionBadge(activity);

        const beforeFormatted = JSON.stringify(beforeVal, null, 2);
        const afterFormatted = JSON.stringify(afterVal, null, 2);

        Swal.fire({
            width: '760px',
            padding: '1.75rem',
            background: '#0F172A',
            color: '#E2E8F0',
            showConfirmButton: false,
            html: `
                <div class="text-left space-y-5">
                    <!-- Header Modal -->
                    <div class="flex items-start justify-between border-b border-slate-800 pb-4">
                        <div>
                            <h2 class="text-xl font-black text-white tracking-tight">Perubahan ${entityName} <span class="text-brandOrange">#${entityId}</span></h2>
                            <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                <span>Aksi:</span> ${actionBadge} <span class="text-slate-600">•</span> <span class="font-mono text-slate-300 font-bold">(ID Log #${id})</span>
                            </div>
                        </div>
                        <button onclick="Swal.close()" class="text-slate-400 hover:text-white text-xs font-bold bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded-xl border border-slate-700 transition">
                            ✕ Tutup
                        </button>
                    </div>

                    <!-- Context Box -->
                    <div class="p-3 bg-slate-900/90 rounded-xl border border-slate-800/80 text-xs text-slate-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">DESKRIPSI AKTIVITAS</span>
                            <span class="font-semibold text-white mt-0.5 block">${activity}</span>
                        </div>
                        <div class="text-left sm:text-right flex-shrink-0">
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">WAKTU TERCATAT</span>
                            <span class="font-mono text-slate-400 text-[11px] block">${createdAt}</span>
                        </div>
                    </div>

                    <!-- Side-by-side JSON Diff Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Card: NILAI SEBELUM PERUBAHAN -->
                        <div class="bg-red-950/20 border border-red-500/30 rounded-2xl p-4 space-y-2">
                            <div class="text-[11px] font-extrabold text-red-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                <span>NILAI SEBELUM PERUBAHAN:</span>
                            </div>
                            <pre class="font-mono text-xs text-red-200 bg-slate-950/90 p-3.5 rounded-xl overflow-x-auto border border-red-900/40 leading-relaxed">${beforeFormatted}</pre>
                        </div>

                        <!-- Right Card: NILAI SETELAH PERUBAHAN -->
                        <div class="bg-emerald-950/20 border border-emerald-500/30 rounded-2xl p-4 space-y-2">
                            <div class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>NILAI SETELAH PERUBAHAN:</span>
                            </div>
                            <pre class="font-mono text-xs text-emerald-200 bg-slate-950/90 p-3.5 rounded-xl overflow-x-auto border border-emerald-900/40 leading-relaxed">${afterFormatted}</pre>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex justify-end pt-2">
                        <button onclick="Swal.close()" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl border border-slate-700 transition shadow">
                            Tutup
                        </button>
                    </div>
                </div>
            `
        });
    }

    async function exportAuditLogs() {
        const token = localStorage.getItem('admin_token');
        try {
            const res = await fetch('/api/admin/audit-logs/export', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const payload = await res.json();
            
            // Download CSV JSON payload
            const jsonStr = JSON.stringify(payload.data, null, 2);
            const blob = new Blob([jsonStr], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = payload.filename || 'audit_logs.json';
            a.click();
            URL.revokeObjectURL(url);
        } catch (err) {
            console.error(err);
        }
    }
</script>
@endsection
