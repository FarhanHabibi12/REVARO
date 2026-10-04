<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 — Terlalu Banyak Permintaan | REVARO</title>
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
                        amberBrand: '#D97706'
                    },
                    boxShadow: {
                        'form': '6px 6px 0px 0px rgba(11, 74, 143, 1)', 
                        'orange': '6px 6px 0px 0px #FF7F00',
                        'btn': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
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
                <span class="text-[9px] font-bold text-secondary uppercase tracking-widest block">Perlindungan Lalu Lintas Server</span>
            </div>
        </a>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-2xl mx-auto my-auto relative z-10 py-6">
        <div class="bg-white border-4 border-primary rounded-[2.5rem] p-8 sm:p-12 md:p-14 text-center relative shadow-[6px_6px_0px_0px_#FF7F00] w-full transform transition-all">
            
            <!-- Floating Icon (Speedometer / Tachometer) -->
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-secondary border-4 border-primary rounded-2xl flex items-center justify-center rotate-6 shadow-[4px_4px_0px_0px_#0B4A8F]">
                <svg class="w-9 h-9 text-white transform -rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <div class="mt-6 sm:mt-8">
                <h1 class="text-7xl sm:text-8xl md:text-9xl font-extrabold text-secondary tracking-tighter drop-shadow-md">
                    429
                </h1>
                <div class="w-16 h-1.5 bg-primary mx-auto my-5 rounded-full"></div>
                
                <h2 class="text-2xl sm:text-3xl font-extrabold text-textDark mb-3">
                    Terlalu Banyak Permintaan
                    <span class="text-sm font-bold text-secondary block mt-1">(Anti Brute-Force Rate Limit)</span>
                </h2>
                <p class="text-textLight mb-5 text-sm sm:text-base max-w-md mx-auto leading-relaxed">
                    {{ isset($exception) && $exception && $exception->getMessage() ? $exception->getMessage() : 'Sistem mendeteksi terlalu banyak percobaan dalam waktu singkat. Akses sementara dibatasi untuk melindungi keamanan akun Anda dari serangan brute-force.' }}
                </p>

                <!-- Dynamic Cooldown Timer Badge -->
                <div class="inline-flex items-center gap-2 bg-orange-50 text-orange-900 border-2 border-primary px-4 py-2 rounded-xl text-xs font-black mb-6 shadow-[3px_3px_0px_0px_#0B4A8F]">
                    <span class="animate-spin text-sm">⏱️</span>
                    <span>Cooldown Keamanan: <span id="cooldownTimer" class="font-mono text-sm text-secondary">60</span> detik</span>
                </div>

                <div class="flex justify-center">
                    <button id="retryBtn" onclick="navigateToTarget()" class="revaro-btn px-8 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2 opacity-50 cursor-not-allowed" disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span id="retryBtnText">Tunggu Cooldown (60s)...</span>
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
        let targetUrl = '/?auth=login';
        if (document.referrer.includes('admin') || localStorage.getItem('last_portal') === 'admin') {
            targetUrl = '/login-admin';
        }

        function navigateToTarget() {
            window.location.href = targetUrl;
        }

        let countdown = 60;
        const timerElem = document.getElementById('cooldownTimer');
        const retryBtn = document.getElementById('retryBtn');
        const retryBtnText = document.getElementById('retryBtnText');

        const interval = setInterval(() => {
            countdown--;
            if (timerElem) timerElem.innerText = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                if (timerElem) timerElem.innerText = '0';
                if (retryBtn) {
                    retryBtn.disabled = false;
                    retryBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                if (retryBtnText) {
                    retryBtnText.innerText = 'Mengalihkan ke Login...';
                }
                // Otomatis kembali ke Halaman Login / Landing Page saat cooldown selesai
                setTimeout(() => {
                    navigateToTarget();
                }, 400);
            }
        }, 1000);
    </script>
</body>
</html>
