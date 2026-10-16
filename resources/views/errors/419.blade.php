<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Sesi Halaman Kedaluwarsa | REVARO</title>
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
                        'amber': '6px 6px 0px 0px #D97706',
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
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-amber-500 opacity-15 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-primary opacity-15 blur-3xl pointer-events-none"></div>

    <!-- Header Logo -->
    <header class="w-full max-w-2xl mx-auto flex items-center justify-between py-4 relative z-10">
        <a href="/" class="flex items-center gap-2.5 text-decoration-none">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white font-black text-xl shadow-md border-2 border-primary">
                R
            </div>
            <div>
                <span class="text-xl font-extrabold text-primary tracking-tight block leading-tight">REVARO</span>
                <span class="text-[9px] font-bold text-secondary uppercase tracking-widest block">Keamanan Sesi Pengguna</span>
            </div>
        </a>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-2xl mx-auto my-auto relative z-10 py-6">
        <div class="bg-white border-4 border-primary rounded-[2.5rem] p-8 sm:p-12 md:p-14 text-center relative shadow-[6px_6px_0px_0px_#D97706] w-full transform transition-all">
            
            <!-- Floating Icon (Clock / Hourglass) -->
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-amber-500 border-4 border-primary rounded-2xl flex items-center justify-center -rotate-6 shadow-[4px_4px_0px_0px_#0B4A8F]">
                <svg class="w-9 h-9 text-white transform rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <div class="mt-6 sm:mt-8">
                <h1 class="text-7xl sm:text-8xl md:text-9xl font-extrabold text-amber-600 tracking-tighter drop-shadow-md">
                    419
                </h1>
                <div class="w-16 h-1.5 bg-secondary mx-auto my-5 rounded-full"></div>
                
                <h2 class="text-2xl sm:text-3xl font-extrabold text-textDark mb-3">
                    Sesi Halaman Kedaluwarsa
                    <span class="text-sm font-bold text-amber-600 block mt-1">(CSRF / Timeout)</span>
                </h2>
                <p class="text-textLight mb-8 text-sm sm:text-base max-w-md mx-auto leading-relaxed">
                    Token keamanan atau sesi penjelajahan Anda telah habis karena tidak ada aktivitas dalam waktu lama. Silakan segarkan halaman untuk melanjutkan.
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-3.5">
                    <button onclick="window.location.reload()" class="revaro-btn px-6 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Segarkan Halaman</span>
                    </button>
                    <a href="/login-admin?session_required=1" id="loginAdminBtn" class="revaro-btn-orange px-6 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Masuk Portal Admin</span>
                    </a>
                    <a href="/?auth=login&session_required=1" id="loginBtn" class="revaro-btn-outline px-6 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Masuk Akun Pengguna</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-2xl mx-auto text-center py-4 text-xs font-semibold text-textLight relative z-10">
        &copy; 2026 REVARO — Solusi Perbaikan &amp; Komponen Otomotif Terpercaya.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Bersihkan storage basi saat berada di halaman session expired agar login berikutnya fresh
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            document.cookie = "auth_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
        });
    </script>
</body>
</html>
