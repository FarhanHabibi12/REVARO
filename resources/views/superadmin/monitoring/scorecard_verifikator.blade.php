@extends('layouts.superadmin')

@section('title', 'Scorecard Verifikator — REVARO')
@section('page-title', '📋 Scorecard Admin Verifikator')
@section('page-subtitle', 'Evaluasi kinerja harian admin verifikator — volume proses, akurasi keputusan, dan waktu rata-rata')

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
        <h3 class="text-sm font-extrabold text-white mb-4">Kinerja Admin Verifikator</h3>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Total Diproses</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Revisi</th>
                        <th>Avg. Waktu Proses</th>
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

    <!-- Individual Detail -->
    <div class="card p-6" id="detailPanel" style="display:none;">
        <h3 class="text-sm font-extrabold text-white mb-4">Detail Kinerja: <span id="detailName"></span></h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="detailCards"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadScorecard() {
    const token = localStorage.getItem('admin_token');
    const days = document.getElementById('periodSelect').value;
    try {
        const r = await fetch(`/api/superadmin/scorecard-verifikator?days=${days}`, { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const data = await r.json();
        const tbody = document.getElementById('scorecardBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="8" class="text-center text-slate-500 py-8">Tidak ada data verifikator</td></tr>'; return; }
        tbody.innerHTML = data.map(a => {
            const skor = parseFloat(a.skor_kinerja || 0);
            const scoreBadge = skor >= 80 ? 'badge-green' : skor >= 60 ? 'badge-yellow' : 'badge-red';
            const scoreLabel = skor >= 80 ? 'Baik' : skor >= 60 ? 'Cukup' : 'Perlu Perbaikan';
            return `
                <tr style="cursor:pointer;" onclick="showDetail('${a.nama}', ${JSON.stringify(a).replace(/"/g,'&quot;')})">
                    <td class="font-semibold text-white">${a.nama}</td>
                    <td class="text-center text-white font-bold">${a.total_proses ?? 0}</td>
                    <td class="text-center text-emerald-400 font-bold">${a.approved ?? 0}</td>
                    <td class="text-center text-red-400 font-bold">${a.rejected ?? 0}</td>
                    <td class="text-center text-yellow-400 font-bold">${a.revisi ?? 0}</td>
                    <td class="text-slate-400 text-center">${a.avg_waktu_jam ? a.avg_waktu_jam + ' jam' : '—'}</td>
                    <td class="text-center font-black text-lg ${skor >= 80 ? 'text-emerald-400' : skor >= 60 ? 'text-yellow-400' : 'text-red-400'}">${skor}%</td>
                    <td><span class="badge ${scoreBadge}">${scoreLabel}</span></td>
                </tr>
            `;
        }).join('');
    } catch(e) {
        document.getElementById('scorecardBody').innerHTML = '<tr><td colspan="8" class="text-center text-red-400 py-8">Gagal memuat data scorecard</td></tr>';
    }
}

function showDetail(nama, data) {
    document.getElementById('detailName').textContent = nama;
    document.getElementById('detailPanel').style.display = 'block';
    document.getElementById('detailCards').innerHTML = `
        <div class="card p-4 text-center"><p class="text-[9px] font-bold text-slate-400 uppercase">Total Proses</p><p class="text-2xl font-black text-white mt-1">${data.total_proses ?? 0}</p></div>
        <div class="card p-4 text-center"><p class="text-[9px] font-bold text-slate-400 uppercase">Tingkat Approve</p><p class="text-2xl font-black text-emerald-400 mt-1">${data.total_proses ? Math.round((data.approved/data.total_proses)*100) : 0}%</p></div>
        <div class="card p-4 text-center"><p class="text-[9px] font-bold text-slate-400 uppercase">Avg. Waktu</p><p class="text-2xl font-black text-blue-400 mt-1">${data.avg_waktu_jam ?? '—'} jam</p></div>
        <div class="card p-4 text-center"><p class="text-[9px] font-bold text-slate-400 uppercase">Skor Kinerja</p><p class="text-2xl font-black text-brandOrange mt-1">${data.skor_kinerja ?? 0}%</p></div>
    `;
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', loadScorecard);
</script>
@endsection
