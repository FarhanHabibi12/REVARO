@extends('layouts.superadmin')

@section('title', 'Eskalasi Dispute — REVARO')
@section('page-title', 'Eskalasi Dispute & Pengawasan Pemilik')
@section('page-subtitle', 'Pantau investigasi sengketa transaksi dan terbitkan instruksi / arahan pemilik ke Admin Resolusi')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Eskalasi Masuk</p>
            <p class="text-3xl font-black text-red-400 mt-1" id="dTotal">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Investigasi</p>
            <p class="text-3xl font-black text-yellow-400 mt-1" id="dWaiting">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">SLA Terancam</p>
            <p class="text-3xl font-black text-orange-400 mt-1" id="dSla">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Diselesaikan</p>
            <p class="text-3xl font-black text-emerald-400 mt-1" id="dResolved">0</p>
        </div>
    </div>

    <!-- Dispute List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-sm font-extrabold text-white">Daftar Sengketa &amp; Eskalasi Transaksi</h3>
                <p class="text-xs text-slate-500 mt-0.5">Keputusan resolusi dieksekusi oleh Admin Resolusi sesuai arahan dan kepatuhan SLA</p>
            </div>
            <select id="filterDispute" class="input-field" style="width:180px;" onchange="filterDispute()">
                <option value="">Semua Status</option>
                <option value="ESCALATED">Eskalasi</option>
                <option value="OPEN">Open</option>
                <option value="INVESTIGATING">Investigating</option>
                <option value="RESOLVED">Resolved</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Dispute</th>
                        <th>ID Order</th>
                        <th>Admin Penangan</th>
                        <th>Status</th>
                        <th>SLA Deadline</th>
                        <th>Alasan &amp; Bukti</th>
                        <th>Aksi Pengawasan</th>
                    </tr>
                </thead>
                <tbody id="disputeTableBody">
                    <tr><td colspan="7" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Instruction / Review Modal -->
<div id="decisionModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:999;backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div class="card p-6 md:p-8" style="width:560px;max-width:92vw;">
        <div class="flex justify-between items-start border-b border-slate-800 pb-4 mb-5">
            <div>
                <h3 class="text-base font-black text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Review Investigasi Dispute #</span><span id="modalDisputeId"></span>
                </h3>
                <p class="text-xs text-slate-400 mt-1" id="modalSubTitle">Admin Penangan: Memuat...</p>
            </div>
            <button onclick="closeDecisionModal()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
        </div>

        <div class="space-y-4 mb-6">
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>ID Order: <strong class="text-white" id="modalOrderId">-</strong></span>
                    <span>Status Kasus: <strong class="text-orange-400" id="modalStatusText">-</strong></span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Keluhan / Alasan Sengketa:</span>
                    <p class="text-slate-200 font-medium" id="modalAlasanText">-</p>
                </div>
                <div id="modalResolutionBox" class="hidden pt-2 border-t border-slate-800">
                    <span class="text-emerald-400 block mb-0.5 font-bold">Hasil Keputusan Admin Resolusi:</span>
                    <p class="text-slate-300" id="modalResolutionText">-</p>
                </div>
            </div>

            <div>
                <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span>Terbitkan Instruksi / Catatan Khusus Pemilik ke Admin Resolusi</span>
                </label>
                <textarea id="modalInstruksiPesan" class="input-field text-xs font-sans" rows="3" placeholder="Contoh: Tolong prioritaskan mediasi dengan pihak bengkel dan verifikasi bukti foto pekerjaan ulang dalam 2 jam ke depan..."></textarea>
                <p class="text-[10px] text-slate-500 mt-1">Instruksi ini akan otomatis masuk ke menu Notifikasi & Arahan pada akun Admin Resolusi terkait.</p>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
            <button onclick="closeDecisionModal()" class="btn-outline text-xs">Tutup</button>
            <button onclick="sendOwnerInstruction()" id="btnSubmitInstruction" class="btn-primary text-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                <span>Kirim Instruksi ke Admin Resolusi</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allDisputes = [];
let activeDispute = null;

async function loadDisputes() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    try {
        const r = await fetch('/api/admin/disputes', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        allDisputes = await r.json();
        const escalated = allDisputes.filter(d => d.status === 'ESCALATED');
        document.getElementById('dTotal').textContent = escalated.length;
        document.getElementById('dWaiting').textContent = allDisputes.filter(d => d.status === 'OPEN' || d.status === 'INVESTIGATING' || d.status === 'ESCALATED').length;
        document.getElementById('dResolved').textContent = allDisputes.filter(d => d.status === 'RESOLVED').length;
        const now = new Date();
        document.getElementById('dSla').textContent = allDisputes.filter(d => d.sla_deadline && new Date(d.sla_deadline) < now && d.status !== 'RESOLVED').length;
        renderDisputes(allDisputes);
    } catch(e) {}
}

const statusBadge = { OPEN: 'badge-red', INVESTIGATING: 'badge-blue', ESCALATED: 'badge-yellow', RESOLVED: 'badge-green' };

