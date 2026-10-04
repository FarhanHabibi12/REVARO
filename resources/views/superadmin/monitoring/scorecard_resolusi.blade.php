@extends('layouts.superadmin')

@section('title', 'Scorecard Resolusi — REVARO')
@section('page-title', 'Scorecard Admin Resolusi')
@section('page-subtitle', 'Evaluasi kinerja admin resolusi — volume dispute, tingkat penyelesaian, dan kepatuhan SLA')

@section('content')
<div class="space-y-6">
    <!-- Period Selector -->
    <div class="flex gap-3 items-center">
        <label class="text-xs font-bold text-slate-400">Periode:</label>
        <select id="periodSelect" class="input-field" style="width:180px;" onchange="loadScorecard()">
            <option value="7">7 Hari Terakhir</option>
            <option value="30" selected>30 Hari Terakhir</option>
            <option value="90">90 Hari Terakhir</option>
        </select>
    </div>

    <!-- Scorecard Table -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Kinerja Admin Resolusi</h3>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Total Ditangani</th>
                        <th>Resolved</th>
                        <th>Dieskalasi</th>
                        <th>Pelanggaran SLA</th>
                        <th>Avg. Waktu Selesai</th>
                        <th>Skor Kinerja</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="scorecardBody">
                    <tr><td colspan="8" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SLA Compliance Chart -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tren Kepatuhan SLA (Per Admin)</h3>
        <div class="h-64 relative">
            <canvas id="slaChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('scripts')
<script>
async function loadScorecard() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const days = document.getElementById('periodSelect').value;
    try {
        const r = await fetch(`/api/superadmin/scorecard-resolusi?days=${days}`, { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const data = await r.json();
        const tbody = document.getElementById('scorecardBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="8" class="text-center text-slate-500 py-8">Tidak ada data resolusi</td></tr>'; return; }
        tbody.innerHTML = data.map(a => {
            const skor = parseFloat(a.skor_kinerja || 0);
            const scoreBadge = skor >= 80 ? 'badge-green' : skor >= 60 ? 'badge-yellow' : 'badge-red';
            return `
                <tr>
                    <td class="font-semibold text-white">${a.nama}</td>
                    <td class="text-center text-white font-bold">${a.total_ditangani ?? 0}</td>
                    <td class="text-center text-emerald-400 font-bold">${a.resolved ?? 0}</td>
                    <td class="text-center text-yellow-400 font-bold">${a.eskalasi ?? 0}</td>
                    <td class="text-center text-red-400 font-bold">${a.sla_breached ?? 0}</td>
                    <td class="text-slate-400 text-center">${a.avg_waktu_jam ? a.avg_waktu_jam + ' jam' : '—'}</td>
                    <td class="text-center font-black text-lg ${skor >= 80 ? 'text-emerald-400' : skor >= 60 ? 'text-yellow-400' : 'text-red-400'}">${skor}%</td>
                    <td><span class="badge ${scoreBadge}">${skor >= 80 ? 'Baik' : skor >= 60 ? 'Cukup' : 'Perlu Perbaikan'}</span></td>
                </tr>
            `;
        }).join('');

        // Build SLA chart
        if (data.length) {
            new Chart(document.getElementById('slaChart'), {
                type: 'bar',
                data: {
                    labels: data.map(a => a.nama),
                    datasets: [
                        { label: 'Tepat SLA', data: data.map(a => a.resolved ?? 0), backgroundColor: 'rgba(16,185,129,0.7)', borderRadius: 6 },
                        { label: 'Breach SLA', data: data.map(a => a.sla_breached ?? 0), backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 6 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#94A3B8' } } }, scales: { x: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } }, y: { ticks: { color: '#64748B' }, grid: { color: 'rgba(71,85,105,0.2)' } } } }
            });
        }
    } catch(e) {
        document.getElementById('scorecardBody').innerHTML = '<tr><td colspan="8" class="text-center text-red-400 py-8">Gagal memuat data scorecard</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', loadScorecard);
</script>
@endsection
