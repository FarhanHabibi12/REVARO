@extends('layouts.superadmin')

@section('title', 'Peringatan SLA — REVARO')
@section('page-title', '🔔 Peringatan SLA & Anomali')
@section('page-subtitle', 'Pantau pelanggaran SLA, dispute yang terbengkalai, dan anomali kinerja operasional')

@section('content')
<div class="space-y-6">
    <!-- Alert Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-5 relative overflow-hidden" style="border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);">
            <div class="absolute right-3 top-3 text-3xl opacity-20">🆘</div>
            <p class="text-[9px] font-bold text-red-400 uppercase tracking-wider">Kritis — SLA Breach</p>
            <p class="text-3xl font-black text-red-400 mt-1" id="alertKritis">0</p>
            <p class="text-[10px] text-slate-500 mt-1">Dispute sudah lewat deadline</p>
        </div>
        <div class="card p-5 relative overflow-hidden" style="border-color:rgba(234,179,8,0.3);background:rgba(234,179,8,0.05);">
            <div class="absolute right-3 top-3 text-3xl opacity-20">⚠️</div>
            <p class="text-[9px] font-bold text-yellow-400 uppercase tracking-wider">Peringatan — Hampir Breach</p>
            <p class="text-3xl font-black text-yellow-400 mt-1" id="alertWarning">0</p>
            <p class="text-[10px] text-slate-500 mt-1">SLA deadline < 6 jam lagi</p>
        </div>
        <div class="card p-5 relative overflow-hidden" style="border-color:rgba(59,130,246,0.3);background:rgba(59,130,246,0.05);">
            <div class="absolute right-3 top-3 text-3xl opacity-20">ℹ️</div>
            <p class="text-[9px] font-bold text-blue-400 uppercase tracking-wider">Info — Eskalasi Pending</p>
            <p class="text-3xl font-black text-blue-400 mt-1" id="alertEskalasi">0</p>
            <p class="text-[10px] text-slate-500 mt-1">Belum diputuskan pemilik</p>
        </div>
    </div>

    <!-- SLA Breach List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Pelanggaran & Ancaman SLA</h3>
            <button onclick="loadAlerts()" class="btn-outline" style="font-size:0.72rem;padding:6px 14px;">🔄 Refresh</button>
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
    const token = localStorage.getItem('admin_token');
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
        if (!data.length) { container.innerHTML = '<div class="text-center py-8"><span class="text-3xl">✅</span><p class="text-emerald-400 font-bold mt-2">Tidak ada pelanggaran SLA aktif</p></div>'; return; }

        container.innerHTML = data.map(d => {
            const deadline = new Date(d.sla_deadline);
            const isBreached = deadline < now;
            const hoursLeft = Math.round((deadline - now) / 3600000);
            const alertColor = isBreached ? 'border-red-500/30 bg-red-500/5' : hoursLeft < 6 ? 'border-yellow-500/30 bg-yellow-500/5' : 'border-slate-700/50 bg-slate-800/30';
            const icon = isBreached ? '🆘' : hoursLeft < 6 ? '⚠️' : '🕐';
            return `
                <div class="flex gap-4 p-4 rounded-xl border ${alertColor}">
                    <span class="text-xl">${icon}</span>
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
