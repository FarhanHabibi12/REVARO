@extends('layouts.superadmin')

@section('title', 'Instruksi & Peringatan Staf — REVARO')
@section('page-title', 'Instruksi & Peringatan Staf Operasional')
@section('page-subtitle', 'Terbitkan arahan resmi, instruksi langsung, atau peringatan dari Pemilik Sistem kepada Admin Verifikator dan Admin Resolusi')

@section('content')
<div class="space-y-6">
    <!-- Log Flags & Directives -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4 border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    <span>Log Instruksi &amp; Laporan Staf</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar instruksi pengawasan Pemilik dan laporan monitoring staf admin</p>
            </div>
            <span class="badge badge-red" id="unreadFlagCount">0 Belum Ditindaklanjuti</span>
        </div>
        <div id="flagInboxList" class="space-y-3">
            <p class="text-slate-500 text-sm text-center py-6">Memuat...</p>
        </div>
    </div>

    <!-- Send Flag to Admin -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Terbitkan Instruksi / Peringatan Baru ke Staf Admin</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tujuan Admin Staf</label>
                <select id="flagTarget" class="input-field">
                    <option value="">— Pilih Admin Staf —</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Judul Instruksi / Peringatan</label>
                <input type="text" id="flagJudul" class="input-field" placeholder="cth. Tindak Lanjut Prioritas Sengketa SLA">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Isi Instruksi / Arahan</label>
                <input type="text" id="flagPesan" class="input-field" placeholder="Rincian arahan dari Pemilik Platform...">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="sendFlag()" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                <span>Terbitkan Instruksi</span>
            </button>
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
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ${f.dibaca ? 'bg-slate-800 text-slate-400' : 'bg-orange-500/20 text-orange-400'}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <p class="font-bold text-sm ${f.dibaca ? 'text-slate-300' : 'text-white'}">${f.judul}</p>
                        <span class="text-[10px] text-slate-500 font-mono">${new Date(f.created_at).toLocaleString('id-ID')}</span>
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
