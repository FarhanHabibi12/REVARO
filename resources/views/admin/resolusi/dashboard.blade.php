@extends('layouts.admin')

@section('title', 'Antrian Sengketa — Resolusi')

@section('styles')
<style>
    .investigate-panel {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Top Nav Tabs for Dispute vs Rating Evaluation -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/60 p-6 md:p-8 rounded-3xl border border-slate-800">
        <div>
            <span class="px-2.5 py-1 rounded-lg bg-brandOrange/10 text-brandOrange border border-brandOrange/20 font-black text-[10px] uppercase tracking-wider inline-flex items-center gap-1.5 mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                <span>KONSOL RESOLUSI HUKUM</span>
            </span>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">Antrian Sengketa Transaksi (Garansi &amp; Escrow)</h2>
            <p class="text-xs text-slate-400 mt-1 max-w-xl">
                Tangani klaim garansi servis aktif, pembekuan dana escrow, dan sengketa transaksi pelanggan dengan mitra.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/resolusi/dashboard" class="px-4 py-2.5 rounded-xl bg-brandOrange text-white font-extrabold text-xs shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Sengketa Transaksi</span>
            </a>
            <a href="/admin/resolusi/evaluasi-rating" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <span>Evaluasi Rating &amp; Disiplin</span>
            </a>
        </div>
    </div>

    <!-- Main Grid with Generous Spacing -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Sengketa List (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex justify-between items-center px-1">
                <div>
                    <h3 class="text-sm font-black text-white">Daftar Sengketa Aktif</h3>
                    <p class="text-[10px] text-slate-400">Diurutkan berdasarkan batas waktu SLA.</p>
                </div>
                <button onclick="loadDisputes()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-[10px] py-2 px-3.5 rounded-xl transition shrink-0 flex items-center gap-1.5 shadow">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh</span>
                </button>
            </div>

            <div id="disputeListContainer" class="space-y-4 overflow-y-auto max-h-[75vh] pr-1.5 scrollbar-thin scrollbar-thumb-slate-800">
                <p class="text-slate-500 text-xs text-center py-8">Memuat daftar sengketa...</p>
            </div>
        </div>

        <!-- Right Column: Tampilan Investigasi Terpadu (7 Cols) -->
        <div class="lg:col-span-7">
            <div id="noSelectionPanel" class="bg-slate-900/40 rounded-3xl border border-slate-800/80 p-12 text-center flex flex-col items-center justify-center min-h-[520px] space-y-4">
                <div class="w-16 h-16 rounded-3xl bg-slate-800/80 border border-slate-700/50 flex items-center justify-center text-slate-400 text-2xl mx-auto shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-300">Pilih Kasus Sengketa</h3>
                <p class="text-xs text-slate-500 max-w-sm leading-relaxed">Klik salah satu antrian sengketa di sebelah kiri untuk membuka konsol investigasi terpadu &amp; formulir keputusan resolusi.</p>
            </div>

            <div id="investigationConsole" style="display:none;" class="space-y-6">
                <!-- Case Header -->
                <div class="bg-slate-900/60 rounded-3xl border border-slate-850 p-6 md:p-7 flex justify-between items-start gap-4">
                <div>
                    <span class="px-2 py-0.5 rounded bg-brandOrange/10 text-brandOrange border border-brandOrange/20 font-black text-[9px] uppercase tracking-wide">Investigating Case</span>
                    <h3 class="text-base font-black text-white mt-2" id="disputeCode">Sengketa #—</h3>
                    <p class="text-xs text-slate-400 mt-1">Kode Order: <span class="font-mono text-white" id="orderCode">#—</span></p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-bold text-slate-500 uppercase">SLA Deadline</p>
                    <p class="text-xs font-black text-red-400 mt-1" id="slaText">—</p>
                </div>
            </div>

            <!-- Tabbed Investigation Sections -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Case Summary & Nota -->
                <div class="bg-slate-900/40 rounded-2xl border border-slate-800 p-5 space-y-4">
                    <h4 class="text-xs font-extrabold text-white flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Alasan Klaim &amp; e-Nota</span>
                    </h4>
                    <div class="bg-slate-950 p-4 rounded-xl space-y-3 text-xs">
                        <div>
                            <p class="text-[9px] text-slate-500 font-bold uppercase">Alasan Klaim (Pelanggan)</p>
                            <p class="text-slate-300 italic mt-1" id="customerComplaint">—</p>
                        </div>
                        <hr class="border-slate-900" />
                        <div>
                            <p class="text-[9px] text-slate-500 font-bold uppercase">Rincian e-Nota Pembayaran</p>
                            <div class="flex justify-between mt-1 text-slate-400">
                                <span>Biaya Jasa Servis:</span>
                                <span class="text-white" id="notaJasa">Rp —</span>
                            </div>
                            <div class="flex justify-between mt-1 text-slate-400">
                                <span>Biaya Suku Cadang:</span>
                                <span class="text-white" id="notaParts">Rp —</span>
                            </div>
                            <div class="flex justify-between mt-2 pt-2 border-t border-slate-900 font-bold text-brandOrange">
                                <span>Total Escrow:</span>
                                <span id="notaTotal">Rp —</span>
                            </div>
                        </div>
                    </div>

                    <!-- Offender Violation History -->
                    <div class="bg-slate-950 p-4 rounded-xl space-y-3 text-xs border border-red-950/20">
                        <p class="text-[9px] text-red-400 font-bold uppercase tracking-wider">Riwayat Pelanggaran Mitra</p>
                        <div id="violationHistory" class="space-y-1.5 max-h-24 overflow-y-auto pr-1">
                            <!-- Filled by JS -->
                        </div>
                    </div>
                </div>

                <!-- Chat History Logs -->
                <div class="bg-slate-900/40 rounded-2xl border border-slate-800 p-5 flex flex-col h-[350px]">
                    <h4 class="text-xs font-extrabold text-white flex items-center gap-1.5 mb-3">
                        <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Log Chat Pelanggan - Teknisi</span>
                    </h4>
                    <div id="chatHistoryContainer" class="flex-1 overflow-y-auto bg-slate-950 rounded-xl p-4 space-y-3 scrollbar-thin scrollbar-thumb-slate-900 text-xs">
                        <p class="text-slate-600 text-center py-8">Pilih kasus untuk melihat pesan chat.</p>
                    </div>
                </div>
            </div>

            <!-- Decision Form Panel -->
            <div class="bg-slate-900/60 rounded-2xl border border-slate-855 p-6 space-y-4">
                <h4 class="text-xs font-extrabold text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    <span>Formulir Keputusan Resolusi Staf</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Hasil Akhir Keputusan</label>
                        <select id="resolveDecision" class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition">
                            <option value="">— Pilih Keputusan Resolusi —</option>
                            <option value="REFUND_PELANGGAN">Refund Penuh ke Pelanggan</option>
                            <option value="RELEASE_TEKNISI">Cairkan (Release) Dana ke Mitra</option>
                            <option value="KUNJUNGAN_ULANG">Kunjungan Ulang Gratis oleh Teknisi</option>
                            <option value="FREEZE_AKUN">Bekukan (Freeze) Akun Pelaku Pelanggaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Catatan Resolusi (Wajib)</label>
                        <textarea id="resolveNotes" rows="1" class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Tuliskan alasan hukum/bukti pendukung keputusan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button onclick="submitResolution()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3 px-6 rounded-xl transition shadow flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Ketuk &amp; Terapkan Keputusan Hukum</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentDispute = null;
let allDisputes = [];

async function loadDisputes() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const container = document.getElementById('disputeListContainer');
    try {
        const r = await fetch('/api/disputes/my-assigned?type=sengketa', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const data = await r.json();
        allDisputes = Array.isArray(data) ? data : (data.disputes || []);

        if (!allDisputes.length) {
            container.innerHTML = '<p class="text-slate-500 text-xs text-center py-8">Tidak ada sengketa aktif yang perlu diselesaikan.</p>';
            return;
        }

        const myId = (typeof currentUser !== 'undefined' && currentUser) ? currentUser.id : null;

        container.innerHTML = allDisputes.map(d => {
            const deadline = new Date(d.sla_deadline);
            const now = new Date();
            const timeDiff = deadline - now;
            const hoursLeft = Math.round(timeDiff / (1000 * 60 * 60));
            const isCritical = hoursLeft < 12;
            const isMine = myId && d.admin_id === myId;
            const isUnassigned = !d.admin_id;

            let assignBadge = '';
            if (isMine) {
                assignBadge = '<span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[8px] font-bold">Ditugaskan ke Anda</span>';
            } else if (isUnassigned) {
                assignBadge = '<span class="px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[8px] font-bold">Antrian Terbuka</span>';
            }

            const isRatingDispute = (d.alasan && (d.alasan.includes('Rating Buruk') || d.alasan.includes('Eskalasi Mutu')));
            const typeBadge = isRatingDispute 
                ? '<span class="px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[8px] font-bold">Eskalasi Rating</span>' 
                : '<span class="px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[8px] font-bold">Garansi / Sengketa</span>';

            const cleanReason = (d.alasan || '—').replace(/"/g, '&quot;');
            const pelaporName = (d.order?.pelanggan?.nama || 'Pelanggan').replace(/"/g, '&quot;');

            return `
                <div onclick="selectDisputeById(${d.id})" class="p-4 rounded-xl border border-slate-800 bg-slate-900/30 hover:border-brandOrange transition cursor-pointer flex justify-between items-start gap-3">
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="font-extrabold text-xs text-white">Order #${d.order?.kode_order || '—'}</p>
                            ${assignBadge}
                            ${typeBadge}
                        </div>
                        <p class="text-[10px] text-slate-400 max-w-[220px] truncate" title="${cleanReason}">${cleanReason}</p>
                        <p class="text-[9px] text-slate-500">Pelapor: ${pelaporName}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded text-[8px] font-black tracking-wide ${isCritical ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20'}">
                            ${hoursLeft > 0 ? hoursLeft + ' Jam' : 'EXPIRED'}
                        </span>
                    </div>
                </div>
            `;
        }).join('');

        if (currentDispute) {
            const updated = allDisputes.find(x => x.id === currentDispute.id);
            if (updated) selectDispute(updated);
        }

    } catch (e) {
        console.error(e);
        container.innerHTML = '<p class="text-red-400 text-xs text-center py-8">Gagal memuat daftar sengketa</p>';
    }
}

function selectDisputeById(id) {
    const d = allDisputes.find(x => x.id === id);
    if (d) selectDispute(d);
}

async function selectDispute(dispute) {
    currentDispute = dispute;

    document.getElementById('noSelectionPanel').style.display = 'none';
    document.getElementById('investigationConsole').style.display = 'block';

    document.getElementById('disputeCode').textContent = `Kasus Sengketa #${dispute.id}`;
    document.getElementById('orderCode').textContent = dispute.order?.kode_order || '—';
    document.getElementById('slaText').textContent = dispute.sla_deadline ? new Date(dispute.sla_deadline).toLocaleString('id-ID') : '—';
    document.getElementById('customerComplaint').textContent = dispute.alasan || '—';

    const totalJasa = dispute.order?.transaksi?.biaya_jasa || 0;
    const totalParts = dispute.order?.transaksi?.biaya_part || 0;
    const totalAll = dispute.order?.transaksi?.total || 0;

    document.getElementById('notaJasa').textContent = `Rp ${Number(totalJasa).toLocaleString('id-ID')}`;
    document.getElementById('notaParts').textContent = `Rp ${Number(totalParts).toLocaleString('id-ID')}`;
    document.getElementById('notaTotal').textContent = `Rp ${Number(totalAll).toLocaleString('id-ID')}`;

    document.getElementById('resolveDecision').value = '';
    document.getElementById('resolveNotes').value = '';

    // Load Chats
    if (dispute.order_id) {
        loadOrderChats(dispute.order_id);
    }

    // Load Offender Violations history
    const techId = dispute.order?.teknisi_id || null;
    loadMitraViolations(techId);
}

async function loadOrderChats(orderId) {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const container = document.getElementById('chatHistoryContainer');
    container.innerHTML = '<p class="text-slate-500 text-[10px] text-center py-6">Memuat log obrolan...</p>';

    try {
        const r = await fetch(`/api/orders/${orderId}/chats`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const messages = await r.json();

        if (!messages.length) {
            container.innerHTML = '<p class="text-slate-600 text-[10px] text-center py-6">Tidak ada obrolan terekam di sistem</p>';
            return;
        }

        container.innerHTML = messages.map(m => {
            const isSystem = !m.dari_user_id;
            const name = m.dari_user?.nama || 'Sistem';
            return `
                <div class="p-2 rounded-lg border border-slate-800 bg-slate-900/20 space-y-1">
                    <div class="flex justify-between items-center text-[9px] text-slate-500">
                        <strong class="text-slate-300">${name}</strong>
                        <span>${new Date(m.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                    <p class="text-slate-400 text-[11px] leading-relaxed">${m.pesan}</p>
                </div>
            `;
        }).join('');

    } catch (e) {
        container.innerHTML = '<p class="text-red-400 text-[10px] text-center py-6">Gagal memuat log obrolan</p>';
    }
}

async function loadMitraViolations(techId) {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const container = document.getElementById('violationHistory');
    container.innerHTML = '<p class="text-slate-600 text-[10px]">Memuat riwayat...</p>';

    if (!techId) {
        container.innerHTML = '<p class="text-slate-500 text-[10px]">Bukan transaksi teknisi.</p>';
        return;
    }

    try {
        const r = await fetch(`/api/disputes/history/mitra?teknisi_id=${techId}`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const logs = await r.json();

        if (!logs.length) {
            container.innerHTML = '<p class="text-emerald-400 text-[10px] font-bold">✓ Mitra Bersih (0 Pelanggaran)</p>';
            return;
        }

        container.innerHTML = logs.map(l => `
            <div class="bg-red-950/20 border border-red-900/30 rounded p-2 text-[10px]">
                <div class="flex justify-between text-[9px] font-black text-red-400">
                    <span>${l.tingkat_penalti}</span>
                    <span>${new Date(l.created_at).toLocaleDateString('id-ID')}</span>
                </div>
                <p class="text-slate-400 leading-normal mt-0.5">${l.jenis_pelanggaran}</p>
            </div>
        `).join('');

    } catch (e) {
        container.innerHTML = '<p class="text-red-400 text-[10px]">Gagal memuat riwayat</p>';
    }
}

async function submitResolution() {
    if (!currentDispute) return;

    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const decision = document.getElementById('resolveDecision').value;
    const resolusi = document.getElementById('resolveNotes').value.trim();

    if (!decision || !resolusi) {
        Swal.fire({
            title: 'Lengkapi Resolusi',
            text: 'Keputusan dan rincian catatan resolusi wajib diisi.',
            icon: 'warning',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#F97316'
        });
        return;
    }

    try {
        const r = await fetch(`/api/disputes/${currentDispute.id}/resolve`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                keputusan: decision,
                resolusi: resolusi
            })
        });

        const d = await r.json();
        if (r.ok) {
            Swal.fire({
                title: 'Sengketa Ditutup!',
                text: d.message,
                icon: 'success',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            document.getElementById('investigationConsole').style.display = 'none';
            document.getElementById('noSelectionPanel').style.display = 'flex';
            currentDispute = null;
            loadDisputes();
        } else {
            Swal.fire({
                title: 'Gagal',
                text: d.message || 'Terjadi kesalahan.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    } catch(e) {
        console.error(e);
    }
}

function initPage() {
    loadDisputes();
}
</script>
@endsection
