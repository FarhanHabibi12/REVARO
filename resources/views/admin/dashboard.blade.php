@extends('layouts.admin')

@section('title', 'Dashboard admin')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Selamat datang, {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }} 👋</h1>
        <p class="text-sm text-slate-500 mt-1">Berikut ringkasan antrian yang perlu ditindaklanjuti hari ini.</p>
    </div>
    <div class="hidden sm:flex items-center gap-3">
        <p class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</p>
        <button type="button" onclick="document.getElementById('logout-modal').classList.remove('hidden'); document.getElementById('logout-modal').classList.add('flex');"
                class="inline-flex items-center gap-1.5 text-[11px] font-medium bg-red-50 text-red-600 px-3 py-1.5 rounded-full hover:bg-red-100">
            <i class="ti ti-logout text-sm"></i> Keluar
        </button>
    </div>
</div>

{{-- Metric cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl card-soft p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="ti ti-clock-hour-4 text-amber-500 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl card-soft p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <i class="ti ti-alert-triangle text-red-500 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl card-soft p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                <i class="ti ti-check text-teal-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl card-soft p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                <i class="ti ti-users text-slate-600 text-lg"></i>
            </div>
        </div>
        <p class="text-2xl font-semibold text-slate-900">{{ $teknisiAktif ?? 42 }}</p>
        <p class="text-xs text-slate-500 mt-1">Teknisi aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Antrian verifikasi --}}
    <div class="bg-white rounded-2xl card-soft p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-slate-900">Antrian verifikasi</h2>
                <p class="text-xs text-slate-400 mt-0.5">Akun teknisi & toko baru</p>
            </div>
            <span class="text-[11px] font-medium bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full">
                {{ ($verifikasiTeknisi->count() ?? 0) + ($verifikasiToko->count() ?? 0) }} pending
            </span>
        </div>

        <div class="flex flex-col divide-y divide-slate-100">
            @forelse (($verifikasiTeknisi ?? []) as $item)
                <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-semibold text-slate-600">
                            {{ strtoupper(substr($item->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $item->nama }}</p>
                            <p class="text-xs text-slate-400">Teknisi · {{ $item->kategori }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <form method="POST" action="{{ route('admin.verifikasi.teknisi.approve', $item->id) }}">
                            @csrf
                            <button class="text-xs px-3 py-1.5 rounded-lg bg-teal-600 text-white hover:bg-teal-700 font-medium">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.verifikasi.teknisi.reject', $item->id) }}">
                            @csrf
                            <button class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 font-medium">Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
            @endforelse

            @forelse (($verifikasiToko ?? []) as $item)
                <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-semibold text-slate-600">
                            {{ strtoupper(substr($item->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $item->nama }}</p>
                            <p class="text-xs text-slate-400">Toko sparepart · {{ $item->izin_usaha }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <form method="POST" action="{{ route('admin.verifikasi.toko.approve', $item->id) }}">
                            @csrf
                            <button class="text-xs px-3 py-1.5 rounded-lg bg-teal-600 text-white hover:bg-teal-700 font-medium">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.verifikasi.toko.reject', $item->id) }}">
                            @csrf
                            <button class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 font-medium">Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
                @if(($verifikasiTeknisi ?? collect())->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-8">Tidak ada antrian verifikasi. Semua bersih ✨</p>
                @endif
            @endforelse
        </div>
    </div>

    {{-- Antrian sengketa --}}
    <div class="bg-white rounded-2xl card-soft p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-semibold text-slate-900">Antrian sengketa</h2>
                <p class="text-xs text-slate-400 mt-0.5">Klaim garansi yang butuh investigasi</p>
            </div>
            <span class="text-[11px] font-medium bg-red-50 text-red-600 px-2.5 py-1 rounded-full">
                {{ ($disputes ?? collect())->where('status', '!=', 'selesai')->count() }} aktif
            </span>
        </div>

        <div class="flex flex-col divide-y divide-slate-100">
            @forelse (($disputes ?? []) as $dispute)
                <a href="{{ route('admin.sengketa.show', $dispute->id) }}"
                   class="flex items-center justify-between py-3 first:pt-0 last:pb-0 group">
                    <div>
                        <p class="text-sm font-medium text-slate-800 group-hover:text-teal-700">Order #{{ $dispute->order_id }}</p>
                        <p class="text-xs text-slate-400">Diajukan {{ $dispute->created_at->diffForHumans() }}</p>
                    </div>
                    @php
                        $badge = match($dispute->status) {
                            'diajukan' => ['bg-red-50', 'text-red-600', 'Diajukan'],
                            'diinvestigasi' => ['bg-amber-50', 'text-amber-600', 'Diinvestigasi'],
                            default => ['bg-emerald-50', 'text-emerald-600', 'Selesai'],
                        };
                    @endphp
                    <span class="text-[11px] font-medium {{ $badge[0] }} {{ $badge[1] }} px-2.5 py-1 rounded-full">{{ $badge[2] }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-8">Tidak ada sengketa aktif. Aman terkendali 👍</p>
            @endforelse
        </div>
    </div>

</div>
@endsection