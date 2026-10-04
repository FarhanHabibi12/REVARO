@extends('layouts.superadmin')

@section('title', 'Wilayah Operasional — REVARO')
@section('page-title', 'Data Master — Wilayah Operasional')
@section('page-subtitle', 'Kelola cakupan wilayah layanan REVARO — tambah, aktifkan, atau nonaktifkan area operasional')

@section('content')
<div class="space-y-6">
    <!-- Add Region Form -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tambah Wilayah Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Wilayah</label>
                <input type="text" id="regNama" class="input-field" placeholder="cth. Jakarta Selatan">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Prefix Kode Pos (opsional)</label>
                <input type="text" id="regKodePos" class="input-field" placeholder="cth. 12 (untuk 12xxx)">
            </div>
            <div class="flex items-end">
                <button onclick="addRegion()" class="btn-primary w-full">+ Tambah Wilayah</button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Wilayah</p>
            <p class="text-3xl font-black text-white mt-1" id="regTotal">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Wilayah Aktif</p>
            <p class="text-3xl font-black text-emerald-400 mt-1" id="regAktif">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Wilayah Nonaktif</p>
            <p class="text-3xl font-black text-slate-500 mt-1" id="regNonaktif">0</p>
        </div>
    </div>

    <!-- Region List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Wilayah Operasional</h3>
            <input type="text" id="searchReg" class="input-field" style="width:220px;" placeholder="Cari wilayah..." oninput="filterReg()">
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Wilayah</th>
                        <th>Prefix Kode Pos</th>
                        <th>Status</th>
                        <th>Ditambahkan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="regTableBody">
                    <tr><td colspan="5" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allRegions = [];

async function loadRegions() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/regions', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        allRegions = await r.json();
        renderReg(allRegions);
        document.getElementById('regTotal').textContent = allRegions.length;
        document.getElementById('regAktif').textContent = allRegions.filter(x => x.is_active).length;
        document.getElementById('regNonaktif').textContent = allRegions.filter(x => !x.is_active).length;
    } catch(e) {
        document.getElementById('regTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-red-400 py-8">Gagal memuat data</td></tr>';
    }
}

function renderReg(data) {
    const tbody = document.getElementById('regTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-slate-500 py-8">Belum ada wilayah</td></tr>'; return; }
    tbody.innerHTML = data.map(r => `
        <tr>
            <td class="font-semibold text-white">${r.nama_wilayah}</td>
            <td class="text-slate-400">${r.kode_pos_prefix || '—'}</td>
            <td><span class="badge ${r.is_active ? 'badge-green' : 'badge-gray'}">${r.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
            <td class="text-slate-500 text-xs">${new Date(r.created_at).toLocaleDateString('id-ID')}</td>
            <td><button onclick="toggleRegion(${r.id}, ${r.is_active})" class="btn-outline" style="font-size:0.7rem;padding:5px 12px;">${r.is_active ? 'Nonaktifkan' : 'Aktifkan'}</button></td>
        </tr>
    `).join('');
}

function filterReg() {
    const q = document.getElementById('searchReg').value.toLowerCase();
    renderReg(allRegions.filter(r => r.nama_wilayah.toLowerCase().includes(q)));
}

async function addRegion() {
    const token = localStorage.getItem('admin_token');
    const nama = document.getElementById('regNama').value.trim();
    const kodePos = document.getElementById('regKodePos').value.trim();
    if (!nama) {
        Swal.fire({ title: 'Nama Wajib Diisi', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        return;
    }
    try {
        const r = await fetch('/api/admin/regions', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ nama_wilayah: nama, kode_pos_prefix: kodePos || null }) });
        const d = await r.json();
        if (r.ok) {
            Swal.fire({ title: 'Ditambahkan!', text: `${nama} berhasil didaftarkan.`, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            document.getElementById('regNama').value = '';
            document.getElementById('regKodePos').value = '';
            loadRegions();
        } else {
            let errMsg = d.message || 'Terjadi kesalahan.';
            if (d.errors) {
                errMsg = Object.values(d.errors).flat().join('<br>');
            }
            Swal.fire({ title: 'Gagal', html: errMsg, icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    } catch(e) {
        Swal.fire({ title: 'Error', text: 'Tidak dapat terhubung ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
    }
}

async function toggleRegion(id, current) {
    const token = localStorage.getItem('admin_token');
    try {
        await fetch(`/api/admin/regions/${id}/toggle`, { method: 'PATCH', headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        loadRegions();
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', loadRegions);
</script>
@endsection
