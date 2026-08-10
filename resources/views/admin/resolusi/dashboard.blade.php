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
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Column: Sengketa List (4 Cols) -->
    <div class="lg:col-span-5 space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-base font-black text-white">⚖️ Antrian Sengketa Saya</h2>
                <p class="text-[10px] text-slate-400">Daftar kasus sengketa aktif ter-assign. Diurutkan berdasarkan batas waktu SLA.</p>
            </div>
            <button onclick="loadDisputes()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-[9px] py-1.5 px-3 rounded-lg transition shrink-0">
                🔄 Refresh
            </button>
        </div>

        <div id="disputeListContainer" class="space-y-3 overflow-y-auto max-h-[75vh] pr-1">
            <p class="text-slate-500 text-xs text-center py-8">Memuat daftar sengketa...</p>
        </div>
    </div>

    <!-- Right Column: Tampilan Investigasi Terpadu (7 Cols) -->
    <div class="lg:col-span-7">
        <div id="noSelectionPanel" class="bg-slate-900/30 rounded-3xl border border-slate-800/80 p-12 text-center flex flex-col items-center justify-center h-full min-h-[500px] space-y-3">
            <div class="text-5xl">🔍</div>
            <h3 class="text-sm font-bold text-slate-300">Pilih Kasus Sengketa</h3>
            <p class="text-xs text-slate-500 max-w-sm">Klik salah satu antrian sengketa di sebelah kiri untuk membuka konsol investigasi terpadu & formulir keputusan resolusi.</p>
        </div>

        <div id="investigationConsole" style="display:none;" class="space-y-6">
            <!-- Case Header -->
            <div class="bg-slate-900/60 rounded-2xl border border-slate-850 p-6 flex justify-between items-start gap-4">
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
                        <span>📄</span> Alasan Klaim & e-Nota
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
                        <span>💬</span> Log Chat Pelanggan - Teknisi
                    </h4>
                    <div id="chatHistoryContainer" class="flex-1 overflow-y-auto bg-slate-950 rounded-xl p-4 space-y-3 scrollbar-thin scrollbar-thumb-slate-900 text-xs">
                        <p class="text-slate-600 text-center py-8">Pilih kasus untuk melihat pesan chat.</p>
                    </div>
                </div>
            </div>

            <!-- Decision Form Panel -->
            <div class="bg-slate-900/60 rounded-2xl border border-slate-850 p-6 space-y-4">
                <h4 class="text-xs font-extrabold text-white flex items-center gap-1.5">
                    <span>⚖️</span> Formulir Keputusan Resolusi Staf
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
                    <button onclick="submitResolution()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3 px-6 rounded-xl transition shadow">
                        🔒 Ketuk & Terapkan Keputusan Hukum
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

async function loadDisputes() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/disputes/my-assigned', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const data = await r.json();

        const container = document.getElementById('disputeListContainer');
        if (!data.length) {
            container.innerHTML = '<p class="text-slate-500 text-xs text-center py-8">Tidak ada sengketa yang ditugaskan kepada Anda</p>';
            return;
        }

        container.innerHTML = data.map(d => {
            const deadline = new Date(d.sla_deadline);
            const now = new Date();
            const timeDiff = deadline - now;
            const hoursLeft = Math.round(timeDiff / (1000 * 60 * 60));
            const isCritical = hoursLeft < 12;

            return `
                <div onclick="selectDispute(${JSON.stringify(d).replace(/"/g, '&quot;')})" class="p-4 rounded-xl border border-slate-800 bg-slate-900/30 hover:border-brandOrange transition cursor-pointer flex justify-between items-start gap-3">
                    <div class="space-y-1.5">
                        <p class="font-extrabold text-xs text-white">Order #${d.order.kode_order}</p>
                        <p class="text-[10px] text-slate-400 max-w-[200px] truncate">${d.alasan}</p>
                        <p class="text-[9px] text-slate-500">Pelapor: ${d.order.pelanggan?.nama || 'Pelanggan'}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded text-[8px] font-black tracking-wide ${isCritical ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20'}">
                            ${hoursLeft > 0 ? hoursLeft + ' Jam' : 'EXPIRED'}
                        </span>
                    </div>
                </div>
            `;
        }).join('');

    } catch (e) {
        console.error(e);
    }
}

async function selectDispute(dispute) {
    currentDispute = dispute;

    document.getElementById('noSelectionPanel').style.display = 'none';
    document.getElementById('investigationConsole').style.display = 'block';

    document.getElementById('disputeCode').textContent = `Kasus Sengketa #${dispute.id}`;
    document.getElementById('orderCode').textContent = dispute.order.kode_order;
    document.getElementById('slaText').textContent = new Date(dispute.sla_deadline).toLocaleString('id-ID');
    document.getElementById('customerComplaint').textContent = dispute.alasan;

    const totalJasa = dispute.order.transaksi?.biaya_jasa || 0;
    const totalParts = dispute.order.transaksi?.biaya_part || 0;
    const totalAll = dispute.order.transaksi?.total || 0;

    document.getElementById('notaJasa').textContent = `Rp ${Number(totalJasa).toLocaleString('id-ID')}`;
    document.getElementById('notaParts').textContent = `Rp ${Number(totalParts).toLocaleString('id-ID')}`;
    document.getElementById('notaTotal').textContent = `Rp ${Number(totalAll).toLocaleString('id-ID')}`;

    document.getElementById('resolveDecision').value = '';
    document.getElementById('resolveNotes').value = '';

    // Load Chats
    loadOrderChats(dispute.order_id);

    // Load Offender Violations history
    const techId = dispute.order.teknisi_id || null;
    loadMitraViolations(techId);
}

async function loadOrderChats(orderId) {
    const token = localStorage.getItem('admin_token');
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
    const token = localStorage.getItem('admin_token');
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

    const token = localStorage.getItem('admin_token');
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
