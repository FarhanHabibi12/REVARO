<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($error_type ?? '') === 'role_unregistered' ? 'Portal Belum Terdaftar' : '403 — Akses Dibatasi' }} | REVARO</title>
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
                        'danger': '6px 6px 0px 0px rgba(239, 68, 68, 1)',
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
        .revaro-btn-orange {
            background-color: #FF7F00;
            color: white;
            border: 2px solid #FF7F00;
            box-shadow: 4px 4px 0px 0px #0B4A8F;
            transition: all 0.2s ease;
        }
        .revaro-btn-orange:hover {
            box-shadow: 2px 2px 0px 0px #0B4A8F;
            transform: translate(2px, 2px);
        }
        .revaro-btn-danger {
            background-color: #EF4444;
            color: white;
            border: 2px solid #EF4444;
            box-shadow: 4px 4px 0px 0px #991B1B;
            transition: all 0.2s ease;
        }
        .revaro-btn-danger:hover {
            box-shadow: 2px 2px 0px 0px #991B1B;
            transform: translate(2px, 2px);
        }
        .revaro-btn-outline {
            background-color: white;
            color: #1E293B;
            border: 2px solid #CBD5E1;
            box-shadow: 3px 3px 0px 0px #CBD5E1;
            transition: all 0.2s ease;
        }
        .revaro-btn-outline:hover {
            box-shadow: 1px 1px 0px 0px #94A3B8;
            transform: translate(2px, 2px);
        }
    </style>
