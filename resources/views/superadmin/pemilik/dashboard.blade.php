@extends('layouts.superadmin')

@section('title', 'Dashboard Keuangan — REVARO')
@section('page-title', '📊 Dashboard Analitik Keuangan')
@section('page-subtitle', 'Ringkasan GMV, pendapatan komisi bersih, dan transaksi aktif platform')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="card p-6 relative overflow-hidden">
            <div class="absolute right-4 top-4 text-3xl opacity-20">💰</div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gross Merchandise Value</p>
            <h3 class="text-2xl font-black text-white mt-1" id="kpiGmv">Rp 0</h3>
            <p class="text-[10px] text-emerald-400 font-bold mt-1">↑ 12% dari bulan lalu</p>
        </div>
        <div class="card p-6 relative overflow-hidden">
            <div class="absolute right-4 top-4 text-3xl opacity-20">💼</div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendapatan Komisi Bersih</p>
            <h3 class="text-2xl font-black text-brandOrange mt-1" id="kpiKomisi">Rp 0</h3>
            <p class="text-[10px] text-slate-400 font-medium mt-1">Berdasarkan tarif komisi aktif</p>
        </div>
        <div class="card p-6 relative overflow-hidden">
            <div class="absolute right-4 top-4 text-3xl opacity-20">⚡</div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Transaksi Aktif Berjalan</p>
            <h3 class="text-2xl font-black text-white mt-1" id="kpiAktif">0 Pesanan</h3>
            <p class="text-[10px] text-emerald-400 font-bold mt-1">Semua wilayah operasional aktif</p>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Grafik Tren GMV & Komisi Bulanan</h3>
            <span class="text-[9px] bg-orange-500/20 text-orange-400 font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider">2026</span>
        </div>
        <div class="h-72 relative">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Bottom Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Verification Summary -->
        <div class="card p-6">
            <h3 class="text-sm font-extrabold text-white mb-4">📋 Ringkasan Verifikasi Onboarding</h3>
            <div class="grid grid-cols-3 gap-3" id="verifSummary">
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase">Approved</p>
                    <p class="text-xl font-black text-emerald-400" id="verifApproved">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase">Pending</p>
                    <p class="text-xl font-black text-yellow-400" id="verifPending">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase">Rejected</p>
                    <p class="text-xl font-black text-red-400" id="verifRejected">0</p>
                </div>
            </div>
        </div>

        <!-- Dispute Summary -->
        <div class="card p-6">
            <h3 class="text-sm font-extrabold text-white mb-4">⚖️ Ringkasan Dispute & Eskalasi</h3>
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase">Open</p>
                    <p class="text-xl font-black text-red-400" id="dispOpen">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase">Proses</p>
                    <p class="text-xl font-black text-blue-400" id="dispInvest">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[8px] font-bold text-slate-500 uppercase">Resolved</p>
                    <p class="text-xl font-black text-emerald-400" id="dispResolved">0</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('admin_token');
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    // Fetch financial data
    try {
        const r = await fetch('/api/admin/financial-summary', { headers });
        const d = await r.json();
        document.getElementById('kpiGmv').textContent = 'Rp ' + Number(d.gmv ?? 0).toLocaleString('id-ID');
        document.getElementById('kpiKomisi').textContent = 'Rp ' + Number(d.komisi_bersih ?? 0).toLocaleString('id-ID');
        document.getElementById('kpiAktif').textContent = (d.transaksi_aktif ?? 0) + ' Pesanan';
    } catch(e) {}

    // Chart
    try {
        const r = await fetch('/api/admin/monthly-trends', { headers });
        const d = await r.json();
        const labels = d.map ? d.map(x => x.bulan) : ['Jan','Feb','Mar','Apr','Mei','Jun'];
        const gmvData = d.map ? d.map(x => x.gmv) : [0,0,0,0,0,0];
        const komisiData = d.map ? d.map(x => x.komisi) : [0,0,0,0,0,0];
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'GMV', data: gmvData, borderColor: '#3B82F6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, fill: true },
                    { label: 'Komisi', data: komisiData, borderColor: '#F97316', backgroundColor: 'rgba(249,115,22,0.1)', tension: 0.4, fill: true }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#94A3B8', font: { size: 11 } } } }, scales: { x: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } }, y: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } } } }
        });
    } catch(e) {}

    // Verif & Dispute summary
    try {
        const r = await fetch('/api/admin/verification-summary', { headers });
        const d = await r.json();
        document.getElementById('verifApproved').textContent = d.approved ?? 0;
        document.getElementById('verifPending').textContent = d.pending ?? 0;
        document.getElementById('verifRejected').textContent = d.rejected ?? 0;
        document.getElementById('dispOpen').textContent = d.dispute_open ?? 0;
        document.getElementById('dispInvest').textContent = d.dispute_investigating ?? 0;
        document.getElementById('dispResolved').textContent = d.dispute_resolved ?? 0;
    } catch(e) {}
});
</script>
@endsection
