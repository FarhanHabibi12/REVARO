@extends('layouts.superadmin')

@section('title', 'Verifikasi Mitra — REVARO')
@section('page-title', 'Verifikasi Mitra Baru')
@section('page-subtitle', 'Pantau status onboarding teknisi dan toko sparepart yang mendaftar ke platform REVARO')

@section('content')
<div class="space-y-6">
    <!-- Filter Tabs -->
    <div class="flex gap-2 flex-wrap">
        <button onclick="filterVerif('all')" id="tabAll" class="btn-primary" style="font-size:0.75rem;padding:7px 16px;">Semua</button>
        <button onclick="filterVerif('PENDING')" id="tabPending" class="btn-outline" style="font-size:0.75rem;padding:7px 16px;">Pending</button>
        <button onclick="filterVerif('APPROVED')" id="tabApproved" class="btn-outline" style="font-size:0.75rem;padding:7px 16px;">Disetujui</button>
        <button onclick="filterVerif('REJECTED')" id="tabRejected" class="btn-outline" style="font-size:0.75rem;padding:7px 16px;">Ditolak</button>
        <button onclick="filterVerif('NEEDS_REVISION')" id="tabRevisi" class="btn-outline" style="font-size:0.75rem;padding:7px 16px;">Revisi</button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-black text-white mt-1" id="vTotal">0</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Pending</p>
            <p class="text-2xl font-black text-yellow-400 mt-1" id="vPending">0</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Disetujui</p>
            <p class="text-2xl font-black text-emerald-400 mt-1" id="vApproved">0</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Ditolak</p>
            <p class="text-2xl font-black text-red-400 mt-1" id="vRejected">0</p>
        </div>
    </div>

    <!-- Verifications Table -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Riwayat Verifikasi Onboarding</h3>
            <input type="text" id="searchVerif" class="input-field" style="width:220px;" placeholder="Cari nama..." oninput="applyFilter()">
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Nama Mitra</th>
                        <th>Status</th>
                        <th>Admin Peninjau</th>
                        <th>Catatan</th>
                        <th>Diverifikasi</th>
                    </tr>
                </thead>
                <tbody id="verifTableBody">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allVerifs = [];
let activeFilter = 'all';

async function loadVerifs() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/verifications', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        allVerifs = await r.json();
        document.getElementById('vTotal').textContent = allVerifs.length;
        document.getElementById('vPending').textContent = allVerifs.filter(x => x.status === 'PENDING').length;
        document.getElementById('vApproved').textContent = allVerifs.filter(x => x.status === 'APPROVED').length;
        document.getElementById('vRejected').textContent = allVerifs.filter(x => x.status === 'REJECTED').length;
        applyFilter();
    } catch(e) {}
}

const vBadge = { PENDING: 'badge-yellow', APPROVED: 'badge-green', REJECTED: 'badge-red', NEEDS_REVISION: 'badge-blue' };

function applyFilter() {
    const q = document.getElementById('searchVerif').value.toLowerCase();
    let data = activeFilter === 'all' ? allVerifs : allVerifs.filter(v => v.status === activeFilter);
    if (q) data = data.filter(v => {
        const nama = (v.teknisi?.nama || v.toko?.nama || '').toLowerCase();
        return nama.includes(q);
    });
    renderVerif(data);
}

function filterVerif(status) {
    activeFilter = status;
    document.querySelectorAll('[id^="tab"]').forEach(btn => { btn.className = 'btn-outline'; btn.style.cssText = 'font-size:0.75rem;padding:7px 16px;'; });
    const activeBtn = { all: 'tabAll', PENDING: 'tabPending', APPROVED: 'tabApproved', REJECTED: 'tabRejected', NEEDS_REVISION: 'tabRevisi' }[status];
    if (activeBtn) { document.getElementById(activeBtn).className = 'btn-primary'; document.getElementById(activeBtn).style.cssText = 'font-size:0.75rem;padding:7px 16px;'; }
    applyFilter();
}

function renderVerif(data) {
    const tbody = document.getElementById('verifTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Tidak ada data</td></tr>'; return; }
    tbody.innerHTML = data.map(v => `
        <tr>
            <td><span class="badge ${v.teknisi_id ? 'badge-blue' : 'badge-yellow'}">${v.teknisi_id ? 'Teknisi' : 'Toko'}</span></td>
            <td class="font-semibold text-white">${v.teknisi?.nama || v.toko?.nama || '—'}</td>
            <td><span class="badge ${vBadge[v.status] || 'badge-gray'}">${v.status}</span></td>
            <td class="text-slate-400 text-xs">${v.admin?.nama || '—'}</td>
            <td class="text-slate-500 text-xs max-w-xs truncate">${v.catatan || '—'}</td>
            <td class="text-slate-500 text-xs">${v.verified_at ? new Date(v.verified_at).toLocaleDateString('id-ID') : 'Belum'}</td>
        </tr>
    `).join('');
}

document.addEventListener('DOMContentLoaded', loadVerifs);
</script>
@endsection
