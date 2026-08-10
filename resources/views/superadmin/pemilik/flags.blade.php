@extends('layouts.superadmin')

@section('title', 'Flag Notifikasi — REVARO')
@section('page-title', '🚩 Flag Notifikasi untuk Admin')
@section('page-subtitle', 'Kirim peringatan resmi kepada Admin Verifikator atau Resolusi berdasarkan laporan monitoring')

@section('content')
<div class="space-y-6">
    <!-- Unread Flags from Monitoring -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-sm font-extrabold text-white">Inbox Flag dari Monitoring Kinerja</h3>
                <p class="text-xs text-slate-500 mt-0.5">Flag yang dikirim oleh Super Admin Monitoring untuk ditindaklanjuti</p>
            </div>
            <span class="badge badge-red" id="unreadFlagCount">0 Belum Dibaca</span>
        </div>
        <div id="flagInboxList" class="space-y-3">
            <p class="text-slate-500 text-sm text-center py-6">Memuat...</p>
        </div>
    </div>

    <!-- Send Flag to Admin -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Kirim Flag ke Admin Internal</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tujuan Admin</label>
                <select id="flagTarget" class="input-field">
                    <option value="">— Pilih Admin —</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Judul Flag</label>
                <input type="text" id="flagJudul" class="input-field" placeholder="cth. Tindak Lanjut Laporan Teknisi">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Pesan</label>
                <input type="text" id="flagPesan" class="input-field" placeholder="Detail pesan peringatan...">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="sendFlag()" class="btn-primary">🚩 Kirim Flag</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadFlagInbox() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/flags', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const flags = await r.json();
        const unread = flags.filter(f => !f.dibaca).length;
        document.getElementById('unreadFlagCount').textContent = unread + ' Belum Dibaca';
        const container = document.getElementById('flagInboxList');
        if (!flags.length) { container.innerHTML = '<p class="text-slate-500 text-sm text-center py-6">Tidak ada flag.</p>'; return; }
        container.innerHTML = flags.map(f => `
            <div class="flex gap-4 p-4 rounded-xl border ${f.dibaca ? 'border-slate-800 bg-slate-900/30' : 'border-orange-500/30 bg-orange-500/5'}">
                <div class="text-2xl">${f.dibaca ? '📩' : '🚩'}</div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <p class="font-bold text-sm ${f.dibaca ? 'text-slate-300' : 'text-white'}">${f.judul}</p>
                        <span class="text-[10px] text-slate-500">${new Date(f.created_at).toLocaleString('id-ID')}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">${f.pesan}</p>
                    ${!f.dibaca ? `<button onclick="markRead(${f.id})" class="btn-outline mt-2" style="font-size:0.65rem;padding:3px 10px;">Tandai Dibaca</button>` : ''}
                </div>
            </div>
        `).join('');
    } catch(e) {}
}

async function markRead(id) {
    const token = localStorage.getItem('admin_token');
    try {
        await fetch(`/api/admin/flags/${id}/read`, { method: 'PATCH', headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        loadFlagInbox();
    } catch(e) {}
}

async function loadAdminOptions() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/accounts', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const admins = await r.json();
        const sel = document.getElementById('flagTarget');
        admins.filter(a => a.is_active).forEach(a => {
            const o = document.createElement('option');
            o.value = a.id;
            o.textContent = `${a.nama} (${a.role})`;
            sel.appendChild(o);
        });
    } catch(e) {}
}

async function sendFlag() {
    const token = localStorage.getItem('admin_token');
    const payload = {
        user_id: document.getElementById('flagTarget').value,
        judul: document.getElementById('flagJudul').value.trim(),
        pesan: document.getElementById('flagPesan').value.trim(),
    };
    if (!payload.user_id || !payload.judul || !payload.pesan) {
        Swal.fire({ title: 'Lengkapi Form', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        return;
    }
    try {
        const r = await fetch('/api/admin/notifications', { method: 'POST', headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
        if (r.ok) {
            Swal.fire({ title: 'Flag Terkirim!', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            ['flagJudul','flagPesan'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('flagTarget').value = '';
        }
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => { loadFlagInbox(); loadAdminOptions(); });
</script>
@endsection
