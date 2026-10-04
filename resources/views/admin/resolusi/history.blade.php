@extends('layouts.admin')

@section('title', 'Riwayat Resolusi Saya — Resolusi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-black text-white">5. Riwayat Resolusi Sengketa Saya</h2>
            <p class="text-xs text-slate-400">Log semua kasus sengketa garansi yang berhasil Anda putuskan dan selesaikan.</p>
        </div>
        <button onclick="loadHistoryData()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-2 px-4 rounded-xl transition shadow flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Refresh History</span>
        </button>
    </div>

    <!-- History Table -->
    <div class="bg-slate-900/40 rounded-2xl p-6 border border-slate-800 space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                <span>Riwayat Kasus Selesai</span>
            </h3>
            <input type="text" id="searchHistory" class="text-xs bg-slate-950 border border-slate-800 rounded-xl p-2.5 outline-none text-white focus:border-brandOrange transition w-64" placeholder="Cari kode order..." oninput="filterHistory()">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="pb-3">Kasus ID</th>
                        <th class="pb-3">Kode Order</th>
                        <th class="pb-3">Pelapor</th>
                        <th class="pb-3">Keputusan Resolusi</th>
                        <th class="pb-3">Alasan Sengketa</th>
                        <th class="pb-3">Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="text-xs text-slate-300 divide-y divide-slate-800/50">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat riwayat resolusi...</td></tr>
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
        const r = await fetch('/api/disputes/my-decisions', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        allHistory = await r.json();
        renderHistoryTable(allHistory);
    } catch (e) {
        console.error(e);
        document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-red-400 py-8">Gagal memuat riwayat resolusi</td></tr>';
    }
}

function renderHistoryTable(data) {
    const tbody = document.getElementById('historyTableBody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada kasus sengketa yang terselesaikan</td></tr>';
        return;
    }

    const decisionLabels = {
        'REFUND_PELANGGAN': 'Refund Pelanggan',
        'RELEASE_TEKNISI': 'Cairkan ke Mitra',
        'KUNJUNGAN_ULANG': 'Kunjungan Ulang',
        'FREEZE_AKUN': 'Freeze Akun Pelaku'
    };

    const badgeColor = {
        'REFUND_PELANGGAN': 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        'RELEASE_TEKNISI': 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        'KUNJUNGAN_ULANG': 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
        'FREEZE_AKUN': 'bg-red-500/10 text-red-400 border border-red-500/20'
    };

    tbody.innerHTML = data.map(h => {
        const decisionText = decisionLabels[h.keputusan] || h.keputusan;
        return `
            <tr class="hover:bg-slate-800/30">
                <td class="py-3.5 font-bold text-slate-500">#${h.id}</td>
                <td class="py-3.5 font-mono font-semibold text-white">${h.order?.kode_order || '—'}</td>
                <td class="py-3.5 text-slate-400">${h.order?.pelanggan?.nama || '—'}</td>
                <td class="py-3.5">
                    <span class="px-2 py-1 rounded-md text-[9px] font-black tracking-wide uppercase ${badgeColor[h.keputusan] || 'bg-slate-800 text-slate-400'}">
                        ${decisionText}
                    </span>
                </td>
                <td class="py-3.5 text-slate-400 italic max-w-xs truncate" title="${h.alasan}">${h.alasan}</td>
                <td class="py-3.5 text-slate-500">${new Date(h.resolved_at || h.updated_at).toLocaleString('id-ID')}</td>
            </tr>
        `;
    }).join('');
}

function filterHistory() {
    const q = document.getElementById('searchHistory').value.toLowerCase();
    const filtered = allHistory.filter(h => {
        const orderCode = (h.order?.kode_order || '').toLowerCase();
        return orderCode.includes(q);
    });
    renderHistoryTable(filtered);
}

function initPage() {
    loadHistoryData();
}
</script>
@endsection
