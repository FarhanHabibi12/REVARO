@extends('layouts.superadmin')

@section('title', 'Peringatan SLA — REVARO')
@section('page-title', 'Peringatan SLA & Anomali')
@section('page-subtitle', 'Pantau pelanggaran SLA, dispute yang terbengkalai, dan anomali kinerja operasional')

@section('content')
<div class="space-y-6">
    <!-- Alert Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-5 relative overflow-hidden" style="border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);">
            <p class="text-[9px] font-bold text-red-400 uppercase tracking-wider">Kritis — SLA Breach</p>
            <p class="text-3xl font-black text-red-400 mt-1" id="alertKritis">0</p>
            <p class="text-[10px] text-slate-500 mt-1">Dispute sudah lewat deadline</p>
        </div>
        <div class="card p-5 relative overflow-hidden" style="border-color:rgba(234,179,8,0.3);background:rgba(234,179,8,0.05);">
            <p class="text-[9px] font-bold text-yellow-400 uppercase tracking-wider">Peringatan — Hampir Breach</p>
            <p class="text-3xl font-black text-yellow-400 mt-1" id="alertWarning">0</p>
            <p class="text-[10px] text-slate-500 mt-1">SLA deadline &lt; 6 jam lagi</p>
        </div>
        <div class="card p-5 relative overflow-hidden" style="border-color:rgba(59,130,246,0.3);background:rgba(59,130,246,0.05);">
            <p class="text-[9px] font-bold text-blue-400 uppercase tracking-wider">Info — Eskalasi Pending</p>
            <p class="text-3xl font-black text-blue-400 mt-1" id="alertEskalasi">0</p>
            <p class="text-[10px] text-slate-500 mt-1">Belum diputuskan pemilik</p>
        </div>
    </div>

    <!-- SLA Breach List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Pelanggaran & Ancaman SLA</h3>
            <button onclick="loadAlerts()" class="btn-outline flex items-center gap-1.5" style="font-size:0.72rem;padding:6px 14px;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Refresh</span>
            </button>
        </div>
        <div id="alertList" class="space-y-3">
            <p class="text-slate-500 text-sm text-center py-6">Memuat peringatan...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadAlerts() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    try {
        const r = await fetch('/api/superadmin/sla-alerts', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const data = await r.json();

        const now = new Date();
        const breached = data.filter(d => new Date(d.sla_deadline) < now);
        const warning = data.filter(d => { const deadline = new Date(d.sla_deadline); return deadline > now && (deadline - now) < 6 * 3600000; });
        const eskalasi = data.filter(d => d.status === 'ESCALATED');

        document.getElementById('alertKritis').textContent = breached.length;
        document.getElementById('alertWarning').textContent = warning.length;
        document.getElementById('alertEskalasi').textContent = eskalasi.length;

        const container = document.getElementById('alertList');
        if (!data.length) { container.innerHTML = '<div class="text-center py-8"><svg class="w-10 h-10 text-emerald-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-emerald-400 font-bold">Tidak ada pelanggaran SLA aktif</p></div>'; return; }

        container.innerHTML = data.map(d => {
            const deadline = new Date(d.sla_deadline);
            const isBreached = deadline < now;
            const hoursLeft = Math.round((deadline - now) / 3600000);
            const alertColor = isBreached ? 'border-red-500/30 bg-red-500/5' : hoursLeft < 6 ? 'border-yellow-500/30 bg-yellow-500/5' : 'border-slate-700/50 bg-slate-800/30';
            const iconSvg = isBreached
                ? `<svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
                : hoursLeft < 6
                ? `<svg class="w-5 h-5 text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
                : `<svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
            return `
                <div class="flex gap-4 p-4 rounded-xl border ${alertColor}">
                    ${iconSvg}
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <p class="font-bold text-sm text-white">Dispute #${d.id} — Order #${d.order_id}</p>
                            <span class="badge ${isBreached ? 'badge-red' : 'badge-yellow'} text-[9px]">${isBreached ? 'BREACH' : hoursLeft + ' jam tersisa'}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">${d.alasan}</p>
                        <p class="text-[10px] text-slate-600 mt-1">SLA Deadline: ${deadline.toLocaleString('id-ID')} · Admin: ${d.admin?.nama || 'Belum ditugaskan'}</p>
                    </div>
                </div>
            `;
        }).join('');
    } catch(e) {
        document.getElementById('alertList').innerHTML = '<p class="text-center text-red-400 py-6">Gagal memuat peringatan</p>';
    }
}

document.addEventListener('DOMContentLoaded', loadAlerts);
</script>
@endsection
