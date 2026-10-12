<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVARO - Jasa Servis Terpercaya</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/images/revaro-icon-transparent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0B4A8F', // Deep Blue from the form image
                        secondary: '#FF7F00', // Vibrant Orange
                        accent: '#E6EFFF', // Light blue background
                        textDark: '#1E293B',
                        textLight: '#64748B',
                        brandBlue: '#0B4A8F',
                        brandOrange: '#FF7F00'
                    },
                    boxShadow: {
                        'form': '4px 4px 0px 0px rgba(11, 74, 143, 1)', // Neumorphic/hard shadow like the form
                        'btn': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        /* Custom styles for the hard shadow effect seen in the form image */
        .revaro-input {
            border: 2px solid #0B4A8F;
            box-shadow: 3px 3px 0px 0px #0B4A8F;
            transition: all 0.2s ease;
        }
        .revaro-input:focus {
            outline: none;
            box-shadow: 1px 1px 0px 0px #0B4A8F;
            transform: translate(2px, 2px);
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

        /* Modal Auth Retro Boxy Styling */
        .wrapper {
            --input-focus: #0B4A8F;
            --font-color: #0B4A8F;
            --font-color-sub: #64748B;
            --bg-color: #ffffff;
            --bg-card: #ffffff;
            --main-color: #0B4A8F;
            --main-shadow: #062F56;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            background: #ffffff !important;
            border-radius: 14px;
            border: 2px solid var(--main-color) !important;
            box-shadow: 5px 5px var(--main-shadow) !important;
            padding: 24px 24px;
        }

        .card-switch-box {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            border: 2px solid var(--main-color);
            box-shadow: 2px 2px var(--main-shadow);
            border-radius: 10px;
            padding: 4px 14px;
            margin-bottom: 16px;
            width: auto;
        }

        .card-switch {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 200px;
            height: 32px;
        }

        .switch {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 52px;
            height: 26px;
            cursor: pointer;
        }

        .card-side::before {
            position: absolute;
            content: 'Login';
            right: 66px;
            top: 50%;
            transform: translateY(-50%);
            width: 65px;
            text-align: right;
            text-decoration: underline;
            text-underline-offset: 3px;
            color: #0B4A8F;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            line-height: 1;
        }

        .card-side::after {
            position: absolute;
            content: 'Daftar';
            left: 66px;
            top: 50%;
            transform: translateY(-50%);
            width: 65px;
            text-align: left;
            text-decoration: none;
            color: #475569;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            line-height: 1;
        }

        .toggle {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .slider {
            box-sizing: border-box;
            border-radius: 6px;
            border: 2px solid var(--main-color);
            box-shadow: 2px 2px var(--main-shadow);
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ffffff;
            transition: none !important;
            animation: none !important;
        }

        .slider:before {
            box-sizing: border-box;
            position: absolute;
            content: "";
            height: 18px;
            width: 20px;
            border: 2px solid var(--main-color);
            border-radius: 4px;
            left: 2px;
            bottom: 2px;
            background-color: var(--main-color);
            box-shadow: 0 1px 0 var(--main-shadow);
            transition: none !important;
            animation: none !important;
        }

        .toggle:checked + .slider {
            background-color: #F1F5F9;
        }

        .toggle:checked + .slider:before {
            transform: translateX(24px);
        }

        .toggle:checked ~ .card-side::before {
            text-decoration: none;
            color: #475569;
            font-weight: 700;
        }

        .toggle:checked ~ .card-side::after {
            text-decoration: underline;
            text-underline-offset: 3px;
            color: #0B4A8F;
            font-weight: 800;
        }

        .flip-card__inner {
            width: 100%;
            max-width: 360px;
            position: relative;
            background-color: transparent;
            text-align: center;
            transition: none !important;
            animation: none !important;
        }

        .flip-card__front, .flip-card__back, .flip-card__otp {
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            background: transparent !important;
            gap: 12px;
            border: none !important;
            box-shadow: none !important;
            width: 100%;
            max-width: 360px;
            box-sizing: border-box;
            transition: none !important;
            animation: none !important;
        }

        .flip-card__form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 360px;
        }

        .flip-card__form > div {
            width: 100%;
            max-width: 360px;
        }

        .title {
            margin: 0 0 6px 0;
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            color: #0B4A8F;
            letter-spacing: 0.5px;
        }

        .flip-card__input {
            width: 100%;
            max-width: 360px;
            height: 44px;
            border-radius: 8px;
            border: 2px solid var(--main-color) !important;
            background-color: #ffffff !important;
            box-shadow: 3px 3px var(--main-shadow) !important;
            font-size: 13.5px;
            font-weight: 700;
            color: #0F172A !important;
            padding: 8px 14px;
            outline: none;
            box-sizing: border-box;
            transition: none !important;
            animation: none !important;
            display: block;
        }

        .password-field-wrap {
            position: relative;
            width: 100%;
            max-width: 360px;
            display: block;
        }

        .flip-card__input::placeholder {
            color: #475569 !important;
            opacity: 1 !important;
            font-size: 12.5px;
            font-weight: 600;
        }

        .flip-card__input:focus {
            border: 2px solid #FF7F00 !important;
            box-shadow: 3px 3px #FF7F00 !important;
        }

        .flip-card__btn {
            margin: 10px 0 4px 0;
            width: 100%;
            max-width: 360px;
            height: 44px;
            border-radius: 8px;
            border: 2px solid var(--main-color) !important;
            background-color: #0B4A8F !important;
            box-shadow: 3px 3px var(--main-shadow) !important;
            font-size: 14.5px;
            font-weight: 800;
            letter-spacing: 0.3px;
            color: #ffffff !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: none !important;
            animation: none !important;
        }

        .flip-card__btn:hover {
            background-color: #062F56 !important;
        }

        .flip-card__btn:active {
            box-shadow: 0px 0px var(--main-shadow) !important;
            transform: translate(2px, 2px);
        }

        .auth-close-btn {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            background: #ffffff;
            border: 2px solid #0B4A8F;
            box-shadow: 2px 2px #062F56;
            color: #0B4A8F;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 30;
            padding: 0;
            transition: none !important;
        }
        .auth-close-btn:hover {
            background: #F1F5F9;
            color: #FF7F00;
            border-color: #FF7F00;
        }

        .password-toggle-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 12px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0B4A8F;
            cursor: pointer;
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            z-index: 20;
        }
        .password-toggle-btn:hover {
            color: #FF7F00;
        }
    </style>
</head>
<body class="bg-gray-50 text-textDark">

    <!-- Navbar (Sesuai dengan Menu Asli Sistem) -->
    <nav class="fixed w-full bg-white z-40 border-b-2 border-primary shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="/" class="flex-shrink-0 flex items-center cursor-pointer">
                    <img src="/images/revaro-logo-horizontal.png" alt="REVARO" class="h-10 w-auto object-contain">
                </a>
                
                <!-- Desktop Menu (Menu Asli) -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#beranda" class="text-textDark hover:text-secondary font-semibold transition-colors">Beranda</a>
                    <a href="#layanan" class="text-textDark hover:text-secondary font-semibold transition-colors">Layanan Kami</a>
                    <a href="/partner/register" id="navGabungMitra" class="text-textDark hover:text-secondary font-semibold transition-colors">Gabung Mitra</a>
                    <a href="#tentang" class="text-textDark hover:text-secondary font-semibold transition-colors">Tentang Kami</a>
                    <a href="#kontak" class="text-textDark hover:text-secondary font-semibold transition-colors">Hubungi Kami</a>
                </div>

                <!-- Auth Buttons / User Profile (Status Login Asli) -->
                <div class="flex items-center space-x-3">
                    <div id="userProfileSection" class="hidden flex items-center space-x-2">
                        <span id="navUserName" class="text-xs font-bold bg-accent text-primary px-3 py-1.5 rounded-full border border-primary"></span>
                        <button id="dashboardBtn" class="revaro-btn text-xs px-3.5 py-2 rounded-lg font-bold transition">Ke Dashboard</button>
                        <button onclick="handleLogout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">Logout</button>
                    </div>
                    <div id="authNavButtons" class="flex space-x-2">
                        <button onclick="openAuthModal('login')" class="revaro-btn-outline text-xs px-3.5 py-2 rounded-lg font-bold transition">Masuk</button>
                        <button onclick="openAuthModal('register')" class="revaro-btn text-xs px-3.5 py-2 rounded-lg font-bold transition shadow-sm">Daftar</button>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center ml-2">
                        <button onclick="toggleMobileMenu()" class="text-primary hover:text-secondary focus:outline-none">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Menu Asli) -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-3 pt-2 pb-4 space-y-2">
                <a href="#beranda" class="block px-3 py-2 text-base font-semibold text-textDark hover:text-secondary hover:bg-gray-50 rounded-lg">Beranda</a>
                <a href="#layanan" class="block px-3 py-2 text-base font-semibold text-textDark hover:text-secondary hover:bg-gray-50 rounded-lg">Layanan Kami</a>
                <a href="/partner/register" id="mobileNavGabungMitra" class="block px-3 py-2 text-base font-semibold text-textDark hover:text-secondary hover:bg-gray-50 rounded-lg">Gabung Mitra</a>
                <a href="#tentang" class="block px-3 py-2 text-base font-semibold text-textDark hover:text-secondary hover:bg-gray-50 rounded-lg">Tentang Kami</a>
                <a href="#kontak" class="block px-3 py-2 text-base font-semibold text-textDark hover:text-secondary hover:bg-gray-50 rounded-lg">Hubungi Kami</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="pt-28 pb-16 md:pt-40 md:pb-24 bg-accent overflow-hidden relative">
        <!-- Decorative blob -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-secondary opacity-10"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-primary opacity-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="space-y-6 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border-2 border-primary text-primary text-xs font-bold shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        On-Demand Electronic Repair Marketplace
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-primary leading-tight">
                        Solusi Tepat Servis <br>
                        <span class="text-secondary relative">
                            Cepat, Transparan &amp; Bergaransi
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-secondary opacity-50" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="2" fill="transparent" />
                            </svg>
                        </span>
                    </h1>
                    <p class="text-lg md:text-xl text-textLight max-w-lg mx-auto md:mx-0 leading-relaxed">
                        Menjadikan perbaikan elektronik rumah tangga semudah memesan transportasi online. Menghubungkan Pelanggan, Teknisi Terverifikasi, dan Toko Sparepart Resmi dalam satu ekosistem digital yang transparan dan aman.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center md:justify-start space-y-4 sm:space-y-0 sm:space-x-4 pt-4">
                        <a href="#layanan" class="revaro-btn px-8 py-3.5 rounded-xl font-bold text-lg text-center flex items-center justify-center gap-2">
                            <i class="fas fa-tools text-secondary"></i> Lihat Layanan
                        </a>
                        <button onclick="openAuthModal('register')" class="revaro-btn-outline px-8 py-3.5 rounded-xl font-bold text-lg text-center flex items-center justify-center gap-2">
                            Daftar Sekarang <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Trust Stats Mini -->
                    <div class="pt-4 grid grid-cols-3 gap-4 border-t border-blue-200/60 max-w-md mx-auto md:mx-0">
                        <div>
                            <p class="text-2xl font-black text-primary">100%</p>
                            <p class="text-xs text-textLight font-medium">Teknisi Teruji</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-secondary">Escrow</p>
                            <p class="text-xs text-textLight font-medium">Rekening Bersama</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-primary">Resmi</p>
                            <p class="text-xs text-textLight font-medium">Toko Sparepart</p>
                        </div>
                    </div>
                </div>

                <!-- Image Placeholder (Hero Visual) -->
                <div class="relative mx-auto w-full max-w-md">
                    <!-- Placeholder Frame -->
                    <div class="aspect-square bg-white border-4 border-primary rounded-3xl p-3 relative shadow-form transform rotate-2 hover:rotate-0 transition-transform duration-300">
                        <div class="w-full h-full bg-gradient-to-br from-blue-50 to-orange-50 rounded-2xl overflow-hidden flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-primary/40 relative">
                            <div class="w-20 h-20 rounded-2xl bg-primary text-white flex items-center justify-center text-3xl shadow-btn mb-4">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <h3 class="text-xl font-extrabold text-primary">REVARO Ecosystem</h3>
                            <p class="text-xs text-textLight mt-2 max-w-xs">
                                Integrasi langsung Pelanggan &bull; Teknisi Independen &bull; Toko Sparepart Terverifikasi dengan Garansi Sistem
                            </p>
                            <div class="mt-4 flex gap-2">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white border border-primary text-primary">AC</span>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white border border-primary text-primary">Kulkas</span>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white border border-primary text-primary">Mesin Cuci</span>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white border border-primary text-primary">TV</span>
                            </div>
                        </div>
                        
                        <!-- Floating Badge 1 (Kiri Bawah) -->
                        <div class="absolute -bottom-6 -left-6 bg-white border-2 border-primary rounded-xl p-3 shadow-form flex items-center gap-3 transform -rotate-6">
                            <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center text-white font-bold">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <p class="font-bold text-primary text-sm leading-tight">Garansi 30-45 Hari</p>
                                <p class="text-xs text-textLight">Klaim Terlacak Sistem</p>
                            </div>
                        </div>

                        <!-- Floating Badge 2 (Kanan Atas) -->
                        <div class="absolute -top-4 -right-4 bg-white border-2 border-primary rounded-xl p-2.5 shadow-form flex items-center gap-2 transform rotate-6">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-secondary font-bold">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <p class="font-extrabold text-primary text-xs">Anti-Markup</p>
                                <p class="text-[10px] text-textLight">e-Nota Transparan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section (Carousel 2 Row Dinamis Sesuai Master Data Super Admin) -->
    <section id="layanan" class="py-20 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-secondary font-bold tracking-wider uppercase text-sm">Layanan Kami</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary mt-2">Kategori Servis Elektronik Resmi</h2>
                    <p class="text-textLight max-w-2xl mt-2 text-sm md:text-base">
                        Standar tarif baseline transparan dan masa garansi resmi sesuai ketetapan Master Data Super Admin REVARO.
                    </p>
                </div>
                <!-- Tombol Navigasi Carousel Geser Menyamping -->
                <div class="flex items-center gap-2 self-end md:self-auto">
                    <button onclick="scrollServiceCarousel(-1)" type="button" aria-label="Geser ke kiri" class="w-11 h-11 rounded-xl border-2 border-primary bg-white text-primary hover:bg-primary hover:text-white flex items-center justify-center transition-all shadow-[2px_2px_0px_0px_#0B4A8F] active:translate-x-0.5 active:translate-y-0.5">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="scrollServiceCarousel(1)" type="button" aria-label="Geser ke kanan" class="w-11 h-11 rounded-xl border-2 border-primary bg-secondary text-white hover:bg-orange-600 flex items-center justify-center transition-all shadow-[2px_2px_0px_0px_#0B4A8F] active:translate-x-0.5 active:translate-y-0.5">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Wadah Carousel: 2 Baris per Kolom (Render Langsung dari Database Super Admin) -->
            <div id="serviceCarouselContainer" class="flex gap-6 overflow-x-auto pb-6 pt-2 snap-x snap-mandatory scroll-smooth" style="scrollbar-width: thin;">
                @php
                    $chunks = isset($jasaServis) && $jasaServis->count() > 0 
                        ? $jasaServis->chunk(2) 
                        : \App\Models\JasaServis::where('is_active', true)->orderBy('id', 'asc')->get()->chunk(2);
                @endphp

                @forelse($chunks as $pair)
                    <div class="w-[320px] md:w-[350px] shrink-0 flex flex-col gap-5 snap-start">
                        @foreach($pair as $s)
                            @php
                                $katLower = strtolower($s->kategori ?? '');
                                $icon = 'fa-wrench';
                                $iconColor = 'text-secondary bg-orange-50 border-secondary/30';
                                if (str_contains($katLower, 'ac')) {
                                    $icon = 'fa-snowflake';
                                    $iconColor = 'text-blue-600 bg-blue-50 border-blue-200';
                                } elseif (str_contains($katLower, 'kulkas') || str_contains($katLower, 'freezer')) {
                                    $icon = 'fa-temperature-low';
                                    $iconColor = 'text-amber-600 bg-amber-50 border-amber-200';
                                } elseif (str_contains($katLower, 'cuci')) {
                                    $icon = 'fa-sync-alt';
                                    $iconColor = 'text-cyan-600 bg-cyan-50 border-cyan-200';
                                } elseif (str_contains($katLower, 'tv') || str_contains($katLower, 'layar')) {
                                    $icon = 'fa-tv';
                                    $iconColor = 'text-purple-600 bg-purple-50 border-purple-200';
                                } elseif (str_contains($katLower, 'komputer') || str_contains($katLower, 'laptop')) {
                                    $icon = 'fa-laptop';
                                    $iconColor = 'text-emerald-600 bg-emerald-50 border-emerald-200';
                                } elseif (str_contains($katLower, 'pompa')) {
                                    $icon = 'fa-water';
                                    $iconColor = 'text-sky-600 bg-sky-50 border-sky-200';
                                }
                            @endphp
                            <div class="bg-white border-2 border-primary rounded-2xl p-5 hover:-translate-y-1 hover:border-secondary transition-all duration-200 shadow-[4px_4px_0px_0px_#0B4A8F] flex flex-col justify-between min-h-[160px]">
                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-2.5">
                                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl border-2 {{ $iconColor }} shadow-sm shrink-0">
                                            <i class="fas {{ $icon }}"></i>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-[11px] font-black bg-blue-50 text-primary border border-primary/40 uppercase tracking-wider shrink-0 truncate max-w-[150px]">
                                            Kategori {{ $s->kategori }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-extrabold text-primary line-clamp-1 leading-snug" title="{{ $s->nama_jasa }}">
                                        {{ $s->nama_jasa }}
                                    </h3>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-2">
                                    <div>
                                        <span class="text-[10px] text-textLight font-medium block">Harga Baseline</span>
                                        <span class="text-sm font-black text-primary">Rp {{ number_format($s->harga_baseline, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-extrabold text-secondary bg-orange-50 px-2.5 py-1 rounded-lg border border-secondary/40 whitespace-nowrap shadow-sm">
                                            <i class="fas fa-shield-alt mr-0.5"></i> {{ $s->garansi_hari ?? 30 }} Hari
                                        </span>
                                        <button onclick="handleServiceOrder('{{ addslashes($s->kategori) }}')" type="button" class="revaro-btn w-8 h-8 rounded-xl font-black text-xs flex items-center justify-center shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition" title="Pesan Layanan {{ $s->nama_jasa }}">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($pair->count() === 1)
                            <div class="min-h-[160px] rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center text-slate-400 text-xs italic p-4 text-center">
                                <i class="fas fa-plus-circle text-lg mb-1 opacity-40"></i>
                                <span>Kategori baru akan ditambahkan oleh Super Admin</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 w-full text-sm">
                        Belum ada layanan servis yang diaktifkan oleh Super Admin di database.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Section Tentang REVARO (Sesuai Penjelasan PRD & Masalah Yang Dipecahkan) -->
    <section id="tentang" class="py-20 bg-accent relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Tentang Kami -->
            <div class="text-center mb-16">
                <span class="text-secondary font-bold tracking-wider uppercase text-sm">Tentang REVARO — Repair &amp; Restore</span>
                <h2 class="text-3xl md:text-5xl font-extrabold text-primary mt-2">Solusi Nyata Servis Elektronik On-Demand</h2>
                <p class="text-textLight max-w-3xl mx-auto mt-4 text-base md:text-lg leading-relaxed">
                    REVARO hadir untuk menuntaskan kelemahan servis konvensional melalui integrasi langsung antara <strong>Pelanggan</strong>, <strong>Teknisi Berkompeten</strong>, dan <strong>Toko Sparepart Resmi</strong> dalam satu sistem digital yang saling mengawasi.
                </p>
                <div class="w-24 h-1.5 bg-secondary mx-auto mt-4 rounded-full"></div>
            </div>

            <!-- 4 Pilar Masalah & Solusi (Sesuai Bab 2 PRD) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                <!-- Pilar 1 -->
                <div class="bg-white border-2 border-primary rounded-2xl p-6 shadow-form flex flex-col justify-between hover:-translate-y-1 transition-transform">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-primary text-secondary flex items-center justify-center text-xl mb-4 border border-primary">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">1. Teknisi Tepercaya</h4>
                        <p class="text-xs text-textLight leading-relaxed">
                            Bukan teknisi panggilan tanpa identitas. Semua teknisi melewati seleksi verifikasi dokumen legal &amp; sertifikasi oleh Admin Verifikator dengan sistem reputasi bertingkat (Bronze, Silver, Gold).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-1.5 text-xs font-bold text-primary">
                        <i class="fas fa-check-circle text-green-500"></i> Terverifikasi Ketat
                    </div>
                </div>

                <!-- Pilar 2 -->
                <div class="bg-white border-2 border-primary rounded-2xl p-6 shadow-form flex flex-col justify-between hover:-translate-y-1 transition-transform">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-secondary text-white flex items-center justify-center text-xl mb-4 border border-secondary">
                            <i class="fas fa-tag"></i>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">2. Anti-Markup Sparepart</h4>
                        <p class="text-xs text-textLight leading-relaxed">
                            Tidak ada lagi kenaikan harga sepihak. Suku cadang dipasok langsung dari Toko Sparepart resmi dengan katalog harga pasti. Pelanggan menerima e-Nota terpisah antara Biaya Jasa dan Biaya Alat.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-1.5 text-xs font-bold text-primary">
                        <i class="fas fa-check-circle text-green-500"></i> Harga Katalog Tetap
                    </div>
                </div>

                <!-- Pilar 3 -->
                <div class="bg-white border-2 border-primary rounded-2xl p-6 shadow-form flex flex-col justify-between hover:-translate-y-1 transition-transform">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-primary text-secondary flex items-center justify-center text-xl mb-4 border border-primary">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">3. Garansi Terikat Sistem</h4>
                        <p class="text-xs text-textLight leading-relaxed">
                            Bukan sekadar janji lisan. Setiap pengerjaan servis memiliki masa garansi resmi (30 s/d 45 hari) yang tersimpan di sistem. Didukung Admin Resolusi independen untuk klaim tanpa ribet.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-1.5 text-xs font-bold text-primary">
                        <i class="fas fa-check-circle text-green-500"></i> Kepastian Klaim
                    </div>
                </div>

                <!-- Pilar 4 -->
                <div class="bg-white border-2 border-primary rounded-2xl p-6 shadow-form flex flex-col justify-between hover:-translate-y-1 transition-transform">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-secondary text-white flex items-center justify-center text-xl mb-4 border border-secondary">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="text-lg font-bold text-primary mb-2">4. Perlindungan Finansial Escrow</h4>
                        <p class="text-xs text-textLight leading-relaxed">
                            Perlindungan dua arah. Dana pelanggan ditahan aman di sistem Rekening Bersama (Escrow) dan baru dicairkan setelah pekerjaan tuntas dan disetujui. Pelanggan aman, teknisi pasti dibayar.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-1.5 text-xs font-bold text-primary">
                        <i class="fas fa-check-circle text-green-500"></i> Rekening Bersama Aman
                    </div>
                </div>
            </div>

            <!-- Ekosistem 3 Pihak REVARO (Sesuai Penjelasan PRD) -->
            <div class="bg-white border-4 border-primary rounded-3xl p-8 md:p-12 shadow-form">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <span class="text-xs font-black text-secondary tracking-widest uppercase">Checks &amp; Balances</span>
                    <h3 class="text-2xl md:text-3xl font-extrabold text-primary mt-1">Ekosistem 3 Pihak yang Saling Menguntungkan</h3>
                    <p class="text-xs md:text-sm text-textLight mt-2">Setiap pihak memiliki peran jelas untuk menjaga transparansi dan kualitas servis dari ujung ke ujung.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Kartu Pelanggan -->
                    <div class="border-2 border-primary/30 rounded-2xl p-6 bg-accent/40 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold mb-4">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4 class="text-lg font-extrabold text-primary mb-3">Untuk Pelanggan</h4>
                            <ul class="space-y-2 text-xs text-textDark">
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Pesan servis semudah SOS dengan foto kerusakan.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Pelacakan rute GPS &amp; estimasi waktu tiba teknisi.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Konfirmasi persetujuan sparepart sebelum dibeli.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>e-Nota resmi &amp; garansi servis tercatat sistem.</span></li>
                            </ul>
                        </div>
                        <button onclick="openAuthModal('register')" class="revaro-btn w-full mt-6 py-2 rounded-xl text-xs font-bold text-center">
                            Daftar Pelanggan
                        </button>
                    </div>

                    <!-- Kartu Teknisi -->
                    <div class="border-2 border-primary rounded-2xl p-6 bg-primary text-white flex flex-col justify-between shadow-btn">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-white text-primary flex items-center justify-center font-bold mb-4">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h4 class="text-lg font-extrabold text-white mb-3">Untuk Teknisi Mitra</h4>
                            <ul class="space-y-2 text-xs text-blue-100">
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Feed order servis real-time sesuai radius kerja.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Akses katalog suku cadang resmi toko terdekat.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Dompet digital &amp; penarikan dana tanpa penundaan.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Kenaikan tier reputasi (Bronze, Silver, Gold).</span></li>
                            </ul>
                        </div>
                        <a href="/partner/register" class="bg-secondary hover:bg-orange-600 text-white w-full mt-6 py-2 rounded-xl text-xs font-bold text-center block transition-colors">
                            Gabung Mitra Teknisi
                        </a>
                    </div>

                    <!-- Kartu Toko Sparepart -->
                    <div class="border-2 border-primary/30 rounded-2xl p-6 bg-accent/40 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-secondary text-white flex items-center justify-center font-bold mb-4">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4 class="text-lg font-extrabold text-primary mb-3">Untuk Toko Sparepart</h4>
                            <ul class="space-y-2 text-xs text-textDark">
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Kelola katalog produk suku cadang dengan harga tetap.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Terima pesanan langsung dari teknisi yang sedang servis.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Kepastian pembayaran aman melalui sistem REVARO.</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-check text-secondary mt-0.5"></i> <span>Badge Toko Resmi Terverifikasi untuk kepercayaan.</span></li>
                            </ul>
                        </div>
                        <a href="/partner/register" class="revaro-btn-outline w-full mt-6 py-2 rounded-xl text-xs font-bold text-center block">
                            Daftar Toko Sparepart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white pt-16 pb-8 border-t-8 border-secondary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <!-- Brand Info -->
                <div>
                    <div class="mb-4 inline-block bg-white/95 px-4 py-2 rounded-2xl shadow-md border-2 border-secondary/40">
                        <img src="/images/revaro-logo-horizontal.png" alt="REVARO Logo" class="h-10 w-auto object-contain">
                    </div>
                    <p class="text-xs text-blue-200 uppercase tracking-widest font-bold mb-3">Repair &amp; Restore Platform</p>
                    <p class="text-blue-100 text-sm mb-6 max-w-sm leading-relaxed">
                        Marketplace jasa servis elektronik on-demand terpercaya. Menghubungkan Pelanggan, Teknisi Terverifikasi, dan Toko Sparepart resmi dengan sistem Rekening Bersama (Escrow) dan garansi baku terlacak.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors text-white">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links (Sesuai Menu Asli) -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-secondary flex items-center gap-2">
                        <i class="fas fa-link text-sm"></i> Tautan Cepat
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#beranda" class="text-blue-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-xs text-secondary"></i> Beranda</a></li>
                        <li><a href="#layanan" class="text-blue-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-xs text-secondary"></i> Layanan Kami</a></li>
                        <li><a href="/partner/register" class="text-blue-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-xs text-secondary"></i> Gabung Mitra Teknisi &amp; Toko</a></li>
                        <li><a href="#tentang" class="text-blue-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-xs text-secondary"></i> Tentang REVARO</a></li>
                        <li><a href="javascript:void(0)" onclick="openAuthModal('register')" class="text-blue-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-xs text-secondary"></i> Daftar Akun Pelanggan</a></li>
                        <li><a href="javascript:void(0)" onclick="openAuthModal('login')" class="text-blue-100 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-angle-right text-xs text-secondary"></i> Masuk ke Akun</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div id="kontak">
                    <h4 class="text-lg font-bold mb-6 text-secondary flex items-center gap-2">
                        <i class="fas fa-headset text-sm"></i> Hubungi Kami
                    </h4>
                    <ul class="space-y-4 text-sm text-blue-100">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-secondary text-base"></i>
                            <span>5, Jl. G1 No.7, RT.1/RW.3, Slipi, Kec. Palmerah, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11410</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-secondary text-base"></i>
                            <span>+62 857 7248 4190</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-secondary text-base"></i>
                            <span>s.elfairuzi@gmail.com</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-clock text-secondary text-base"></i>
                            <span>Operasional: 08:00 - 21:00 WIB</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-blue-800 pt-8 text-center text-blue-200 text-xs font-medium">
                <p>&copy; 2026 REVARO — Repair &amp; Restore. Hak Cipta Dilindungi Undang-Undang.</p>
            </div>
        </div>
    </footer>

    <!-- Auth Modal (Pop-up Modal Presisi: Login, Daftar, OTP Verifikasi) -->
    <div id="authModal" class="hidden fixed inset-0 bg-slate-950/75 backdrop-blur-md z-50 flex items-start justify-center p-4 overflow-y-auto">
        <div class="wrapper mt-8 md:mt-14 mb-8">
            <!-- Tombol Close Modal (SVG Presisi & Sempurna di Tengah) -->
            <button type="button" onclick="closeAuthModal()" class="auth-close-btn" title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Card Switch (Login vs Daftar) dengan Kotak Kontras Retro Biru/Putih -->
            <div id="uiverseSwitchWrapper" class="card-switch-box">
                <div class="card-switch">
                    <label class="switch">
                       <input class="toggle" type="checkbox" id="authModeToggle" onchange="toggleAuthMode(this.checked)">
                       <span class="slider"></span>
                       <span class="card-side"></span>
                    </label>
                </div>
            </div>

            <!-- Flip Card Container (Front: Login, Back: Daftar) -->
            <div id="uiverseCardInner" class="flip-card__inner">
                <!-- FRONT: Login -->
                <div id="cardFrontLogin" class="flip-card__front">
                    <div class="title">Login</div>
                    <form id="userLoginForm" onsubmit="handleUserLogin(event)" class="flip-card__form">
                        <div class="w-full">
                            <input type="email" id="userLoginEmail" placeholder="Email" name="email" required class="flip-card__input">
                        </div>
                        <div class="password-field-wrap">
                            <input type="password" id="userLoginPassword" placeholder="Kata Sandi" name="password" required class="flip-card__input pr-11">
                            <button type="button" onclick="toggleGenericPassword('userLoginPassword', 'loginEyeIcon')" class="password-toggle-btn" title="Lihat Kata Sandi">
                                <span id="loginEyeIcon" class="flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brandBlue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>
                        <button type="submit" id="loginSubmitBtn" class="flip-card__btn">Masuk Sekarang</button>
                    </form>

                    <!-- Divider & Google Login (Admin Link Dirahasiakan / Dihapus) -->
                    <div class="w-full pt-1 space-y-2 text-center">
                        <div class="relative flex items-center justify-center my-1">
                            <div class="border-t border-slate-300 w-full"></div>
                            <span class="bg-white px-2 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider relative">atau</span>
                        </div>
                        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 rounded-md border-2 border-brandBlue bg-white hover:bg-slate-50 text-brandBlue text-xs font-bold shadow-[2px_2px_#062F56] transition">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Masuk dengan Google</span>
                        </a>
                    </div>
                </div>

                <!-- BACK: Daftar (Form Lengkap) -->
                <div id="cardBackRegister" class="flip-card__back hidden">
                    <div class="title">Daftar Akun</div>
                    <form id="userRegisterForm" onsubmit="handleUserRegister(event)" class="flip-card__form">
                        <!-- Nama Lengkap -->
                        <div class="w-full">
                            <input type="text" id="userRegisterNama" placeholder="Nama Lengkap" required class="flip-card__input">
                        </div>

                        <!-- Email -->
                        <div class="w-full">
                            <input type="email" id="userRegisterEmail" placeholder="Alamat Email" required class="flip-card__input">
                        </div>

                        <!-- Nomor HP / WhatsApp -->
                        <div class="w-full">
                            <input type="tel" id="userRegisterHp" placeholder="Nomor HP / WhatsApp (cth: 08123456789)" required class="flip-card__input">
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="w-full">
                            <input type="text" id="userRegisterAlamat" placeholder="Alamat Rumah Lengkap" required class="flip-card__input">
                        </div>

                        <!-- Kata Sandi -->
                        <div class="password-field-wrap">
                            <input type="password" id="userRegisterPassword" placeholder="Kata Sandi (min. 6 karakter)" required class="flip-card__input pr-11">
                            <button type="button" onclick="toggleGenericPassword('userRegisterPassword', 'registerEyeIcon')" class="password-toggle-btn" title="Lihat Kata Sandi">
                                <span id="registerEyeIcon" class="flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brandBlue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>

                        <!-- Konfirmasi Kata Sandi -->
                        <div class="password-field-wrap">
                            <input type="password" id="userRegisterConfirmPassword" placeholder="Ulangi Kata Sandi" required class="flip-card__input pr-11">
                            <button type="button" onclick="toggleGenericPassword('userRegisterConfirmPassword', 'registerConfirmEyeIcon')" class="password-toggle-btn" title="Lihat Kata Sandi">
                                <span id="registerConfirmEyeIcon" class="flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brandBlue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>

                        <button type="submit" id="registerSubmitBtn" class="flip-card__btn">Daftar Sekarang</button>
                    </form>

                    <!-- Divider & Google Sign up -->
                    <div class="w-full pt-1 space-y-2 text-center">
                        <div class="relative flex items-center justify-center my-1">
                            <div class="border-t border-slate-300 w-full"></div>
                            <span class="bg-white px-2 text-[11px] font-extrabold text-slate-600 uppercase tracking-wider relative">atau</span>
                        </div>
                        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 rounded-md border-2 border-brandBlue bg-white hover:bg-slate-50 text-brandBlue text-xs font-bold shadow-[2px_2px_#062F56] transition">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Daftar dengan Google</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- KARTU KHUSUS: VERIFIKASI OTP (Desain sama persis putih + biru REVARO) -->
            <div id="uiverseOtpCard" class="flip-card__otp hidden">
                <div class="title">Verifikasi Kode OTP</div>
                <p class="text-xs text-slate-600 text-center -mt-1 leading-relaxed">
                    Kode 6-digit verifikasi telah dikirim ke: <br>
                    <span id="uiverseOtpTargetEmail" class="font-bold text-brandBlue font-mono text-sm">user@email.com</span>
                </p>
                <form id="userOtpForm" onsubmit="handleOtpSubmit(event)" class="flip-card__form">
                    <div class="w-full">
                        <input type="text" id="otpCodeInput" placeholder="••••••" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code" required
                            class="flip-card__input text-center text-xl font-mono tracking-[0.3em] font-extrabold text-brandBlue">
                    </div>
                    <button type="submit" id="verifyOtpBtn" class="flip-card__btn">Verifikasi</button>
                </form>
                <div class="flex flex-col items-center gap-2 pt-1 text-xs">
                    <button type="button" id="resendOtpBtn" onclick="handleResendOtp()" class="text-brandBlue hover:underline font-semibold disabled:text-slate-400">
                        Kirim Ulang Kode (<span id="resendTimer">60</span>s)
                    </button>
                    <button type="button" onclick="backToLoginOrRegister()" class="text-slate-500 hover:text-slate-800 transition text-[12px] font-medium">
                        ← Kembali ke Form
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-Side Routing & Logic -->
    <script>
        let currentOtpEmail = '';
        let resendTimerInterval = null;

        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }

        // Close mobile menu when clicking a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });

        // Smooth scroll adjustment for fixed header
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#' || !targetId.startsWith('#')) return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
      
                    window.scrollTo({
                         top: offsetPosition,
                         behavior: "smooth"
                    });
                }
            });
        });

        function getRoleDashboardUrl(roleData, userObj = null) {
            let roleName = '';
            if (typeof roleData === 'object' && roleData !== null) {
                roleName = roleData.nama || roleData.name || '';
            } else if (typeof roleData === 'string') {
                roleName = roleData;
            }

            const lower = roleName.toLowerCase();

            if (lower.includes('pemilik') || lower === 'super-admin-pemilik' || lower === 'super admin (pemilik sistem)' || lower === 'super admin pemilik') {
                return '/superadmin/pemilik/dashboard';
            }
            if (lower.includes('monitoring') || lower === 'super-admin-monitoring' || lower === 'super admin (monitoring kinerja)') {
                return '/superadmin/monitoring/scorecard-verifikator';
            }
            if (lower.includes('verifikator') || lower === 'admin-verifikator') {
                return '/admin/verifikator/dashboard';
            }
            if (lower.includes('resolusi') || lower === 'admin-resolusi') {
                return '/admin/resolusi/dashboard';
            }
            if (lower.includes('teknisi')) {
                if (userObj && userObj.teknisi && userObj.teknisi.status === 'PENDING_REVIEW') {
                    return null;
                }
                return '/teknisi/dashboard';
            }
            if (lower.includes('toko') || lower.includes('sparepart')) {
                if (userObj && userObj.toko && userObj.toko.status === 'PENDING_REVIEW') {
                    return null;
                }
                return '/toko/dashboard';
            }
            return '/pelanggan/dashboard';
        }

        function getRoleSlugName(roleData, userObj) {
            const url = getRoleDashboardUrl(roleData, userObj);
            if (!url) return 'pending-mitra';
            if (url.includes('pemilik')) return 'super-admin-pemilik';
            if (url.includes('monitoring')) return 'super-admin-monitoring';
            if (url.includes('verifikator')) return 'admin-verifikator';
            if (url.includes('resolusi')) return 'admin-resolusi';
            if (url.includes('teknisi')) return 'teknisi';
            if (url.includes('toko')) return 'toko-sparepart';
            return 'pelanggan';
        }

        // Check session and redirect accordingly
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const authParam = urlParams.get('auth');
            if (authParam === 'login' || authParam === 'register') {
                openAuthModal(authParam);
            }

            @if(session('error'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Autentikasi Google',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#0B4A8F'
                });
            @endif

            const token = localStorage.getItem('token') || localStorage.getItem('admin_token');
            const userRaw = localStorage.getItem('user') || localStorage.getItem('admin_user');

            if (token && userRaw) {
                const user = JSON.parse(userRaw);
                const roleName = user.role ? (user.role.nama || user.role) : '';
                const dashUrl = getRoleDashboardUrl(roleName, user);
                
                document.getElementById('authNavButtons').classList.add('hidden');
                document.getElementById('userProfileSection').classList.remove('hidden');
                document.getElementById('navUserName').innerText = user.nama;
                
                // Hide Gabung Mitra link if already a partner or admin
                const lowerRole = (typeof roleName === 'string' ? roleName : (roleName.nama || '')).toLowerCase();
                if (lowerRole.includes('teknisi') || lowerRole.includes('toko') || lowerRole.includes('admin') || lowerRole.includes('pemilik')) {
                    const navMitra = document.getElementById('navGabungMitra');
                    if (navMitra) navMitra.classList.add('hidden');
                    const mobileNavMitra = document.getElementById('mobileNavGabungMitra');
                    if (mobileNavMitra) mobileNavMitra.classList.add('hidden');
                }

                // Dashboard button redirect link based on role
                const dashBtn = document.getElementById('dashboardBtn');
                if (dashBtn) {
                    if (dashUrl) {
                        dashBtn.addEventListener('click', () => {
                            window.location.href = dashUrl;
                        });
                    } else {
                        dashBtn.innerHTML = '⏳ Menunggu ACC Admin';
                        dashBtn.className = 'bg-amber-100 text-amber-900 border-2 border-amber-500 text-xs px-3 py-1.5 rounded-lg font-bold shadow-sm';
                        dashBtn.addEventListener('click', () => {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pendaftaran Menunggu ACC ⏳',
                                text: 'Akun Anda sedang dalam antrean verifikasi Admin Verifikator. Anda belum dapat masuk ke dasbor sebelum akun di-ACC oleh Admin.',
                                confirmButtonColor: '#0B4A8F'
                            });
                        });
                    }
                }

                // Auto redirect immediately upon visiting home if logged in and not explicitly requesting home
                if (dashUrl && !urlParams.has('stay') && !urlParams.has('auth')) {
                    window.location.href = dashUrl;
                }
            }
        });

        function openAuthModal(mode) {
            document.getElementById('authModal').classList.remove('hidden');
            backToLoginOrRegister();
            toggleAuthMode(mode === 'register');
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.add('hidden');
            if (resendTimerInterval) clearInterval(resendTimerInterval);
        }

        function toggleAuthMode(isRegister) {
            const toggle = document.getElementById('authModeToggle');
            const front = document.getElementById('cardFrontLogin');
            const back = document.getElementById('cardBackRegister');
            const otpCard = document.getElementById('uiverseOtpCard');

            if (otpCard) otpCard.classList.add('hidden');
            if (toggle) toggle.checked = isRegister;

            if (isRegister) {
                if (front) front.classList.add('hidden');
                if (back) back.classList.remove('hidden');
            } else {
                if (front) front.classList.remove('hidden');
                if (back) back.classList.add('hidden');
            }
        }

        function showOtpStep(email) {
            currentOtpEmail = (email || '').trim().toLowerCase();
            const switchWrap = document.getElementById('uiverseSwitchWrapper');
            const front = document.getElementById('cardFrontLogin');
            const back = document.getElementById('cardBackRegister');
            const otpCard = document.getElementById('uiverseOtpCard');
            const targetEmailSpan = document.getElementById('uiverseOtpTargetEmail');
            const otpInput = document.getElementById('otpCodeInput');

            if (switchWrap) switchWrap.classList.add('hidden');
            if (front) front.classList.add('hidden');
            if (back) back.classList.add('hidden');

            if (otpCard) otpCard.classList.remove('hidden');
            if (targetEmailSpan) targetEmailSpan.innerText = email;
            if (otpInput) otpInput.value = '';

            startResendTimer(60);
        }

        function backToLoginOrRegister() {
            const switchWrap = document.getElementById('uiverseSwitchWrapper');
            const otpCard = document.getElementById('uiverseOtpCard');
            const toggle = document.getElementById('authModeToggle');

            if (switchWrap) switchWrap.classList.remove('hidden');
            if (otpCard) otpCard.classList.add('hidden');

            const isRegister = toggle ? toggle.checked : false;
            toggleAuthMode(isRegister);
        }

        function startResendTimer(seconds) {
            const btn = document.getElementById('resendOtpBtn');
            const timerSpan = document.getElementById('resendTimer');
            if (!btn || !timerSpan) return;

            btn.disabled = true;
            let left = seconds;
            timerSpan.innerText = left;

            if (resendTimerInterval) clearInterval(resendTimerInterval);
            resendTimerInterval = setInterval(() => {
                left--;
                timerSpan.innerText = left;
                if (left <= 0) {
                    clearInterval(resendTimerInterval);
                    btn.disabled = false;
                    btn.innerText = 'Kirim Ulang Kode';
                }
            }, 1000);
        }

        async function handleResendOtp() {
            if (!currentOtpEmail) return;
            try {
                const res = await fetch('/api/auth/resend-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentOtpEmail })
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: d.message || 'Kode verifikasi baru telah dikirim!',
                        showConfirmButton: false,
                        timer: 2500,
                        background: '#0F172A',
                        color: '#E2E8F0'
                    });
                    startResendTimer(d.cooldown_seconds || 60);
                } else {
                    Swal.fire({ title: 'Gagal', text: d.message || 'Gagal mengirim ulang kode.', icon: 'error', background: '#0F172A', color: '#E2E8F0', confirmButtonColor: '#0B4A8F' });
                }
            } catch(e) {
                console.error(e);
                Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan jaringan.', icon: 'error', background: '#0F172A', color: '#E2E8F0', confirmButtonColor: '#0B4A8F' });
            }
        }

        async function handleUserLogin(e) {
            e.preventDefault();
            const email = document.getElementById('userLoginEmail').value.trim();
            const password = document.getElementById('userLoginPassword').value;

            const submitBtn = document.getElementById('loginSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Memproses...';

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();
                submitBtn.disabled = false;
                submitBtn.innerText = 'Masuk Sekarang';
                if (response.status === 429) {
                    window.location.href = '/error/429';
                    return;
                }

                if (!response.ok) {
                    Swal.fire({
                        title: 'Masuk Gagal',
                        text: result.message || 'Email atau password salah.',
                        icon: 'error',
                        confirmButtonColor: '#0B4A8F'
                    });
                    return;
                }

                // Jika butuh verifikasi OTP
                if (result.requires_otp) {
                    showOtpStep(result.email || email);
                    Swal.fire({
                        icon: 'info',
                        title: 'Kode OTP Terkirim',
                        text: result.message || 'Silakan cek email Anda untuk 6-digit kode OTP.',
                        confirmButtonColor: '#0B4A8F'
                    });
                    return;
                }

                // Login sukses langsung
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                if (result.profile) localStorage.setItem('profile', JSON.stringify(result.profile));
                document.cookie = "auth_token=" + result.access_token + "; path=/; max-age=86400; SameSite=Lax";

                const roleName = result.role || (result.user.role ? (result.user.role.nama || result.user.role) : '');
                const redirectUrl = getRoleDashboardUrl(roleName, result.user);
                const roleSlug = getRoleSlugName(roleName, result.user);

                if (roleSlug.startsWith('admin') || roleSlug.startsWith('super-admin')) {
                    localStorage.setItem('admin_token', result.access_token);
                    localStorage.setItem('admin_user', JSON.stringify({ ...result.user, role: roleSlug }));
                }

                if (!redirectUrl) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pendaftaran Menunggu ACC ⏳',
                        text: 'Akun Anda sedang dalam antrean verifikasi Admin Verifikator. Anda belum dapat masuk ke dasbor sebelum akun di-ACC oleh Admin.',
                        confirmButtonColor: '#0B4A8F'
                    }).then(() => {
                        closeAuthModal();
                        window.location.reload();
                    });
                    return;
                }

                Swal.fire({
                    title: 'Masuk Berhasil!',
                    text: 'Selamat datang kembali, ' + result.user.nama,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1200
                }).then(() => {
                    closeAuthModal();
                    window.location.href = redirectUrl;
                });
            } catch (err) {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Masuk Sekarang';
                Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan koneksi.', icon: 'error', confirmButtonColor: '#0B4A8F' });
            }
        }

        // Handle registrasi dari Modal Pop-up
        async function handleUserRegister(e) {
            e.preventDefault();
            const nama = document.getElementById('userRegisterNama').value.trim();
            const email = document.getElementById('userRegisterEmail').value.trim();
            const hp = document.getElementById('userRegisterHp').value.trim();
            const alamat = document.getElementById('userRegisterAlamat').value.trim();
            const password = document.getElementById('userRegisterPassword').value;
            const confirmPassword = document.getElementById('userRegisterConfirmPassword').value;

            executeRegistration(nama, email, hp, alamat, password, confirmPassword, 'registerSubmitBtn');
        }

        // Handle registrasi dari Form Landing Page Section #daftar
        async function handleSectionRegister(e) {
            e.preventDefault();
            const nama = document.getElementById('secRegNama').value.trim();
            const email = document.getElementById('secRegEmail').value.trim();
            const hp = document.getElementById('secRegHp').value.trim();
            const alamat = document.getElementById('secRegAlamat').value.trim();
            const password = document.getElementById('secRegPassword').value;
            const confirmPassword = document.getElementById('secRegConfirmPassword').value;

            executeRegistration(nama, email, hp, alamat, password, confirmPassword, 'secRegSubmitBtn');
        }

        // Helper Eksekusi Register ke API
        async function executeRegistration(nama, email, hp, alamat, password, confirmPassword, btnId) {
            if (password !== confirmPassword) {
                Swal.fire({
                    title: 'Kata Sandi Tidak Cocok',
                    text: 'Konfirmasi kata sandi harus sama dengan kata sandi yang Anda masukkan.',
                    icon: 'warning',
                    confirmButtonColor: '#0B4A8F'
                });
                return;
            }

            if (password.length < 6) {
                Swal.fire({
                    title: 'Kata Sandi Terlalu Pendek',
                    text: 'Kata sandi minimal 6 karakter.',
                    icon: 'warning',
                    confirmButtonColor: '#0B4A8F'
                });
                return;
            }

            const submitBtn = document.getElementById(btnId);
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Memproses Pendaftaran...';
            }

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        role_slug: 'pelanggan',
                        nama,
                        email,
                        hp,
                        alamat,
                        password
                    })
                });

                const result = await response.json();
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Daftar Sekarang';
                }

                if (response.status === 429) {
                    window.location.href = '/error/429';
                    return;
                }

                if (!response.ok) {
                    let errMsg = result.message;
                    if (result.errors) {
                        const errs = Object.values(result.errors).flat();
                        errMsg = errs.join('<br>');
                    }
                    Swal.fire({ title: 'Pendaftaran Gagal', html: errMsg || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#0B4A8F' });
                    return;
                }

                // Cek kebutuhan OTP
                if (result.requires_otp) {
                    openAuthModal('register');
                    showOtpStep(result.email || email);
                    Swal.fire({
                        icon: 'info',
                        title: 'Kode OTP Terkirim',
                        text: result.message || 'Silakan cek email Anda untuk 6-digit kode verifikasi.',
                        confirmButtonColor: '#0B4A8F'
                    });
                    return;
                }

                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                document.cookie = "auth_token=" + result.access_token + "; path=/; max-age=86400; SameSite=Lax";

                Swal.fire({
                    title: 'Pendaftaran Berhasil!',
                    text: 'Selamat datang di REVARO, ' + result.user.nama,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1200
                }).then(() => {
                    closeAuthModal();
                    window.location.href = '/pelanggan/dashboard';
                });
            } catch (err) {
                console.error(err);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Daftar Sekarang';
                }
                Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan koneksi.', icon: 'error', confirmButtonColor: '#0B4A8F' });
            }
        }

        async function handleOtpSubmit(e) {
            e.preventDefault();
            const otp_code = document.getElementById('otpCodeInput').value.trim();
            if (otp_code.length !== 6) {
                Swal.fire({ title: 'Kode OTP Tidak Lengkap', text: 'Silakan masukkan 6 angka kode OTP dari email Anda.', icon: 'warning', confirmButtonColor: '#0B4A8F' });
                return;
            }

            const btn = document.getElementById('verifyOtpBtn');
            btn.disabled = true;
            btn.innerText = 'Memverifikasi...';

            try {
                const res = await fetch('/api/auth/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: currentOtpEmail, otp_code })
                });

                const result = await res.json();
                btn.disabled = false;
                btn.innerText = 'Verifikasi';

                if (res.status === 429) {
                    window.location.href = '/error/429';
                    return;
                }

                if (!res.ok) {
                    Swal.fire({ title: 'Verifikasi Gagal', text: result.message || 'Kode OTP salah atau kadaluarsa.', icon: 'error', confirmButtonColor: '#0B4A8F' });
                    return;
                }

                // Simpan token
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                if (result.profile) localStorage.setItem('profile', JSON.stringify(result.profile));
                document.cookie = "auth_token=" + result.access_token + "; path=/; max-age=86400; SameSite=Lax";

                const roleName = result.role || (result.user.role ? (result.user.role.nama || result.user.role) : '');
                const redirectUrl = getRoleDashboardUrl(roleName, result.user);
                const roleSlug = getRoleSlugName(roleName, result.user);

                if (roleSlug.startsWith('admin') || roleSlug.startsWith('super-admin')) {
                    localStorage.setItem('admin_token', result.access_token);
                    localStorage.setItem('admin_user', JSON.stringify({ ...result.user, role: roleSlug }));
                }

                if (!redirectUrl) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Pendaftaran Menunggu ACC ⏳',
                        text: 'Akun Anda sedang dalam antrean verifikasi Admin Verifikator. Anda belum dapat masuk ke dasbor sebelum akun di-ACC oleh Admin.',
                        confirmButtonColor: '#0B4A8F'
                    }).then(() => {
                        closeAuthModal();
                        window.location.reload();
                    });
                    return;
                }

                Swal.fire({
                    title: 'Verifikasi Berhasil!',
                    text: 'Selamat datang di REVARO, ' + result.user.nama,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1200
                }).then(() => {
                    closeAuthModal();
                    window.location.href = redirectUrl;
                });
            } catch(err) {
                console.error(err);
                btn.disabled = false;
                btn.innerText = 'Verifikasi';
                Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan sistem.', icon: 'error', confirmButtonColor: '#0B4A8F' });
            }
        }

        function toggleGenericPassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<svg class="w-5 h-5 text-brandBlue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 014.122-.988c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<svg class="w-5 h-5 text-brandBlue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
            }
        }

        function handleServiceOrder(kategori) {
            const token = localStorage.getItem('token');
            const userStr = localStorage.getItem('user');
            if (token && userStr) {
                try {
                    const user = JSON.parse(userStr);
                    const roleName = user.role ? (user.role.nama || user.role) : '';
                    const roleSlug = getRoleSlugName(roleName);
                    if (roleSlug === 'pelanggan') {
                        window.location.href = '/pelanggan/dashboard?service=' + encodeURIComponent(kategori);
                        return;
                    } else {
                        // User is partner/admin
                        if (window.Swal) {
                            Swal.fire({
                                title: 'Pesan Servis ' + kategori,
                                html: `
                                    <div class="text-left space-y-2 text-xs text-textDark">
                                        <p>Anda sedang masuk menggunakan akun <strong>${roleName}</strong>.</p>
                                        <p class="text-slate-500">Pemesanan jasa perbaikan elektronik REVARO ditujukan untuk akun <strong>Pelanggan</strong>. Silakan beralih atau masuk menggunakan akun Pelanggan untuk melakukan order servis.</p>
                                    </div>
                                `,
                                icon: 'info',
                                confirmButtonColor: '#0B4A8F',
                                confirmButtonText: 'Buka Dasbor Saya',
                                showCancelButton: true,
                                cancelButtonText: 'Tutup'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    window.location.href = getRoleDashboardUrl(roleName, user);
                                }
                            });
                        } else {
                            window.location.href = getRoleDashboardUrl(roleName, user);
                        }
                        return;
                    }
                } catch(e) {}
            }
            // If not logged in, prompt login modal
            openAuthModal('login');
            if (window.Swal) {
                Swal.fire({
                    title: 'Pesan Servis ' + kategori,
                    text: 'Silakan masuk atau daftar akun Pelanggan terlebih dahulu untuk memesan servis perbaikan elektronik.',
                    icon: 'info',
                    confirmButtonColor: '#0B4A8F',
                    confirmButtonText: 'Siap, Mengerti'
                });
            }
        }

        // === Carousel Geser Menyamping ===
        function scrollServiceCarousel(direction) {
            const container = document.getElementById('serviceCarouselContainer');
            if (!container) return;
            const scrollDistance = 370; // geser 1 kolom
            container.scrollBy({ left: direction * scrollDistance, behavior: 'smooth' });
        }

        async function handleLogout() {
            const token = localStorage.getItem('token');
            try {
                if (token) {
                    await fetch('/api/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                }
            } catch(e) {}
            localStorage.clear();
            sessionStorage.clear();
            window.location.reload();
        }
    </script>
</body>
</html>
