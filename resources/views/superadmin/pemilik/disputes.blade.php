@extends('layouts.superadmin')

@section('title', 'Eskalasi Dispute — REVARO')
@section('page-title', '⚖️ Eskalasi Dispute ke Pemilik')
@section('page-subtitle', 'Dispute yang dieskalasikan oleh Admin Resolusi dan memerlukan keputusan final pemilik')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Eskalasi Masuk</p>
            <p class="text-3xl font-black text-red-400 mt-1" id="dTotal">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Keputusan</p>
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
            <h3 class="text-sm font-extrabold text-white">Daftar Dispute Tereskalasi</h3>
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
                        <th>Status</th>
                        <th>SLA Deadline</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="disputeTableBody">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Decision Modal -->
<div id="decisionModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;display:none;align-items:center;justify-content:center;">
    <div class="card p-6" style="width:480px;max-width:90vw;">
        <h3 class="text-sm font-extrabold text-white mb-4">Keputusan Final Dispute #<span id="modalDisputeId"></span></h3>
        <div class="mb-4">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Keputusan</label>
            <select id="modalKeputusan" class="input-field">
                <option value="">— Pilih Keputusan —</option>
                <option value="REFUND_PELANGGAN">Refund ke Pelanggan</option>
                <option value="RELEASE_TEKNISI">Release ke Teknisi</option>
                <option value="KUNJUNGAN_ULANG">Kunjungan Ulang</option>
                <option value="FREEZE_AKUN">Freeze Akun Mitra</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Catatan Resolusi</label>
            <textarea id="modalResolusi" class="input-field" rows="3" placeholder="Jelaskan dasar keputusan..."></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeDecisionModal()" class="btn-outline">Batal</button>
            <button onclick="submitDecision()" class="btn-primary">✅ Terapkan Keputusan</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allDisputes = [];
let activeDisputeId = null;

async function loadDisputes() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/disputes', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        allDisputes = await r.json();
        const escalated = allDisputes.filter(d => d.status === 'ESCALATED');
        document.getElementById('dTotal').textContent = escalated.length;
        document.getElementById('dWaiting').textContent = allDisputes.filter(d => d.status === 'OPEN' || d.status === 'ESCALATED').length;
        document.getElementById('dResolved').textContent = allDisputes.filter(d => d.status === 'RESOLVED').length;
        const now = new Date();
        document.getElementById('dSla').textContent = allDisputes.filter(d => d.sla_deadline && new Date(d.sla_deadline) < now && d.status !== 'RESOLVED').length;
        renderDisputes(allDisputes);
    } catch(e) {}
}

const statusBadge = { OPEN: 'badge-red', INVESTIGATING: 'badge-blue', ESCALATED: 'badge-yellow', RESOLVED: 'badge-green' };

function renderDisputes(data) {
    const tbody = document.getElementById('disputeTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Tidak ada data</td></tr>'; return; }
    tbody.innerHTML = data.map(d => `
        <tr>
            <td class="font-mono text-slate-300">#${d.id}</td>
            <td class="font-mono text-slate-400">${d.order_id}</td>
            <td><span class="badge ${statusBadge[d.status] || 'badge-gray'}">${d.status}</span></td>
            <td class="text-xs ${d.sla_deadline && new Date(d.sla_deadline) < new Date() ? 'text-red-400 font-bold' : 'text-slate-400'}">${d.sla_deadline ? new Date(d.sla_deadline).toLocaleString('id-ID') : '—'}</td>
            <td class="text-slate-400 text-xs max-w-xs truncate">${d.alasan}</td>
            <td>${d.status !== 'RESOLVED' ? `<button onclick="openDecisionModal(${d.id})" class="btn-primary" style="font-size:0.7rem;padding:5px 12px;">Putuskan</button>` : '<span class="text-emerald-400 text-xs font-bold">✓ Selesai</span>'}</td>
        </tr>
    `).join('');
}

function filterDispute() {
    const val = document.getElementById('filterDispute').value;
    renderDisputes(val ? allDisputes.filter(d => d.status === val) : allDisputes);
}

function openDecisionModal(id) {
    activeDisputeId = id;
    document.getElementById('modalDisputeId').textContent = id;
    document.getElementById('modalKeputusan').value = '';
    document.getElementById('modalResolusi').value = '';
    document.getElementById('decisionModal').style.display = 'flex';
}
function closeDecisionModal() {
    document.getElementById('decisionModal').style.display = 'none';
    activeDisputeId = null;
}

async function submitDecision() {
    const token = localStorage.getItem('admin_token');
    const keputusan = document.getElementById('modalKeputusan').value;
    const resolusi = document.getElementById('modalResolusi').value.trim();
    if (!keputusan || !resolusi) {
        Swal.fire({ title: 'Lengkapi keputusan dan catatan', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' }); return;
    }
    try {
        const r = await fetch(`/api/admin/disputes/${activeDisputeId}/resolve`, { method: 'PATCH', headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ keputusan, resolusi }) });
        if (r.ok) {
            Swal.fire({ title: 'Keputusan Diterapkan!', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            closeDecisionModal(); loadDisputes();
        }
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', loadDisputes);
</script>
@endsection
