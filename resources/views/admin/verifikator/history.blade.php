@extends('layouts.admin')

@section('title', 'Riwayat Keputusan Saya — Verifikator')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-black text-white">📜 5. Riwayat Keputusan Verifikasi Saya</h2>
            <p class="text-xs text-slate-400">Log semua verifikasi onboarding yang pernah Anda ambil keputusannya.</p>
        </div>
        <button onclick="loadHistoryData()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-2 px-4 rounded-xl transition shadow">
            🔄 Refresh History
        </button>
    </div>

    <!-- History Table -->
    <div class="bg-slate-900/40 rounded-2xl p-6 border border-slate-800 space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <span>📋</span> Riwayat Audit Keputusan
            </h3>
            <input type="text" id="searchHistory" class="text-xs bg-slate-950 border border-slate-800 rounded-xl p-2.5 outline-none text-white focus:border-brandOrange transition w-64" placeholder="🔍 Cari nama..." oninput="filterHistory()">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="pb-3">Tipe Mitra</th>
                        <th class="pb-3">Nama Mitra</th>
                        <th class="pb-3">Keputusan</th>
                        <th class="pb-3">Catatan Verifikasi</th>
                        <th class="pb-3">Tanggal Keputusan</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="text-xs text-slate-300 divide-y divide-slate-800/50">
                    <tr><td colspan="5" class="text-center text-slate-500 py-8">Memuat riwayat verifikasi...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allHistory = [];

async function loadHistoryData() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/verifications/my-decisions', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        allHistory = await r.json();
        renderHistoryTable(allHistory);
    } catch (e) {
        console.error(e);
        document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-red-400 py-8">Gagal memuat riwayat keputusan</td></tr>';
    }
}

function renderHistoryTable(data) {
    const tbody = document.getElementById('historyTableBody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-slate-500 py-8">Belum ada riwayat keputusan yang tercatat</td></tr>';
        return;
    }

    const badgeColor = {
        'APPROVED': 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        'NEEDS_REVISION': 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        'REJECTED': 'bg-red-500/10 text-red-400 border border-red-500/20'
    };

    tbody.innerHTML = data.map(h => {
        const type = h.teknisi_id ? '🔧 Teknisi' : '🏪 Toko';
        const name = h.teknisi?.nama || h.toko?.nama || '—';
        return `
            <tr class="hover:bg-slate-800/30">
                <td class="py-3.5"><span class="badge font-bold text-[9px]">${type}</span></td>
                <td class="py-3.5 font-semibold text-white">${name}</td>
                <td class="py-3.5">
                    <span class="px-2 py-1 rounded-md text-[9px] font-black tracking-wide uppercase ${badgeColor[h.status] || 'bg-slate-800 text-slate-400'}">
                        ${h.status}
                    </span>
                </td>
                <td class="py-3.5 text-slate-400 italic max-w-sm truncate" title="${h.catatan}">${h.catatan}</td>
                <td class="py-3.5 text-slate-500">${new Date(h.verified_at || h.created_at).toLocaleString('id-ID')}</td>
            </tr>
        `;
    }).join('');
}

function filterHistory() {
    const q = document.getElementById('searchHistory').value.toLowerCase();
    const filtered = allHistory.filter(h => {
        const name = (h.teknisi?.nama || h.toko?.nama || '').toLowerCase();
        return name.includes(q);
    });
    renderHistoryTable(filtered);
}

function initPage() {
    loadHistoryData();
}
</script>
@endsection
