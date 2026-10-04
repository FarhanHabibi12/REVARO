@extends('layouts.superadmin')

@section('title', 'Dashboard Keuangan — REVARO')
@section('page-title', 'Dashboard Analitik Keuangan')
@section('page-subtitle', 'Ringkasan GMV, pendapatan komisi bersih, dan transaksi aktif platform')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="card p-6 relative overflow-hidden">
            <div class="absolute right-4 top-4 text-slate-500 opacity-20">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gross Merchandise Value</p>
            <h3 class="text-2xl font-black text-white mt-1" id="kpiGmv">Rp 0</h3>
            <p class="text-[10px] text-emerald-400 font-bold mt-1">&uarr; 12% dari bulan lalu</p>
        </div>
        <div class="card p-6 relative overflow-hidden">
            <div class="absolute right-4 top-4 text-orange-500 opacity-20">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendapatan Komisi Bersih</p>
            <h3 class="text-2xl font-black text-brandOrange mt-1" id="kpiKomisi">Rp 0</h3>
            <p class="text-[10px] text-slate-400 font-medium mt-1">Berdasarkan tarif komisi aktif</p>
        </div>
        <div class="card p-6 relative overflow-hidden">
            <div class="absolute right-4 top-4 text-blue-500 opacity-20">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Transaksi Aktif Berjalan</p>
            <h3 class="text-2xl font-black text-white mt-1" id="kpiAktif">0 Pesanan</h3>
            <p class="text-[10px] text-emerald-400 font-bold mt-1">Semua wilayah operasional aktif</p>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Grafik Tren GMV &amp; Komisi Bulanan</h3>
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
            <div class="flex items-center gap-2 mb-4 border-b border-slate-800 pb-3">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-sm font-extrabold text-white">Ringkasan Verifikasi Onboarding</h3>
            </div>
            <div class="grid grid-cols-3 gap-3" id="verifSummary">
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Approved</p>
                    <p class="text-xl font-black text-emerald-400 mt-1" id="verifApproved">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Pending</p>
                    <p class="text-xl font-black text-yellow-400 mt-1" id="verifPending">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Rejected</p>
                    <p class="text-xl font-black text-red-400 mt-1" id="verifRejected">0</p>
                </div>
            </div>
        </div>

        <!-- Dispute Summary -->
        <div class="card p-6">
            <div class="flex items-center gap-2 mb-4 border-b border-slate-800 pb-3">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                <h3 class="text-sm font-extrabold text-white">Ringkasan Dispute &amp; Eskalasi</h3>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Open</p>
                    <p class="text-xl font-black text-red-400 mt-1" id="dispOpen">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Proses</p>
                    <p class="text-xl font-black text-blue-400 mt-1" id="dispInvest">0</p>
                </div>
                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Resolved</p>
                    <p class="text-xl font-black text-emerald-400 mt-1" id="dispResolved">0</p>
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