</head>
<body class="bg-accent min-h-screen relative overflow-hidden flex flex-col justify-between p-4 selection:bg-secondary selection:text-white">

    <!-- Decorative blobs (Background) -->
    @if(($error_type ?? '') === 'role_unregistered')
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-secondary opacity-15 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-primary opacity-15 blur-3xl pointer-events-none"></div>
    @else
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-danger opacity-15 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-primary opacity-15 blur-3xl pointer-events-none"></div>
    @endif

    <!-- Header Logo -->
    <header class="w-full max-w-2xl mx-auto flex items-center justify-between py-4 relative z-10">
        <a href="/" class="flex items-center gap-2.5 text-decoration-none">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr {{ ($error_type ?? '') === 'role_unregistered' ? 'from-primary to-blue-600' : 'from-danger to-rose-600' }} flex items-center justify-center text-white font-black text-xl shadow-md border-2 border-primary">
                R
            </div>
            <div>
                <span class="text-xl font-extrabold text-primary tracking-tight block leading-tight">REVARO</span>
                <span class="text-[9px] font-bold {{ ($error_type ?? '') === 'role_unregistered' ? 'text-secondary' : 'text-danger' }} uppercase tracking-widest block">
                    {{ ($error_type ?? '') === 'role_unregistered' ? 'Ekosistem Servis & Sparepart' : 'Keamanan & Integritas Akun' }}
                </span>
            </div>
        </a>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-2xl mx-auto my-auto relative z-10 py-6">
        @if(($error_type ?? '') === 'role_unregistered')
            <!-- KASUS 1: PENGGUNA BELUM DAFTAR PERAN INI (Bukan Banned) -->
            <div class="bg-white border-4 border-primary rounded-[2.5rem] p-8 sm:p-12 text-center relative shadow-[6px_6px_0px_0px_#0B4A8F] w-full transform transition-all">
                
                <!-- Floating Icon with Badge -->
                <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-primary border-4 border-white rounded-2xl flex items-center justify-center shadow-[4px_4px_0px_0px_#FF7F00]">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <div class="mt-6 sm:mt-8">
                    <span class="inline-block bg-blue-100 text-primary border-2 border-primary/20 font-extrabold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider mb-2">
                        Peran Belum Terdaftar
                    </span>
                    
                    <h1 class="text-5xl sm:text-6xl font-extrabold text-primary tracking-tighter drop-shadow-sm my-1">
                        403
                    </h1>
                    <div class="w-16 h-1.5 bg-secondary mx-auto my-4 rounded-full"></div>
                    
                    <h2 class="text-xl sm:text-2xl font-extrabold text-textDark mb-3">
                        {{ $target_portal ?? 'Portal' }} Belum Terdaftar
                    </h2>
                    
                    <div class="bg-blue-50 border-2 border-primary/30 rounded-2xl p-5 mb-6 text-left space-y-2">
                        <div class="flex items-center gap-2 text-primary font-bold text-xs sm:text-sm">
                            <svg class="w-5 h-5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Peran Akun Anda: <strong class="text-secondary uppercase">{{ $user_role ?? 'Pengguna' }}</strong></span>
                        </div>
                        <p class="text-textDark font-medium text-xs sm:text-sm leading-relaxed">
                            Halo <strong>{{ $user_name ?? 'Pengguna' }}</strong>! Akun Anda saat ini masuk dengan hak akses <strong>{{ $user_role ?? 'Pengguna' }}</strong>, sehingga belum memiliki akses ke <strong>{{ $target_portal ?? 'portal ini' }}</strong>.
                        </p>
                    </div>

                    <p class="text-textLight mb-8 text-xs sm:text-sm max-w-md mx-auto leading-relaxed">
                        Ingin menggunakan fitur ini? Anda dapat mendaftarkan akun Anda untuk peran ini atau kembali ke dasbor Anda yang sudah aktif.
                    </p>

                    <div class="flex flex-col sm:flex-row justify-center gap-3">
                        @if(!empty($is_admin_portal) || ($target_portal ?? '') === 'Portal Administrasi Khusus')
                            <a href="/login-admin?session_required=1" class="revaro-btn-orange px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span>Masuk ke Portal Admin</span>
                            </a>
                            <button onclick="handleLogoutAndExit()" class="revaro-btn-outline px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Ganti / Keluar Akun</span>
                            </button>
                        @endif

                        @if(!empty($register_url))
                            <a href="{{ $register_url }}" class="revaro-btn-orange px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Daftar {{ $target_portal ?? 'Mitra' }}</span>
                            </a>
                        @endif

                        @if(!empty($user_dashboard))
                            <a href="{{ $user_dashboard }}" class="revaro-btn px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Buka Dasbor Saya</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        @else
            <!-- KASUS 2: AKUN BENAR-BENAR DITANGGUHKAN / DIBLOKIR -->
            <div class="bg-white border-4 border-danger rounded-[2.5rem] p-8 sm:p-12 md:p-14 text-center relative shadow-danger w-full transform transition-all">
                
                <!-- Floating Icon with lock badge -->
                <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 w-20 h-20 bg-danger border-4 border-white rounded-2xl flex items-center justify-center shadow-[4px_4px_0px_0px_#991B1B]">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <div class="mt-6 sm:mt-8">
                    <h1 class="text-7xl sm:text-8xl md:text-9xl font-extrabold text-danger tracking-tighter drop-shadow-md">
                        403
                    </h1>
                    <div class="w-16 h-1.5 bg-danger mx-auto my-5 rounded-full"></div>
                    
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-textDark mb-3">
                        Akses Dibatasi / Akun Ditangguhkan
                    </h2>
                    
                    <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-4 mb-6 text-left flex items-start gap-3">
                        <svg class="w-5 h-5 text-danger shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-red-700 font-semibold text-xs sm:text-sm leading-relaxed">
                            {{ (isset($exception) && $exception && $exception->getMessage()) ? $exception->getMessage() : ($message ?? 'Akses ke halaman ini ditolak atau akun Anda sedang dalam status peninjauan/pembekuan karena pelanggaran SOP atau kebijakan platform REVARO.') }}
                        </p>
                    </div>

                    <p class="text-textLight mb-8 text-sm sm:text-base max-w-md mx-auto leading-relaxed">
                        Jika Anda merasa ini adalah sebuah kekeliruan atau ingin mengajukan banding, silakan hubungi tim Administrator atau Customer Service resmi REVARO.
                    </p>

                    <div class="flex flex-col sm:flex-row justify-center gap-3.5">
                        <a href="mailto:support@revaro.id" class="revaro-btn-danger px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Ajukan Banding ke Admin</span>
                        </a>
                        <button onclick="handleLogoutAndExit()" class="revaro-btn-outline px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar Akun</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-2xl mx-auto text-center py-4 text-xs font-semibold text-textLight relative z-10">
        &copy; 2026 REVARO — Solusi Perbaikan &amp; Komponen Otomotif Terpercaya.
    </footer>

    <script>
        function handleLogoutAndExit() {
            localStorage.clear();
            sessionStorage.clear();
            document.cookie = "auth_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
            window.location.href = '/login-admin?session_required=1';
        }
    </script>

</body>
</html>
