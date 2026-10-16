<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0B4A8F', 
                        secondary: '#FF7F00', 
                        accent: '#E6EFFF', 
                        textDark: '#1E293B',
                        textLight: '#64748B',
                        danger: '#EF4444'
                    },
                    boxShadow: {
                        'form': '6px 6px 0px 0px rgba(11, 74, 143, 1)', 
                        'btn': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
                        'secondary': '4px 4px 0px 0px #FF7F00',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .revaro-btn {
            background-color: #0B4A8F;
            color: white;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #FF7F00;
            transition: all 0.2s ease;
        }
        .revaro-btn:hover {
            box-shadow: 2px 2px 0px 0px #FF7F00;
            transform: translate(2px, 2px);
        }
        .revaro-btn-outline {
            background-color: white;
            color: #0B4A8F;
            border: 2px solid #0B4A8F;
            box-shadow: 3px 3px 0px 0px #0B4A8F;
            transition: all 0.2s ease;
        }
        .revaro-btn-outline:hover {
            box-shadow: 1px 1px 0px 0px #0B4A8F;
            transform: translate(2px, 2px);
        }
    </style>
</head>
<body class="bg-accent min-h-screen relative overflow-hidden flex flex-col justify-between p-4 selection:bg-secondary selection:text-white">

    <!-- Decorative blobs (Background) -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-secondary opacity-15 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-primary opacity-15 blur-3xl pointer-events-none"></div>

    <!-- Header Logo -->
    <header class="w-full max-w-2xl mx-auto flex items-center justify-between py-4 relative z-10">
        <a href="/" class="flex items-center gap-2.5 text-decoration-none">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-secondary to-amber-500 flex items-center justify-center text-white font-black text-xl shadow-md border-2 border-primary">
                R
            </div>
            <div>
                <span class="text-xl font-extrabold text-primary tracking-tight block leading-tight">REVARO</span>
                <span class="text-[9px] font-bold text-secondary uppercase tracking-widest block">Servis &amp; Komponen Otomotif</span>
            </div>
        </a>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-2xl mx-auto my-auto relative z-10 py-6">
        <div class="bg-white border-4 border-primary rounded-[2.5rem] p-8 sm:p-12 md:p-14 text-center relative shadow-form w-full transform transition-all">
            
            <!-- Floating Icon -->
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-secondary border-4 border-primary rounded-2xl flex items-center justify-center rotate-12 shadow-[4px_4px_0px_0px_#0B4A8F]">
                <svg class="w-9 h-9 text-white transform -rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>

            <div class="mt-6 sm:mt-8">
                <h1 class="text-7xl sm:text-8xl md:text-9xl font-extrabold text-primary tracking-tighter drop-shadow-md">
                    404
                </h1>
                <div class="w-16 h-1.5 bg-secondary mx-auto my-5 rounded-full"></div>
                
                <h2 class="text-2xl sm:text-3xl font-extrabold text-textDark mb-3">
                    Waduh, Anda Nyasar!
                </h2>
                <p class="text-textLight mb-8 text-sm sm:text-base max-w-md mx-auto leading-relaxed">
                    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'Halaman yang Anda cari sepertinya tidak ada, sudah dipindahkan, atau Anda salah memasukkan alamat URL.' }}
                </p>

@php
    $user404 = auth()->user();
    $dashUrl404 = '/';
    $btnText404 = 'Kembali ke Beranda';
    if ($user404) {
        $btnText404 = 'Kembali ke Dasbor';
        if ($user404->isSuperAdminPemilik()) $dashUrl404 = '/superadmin/pemilik/dashboard';
        elseif ($user404->isSuperAdminMonitoring()) $dashUrl404 = '/superadmin/monitoring/dashboard';
        elseif ($user404->isAdminVerifikator()) $dashUrl404 = '/admin/verifikator/dashboard';
        elseif ($user404->isAdminResolusi()) $dashUrl404 = '/admin/resolusi/dashboard';
        elseif ($user404->isToko() || $user404->toko) $dashUrl404 = '/toko/dashboard';
        elseif ($user404->isTeknisi() || $user404->teknisi) $dashUrl404 = '/teknisi/dashboard';
        else $dashUrl404 = '/pelanggan/dashboard';
    }
@endphp

                <div class="flex flex-col sm:flex-row justify-center gap-3.5">
                    <a href="{{ $dashUrl404 }}" id="backHomeBtn404" class="revaro-btn px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span id="backHomeText404">{{ $btnText404 }}</span>
                    </a>
                    <button onclick="window.history.back()" class="revaro-btn-outline px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Halaman Sebelumnya</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-2xl mx-auto text-center py-4 text-xs font-semibold text-textLight relative z-10">
        &copy; 2026 REVARO — Solusi Perbaikan &amp; Komponen Otomotif Terpercaya.
    </footer>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('token');
            const userRaw = localStorage.getItem('user');
            if (token && userRaw) {
                try {
                    const u = JSON.parse(userRaw);
                    const r = (u.role?.nama || u.role || '').toLowerCase();
                    let dUrl = '/pelanggan/dashboard';
                    if (r.includes('toko') || u.toko) dUrl = '/toko/dashboard';
                    else if (r.includes('teknisi') || u.teknisi) dUrl = '/teknisi/dashboard';
                    else if (r.includes('pemilik')) dUrl = '/superadmin/pemilik/dashboard';
                    else if (r.includes('verifikator')) dUrl = '/admin/verifikator/dashboard';
                    else if (r.includes('resolusi')) dUrl = '/admin/resolusi/dashboard';

                    const btn = document.getElementById('backHomeBtn404');
                    const txt = document.getElementById('backHomeText404');
                    if (btn) btn.href = dUrl;
                    if (txt) txt.innerText = 'Kembali ke Dasbor';
                } catch(e) {}
            }
        });
    </script>
</body>
</html>
