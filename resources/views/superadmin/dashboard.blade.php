@extends('layouts.superadmin')

@section('title', 'Monitoring kinerja')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Monitoring kinerja</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau performa admin secara objektif — tanpa membuka detail kasus individual.</p>
    </div>
    <div class="hidden sm:flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-medium bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full">
            <i class="ti ti-shield-lock text-sm"></i> Akses super admin
        </span>
        <button type="button" onclick="document.getElementById('logout-modal').classList.remove('hidden'); document.getElementById('logout-modal').classList.add('flex');"
                class="inline-flex items-center gap-1.5 text-[11px] font-medium bg-red-50 text-red-600 px-3 py-1.5 rounded-full hover:bg-red-100">
            <i class="ti ti-logout text-sm"></i> Keluar
        </button>
    </div>
</div>

{{-- Metric cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl card-soft p-5">
        <p class="text-xs text-slate-400 mb-2">Rata-rata SLA sengketa</p>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-semibold text-slate-900">{{ $rataRataSla ?? '36 jam' }}</p>
            <span class="text-[11px] font-medium text-emerald-600 flex items-center gap-0.5"><i class="ti ti-trending-down text-sm"></i>8%</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl card-soft p-5">
        <p class="text-xs text-slate-400 mb-2">Sengketa selesai</p>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-semibold text-slate-900">{{ $tingkatSelesai ?? '92%' }}</p>
            <span class="text-[11px] font-medium text-emerald-600 flex items-center gap-0.5"><i class="ti ti-trending-up text-sm"></i>3%</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl card-soft p-5">
        <p class="text-xs text-slate-400 mb-2">Verifikasi diproses</p>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-semibold text-slate-900">{{ $verifikasiDiproses ?? '148' }}</p>
            <span class="text-[11px] font-medium text-slate-400">bulan ini</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl card-soft p-5">
        <p class="text-xs text-slate-400 mb-2">Alert aktif</p>
        <div class="flex items-end justify-between">
            <p class="text-2xl font-semibold text-red-600">{{ $alertAktif ?? '1' }}</p>
            <span class="text-[11px] font-medium text-red-500 flex items-center gap-0.5"><i class="ti ti-alert-circle text-sm"></i>perlu ditinjau</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Scorecard + chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl card-soft p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-slate-900">Scorecard per admin</h2>
                <p class="text-xs text-slate-400 mt-0.5">Volume, kecepatan, dan kepatuhan SLA</p>
            </div>
        </div>

        <div class="mb-6" style="height:180px">
            <canvas id="slaChart"></canvas>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-100 text-[11px] uppercase tracking-wide">
                    <th class="pb-2 font-medium">Admin</th>
                    <th class="pb-2 font-medium">Peran</th>
                    <th class="pb-2 font-medium">Volume</th>
                    <th class="pb-2 font-medium">Rata-rata waktu</th>
                    <th class="pb-2 font-medium">Kepatuhan SLA</th>
                </tr>
            </thead>
            <tbody>
                @forelse (($adminScorecard ?? []) as $row)
                    <tr class="border-b border-slate-50 last:border-0">
                        <td class="py-3 font-medium text-slate-800">{{ $row->nama }}</td>
                        <td class="py-3 text-slate-500">{{ $row->peran }}</td>
                        <td class="py-3 text-slate-500">{{ $row->volume }}</td>
                        <td class="py-3 text-slate-500">{{ $row->rata_rata_waktu }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $row->kepatuhan_sla >= 90 ? 'bg-emerald-500' : ($row->kepatuhan_sla >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                                         style="width: {{ $row->kepatuhan_sla }}%"></div>
                                </div>
                                <span class="text-xs font-medium {{ $row->kepatuhan_sla >= 90 ? 'text-emerald-600' : ($row->kepatuhan_sla >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $row->kepatuhan_sla }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="border-b border-slate-50">
                        <td class="py-3 font-medium text-slate-800">Dewi</td>
                        <td class="py-3 text-slate-500">Verifikator</td>
                        <td class="py-3 text-slate-500">62</td>
                        <td class="py-3 text-slate-500">2 jam</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full bg-emerald-500" style="width:96%"></div></div>
                                <span class="text-xs font-medium text-emerald-600">96%</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium text-slate-800">Rian</td>
                        <td class="py-3 text-slate-500">Resolusi</td>
                        <td class="py-3 text-slate-500">18</td>
                        <td class="py-3 text-slate-500">44 jam</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full bg-red-500" style="width:70%"></div></div>
                                <span class="text-xs font-medium text-red-600">70%</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Alert panel --}}
    <div class="bg-white rounded-2xl card-soft p-6">
        <h2 class="font-semibold text-slate-900 mb-5">Alert anomali</h2>

        <div class="flex flex-col gap-3">
            @forelse (($alerts ?? []) as $alert)
                <div class="flex items-start gap-2.5 bg-red-50/60 border border-red-100 rounded-xl p-3.5">
                    <i class="ti ti-alert-triangle text-red-500 mt-0.5"></i>
                    <p class="text-xs text-red-700 leading-relaxed">{{ $alert->pesan }}</p>
                </div>
            @empty
                <div class="flex items-start gap-2.5 bg-red-50/60 border border-red-100 rounded-xl p-3.5">
                    <i class="ti ti-alert-triangle text-red-500 mt-0.5"></i>
                    <p class="text-xs text-red-700 leading-relaxed">Rian melewati SLA 48 jam pada 2 kasus minggu ini.</p>
                </div>
                <p class="text-xs text-slate-400 text-center pt-1">Tidak ada anomali lain terdeteksi.</p>
            @endforelse
        </div>

        <button class="w-full mt-5 text-xs px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">
            Kirim laporan ke pemilik sistem
        </button>
    </div>

</div>

<script>
new Chart(document.getElementById('slaChart'), {
    type: 'line',
    data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [{
            label: 'Rata-rata SLA (jam)',
            data: [40, 38, 41, 36, 34, 33, 36],
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            tension: 0.35,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#6366f1',
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#94a3b8' } },
            x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8' } }
        },
        maintainAspectRatio: false
    }
});
</script>
@endsection