<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Sistem Mengalami Gangguan | REVARO</title>
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

        /* Glitch effect for 500 error */
        .glitch-text {
            position: relative;
            display: inline-block;
        }
        .glitch-text::before, .glitch-text::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.8;
        }
        .glitch-text::before {
            left: 2px;
            text-shadow: -1px 0 #ef4444;
            clip: rect(24px, 550px, 90px, 0);
            animation: glitch-anim 2s infinite linear alternate-reverse;
        }
        .glitch-text::after {
            left: -2px;
            text-shadow: -1px 0 #0b4a8f;
            clip: rect(85px, 550px, 140px, 0);
            animation: glitch-anim 2.5s infinite linear alternate-reverse;
        }
        @keyframes glitch-anim {
            0% { clip: rect(10px, 9999px, 31px, 0); }
            20% { clip: rect(65px, 9999px, 12px, 0); }
            40% { clip: rect(13px, 9999px, 98px, 0); }
            60% { clip: rect(87px, 9999px, 23px, 0); }
            80% { clip: rect(43px, 9999px, 76px, 0); }
            100% { clip: rect(21px, 9999px, 89px, 0); }
        }

        @keyframes gearSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: gearSpin 6s linear infinite;
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
        <div class="bg-white border-4 border-primary rounded-[2.5rem] p-8 sm:p-12 md:p-14 text-center relative shadow-[6px_6px_0px_0px_#FF7F00] w-full transform transition-all">
            
            <!-- Floating Icon with spinning gear -->
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-primary border-4 border-primary rounded-2xl flex items-center justify-center -rotate-12 shadow-[4px_4px_0px_0px_#FF7F00]">
                <svg class="w-9 h-9 text-white animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>

            <div class="mt-6 sm:mt-8">
                <h1 class="text-7xl sm:text-8xl md:text-9xl font-extrabold text-secondary tracking-tighter drop-shadow-md glitch-text" data-text="500">
                    500
                </h1>
                <div class="w-16 h-1.5 bg-primary mx-auto my-5 rounded-full"></div>
                
                <h2 class="text-2xl sm:text-3xl font-extrabold text-textDark mb-3">
                    Ups! Sistem Sedang Gangguan
                </h2>
                <p class="text-textLight mb-8 text-sm sm:text-base max-w-md mx-auto leading-relaxed">
                    {{ $exception && $exception->getMessage() ? $exception->getMessage() : 'Server kami sedang mengalami sedikit kendala teknis. Tim teknisi kami sedang menanganinya. Mohon coba beberapa saat lagi.' }}
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-3.5">
                    <button onclick="window.location.reload()" class="revaro-btn px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Muat Ulang Halaman</span>
                    </button>
                    <a href="/" class="revaro-btn-outline px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-2xl mx-auto text-center py-4 text-xs font-semibold text-textLight relative z-10">
        &copy; 2026 REVARO — Solusi Perbaikan &amp; Komponen Otomotif Terpercaya.
    </footer>

</body>
</html>
