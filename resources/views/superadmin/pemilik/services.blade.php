@extends('layouts.superadmin')

@section('title', 'Kategori Servis — REVARO')
@section('page-title', 'Data Master — Kategori Servis')
@section('page-subtitle', 'Kelola daftar jenis servis, masa garansi, dan harga baseline per kategori')

@section('content')
<div class="space-y-6">
    <!-- Add / Edit Form -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tambah / Edit Kategori Servis</h3>
        <input type="hidden" id="editServiceId">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Jasa</label>
                <input type="text" id="svcNama" class="input-field" placeholder="cth. Service AC">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Kategori Perangkat</label>
                <select id="svcKategori" class="input-field">
                    <option value="">— Pilih —</option>
                    <option value="AC">AC</option>
                    <option value="Kulkas">Kulkas</option>
                    <option value="Mesin Cuci">Mesin Cuci</option>
                    <option value="TV">TV</option>
                    <option value="Komputer">Komputer</option>
                    <option value="Pompa Air">Pompa Air</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Harga Baseline (Rp)</label>
                <input type="number" id="svcHarga" class="input-field" placeholder="cth. 150000" min="0">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Masa Garansi (Hari)</label>
                <input type="number" id="svcGaransi" class="input-field" placeholder="cth. 30" min="0">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Deskripsi (opsional)</label>
                <input type="text" id="svcDeskripsi" class="input-field" placeholder="Penjelasan singkat...">
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-3">
            <button onclick="resetForm()" class="btn-outline">Reset</button>
            <button onclick="saveService()" class="btn-primary" id="saveServiceBtn">+ Simpan</button>
        </div>
    </div>

    <!-- Service List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Kategori Servis</h3>
            <input type="text" id="searchSvc" class="input-field" style="width:220px;" placeholder="Cari jasa..." oninput="filterSvc()">
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="svcTable">
                <thead>
                    <tr>
                        <th>Nama Jasa</th>
                        <th>Kategori</th>
                        <th>Harga Baseline</th>
                        <th>Garansi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="svcTableBody">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allServices = [];

async function loadServices() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/services', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        allServices = await r.json();
        renderSvc(allServices);
    } catch(e) {
        document.getElementById('svcTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-red-400 py-8">Gagal memuat data</td></tr>';
    }
}

function renderSvc(data) {
    const tbody = document.getElementById('svcTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada data</td></tr>'; return; }
    tbody.innerHTML = data.map(s => `
        <tr>
            <td class="font-semibold text-white">${s.nama_jasa}</td>
            <td><span class="badge badge-blue">${s.kategori}</span></td>
            <td class="text-slate-300">Rp ${Number(s.harga_baseline).toLocaleString('id-ID')}</td>
            <td class="text-slate-400">${s.garansi_hari} hari</td>
            <td><span class="badge ${s.is_active ? 'badge-green' : 'badge-red'}">${s.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
            <td class="flex gap-2">
                <button onclick="editService(${s.id})" class="btn-outline" style="font-size:0.7rem;padding:5px 12px;">Edit</button>
                <button onclick="toggleService(${s.id}, ${s.is_active})" class="btn-outline" style="font-size:0.7rem;padding:5px 12px;">${s.is_active ? 'Nonaktifkan' : 'Aktifkan'}</button>
            </td>
        </tr>
    `).join('');
}

function filterSvc() {
    const q = document.getElementById('searchSvc').value.toLowerCase();
    renderSvc(allServices.filter(s => s.nama_jasa.toLowerCase().includes(q) || s.kategori.toLowerCase().includes(q)));
}

function editService(id) {
    const s = allServices.find(x => x.id === id);
    if (!s) return;
    document.getElementById('editServiceId').value = s.id;
    document.getElementById('svcNama').value = s.nama_jasa;
    document.getElementById('svcKategori').value = s.kategori;
    document.getElementById('svcHarga').value = s.harga_baseline;
    document.getElementById('svcGaransi').value = s.garansi_hari;
    document.getElementById('svcDeskripsi').value = s.deskripsi || '';
    document.getElementById('saveServiceBtn').textContent = 'Perbarui Data';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('editServiceId').value = '';
    ['svcNama','svcHarga','svcGaransi','svcDeskripsi'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('svcKategori').value = '';
    document.getElementById('saveServiceBtn').textContent = '+ Simpan';
}

async function saveService() {
    const token = localStorage.getItem('admin_token');
    const id = document.getElementById('editServiceId').value;
    const payload = {
        nama_jasa: document.getElementById('svcNama').value.trim(),
        kategori: document.getElementById('svcKategori').value,
        harga_baseline: document.getElementById('svcHarga').value,
        garansi_hari: document.getElementById('svcGaransi').value,
        deskripsi: document.getElementById('svcDeskripsi').value.trim(),
    };
    if (!payload.nama_jasa || !payload.kategori) {
        Swal.fire({ title: 'Lengkapi Form', text: 'Nama jasa dan kategori wajib diisi.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        return;
    }
    const url = id ? `/api/admin/services/${id}` : '/api/admin/services';
    const method = id ? 'PUT' : 'POST';
    try {
        const r = await fetch(url, { method, headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
        const d = await r.json();
        if (r.ok) {
            Swal.fire({ title: 'Berhasil!', text: id ? 'Kategori diperbarui.' : 'Kategori baru ditambahkan.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            resetForm(); loadServices();
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

async function toggleService(id, current) {
    const token = localStorage.getItem('admin_token');
    try {
        await fetch(`/api/admin/services/${id}/toggle`, { method: 'PATCH', headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        loadServices();
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', loadServices);
</script>
@endsection
