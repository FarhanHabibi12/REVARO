@extends('layouts.superadmin')

@section('title', 'Manajemen Akun Admin — REVARO')
@section('page-title', 'Manajemen Akun Admin')
@section('page-subtitle', 'Buat, aktifkan, atau nonaktifkan akun internal (Verifikator, Resolusi, Monitoring)')

@section('content')
<div class="space-y-6">
    <!-- Create Account Form -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tambah Akun Admin Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap Admin</label>
                <input type="text" id="newAdminNama" class="input-field" placeholder="cth. Farhan Habibi">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Akun Google / Email</label>
                <input type="email" id="newAdminEmail" class="input-field" placeholder="admin@gmail.com">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Role / Jabatan</label>
                <select id="newAdminRole" class="input-field">
                    <option value="">— Pilih Role —</option>
                    <option value="admin-verifikator">Admin Verifikator</option>
                    <option value="admin-resolusi">Admin Resolusi</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Password acak sistem 100% full otomatis digenerate. Admin baru wajib mengatur username &amp; kata sandi baru saat login pertama.</span>
            </p>
            <button onclick="createAdmin()" class="btn-primary">+ Buat Akun Admin</button>
        </div>
    </div>

    <!-- Account List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Akun Admin Internal</h3>
            <input type="text" id="searchAdmin" class="input-field" style="width:220px;" placeholder="Cari nama atau email..." oninput="filterTable()">
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
        <h3 class="text-sm font-extrabold text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Audit Log Aktivitas Admin</span>
        </h3>
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
    const nama = document.getElementById('newAdminNama').value.trim();
    const email = document.getElementById('newAdminEmail').value.trim();
    const role = document.getElementById('newAdminRole').value;

    if (!nama || !email || !role) {
        Swal.fire({ title: 'Lengkapi Data', text: 'Nama lengkap, akun email Google, dan role wajib diisi.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        return;
    }

    const payload = { nama, email, role };

    try {
        const r = await fetch('/api/admin/accounts', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const d = await r.json();
        if (r.ok) {
            const tempPwd = d.temporary_password;
            Swal.fire({
                title: 'Akun Admin Berhasil Dibuat!',
                html: `
                    <div style="text-align: left; font-size: 0.85rem; line-height: 1.6; color: #CBD5E1;">
                        <p class="mb-2">Password acak dibuat 100% oleh sistem. Berikan kredensial awal berikut kepada Admin terkait:</p>
                        <div style="background: #0F172A; padding: 12px; border-radius: 8px; border: 1px solid #334155; font-family: monospace;">
                            <div><b>Email:</b> <span class="text-orange-400">${email}</span></div>
                            <div class="mt-1"><b>Password Sistem:</b> <span style="background: #EA580C; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold;">${tempPwd}</span></div>
                        </div>
                        <p class="mt-3 text-xs text-amber-400">
                            <b>Penting:</b> Pada saat pertama kali login di <code>/login-admin</code>, admin wajib mengisi kode OTP, menentukan <b>Username</b> resmi, serta memperbarui <b>Password</b> awal mereka.
                        </p>
                    </div>
                `,
                icon: 'success',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316',
                confirmButtonText: 'Selesai'
            });
            document.getElementById('newAdminNama').value = '';
            document.getElementById('newAdminEmail').value = '';
            document.getElementById('newAdminRole').value = '';
            loadAdmins();
            loadAuditLogs();
        } else {
            let errMsg = d.message || 'Terjadi kesalahan.';
            if (d.errors) {
                errMsg = Object.values(d.errors).flat().join('<br>');
            }
            Swal.fire({ title: 'Gagal Membuat Akun', html: errMsg, icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
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
