<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin') — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/dist/tabler-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .bg-canvas {
            background-color: #f8f8fc;
            background-image: radial-gradient(circle at 10% 0%, rgba(99,102,241,0.06), transparent 40%),
                               radial-gradient(circle at 100% 15%, rgba(15,23,42,0.03), transparent 35%);
        }
        .card-soft { box-shadow: 0 1px 2px rgba(15,23,42,0.04), 0 8px 24px -12px rgba(15,23,42,0.08); }
        .nav-link { transition: all .15s ease; }
    </style>
</head>
<body class="bg-canvas text-slate-800 min-h-screen">

    <nav class="bg-[#0d0b1f] text-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-600 flex items-center justify-center font-bold text-white text-sm shadow-lg shadow-indigo-500/30">R</div>
                    <div class="leading-tight">
                        <p class="font-semibold text-white tracking-tight text-[15px]">REVARO</p>
                        <p class="text-[11px] text-indigo-400/80 -mt-0.5">Super admin console</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('superadmin.dashboard') }}"
                       class="nav-link px-3.5 py-2 rounded-lg text-[13px] font-medium
                       {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                        <i class="ti ti-chart-dots-3 mr-1.5"></i>Monitoring
                    </a>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <button class="relative p-2 rounded-lg text-slate-400 hover:bg-white/5 hover:text-white">
                        <i class="ti ti-bell text-lg"></i>
                        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-red-400"></span>
                    </button>

                    <div class="hidden sm:flex items-center gap-2.5 pl-3 ml-1 border-l border-white/10">
                        <div class="w-8 h-8 rounded-full bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-xs font-semibold text-indigo-300">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'SA' }}
                        </div>
                        <div class="text-[13px] leading-tight">
                            <p class="text-white font-medium">{{ auth()->user()->name ?? 'Super admin' }}</p>
                            <p class="text-slate-500 text-[11px]">pemilik sistem</p>
                        </div>
                        <button type="button" id="logout-trigger" title="Keluar" class="p-2 rounded-lg text-slate-500 hover:bg-white/5 hover:text-white">
                            <i class="ti ti-logout text-base"></i>
                        </button>
                    </div>

                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-400 hover:bg-white/5 hover:text-white">
                        <i class="ti ti-menu-2 text-xl"></i>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="hidden md:hidden pb-3 flex flex-col gap-1">
                <a href="{{ route('superadmin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm text-slate-300">Monitoring</a>
                <button type="button" id="logout-trigger-mobile" class="text-left px-3 py-2 rounded-lg text-sm text-red-400">Keluar</button>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

    {{-- Modal konfirmasi keluar --}}
    <div id="logout-modal" class="hidden fixed inset-0 z-[60] items-center justify-center px-4">
        <div id="logout-overlay" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

        <div class="relative bg-white w-full max-w-sm rounded-2xl card-soft p-6 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <i class="ti ti-logout text-red-500 text-xl"></i>
            </div>
            <h2 class="font-semibold text-slate-900 text-base mb-1.5">Keluar dari console?</h2>
            <p class="text-sm text-slate-500 mb-6">Kamu perlu masuk kembali dengan email dan kata sandi untuk mengakses super admin console.</p>

            <div class="flex items-center gap-3">
                <button type="button" id="logout-cancel"
                        class="flex-1 text-sm font-medium px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full text-sm font-medium px-4 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-700">
                        Ya, keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        (function () {
            const modal = document.getElementById('logout-modal');
            const openModal = () => { modal.classList.remove('hidden'); modal.classList.add('flex'); };
            const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };

            document.getElementById('logout-trigger')?.addEventListener('click', openModal);
            document.getElementById('logout-trigger-mobile')?.addEventListener('click', openModal);
            document.getElementById('logout-cancel')?.addEventListener('click', closeModal);
            document.getElementById('logout-overlay')?.addEventListener('click', closeModal);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });
        })();
    </script>
</body>
</html>