function renderDisputes(data) {
    const tbody = document.getElementById('disputeTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="text-center text-slate-500 py-8">Tidak ada data dispute</td></tr>'; return; }
    tbody.innerHTML = data.map(d => `
        <tr>
            <td class="font-mono text-slate-300">#${d.id}</td>
            <td class="font-mono text-slate-400">#${d.order_id}</td>
            <td class="text-xs text-slate-300">${d.admin?.nama ? `<span class="font-bold text-white">${d.admin.nama}</span> <span class="text-[9px] text-orange-400 block font-mono">Admin Resolusi</span>` : '<span class="text-slate-500 italic">Belum Ditugaskan</span>'}</td>
            <td><span class="badge ${statusBadge[d.status] || 'badge-gray'}">${d.status}</span></td>
            <td class="text-xs ${d.sla_deadline && new Date(d.sla_deadline) < new Date() ? 'text-red-400 font-bold' : 'text-slate-400'}">${d.sla_deadline ? new Date(d.sla_deadline).toLocaleString('id-ID') : '—'}</td>
            <td class="text-slate-400 text-xs max-w-xs truncate">${d.alasan || '—'}</td>
            <td>
                <button onclick="openReviewModal(${d.id})" class="btn-primary flex items-center gap-1.5" style="font-size:0.7rem;padding:5px 12px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Review / Instruksi</span>
                </button>
            </td>
        </tr>
    `).join('');
}

function filterDispute() {
    const val = document.getElementById('filterDispute').value;
    renderDisputes(val ? allDisputes.filter(d => d.status === val) : allDisputes);
}

function openReviewModal(id) {
    activeDispute = allDisputes.find(d => d.id === id);
    if (!activeDispute) return;

    document.getElementById('modalDisputeId').textContent = activeDispute.id;
    document.getElementById('modalOrderId').textContent = '#' + activeDispute.order_id;
    document.getElementById('modalStatusText').textContent = activeDispute.status;
    document.getElementById('modalAlasanText').textContent = activeDispute.alasan || 'Tidak ada catatan keluhan';

    const adminName = activeDispute.admin?.nama || 'Belum ditugaskan ke Admin Resolusi spesifik';
    document.getElementById('modalSubTitle').textContent = `Admin Penangan: ${adminName}`;

    const resBox = document.getElementById('modalResolutionBox');
    if (activeDispute.status === 'RESOLVED') {
        resBox.classList.remove('hidden');
        document.getElementById('modalResolutionText').textContent = `${activeDispute.keputusan || 'SELESAI'} - ${activeDispute.resolusi || '-'}`;
    } else {
        resBox.classList.add('hidden');
    }

    document.getElementById('modalInstruksiPesan').value = '';
    document.getElementById('decisionModal').style.display = 'flex';
}

function closeDecisionModal() {
    document.getElementById('decisionModal').style.display = 'none';
    activeDispute = null;
}

async function sendOwnerInstruction() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const pesan = document.getElementById('modalInstruksiPesan').value.trim();

    if (!pesan) {
        Swal.fire({
            title: 'Tuliskan Instruksi',
            text: 'Silakan ketik instruksi atau arahan yang ingin disampaikan kepada Admin Resolusi.',
            icon: 'warning',
            background: '#0F172A',
            color: '#F8FAFC',
            confirmButtonColor: '#F97316'
        });
        return;
    }

    if (!activeDispute) return;

    const targetAdminId = activeDispute.admin?.id || activeDispute.admin_id;

    if (!targetAdminId) {
        Swal.fire({
            title: 'Admin Belum Ditugaskan',
            text: 'Kasus ini belum memiliki Admin Resolusi yang ditugaskan.',
            icon: 'warning',
            background: '#0F172A',
            color: '#F8FAFC',
            confirmButtonColor: '#F97316'
        });
        return;
    }

    const btn = document.getElementById('btnSubmitInstruction');
    btn.disabled = true;
    btn.innerText = 'Mengirim...';

    try {
        const payload = {
            user_id: targetAdminId,
            judul: `ARAHAN PEMILIK: Dispute #${activeDispute.id} (Order #${activeDispute.order_id})`,
            pesan: `Arahan dari Pemilik Platform REVARO: "${pesan}" (Status Dispute: ${activeDispute.status})`
        };

        const r = await fetch('/api/admin/notifications', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (r.ok) {
            Swal.fire({
                title: 'Instruksi Berhasil Diterbitkan!',
                text: `Arahan telah diteruskan ke ${activeDispute.admin?.nama || 'Admin Resolusi'} dan tercatat dalam database sistem.`,
                icon: 'success',
                background: '#0F172A',
                color: '#F8FAFC',
                confirmButtonColor: '#F97316'
            });
            closeDecisionModal();
            loadDisputes();
        } else {
            const res = await r.json();
            throw new Error(res.message || 'Gagal mengirim instruksi');
        }
    } catch(err) {
        Swal.fire({
            title: 'Gagal Mengirim',
            text: err.message,
            icon: 'error',
            background: '#0F172A',
            color: '#F8FAFC',
            confirmButtonColor: '#F97316'
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg><span>Kirim Instruksi ke Admin Resolusi</span>`;
    }
}

document.addEventListener('DOMContentLoaded', loadDisputes);
</script>
@endsection
