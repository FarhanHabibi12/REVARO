@extends('layouts.superadmin')

@section('title', 'Anti-Hack & Keamanan — REVARO')
@section('page-title', '🛡️ Anti-Hack & Keamanan')
@section('page-subtitle', 'Blokir IP address mencurigakan, kelola pembatasan akun, dan pantau log aktivitas keamanan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ban IP Form (Left/Top) -->
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-6">
            <h3 class="text-sm font-extrabold text-white mb-4">Blokir IP Address Baru</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">IP Address</label>
                    <input type="text" id="blacklistIp" class="input-field" placeholder="cth. 192.168.1.100">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Alasan Pemblokiran</label>
                    <textarea id="blacklistAlasan" rows="3" class="input-field" placeholder="cth. Percobaan SQL Injection / Brute Force"></textarea>
                </div>
                <button onclick="banIpAddress()" class="w-full btn-primary font-bold">🚫 Blokir IP Sekarang</button>
            </div>
        </div>

        <!-- IP Blacklist Table -->
        <div class="card p-6">
            <h3 class="text-sm font-extrabold text-white mb-3 flex justify-between items-center">
                <span>Daftar IP Terblokir</span>
                <span id="ipCount" class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono">0</span>
            </h3>
            <div class="overflow-y-auto max-h-72">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-[10px] py-2">IP</th>
                            <th class="text-[10px] py-2">Alasan</th>
                            <th class="text-[10px] py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ipTableBody" class="text-xs">
                        <tr><td colspan="3" class="text-center text-slate-500 py-4">Memuat data IP...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Account Banning & Security Logs (Right/Bottom) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- User list card -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-extrabold text-white">Kelola Status Keaktifan Akun</h3>
                <input type="text" id="searchUser" class="input-field" style="width:200px;" placeholder="🔍 Cari email/nama..." oninput="filterUserTable()">
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="data-table w-full" id="userTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="5" class="text-center text-slate-500 py-8">Memuat data user...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Security activity logs -->
        <div class="card p-6">
            <h3 class="text-sm font-extrabold text-white mb-4">Log Keamanan Terbaru (Audit Trail)</h3>
            <div class="overflow-y-auto max-h-72">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-[10px]">Waktu</th>
                            <th class="text-[10px]">Pelaku</th>
                            <th class="text-[10px]">Aktivitas</th>
                            <th class="text-[10px]">IP</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody" class="text-[11px] text-slate-300">
                        <tr><td colspan="4" class="text-center text-slate-500 py-4">Memuat audit log...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('admin_token');

    async function loadIpBlacklist() {
        try {
            const res = await fetch('/api/admin/security/blacklist', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            document.getElementById('ipCount').innerText = data.length;
            const tbody = document.getElementById('ipTableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-slate-500 py-4">Tidak ada IP yang diblokir</td></tr>';
            } else {
                tbody.innerHTML = data.map(item => `
                    <tr class="hover:bg-slate-800/20">
                        <td class="font-mono text-brandOrange py-2 font-bold">${item.ip_address}</td>
                        <td class="py-2">${item.alasan || 'Tidak ada alasan'}</td>
                        <td class="py-2 text-right">
                            <button onclick="unbanIpAddress(${item.id})" class="text-xs text-emerald-500 hover:text-emerald-400 font-bold transition">Buka</button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function banIpAddress() {
        const ip = document.getElementById('blacklistIp').value.trim();
        const alasan = document.getElementById('blacklistAlasan').value.trim();

        if (!ip) {
            Swal.fire({ title: 'Gagal', text: 'IP Address wajib diisi.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#FF6A00' });
            return;
        }

        try {
            const res = await fetch('/api/admin/security/blacklist', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ip_address: ip, alasan: alasan })
            });
            const data = await res.json();
            if (res.ok) {
                Swal.fire({ title: 'IP Diblokir!', text: data.message, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#FF6A00' });
                document.getElementById('blacklistIp').value = '';
                document.getElementById('blacklistAlasan').value = '';
                loadIpBlacklist();
                loadSecurityLogs();
            } else {
                Swal.fire({ title: 'Gagal', text: data.message || JSON.stringify(data.errors), icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#FF6A00' });
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function unbanIpAddress(id) {
        try {
            const res = await fetch(`/api/admin/security/blacklist/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (res.ok) {
                Swal.fire({ title: 'Buka Blokir Sukses', text: data.message, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#FF6A00' });
                loadIpBlacklist();
                loadSecurityLogs();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function loadUsers() {
        try {
            const res = await fetch('/api/admin/security/users', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            const tbody = document.getElementById('userTableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-slate-500 py-8">Tidak ada user lain ditemukan</td></tr>';
            } else {
                tbody.innerHTML = data.map(u => {
                    const statusClass = u.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500';
                    const statusLabel = u.is_active ? 'ACTIVE' : 'BANNED';
                    const actionLabel = u.is_active ? '🛑 Blokir Akun' : '✅ Aktifkan';
                    const actionBtnClass = u.is_active ? 'text-red-500 hover:text-red-400' : 'text-emerald-500 hover:text-emerald-400';
                    return `
                        <tr class="hover:bg-slate-800/15">
                            <td class="font-semibold text-white">${u.nama}</td>
                            <td class="text-slate-400 font-mono">${u.email}</td>
                            <td><span class="bg-blue-500/10 text-blue-400 px-2 py-0.5 rounded text-[10px] font-bold">${u.role ? u.role.nama : '-'}</span></td>
                            <td><span class="px-2 py-0.5 rounded text-[10px] font-bold ${statusClass}">${statusLabel}</span></td>
                            <td class="text-right">
                                <button onclick="toggleUserBan(${u.id})" class="text-xs font-bold transition ${actionBtnClass}">${actionLabel}</button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function toggleUserBan(id) {
        try {
            const res = await fetch(`/api/admin/security/users/${id}/toggle-ban`, {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (res.ok) {
                Swal.fire({ title: 'Status Berubah!', text: data.message, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#FF6A00' });
                loadUsers();
                loadSecurityLogs();
            } else {
                Swal.fire({ title: 'Gagal', text: data.message, icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#FF6A00' });
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function loadSecurityLogs() {
        try {
            const res = await fetch('/api/admin/security/logs', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            const tbody = document.getElementById('logsTableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-slate-500 py-4">Belum ada log aktivitas keamanan</td></tr>';
            } else {
                tbody.innerHTML = data.map(log => `
                    <tr class="hover:bg-slate-800/10">
                        <td class="text-slate-500 font-mono py-1.5">${new Date(log.created_at).toLocaleString('id-ID')}</td>
                        <td class="font-semibold text-slate-200">${log.user ? log.user.nama : 'System'}</td>
                        <td>${log.aktivitas}</td>
                        <td class="font-mono text-slate-400">${log.ip_address || '-'}</td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error(e);
        }
    }

    function filterUserTable() {
        const term = document.getElementById('searchUser').value.toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    }

    function initPage() {
        loadIpBlacklist();
        loadUsers();
        loadSecurityLogs();
    }
</script>
@endsection
