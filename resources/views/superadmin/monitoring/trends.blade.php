@extends('layouts.superadmin')

@section('title', 'Tren & Analitik — REVARO')
@section('page-title', '📈 Tren & Analitik Platform')
@section('page-subtitle', 'Pantau tren registrasi mitra, volume dispute, dan pola kinerja operasional secara berkala')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Period -->
    <div class="flex gap-3 items-center">
        <label class="text-xs font-bold text-slate-400">Periode:</label>
        <select id="periodSelect" class="input-field" style="width:180px;" onchange="loadAll()">
            <option value="7">7 Hari</option>
            <option value="30" selected>30 Hari</option>
            <option value="90">90 Hari</option>
        </select>
    </div>

    <!-- KPI Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-5 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Teknisi Baru</p>
            <p class="text-2xl font-black text-blue-400 mt-1" id="trendTeknisiBaru">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Toko Baru</p>
            <p class="text-2xl font-black text-purple-400 mt-1" id="trendTokoBaru">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Dispute Baru</p>
            <p class="text-2xl font-black text-red-400 mt-1" id="trendDispute">0</p>
        </div>
        <div class="card p-5 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Pelanggaran Log</p>
            <p class="text-2xl font-black text-orange-400 mt-1" id="trendPelanggaran">0</p>
        </div>
    </div>

    <!-- Registration Trend Chart -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tren Registrasi Mitra Baru</h3>
        <div class="h-64 relative">
            <canvas id="regChart"></canvas>
        </div>
    </div>

    <!-- Dispute Trend Chart -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tren Dispute vs Pelanggaran</h3>
        <div class="h-64 relative">
            <canvas id="disputeChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let regChartInst, disputeChartInst;

async function loadAll() {
    const token = localStorage.getItem('admin_token');
    const days = document.getElementById('periodSelect').value;
    const h = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    try {
        const r = await fetch(`/api/superadmin/trends?days=${days}`, { headers: h });
        const d = await r.json();

        document.getElementById('trendTeknisiBaru').textContent = d.teknisi_baru ?? 0;
        document.getElementById('trendTokoBaru').textContent = d.toko_baru ?? 0;
        document.getElementById('trendDispute').textContent = d.dispute_baru ?? 0;
        document.getElementById('trendPelanggaran').textContent = d.pelanggaran_baru ?? 0;

        const labels = d.labels || [];
        if (regChartInst) regChartInst.destroy();
        regChartInst = new Chart(document.getElementById('regChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Teknisi', data: d.teknisi_series || [], borderColor: '#3B82F6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, fill: true },
                    { label: 'Toko', data: d.toko_series || [], borderColor: '#A855F7', backgroundColor: 'rgba(168,85,247,0.1)', tension: 0.4, fill: true }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#94A3B8' } } }, scales: { x: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } }, y: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } } } }
        });

        if (disputeChartInst) disputeChartInst.destroy();
        disputeChartInst = new Chart(document.getElementById('disputeChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Dispute', data: d.dispute_series || [], backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 4 },
                    { label: 'Pelanggaran', data: d.pelanggaran_series || [], backgroundColor: 'rgba(249,115,22,0.7)', borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#94A3B8' } } }, scales: { x: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } }, y: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } } } }
        });
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', loadAll);
</script>
@endsection
