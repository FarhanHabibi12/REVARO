@extends('layouts.superadmin')

@section('title', 'Manajemen Akun Admin — REVARO')
@section('page-title', '👥 Manajemen Akun Admin')
@section('page-subtitle', 'Buat, aktifkan, atau nonaktifkan akun internal (Verifikator, Resolusi, Monitoring)')

@section('content')
<div class="space-y-6">
    <!-- Create Account Form -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tambah Akun Admin Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                <input type="text" id="newAdminNama" class="input-field" placeholder="cth. Budi Santoso">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Alamat Email</label>
                <input type="email" id="newAdminEmail" class="input-field" placeholder="budi@revaro.com">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Role / Jabatan</label>
                <select id="newAdminRole" class="input-field">
                    <option value="">— Pilih Role —</option>
                    <option value="admin-verifikator">Admin Verifikator</option>
                    <option value="admin-resolusi">Admin Resolusi</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Password Awal</label>
                <input type="password" id="newAdminPassword" class="input-field" placeholder="Min. 8 karakter">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="createAdmin()" class="btn-primary">+ Buat Akun</button>
        </div>
    </div>

    <!-- Account List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Akun Admin Internal</h3>
            <input type="text" id="searchAdmin" class="input-field" style="width:220px;" placeholder="🔍 Cari nama atau email..." oninput="filterTable()">
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="adminTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">📜 Audit Log Aktivitas Admin</h3>
        <div id="auditLogList" class="space-y-2">
            <p class="text-slate-500 text-sm text-center py-4">Memuat log...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allAdmins = [];

async function loadAdmins() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/accounts', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        allAdmins = await r.json();
        renderTable(allAdmins);
    } catch(e) {
        document.getElementById('adminTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-red-400 py-8">Gagal memuat data</td></tr>';
    }
}

function roleLabel(role) {
    const map = { 'admin-verifikator': 'Verifikator', 'admin-resolusi': 'Resolusi', 'super-admin-monitoring': 'Monitoring', 'super-admin-pemilik': 'Pemilik' };
    return map[role] || role;
}

function renderTable(data) {
    const tbody = document.getElementById('adminTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Tidak ada data</td></tr>'; return; }
    const loggedInUser = JSON.parse(localStorage.getItem('admin_user') || '{}');
    tbody.innerHTML = data.map(a => `
        <tr>
            <td class="font-semibold text-white">${a.nama}</td>
            <td class="text-slate-400">${a.email}</td>
            <td><span class="badge badge-blue">${roleLabel(a.role)}</span></td>
            <td><span class="badge ${a.is_active ? 'badge-green' : 'badge-red'}">${a.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
            <td class="text-slate-500 text-xs">${new Date(a.created_at).toLocaleDateString('id-ID')}</td>
            <td>
                ${a.role === 'super-admin-pemilik' || a.id === loggedInUser.id ? `
                    <span class="text-slate-500 text-xs font-bold">Utama (Permanen)</span>
                ` : `
                    <button onclick="toggleAdmin(${a.id}, ${a.is_active})" class="btn-outline" style="font-size:0.7rem;padding:5px 12px;">${a.is_active ? 'Nonaktifkan' : 'Aktifkan'}</button>
                `}
            </td>
        </tr>
    `).join('');
}

function filterTable() {
    const q = document.getElementById('searchAdmin').value.toLowerCase();
    renderTable(allAdmins.filter(a => a.nama.toLowerCase().includes(q) || a.email.toLowerCase().includes(q)));
}

async function createAdmin() {
    const token = localStorage.getItem('admin_token');
    const payload = {
        nama: document.getElementById('newAdminNama').value.trim(),
        email: document.getElementById('newAdminEmail').value.trim(),
        role: document.getElementById('newAdminRole').value,
        password: document.getElementById('newAdminPassword').value,
    };
    if (!payload.nama || !payload.email || !payload.role || !payload.password) {
        Swal.fire({ title: 'Lengkapi Form', text: 'Semua kolom wajib diisi.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        return;
    }
    try {
        const r = await fetch('/api/admin/accounts', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const d = await r.json();
        if (r.ok) {
            Swal.fire({ title: 'Berhasil!', text: 'Akun admin baru telah dibuat.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            ['newAdminNama','newAdminEmail','newAdminPassword'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('newAdminRole').value = '';
            loadAdmins();
            loadAuditLogs();
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

async function toggleAdmin(id, currentState) {
    const token = localStorage.getItem('admin_token');
    const action = currentState ? 'nonaktifkan' : 'aktifkan';
    const confirm = await Swal.fire({ title: `${action.charAt(0).toUpperCase()+action.slice(1)} akun ini?`, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316', cancelButtonColor: '#475569' });
    if (!confirm.isConfirmed) return;
    try {
        const r = await fetch(`/api/admin/accounts/${id}/toggle`, { method: 'PATCH', headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        if (r.ok) loadAdmins();
    } catch(e) {}
}

async function loadAuditLogs() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/audit-logs', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const logs = await r.json();
        const container = document.getElementById('auditLogList');
        if (!logs.length) { container.innerHTML = '<p class="text-slate-500 text-sm text-center py-4">Belum ada log.</p>'; return; }
        container.innerHTML = logs.slice(0, 20).map(l => `
            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-900/40 border border-slate-800/50">
                <span class="text-slate-500 text-xs mt-0.5 whitespace-nowrap">${new Date(l.created_at).toLocaleString('id-ID')}</span>
                <span class="text-slate-300 text-xs">${l.aktivitas}</span>
                ${l.ip_address ? `<span class="ml-auto text-[10px] text-slate-600">${l.ip_address}</span>` : ''}
            </div>
        `).join('');
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => { loadAdmins(); loadAuditLogs(); });
</script>
@endsection
