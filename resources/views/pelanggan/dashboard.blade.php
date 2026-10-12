<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard — REVARO</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/images/revaro-icon-transparent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ config('services.midtrans.is_production', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0B4A8F', // Deep Blue
                        secondary: '#FF7F00', // Vibrant Orange
                        accent: '#E6EFFF', // Light Blue Accent
                        textDark: '#1E293B',
                        textLight: '#64748B',
                        brandBlue: '#0B4A8F',
                        brandOrange: '#FF7F00',
                    },
                    boxShadow: {
                        'form': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
                        'btn': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
                        'orange': '4px 4px 0px 0px rgba(255, 127, 0, 1)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .revaro-card {
            background-color: #ffffff;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #0B4A8F;
            border-radius: 1rem;
        }
        .revaro-card-secondary {
            background-color: #ffffff;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #FF7F00;
            border-radius: 1rem;
        }
        .revaro-btn {
            background-color: #0B4A8F;
            color: white;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #FF7F00;
            transition: all 0.15s ease;
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
            transition: all 0.15s ease;
        }
        .revaro-btn-orange:hover {
            box-shadow: 2px 2px 0px 0px #0B4A8F;
            transform: translate(2px, 2px);
        }
        .revaro-btn-outline {
            background-color: white;
            color: #0B4A8F;
            border: 2px solid #0B4A8F;
            box-shadow: 3px 3px 0px 0px #0B4A8F;
            transition: all 0.15s ease;
        }
        .revaro-btn-outline:hover {
            box-shadow: 1px 1px 0px 0px #0B4A8F;
            transform: translate(2px, 2px);
        }
        .revaro-input {
            border: 2px solid #0B4A8F;
            box-shadow: 2px 2px 0px 0px #0B4A8F;
            transition: all 0.2s ease;
        }
        .revaro-input:focus {
            outline: none;
            box-shadow: 1px 1px 0px 0px #FF7F00;
            border-color: #FF7F00;
            transform: translate(1px, 1px);
        }
        .shimmer {
            background: linear-gradient(90deg, #E6EFFF 25%, #dbeafe 50%, #E6EFFF 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(11, 74, 143, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 127, 0, 0.4);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 127, 0, 0.7);
        }
    </style>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

</head>
<body class="bg-gray-50 min-h-screen text-textDark flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b-2 border-primary sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center">
            <a href="/pelanggan/dashboard" class="flex items-center space-x-3 py-1">
                <img src="/images/revaro-logo-horizontal.png" alt="REVARO" class="h-11 sm:h-12 w-auto max-w-[210px] object-contain drop-shadow-sm">
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent text-primary font-bold border border-primary hidden sm:inline-block">Pelanggan</span>
            </a>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Tombol Cepat: Pesan Servis -->
                <button onclick="switchPelangganTab('pesan')" type="button" class="revaro-btn-orange text-xs px-3 py-1.5 rounded-xl font-black flex items-center gap-1.5 shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition" title="Pesan Layanan Servis Cepat">
                    <i class="fa-solid fa-wrench"></i>
                    <span class="hidden md:inline">Pesan Servis</span>
                </button>

                <!-- Dropdown Switcher Dasbor / Peran Multi-Akun -->
                <div class="relative inline-block text-left" id="globalRoleSwitcherWrapper">
                    <button onclick="toggleGlobalRoleDropdown()" type="button" class="relative text-xs font-extrabold text-primary bg-white hover:bg-accent/40 px-3 py-1.5 rounded-xl border-2 border-primary shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition flex items-center gap-1.5 focus:outline-none" title="Pindah Dasbor Peran">
                        <span>🔄</span>
                        <span class="hidden sm:inline">Pindah Dasbor</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-textLight"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="globalRoleDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border-2 border-primary rounded-2xl shadow-[4px_4px_0px_0px_#0B4A8F] z-50 overflow-hidden divide-y divide-gray-100">
                        <div class="p-3 bg-accent/60 border-b-2 border-primary flex items-center justify-between">
                            <span class="text-[11px] font-black uppercase text-primary tracking-wider">Peran &amp; Dasbor Akun</span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-primary text-white">Multi-Role</span>
                        </div>
                        <div class="p-2 space-y-1">
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-accent border-2 border-primary">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">👤</span>
                                    <div>
                                        <p class="text-xs font-bold text-primary">Dasbor Pelanggan</p>
                                        <p class="text-[10px] text-textLight">Pesan servis SOS &amp; lacak pesanan</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black bg-primary text-white px-2 py-0.5 rounded-full">Aktif</span>
                            </div>
                            <a href="/teknisi/dashboard" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-accent/50 transition">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">🔧</span>
                                    <div>
                                        <p class="text-xs font-bold text-textDark">Dasbor Teknisi</p>
                                        <p class="text-[10px] text-textLight">Terima &amp; eksekusi order servis</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-primary font-bold">Buka →</span>
                            </a>
                            <a href="/toko/dashboard" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-accent/50 transition">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">🏪</span>
                                    <div>
                                        <p class="text-xs font-bold text-textDark">Dasbor Toko Sparepart</p>
                                        <p class="text-[10px] text-textLight">Katalog suku cadang &amp; omset</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-primary font-bold">Buka →</span>
                            </a>
                        </div>
                        <div class="p-2 bg-gray-50 border-t border-gray-100">
                            <a href="/partner/register" class="flex items-center justify-center gap-1.5 w-full py-2 px-3 text-xs font-black text-secondary hover:bg-orange-50 rounded-xl border-2 border-dashed border-secondary transition">
                                <i class="fa-solid fa-plus"></i> <span>Daftar Kemitraan Baru</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notification Bell Component -->
                <div class="relative inline-block text-left" id="notifBellWrapper">
                    <button onclick="toggleNotificationDropdown()" type="button" class="relative p-2 text-primary hover:text-secondary rounded-xl border-2 border-primary bg-white shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition flex items-center justify-center focus:outline-none" title="Notifikasi">
                        <i class="fa-solid fa-bell text-base"></i>
                        <span id="notifBadge" class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-black px-1.5 py-0.2 rounded-full min-w-[18px] text-center border-2 border-white hidden animate-pulse">0</span>
                    </button>
                    
                    <!-- Notification Dropdown Menu -->
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white border-2 border-primary rounded-2xl shadow-[4px_4px_0px_0px_#0B4A8F] z-50 overflow-hidden">
                        <div class="p-3.5 bg-accent/60 border-b-2 border-primary flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bell text-primary text-sm"></i>
                                <h4 class="font-extrabold text-sm text-primary">Notifikasi</h4>
                                <span id="notifCountLabel" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-primary text-white">0 Baru</span>
                            </div>
                            <button onclick="markAllNotificationsAsRead()" type="button" class="text-xs font-bold text-secondary hover:underline">Tandai Semua Dibaca</button>
                        </div>
                        <div id="notifListContainer" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                            <p class="text-xs text-textLight text-center py-6">Memuat notifikasi...</p>
                        </div>
                    </div>
                </div>

                <span id="navUserName" class="text-xs font-bold bg-accent text-primary px-3 py-1.5 rounded-xl border-2 border-primary hidden sm:inline-block">Pelanggan</span>
                <button onclick="handleLogout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3.5 py-1.5 rounded-xl font-bold border-2 border-red-700 shadow-[2px_2px_0px_0px_#991B1B] transition">Keluar</button>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 md:pl-20 pb-28">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: SOS Booking & Order Status Panel (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
            
            <!-- Booking Form Panel -->
            <div id="bookingFormPanel" class="revaro-card p-6 md:p-8 space-y-5">
                <div class="border-b-2 border-accent pb-3 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-extrabold text-primary flex items-center space-x-2">
                            <span>🚀 Order Servis Elektronik Cepat (SOS)</span>
                        </h3>
                        <p class="text-xs text-textLight mt-0.5">Isi formulir keluhan untuk memanggil Teknisi Terverifikasi ke lokasi Anda secara instan.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-accent text-primary border border-primary hidden sm:inline-block">Garansi Sistem Aktif</span>
                </div>

                <form id="sosForm" onsubmit="submitSOSOrder(event)" class="space-y-4">
                    <!-- Toggle Pemesan -->
                    <div class="flex items-center gap-4 bg-accent/40 border-2 border-primary/20 p-2.5 rounded-xl mb-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-bold text-primary">
                            <input type="radio" name="bookingType" value="sendiri" checked onchange="toggleBookingType()" class="accent-primary w-4 h-4">
                            Pesan untuk Saya Sendiri
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold text-textLight hover:text-primary">
                            <input type="radio" name="bookingType" value="orang_lain" onchange="toggleBookingType()" class="accent-primary w-4 h-4">
                            Pesankan untuk Orang Lain
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-primary">Kategori Servis (Data Master Terverifikasi)</label>
                                <span id="categoryWarrantyBadge" class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 border border-emerald-500/30">Garansi 30 Hari</span>
                            </div>
                            <select required id="bookingCategory" onchange="handleCategoryChange()" class="w-full text-sm bg-white revaro-input rounded-xl p-3 text-primary font-bold outline-none">
                                <option value="" disabled selected>Memuat kategori dari database master...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom inputs for 'Orang Lain' -->
                    <div id="otherPersonFields" class="hidden bg-accent/30 border-2 border-primary/30 p-4 rounded-xl space-y-4">
                        <p class="text-xs font-extrabold text-primary">Data Orang yang Dipesankan</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-textDark mb-1">Nama Penerima Servis</label>
                                <input type="text" id="pemesanNama" class="w-full text-sm bg-white revaro-input rounded-xl p-3 outline-none" placeholder="Contoh: Budi Prasetyo">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-textDark mb-1">Nomor HP / WhatsApp</label>
                                <input type="text" id="pemesanHp" class="w-full text-sm bg-white revaro-input rounded-xl p-3 outline-none" placeholder="08xxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between">
                            <label class="block text-xs font-bold text-primary mb-1">Keluhan / Gejala Kerusakan Perangkat</label>
                            <span class="text-[10px] text-textLight font-semibold" id="charCount">0/500</span>
                        </div>
                        <textarea required id="bookingComplaint" rows="2" maxlength="500" oninput="updateCharCount()" class="w-full text-sm bg-white revaro-input rounded-xl p-3 outline-none font-medium" placeholder="Contoh: AC bocor meneteskan air terus dan tiupan angin tidak dingin..."></textarea>
                    </div>

                    <!-- Real Map Area with GoAPI Suggestion & Saved Address History -->
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1.5">
                            <label class="block text-xs font-bold text-primary">Pilih Lokasi &amp; Alamat Kedatangan Teknisi</label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openSavedAddressModal()" class="text-[11px] font-extrabold text-primary bg-accent/60 hover:bg-accent border border-primary/40 px-2.5 py-1 rounded-lg transition flex items-center gap-1 shadow-sm" title="Pilih dari Riwayat Pemesanan atau Alamat Favorit Tersimpan">
                                    <span>📍</span> <span>Riwayat &amp; Alamat Tersimpan</span>
                                    <span id="savedAddressCountBadge" class="hidden bg-primary text-white text-[9px] px-1.5 py-0.2 rounded-full font-mono">0</span>
                                </button>
                                <button type="button" onclick="saveCurrentAddressPrompt()" class="text-[11px] font-extrabold text-secondary bg-orange-50 hover:bg-orange-100 border border-secondary/40 px-2.5 py-1 rounded-lg transition flex items-center gap-1 shadow-sm" title="Simpan Lokasi Ini ke Penanda Favorit">
                                    <span>⭐</span> <span>Simpan Titik Ini</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <div class="relative">
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" id="searchMapInput" oninput="handleGoApiInput(this.value)" autocomplete="off" class="w-full text-sm bg-white revaro-input rounded-xl p-3 pr-10 outline-none font-medium" placeholder="Cari tempat, nama jalan, atau daerah sekitar...">
                                        <span id="goapiLoading" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-primary">
                                            <i class="fas fa-spinner fa-spin text-sm"></i>
                                        </span>
                                    </div>
                                    <button type="button" onclick="searchLocation()" class="revaro-btn-outline font-bold px-4 rounded-xl text-sm transition">Cari</button>
                                    <button type="button" onclick="getCurrentLocation()" class="revaro-btn font-bold px-4 rounded-xl text-sm transition flex items-center gap-1.5" title="Gunakan Lokasi GPS Saya">
                                        <i class="fas fa-location-arrow"></i> <span>GPS</span>
                                    </button>
                                </div>
                                <!-- GoAPI Suggestions Dropdown -->
                                <div id="goapiSuggestions" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border-2 border-primary rounded-xl shadow-form max-h-60 overflow-y-auto z-50 divide-y divide-accent">
                                    <!-- Dynamic suggestions injected here -->
                                </div>
                            </div>
                            <div id="map" class="w-full h-64 rounded-xl border-2 border-primary z-0 relative shadow-sm"></div>
                            <div>
                                <label class="block text-[11px] font-bold text-primary mb-1 uppercase">Detail Alamat Lengkap (Bahasa Indonesia)</label>
                                <input required type="text" id="bookingAddress" class="w-full text-sm bg-white revaro-input rounded-xl p-3 outline-none font-medium" placeholder="Detail Alamat Lengkap (Contoh: Jl. Slipi IX No. 45, Palmerah, Jakarta Barat)">
                            </div>
                        </div>
                        
                        <!-- Hidden Map Coordinates -->
                        <input type="hidden" id="geoLat" value="-6.2088">
                        <input type="hidden" id="geoLong" value="106.8456">
                    </div>

                    <!-- Radius Pencarian Teknisi (Standar 7 KM vs Premium 20 KM) -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold text-primary uppercase">Jangkauan Radar Pencarian Teknisi</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="searchRadiusOption" value="7" checked onchange="toggleRadiusOption(7)" class="peer sr-only">
                                <div class="p-3.5 rounded-xl border-2 border-primary/30 peer-checked:border-primary peer-checked:bg-accent/40 peer-checked:shadow-[3px_3px_0px_0px_#0B4A8F] transition bg-white space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black text-primary flex items-center gap-1.5">
                                            <span>⚡ Standar Reguler</span>
                                        </span>
                                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">7 KM • Tarif Standar</span>
                                    </div>
                                    <p class="text-[11px] text-textLight">Mencari teknisi terdekat di lingkungan sekitar dalam radius standar 7 KM.</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="searchRadiusOption" value="20" onchange="toggleRadiusOption(20)" class="peer sr-only">
                                <div class="p-3.5 rounded-xl border-2 border-secondary/40 peer-checked:border-secondary peer-checked:bg-orange-50 peer-checked:shadow-[3px_3px_0px_0px_#FF7F00] transition bg-white space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black text-secondary flex items-center gap-1.5">
                                            <span>👑 Jangkauan Luas Prioritas</span>
                                        </span>
                                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-secondary text-white shadow-sm">+ Rp <span id="premiumFeeLabel">25.000</span></span>
                                    </div>
                                    <p class="text-[11px] text-textDark">
                                        Memperluas radar hingga 20 KM untuk menjangkau lebih banyak teknisi ahli tersertifikasi dan prioritas pencarian SOS <em>(dikenakan biaya tambahan)</em>.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnSubmitSOS" class="w-full revaro-btn font-extrabold text-base py-4 px-6 rounded-xl transition flex items-center justify-center space-x-2">
                        <span>Panggil Teknisi Sekarang (SOS — Radius 7 KM)</span> <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- Live Tracking & Dispatch Status -->
            <div id="liveOrderPanel" class="hidden revaro-card p-6 md:p-8 space-y-6">
                <div class="flex justify-between items-center border-b-2 border-accent pb-3">
                    <div>
                        <span id="orderBadge" class="bg-accent text-primary border border-primary text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">Menghubungkan...</span>
                        <h3 id="orderCodeTitle" class="text-base font-extrabold text-primary mt-1">REV-ORDER-0000</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-textLight font-bold uppercase">Estimasi Biaya Jasa</p>
                        <p id="orderPrice" class="text-base font-black text-secondary">Rp 150.000</p>
                    </div>
                </div>

                <!-- Searching state loader -->
                <div id="searchingState" class="space-y-4 py-4 text-center">
                    <div class="shimmer w-16 h-16 rounded-full mx-auto relative flex items-center justify-center border-2 border-primary">
                        <svg class="w-8 h-8 text-primary animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-primary">Mencari Teknisi Terdekat...</h4>
                        <p class="text-xs text-textLight mt-0.5">Sistem sedang mencocokkan Anda dengan mitra teknisi terverifikasi dalam radius 5 - 15 KM.</p>
                    </div>
                </div>

                <!-- Tech info details -->
                <div id="techAssignedDetails" class="hidden p-4 rounded-xl border-2 border-primary bg-accent/40 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-primary text-white border-2 border-primary flex items-center justify-center text-lg font-bold shadow-btn">
                            🔧
                        </div>
                        <div>
                            <h4 id="assignedTechName" class="text-sm font-extrabold text-primary">Nama Teknisi</h4>
                            <p id="assignedTechHP" class="text-xs text-textLight font-medium"></p>
                        </div>
                    </div>
                    <div>
                        <span id="assignedTechStatusBadge" class="bg-secondary text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-sm">Teknisi Meluncur</span>
                    </div>
                </div>

                <!-- Dispute Active Notice Banner -->
                <div id="disputeActiveNotice" class="hidden p-4 rounded-xl border-2 border-amber-400 bg-amber-50 text-amber-900 flex items-center gap-3 shadow-sm">
                    <span class="text-2xl">⚖️</span>
                    <div>
                        <h4 class="text-xs font-black uppercase text-amber-800">Status Sengketa Aktif</h4>
                        <p class="text-[11px] text-amber-700">Pesanan ini sedang dalam proses investigasi oleh Tim Resolusi. Pembayaran dan tindakan layanan dibekukan hingga ada keputusan final.</p>
                    </div>
                </div>

                <!-- Chat simulation box -->
                <div id="chatBoxContainer" class="hidden border-2 border-primary rounded-xl overflow-hidden flex flex-col h-56 bg-white shadow-form">
                    <div class="bg-primary text-white px-4 py-2.5 flex justify-between items-center text-xs">
                        <span class="font-extrabold flex items-center gap-1.5"><i class="fas fa-comments text-secondary"></i> Chat Langsung Dengan Teknisi</span>
                        <span class="text-[10px] text-green-300 font-bold flex items-center space-x-1">
                            <span class="w-2 h-2 rounded-full bg-green-400 inline-block animate-ping"></span>
                            <span>Online</span>
                        </span>
                    </div>
                    <div id="chatMessagesBox" class="flex-grow p-3 overflow-y-auto space-y-2 text-xs bg-slate-50">
                        <!-- Chats populate here -->
                    </div>
                    <form onsubmit="sendChatMessage(event)" class="border-t-2 border-primary flex p-1.5 bg-white">
                        <input id="chatInput" type="text" class="flex-grow text-xs px-3 outline-none font-medium" placeholder="Ketik pesan konfirmasi ke teknisi...">
                        <button type="submit" class="revaro-btn text-xs px-4 py-2 rounded-lg font-extrabold">Kirim</button>
                    </form>
                </div>

                <!-- Sparepart Approval Card -->
                <div id="sparepartApprovalBlock" class="hidden bg-orange-50 border-2 border-secondary rounded-2xl p-5 space-y-3 shadow-form">
                    <div class="flex items-center space-x-2">
                        <span class="text-xl">⚙️</span>
                        <h4 class="text-sm font-black text-primary uppercase tracking-wide">Persetujuan Suku Cadang Resmi Toko</h4>
                    </div>
                    <p class="text-xs text-textDark leading-normal">Teknisi mengajukan suku cadang tambahan resmi. Seluruh suku cadang dipasok oleh Toko Sparepart Terverifikasi dengan harga katalog pasti:</p>
                    
                    <div class="overflow-x-auto bg-white rounded-xl border border-secondary/40 p-2">
                        <table class="w-full text-xs text-left">
                            <thead>
                                <tr class="text-textLight border-b border-orange-200 text-[10px] uppercase font-black">
                                    <th class="pb-1.5">Nama Suku Cadang</th>
                                    <th class="pb-1.5 text-center">Qty</th>
                                    <th class="pb-1.5 text-right">Harga Satuan</th>
                                    <th class="pb-1.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="sparepartsApproveList" class="divide-y divide-orange-100 font-semibold text-textDark">
                                <!-- Spareparts here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button onclick="respondSpareparts(false)" class="revaro-btn-outline text-xs px-4 py-2 rounded-xl font-bold">Tolak Sparepart</button>
                        <button onclick="respondSpareparts(true)" class="revaro-btn-orange text-xs px-5 py-2 rounded-xl font-black shadow">Setujui &amp; Pesan Suku Cadang</button>
                    </div>
                </div>

                <!-- Payment Block (Invoice) -->
                <div id="paymentBlock" class="hidden bg-white text-textDark rounded-2xl p-6 space-y-4 shadow-form border-4 border-primary">
                    <div class="border-b-2 border-accent pb-3 flex justify-between items-center">
                        <h4 class="text-base font-extrabold text-primary flex items-center space-x-2">
                            <span>🧾</span> <span>e-Nota Transaksi Resmi REVARO</span>
                        </h4>
                        <span class="bg-red-100 text-red-700 text-[10px] font-black uppercase px-2.5 py-1 rounded-full border border-red-300">Menunggu Pembayaran</span>
                    </div>

                    <div class="text-xs space-y-2.5 font-medium">
                        <div class="flex justify-between">
                            <span class="text-textLight">Biaya Jasa Servis</span>
                            <span id="invoiceJasa" class="font-bold text-textDark">Rp 150.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-textLight">Biaya Suku Cadang Resmi (Toko Terverifikasi)</span>
                            <span id="invoiceSparepart" class="font-bold text-textDark">Rp 0</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-base font-black text-primary">
                            <span>Total Pembayaran</span>
                            <span id="invoiceTotal" class="text-secondary">Rp 150.000</span>
                        </div>
                        <!-- Rincian sparepart -->
                        <div id="invoiceItemsListContainer" class="hidden pt-2 border-t border-gray-100 space-y-1.5">
                            <p class="text-[10px] uppercase font-black text-textLight">Daftar Suku Cadang:</p>
                            <div id="invoiceItemsList" class="space-y-1.5">
                                <!-- items -->
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button onclick="payInvoice()" class="flex-grow revaro-btn-orange text-sm font-black py-3.5 rounded-xl transition shadow flex items-center justify-center gap-2">
                            <i class="fas fa-lock"></i> Bayar Aman via Rekening Bersama (Escrow)
                        </button>
                        <button onclick="triggerDisputeForm()" class="revaro-btn-outline text-xs px-4 py-3 rounded-xl font-bold transition">
                            Ajukan Sengketa
                        </button>
                    </div>
                </div>

                <!-- Escrow Confirm Release / Selesai -->
                <div id="escrowReleaseBlock" class="hidden bg-emerald-50 text-emerald-950 rounded-2xl p-6 border-2 border-emerald-600 shadow-form space-y-3">
                    <h4 class="text-base font-extrabold text-emerald-800 flex items-center space-x-2">
                        <span>🤝</span> <span>Dana Diamankan di Escrow (Rekening Bersama)</span>
                    </h4>
                    <p class="text-xs text-emerald-900 leading-relaxed font-medium">Dana pembayaran Anda aman tertahan di sistem REVARO. Setelah perbaikan elektronik tuntas dan alat bekerja dengan baik, silakan tekan tombol di bawah untuk mencairkan dana ke Teknisi &amp; Toko.</p>
                    
                    <button onclick="confirmReleaseEscrow()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-extrabold py-3.5 rounded-xl transition shadow border-2 border-emerald-700">
                        Konfirmasi Perbaikan Selesai &amp; Cairkan Dana
                    </button>
                </div>

                <!-- Rating Review Block -->
                <div id="ratingReviewBlock" class="hidden revaro-card p-6 space-y-4">
                    <h4 class="text-base font-extrabold text-primary flex items-center space-x-2">
                        <span>⭐️</span> <span>Beri Ulasan Pekerjaan Teknisi</span>
                    </h4>
                    <p class="text-xs text-textLight">Ulasan Anda membantu menjaga akurasi Tier Reputasi teknisi di REVARO.</p>
                    
                    <div class="flex items-center space-x-3 justify-center py-2">
                        <button onclick="selectRating(1)" class="star-btn text-3xl text-gray-300 transition">★</button>
                        <button onclick="selectRating(2)" class="star-btn text-3xl text-gray-300 transition">★</button>
                        <button onclick="selectRating(3)" class="star-btn text-3xl text-gray-300 transition">★</button>
                        <button onclick="selectRating(4)" class="star-btn text-3xl text-gray-300 transition">★</button>
                        <button onclick="selectRating(5)" class="star-btn text-3xl text-gray-300 transition">★</button>
                    </div>
                    <input type="hidden" id="ratingScore" value="0">
                    <textarea id="ratingComment" rows="2" class="w-full text-xs bg-white revaro-input rounded-xl p-3 outline-none font-medium" placeholder="Tuliskan kepuasan atau saran atas pekerjaan teknisi..."></textarea>
                    
                    <button onclick="submitRating()" class="w-full revaro-btn text-xs font-bold py-3 rounded-xl transition">
                        Kirim Ulasan &amp; Selesaikan Order
                    </button>
                </div>

                <div class="flex justify-between items-center text-xs text-textLight pt-2 border-t border-accent">
                    <button id="cancelOrderBtn" onclick="cancelActiveOrder()" class="text-red-500 hover:text-red-700 font-bold transition">Batalkan Pesanan</button>
                    <span id="liveUpdatesTime" class="font-medium">Pembaruan otomatis aktif...</span>
                </div>
            </div>

            <!-- Dispute Form Panel (Komplain/Garansi) -->
            <div id="disputeFormPanel" class="hidden revaro-card p-6 md:p-8 space-y-4 border-red-500">
                <div class="border-b-2 border-red-100 pb-3 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-extrabold text-red-600">⚖️ Form Sengketa / Klaim Garansi Resmi</h3>
                        <p class="text-xs text-textLight mt-0.5">Ajukan investigasi kepada Admin Resolusi jika hasil perbaikan bermasalah atau terjadi wanprestasi.</p>
                    </div>
                    <button onclick="closeDisputeForm()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-textDark mb-1">Rincian Komplain / Klaim Garansi</label>
                        <textarea id="disputeReason" rows="3" class="w-full text-sm bg-white revaro-input rounded-xl p-3 outline-none" placeholder="Tuliskan kendala servis yang dialami secara rinci..."></textarea>
                    </div>
                    <button onclick="submitDispute()" class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm py-3.5 rounded-xl border-2 border-red-800 shadow-[3px_3px_0px_0px_#991B1B] transition">
                        Kirim Laporan ke Admin Resolusi REVARO
                    </button>
                </div>
            </div>
        </div>

                <!-- Right Column: User Account & Wallet (4 Cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- User Status Card -->
                    <div class="revaro-card p-6 text-center space-y-4">
                        <div id="loginStatusCard" class="space-y-3">
                            <div class="w-14 h-14 bg-accent text-primary border-2 border-primary rounded-2xl flex items-center justify-center mx-auto text-xl font-black shadow-btn">
                                P
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-primary">Pelanggan REVARO</h4>
                                <p class="text-xs text-textLight mt-0.5 font-medium">Memuat profil...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Quick Actions -->
                    <div class="revaro-card p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-black text-primary uppercase tracking-wider">Saldo Dompet</h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">Escrow Ready</span>
                        </div>
                        <div class="bg-accent/40 p-4 rounded-xl border-2 border-primary/20 text-center">
                            <p class="text-[10px] uppercase font-bold text-textLight">Total Saldo Aktif</p>
                            <p id="pelangganSaldoSnapshot" class="text-2xl font-black text-primary mt-1">Rp 0</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <button onclick="triggerTopup()" class="revaro-btn text-xs py-2.5 rounded-xl font-extrabold transition">💵 Isi Saldo</button>
                            <a href="/pelanggan/dompet" class="revaro-btn-outline text-xs py-2.5 rounded-xl font-bold transition text-center block">Buka Dompet →</a>
                        </div>
                    </div>

                    <!-- Quick Link to Riwayat -->
                    <div class="bg-white border-2 border-primary rounded-2xl p-5 shadow-form space-y-2.5">
                        <h4 class="text-xs font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                            <span>📋</span> <span>Riwayat &amp; Sengketa</span>
                        </h4>
                        <p class="text-xs text-textLight leading-relaxed">
                            Cek status pesanan servis Anda sebelumnya, rincian pembayaran, dan status garansi perlindungan.
                        </p>
                        <a href="/pelanggan/riwayat" class="w-full revaro-btn-outline text-xs py-2 rounded-xl font-bold transition text-center block">
                            Lihat Riwayat &amp; Sengketa →
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer Pelanggan dengan Logo REVARO & Kredit GOAPI -->
    <footer class="bg-white border-t-2 border-primary py-6 mt-12 text-center text-xs text-textLight">
        <div class="max-w-6xl mx-auto px-4 space-y-2">
            <div class="flex justify-center items-center">
                <img src="/images/revaro-logo-horizontal.png" alt="REVARO Logo" class="h-8 w-auto object-contain">
            </div>
            <p class="font-bold text-textDark">&copy; 2026 REVARO &mdash; Ekosistem Reparasi &amp; Restorasi Terpercaya Indonesia.</p>
            <p class="text-[11px] text-slate-400 font-medium">
                Sistem pemetaan lokasi dan penunjuk arah didukung oleh teknologi GOAPI Platform.
            </p>
        </div>
    </footer>

    <!-- Client-Side State Controller Logic -->
    <script>
        let currentOrder = null;
        let orderPollInterval = null;
        const token = localStorage.getItem('token');

        if (!token) {
            window.location.href = '/session-expired';
        }

        // Initialize details on load
        window.addEventListener('load', () => {
            fetchUserProfile();
            loadWalletLedger();
            loadDisputesList();
            checkActiveOrders();
            loadRoleSwitcher();
            fetchClientMapsSettings();
            fetchNotifications();
            if(document.getElementById('map')) initMap();
            orderPollInterval = setInterval(checkActiveOrders, 6000);
            setInterval(fetchNotifications, 15000);

            // Handle service deep link or pesan action parameter
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const serviceParam = urlParams.get('service');
                const actionParam = urlParams.get('action');
                const isHashBooking = window.location.hash === '#bookingFormPanel';

                if (serviceParam || actionParam === 'pesan' || isHashBooking) {
                    if (serviceParam) {
                        const catSelect = document.getElementById('bookingCategory');
                        if (catSelect) {
                            for (let i = 0; i < catSelect.options.length; i++) {
                                if (catSelect.options[i].value.toLowerCase() === serviceParam.toLowerCase()) {
                                    catSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    }
                    const bPanel = document.getElementById('bookingFormPanel');
                    if (bPanel) {
                        setTimeout(() => {
                            bPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            bPanel.classList.add('ring-4', 'ring-[#FF7F00]');
                            setTimeout(() => bPanel.classList.remove('ring-4', 'ring-[#FF7F00]'), 3500);
                            const firstInput = bPanel.querySelector('select, input');
                            if (firstInput) firstInput.focus();
                        }, 500);
                    }
                }
            } catch(e) {}
        });

        // --- Global Role Dropdown Switcher Controller ---
        let isGlobalRoleOpen = false;
        function toggleGlobalRoleDropdown() {
            const dropdown = document.getElementById('globalRoleDropdown');
            if (!dropdown) return;
            isGlobalRoleOpen = !isGlobalRoleOpen;
            if (isGlobalRoleOpen) dropdown.classList.remove('hidden');
            else dropdown.classList.add('hidden');
        }

        document.addEventListener('click', (e) => {
            const roleWrapper = document.getElementById('globalRoleSwitcherWrapper');
            if (roleWrapper && !roleWrapper.contains(e.target)) {
                const roleDropdown = document.getElementById('globalRoleDropdown');
                if (roleDropdown && !roleDropdown.classList.contains('hidden')) {
                    roleDropdown.classList.add('hidden');
                    isGlobalRoleOpen = false;
                }
            }
        });

        // Helper perlindungan XSS: Escape HTML strings sebelum dirender ke innerHTML
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // --- In-App Notifications Controller ---
        let isNotifOpen = false;
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notifDropdown');
            isNotifOpen = !isNotifOpen;
            if (isNotifOpen) {
                dropdown.classList.remove('hidden');
                fetchNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        }

        document.addEventListener('click', (e) => {
            const wrapper = document.getElementById('notifBellWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const dropdown = document.getElementById('notifDropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    isNotifOpen = false;
                }
            }
        });

        async function fetchNotifications() {
            if (!token) return;
            try {
                const res = await fetch('/api/notifications', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();
                
                const badge = document.getElementById('notifBadge');
                const countLabel = document.getElementById('notifCountLabel');
                const listContainer = document.getElementById('notifListContainer');

                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                if (countLabel) {
                    countLabel.textContent = `${data.unread_count} Baru`;
                }

                if (listContainer) {
                    if (!data.notifications || data.notifications.length === 0) {
                        listContainer.innerHTML = `
                            <div class="py-8 text-center text-textLight space-y-2">
                                <i class="fa-regular fa-bell-slash text-2xl text-slate-300"></i>
                                <p class="text-xs">Tidak ada notifikasi saat ini.</p>
                            </div>
                        `;
                        return;
                    }

                    listContainer.innerHTML = data.notifications.map(n => {
                        const isUnread = !n.dibaca;
                        const timeStr = new Date(n.created_at).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });
                        const nJson = JSON.stringify(n).replace(/"/g, '&quot;');
                        return `
                            <div onclick="handleNotificationClick(${n.id}, ${nJson})" class="p-3.5 transition cursor-pointer hover:bg-accent/30 ${isUnread ? 'bg-orange-50/70 border-l-4 border-secondary' : 'bg-white'}">
                                <div class="flex items-start justify-between gap-2">
                                    <h5 class="text-xs font-bold ${isUnread ? 'text-primary' : 'text-slate-700'}">${escapeHtml(n.judul)}</h5>
                                    <span class="text-[10px] text-textLight whitespace-nowrap shrink-0">${timeStr}</span>
                                </div>
                                <p class="text-xs text-textDark mt-1 leading-snug">${escapeHtml(n.pesan)}</p>
                            </div>
                        `;
                    }).join('');
                }
            } catch (e) {
                console.error('Error fetching notifications:', e);
            }
        }

        async function markNotificationRead(id) {
            try {
                await fetch(`/api/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                fetchNotifications();
            } catch (e) {}
        }

        async function handleNotificationClick(id, n) {
            // 1. Tandai sebagai telah dibaca
            await markNotificationRead(id);

            // 2. Tutup dropdown notifikasi jika terbuka
            const dd = document.getElementById('notifDropdown');
            if (dd) dd.classList.add('hidden');

            const title = n.judul || 'Notifikasi';
            const body = n.pesan || '';
            const lowerBody = body.toLowerCase();
            const lowerTitle = title.toLowerCase();

            // Deteksi ID Order jika ada
            const orderMatch = body.match(/#?ORD-(\d+)/i);
            const orderId = orderMatch ? orderMatch[1] : null;

            // Kasus 1: Status Verifikasi Mitra
            if (lowerTitle.includes('verifikasi') || lowerTitle.includes('mitra') || lowerBody.includes('verifikasi')) {
                const isApproved = lowerBody.includes('disetujui') || lowerBody.includes('acc') || lowerBody.includes('aktif');
                Swal.fire({
                    icon: isApproved ? 'success' : 'info',
                    title: title,
                    html: `
                        <div class="text-left space-y-2 text-xs text-textDark">
                            <p class="leading-relaxed">${body}</p>
                            ${isApproved ? '<div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 font-semibold">🎉 Selamat! Kemitraan Anda telah disetujui dan aktif.</div>' : ''}
                        </div>
                    `,
                    confirmButtonText: isApproved ? '🚀 Buka Dasbor Mitra' : 'Tutup',
                    confirmButtonColor: '#0B4A8F',
                    showCancelButton: isApproved,
                    cancelButtonText: 'Tetap di Sini'
                }).then((r) => {
                    if (isApproved && r.isConfirmed) {
                        window.location.href = lowerBody.includes('toko') ? '/toko/dashboard' : '/teknisi/dashboard';
                    }
                });
                return;
            }

            // Kasus 2: Status Pesanan / Teknisi
            if (orderId || lowerTitle.includes('pesanan') || lowerTitle.includes('order') || lowerBody.includes('teknisi')) {
                const activeCard = document.getElementById('activeOrderCard') || document.getElementById('activeOrderSection');
                if (activeCard && !activeCard.classList.contains('hidden')) {
                    activeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    activeCard.classList.add('ring-4', 'ring-secondary');
                    setTimeout(() => activeCard.classList.remove('ring-4', 'ring-secondary'), 3500);
                }

                Swal.fire({
                    icon: 'info',
                    title: title,
                    html: `
                        <div class="text-left space-y-2 text-xs text-textDark">
                            <p class="leading-relaxed">${body}</p>
                            ${orderId ? `<div class="p-2 bg-blue-50 border border-blue-200 rounded-lg text-primary font-mono text-[11px]">ID Pesanan: #ORD-${orderId}</div>` : ''}
                        </div>
                    `,
                    confirmButtonText: '📋 Buka Riwayat Pesanan',
                    cancelButtonText: 'Tutup',
                    showCancelButton: true,
                    confirmButtonColor: '#0B4A8F',
                    cancelButtonColor: '#64748B'
                }).then((r) => {
                    if (r.isConfirmed) {
                        window.location.href = '/pelanggan/riwayat';
                    }
                });
                return;
            }

            // Kasus 3: Dompet / Saldo / Pembayaran
            if (lowerTitle.includes('dompet') || lowerTitle.includes('saldo') || lowerTitle.includes('topup') || lowerTitle.includes('top up') || lowerBody.includes('saldo') || lowerBody.includes('pembayaran') || lowerBody.includes('escrow')) {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    html: `
                        <div class="text-left space-y-2 text-xs text-textDark">
                            <p class="leading-relaxed">${body}</p>
                        </div>
                    `,
                    confirmButtonText: '💳 Buka Dompet Digital',
                    cancelButtonText: 'Tutup',
                    showCancelButton: true,
                    confirmButtonColor: '#0B4A8F',
                    cancelButtonColor: '#64748B'
                }).then((r) => {
                    if (r.isConfirmed) {
                        window.location.href = '/pelanggan/dompet';
                    }
                });
                return;
            }

            // Kasus 4: Garansi / Sengketa / Dispute
            if (lowerTitle.includes('garansi') || lowerTitle.includes('sengketa') || lowerTitle.includes('dispute') || lowerBody.includes('garansi') || lowerBody.includes('sengketa')) {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    html: `
                        <div class="text-left space-y-2 text-xs text-textDark">
                            <p class="leading-relaxed">${body}</p>
                        </div>
                    `,
                    confirmButtonText: '🛡️ Buka Pusat Garansi / Resolusi',
                    cancelButtonText: 'Tutup',
                    showCancelButton: true,
                    confirmButtonColor: '#FF7F00',
                    cancelButtonColor: '#64748B'
                }).then((r) => {
                    if (r.isConfirmed) {
                        window.location.href = '/pelanggan/riwayat';
                    }
                });
                return;
            }

            // Kasus 5: Default
            Swal.fire({
                icon: 'info',
                title: title,
                text: body,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0B4A8F'
            });
        }

        async function markAllNotificationsAsRead() {
            try {
                await fetch('/api/notifications/read-all', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                fetchNotifications();
            } catch (e) {}
        }

        async function loadRoleSwitcher() {
            try {
                const res = await fetch('/api/auth/me', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (!res.ok) return;

                const user = data.user;
                const container = document.getElementById('roleButtons');
                let html = '';

                // Always show Pelanggan (current)
                html += `<div class="flex items-center justify-between bg-brandBlue/10 border border-brandBlue/30 rounded-xl px-4 py-2.5">
                    <span class="text-xs font-bold text-brandBlue">👤 Pelanggan (Aktif Sekarang)</span>
                    <span class="text-[9px] bg-brandBlue text-white rounded-full px-2 py-0.5">Aktif</span>
                </div>`;

                if (user.teknisi) {
                    const techStatus = (user.teknisi.status || 'PENDING_REVIEW').toUpperCase();
                    if (techStatus === 'PENDING_REVIEW') {
                        html += `<div class="flex items-center justify-between bg-amber-50 border-2 border-amber-300 rounded-xl px-4 py-2.5">
                            <div>
                                <span class="text-xs font-bold text-amber-900 block">🔧 Mitra Teknisi</span>
                                <span class="text-[10px] text-amber-700">Antrean Verifikasi Admin</span>
                            </div>
                            <button onclick="showPendingNotice('teknisi')" type="button" class="text-[9px] bg-amber-200 hover:bg-amber-300 text-amber-900 font-extrabold px-2.5 py-1 rounded-full border border-amber-400 transition cursor-pointer">
                                ⏳ Menunggu ACC
                            </button>
                        </div>`;
                    } else if (techStatus === 'NEEDS_REVISION') {
                        html += `<div class="flex items-center justify-between bg-red-50 border-2 border-red-300 rounded-xl px-4 py-2.5">
                            <div>
                                <span class="text-xs font-bold text-red-900 block">🔧 Mitra Teknisi</span>
                                <span class="text-[10px] text-red-700">Dokumen perlu revisi</span>
                            </div>
                            <button onclick="openRevisionModal('teknisi')" type="button" class="text-[10px] bg-red-600 hover:bg-red-700 text-white font-extrabold px-3 py-1.5 rounded-lg border-2 border-red-800 shadow-sm transition cursor-pointer flex items-center gap-1">
                                <span>📝 Revisi Dokumen</span>
                            </button>
                        </div>`;
                    } else {
                        html += `<a href="/teknisi/dashboard" class="flex items-center justify-between bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-2.5 hover:bg-slate-100 transition">
                            <span class="text-xs font-bold text-slate-700">🔧 Teknisi (Aktif)</span>
                            <span class="text-[9px] text-brandBlue font-bold">Buka Dasbor →</span>
                        </a>`;
                    }
                }

                if (user.toko) {
                    const tokoStatus = (user.toko.status || 'PENDING_REVIEW').toUpperCase();
                    if (tokoStatus === 'PENDING_REVIEW') {
                        html += `<div class="flex items-center justify-between bg-amber-50 border-2 border-amber-300 rounded-xl px-4 py-2.5">
                            <div>
                                <span class="text-xs font-bold text-amber-900 block">🏪 Toko Sparepart</span>
                                <span class="text-[10px] text-amber-700">Antrean Verifikasi Admin</span>
                            </div>
                            <button onclick="showPendingNotice('toko')" type="button" class="text-[9px] bg-amber-200 hover:bg-amber-300 text-amber-900 font-extrabold px-2.5 py-1 rounded-full border border-amber-400 transition cursor-pointer">
                                ⏳ Menunggu ACC
                            </button>
                        </div>`;
                    } else if (tokoStatus === 'NEEDS_REVISION') {
                        html += `<div class="flex items-center justify-between bg-red-50 border-2 border-red-300 rounded-xl px-4 py-2.5">
                            <div>
                                <span class="text-xs font-bold text-red-900 block">🏪 Toko Sparepart</span>
                                <span class="text-[10px] text-red-700">Dokumen perlu revisi</span>
                            </div>
                            <button onclick="openRevisionModal('toko')" type="button" class="text-[10px] bg-red-600 hover:bg-red-700 text-white font-extrabold px-3 py-1.5 rounded-lg border-2 border-red-800 shadow-sm transition cursor-pointer flex items-center gap-1">
                                <span>📝 Revisi Dokumen</span>
                            </button>
                        </div>`;
                    } else {
                        html += `<a href="/toko/dashboard" class="flex items-center justify-between bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-2.5 hover:bg-slate-100 transition">
                            <span class="text-xs font-bold text-slate-700">🏪 Toko Sparepart (Aktif)</span>
                            <span class="text-[9px] text-brandBlue font-bold">Buka Dasbor →</span>
                        </a>`;
                    }
                }

                container.innerHTML = html;
            } catch(e) {
                console.error('loadRoleSwitcher error', e);
            }
        }

        async function fetchUserProfile() {
            try {
                const response = await fetch('/api/auth/me?role=pelanggan', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (response.status === 401) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    localStorage.removeItem('profile');
                    sessionStorage.clear();
                    window.location.href = '/session-expired';
                    return;
                }
                if (response.status === 503) {
                    window.location.reload();
                    return;
                }
                if (response.ok) {
                    const result = await response.json();
                    const user = result.user;
                    const profile = result.profile;
                    
                    // Sync session details
                    localStorage.setItem('user', JSON.stringify(user));
                    localStorage.setItem('profile', JSON.stringify(profile));

                    // Keep user on customer dashboard to allow booking services
                    const roleName = user.role ? user.role.nama : 'Pelanggan';

                    document.getElementById('navUserName').innerText = user.nama;
                    const unifiedSaldoVal = result.unified_saldo !== undefined ? result.unified_saldo : (result.saldo !== undefined ? result.saldo : (profile ? profile.saldo : 0));
                    const saldoFormatted = parseFloat(unifiedSaldoVal || 0).toLocaleString('id-ID');
                    const snapEl = document.getElementById('pelangganSaldoSnapshot');
                    if (snapEl) snapEl.innerText = 'Rp ' + saldoFormatted;
                    const fullEl = document.getElementById('pelangganSaldoFull');
                    if (fullEl) fullEl.innerText = 'Rp ' + saldoFormatted;

                    document.getElementById('loginStatusCard').innerHTML = `
                        <div class="w-12 h-12 bg-blue-100 text-brandBlue rounded-full flex items-center justify-center mx-auto text-lg font-black">
                            ${user.nama.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">${user.nama}</h4>
                            <p class="text-[10px] text-brandBlue font-bold mt-0.5">Role: ${user.role ? user.role.nama : 'Pelanggan'}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">${user.email}</p>
                        </div>
                        <div id="saldoSection" class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-[9px] uppercase font-bold text-slate-400">Saldo Dompet Pelanggan</p>
                            <p id="saldoValue" class="text-sm font-black text-brandBlue">Rp ${saldoFormatted}</p>
                        </div>
                    `;
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function loadWalletLedger() {
            try {
                const res = await fetch('/api/wallet/balance?role=pelanggan', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const list = document.getElementById('ledgerHistoryList');
                    if (data.transactions.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-4">Belum ada transaksi</p>';
                    } else {
                        list.innerHTML = data.transactions.map(t => {
                            const badge = t.tipe === 'IN' ? 'text-emerald-500 font-bold' : 'text-red-500 font-bold';
                            const prefix = t.tipe === 'IN' ? '+' : '-';
                            return `
                                <div class="p-2 bg-slate-50 border border-slate-100 rounded-lg space-y-1">
                                    <div class="flex justify-between">
                                        <span class="font-bold">${t.deskripsi}</span>
                                        <span class="${badge}">${prefix} Rp ${parseFloat(t.jumlah).toLocaleString('id-ID')}</span>
                                    </div>
                                    <p class="text-[9px] text-slate-400 font-mono">${new Date(t.created_at).toLocaleString('id-ID')}</p>
                                </div>
                            `;
                        }).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function loadDisputesList() {
            try {
                const res = await fetch('/api/disputes', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const list = document.getElementById('disputesStatusList');
                    const disputes = Array.isArray(data) ? data : (data.disputes || []);
                    if (disputes.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-4">Tidak ada sengketa berjalan</p>';
                    } else {
                        const statusLabels = {
                            'OPEN': 'Menunggu Investigasi',
                            'INVESTIGATING': 'Sedang Diinvestigasi',
                            'RESOLVED': 'Selesai Diputuskan'
                        };
                        const decisionLabels = {
                            'REFUND_PELANGGAN': 'Refund Penuh ke Pelanggan',
                            'RELEASE_TEKNISI': 'Pencairan Dana ke Mitra',
                            'KUNJUNGAN_ULANG': 'Kunjungan Servis Ulang',
                            'FREEZE_AKUN': 'Sanksi Pelanggaran Mitra'
                        };

                        list.innerHTML = disputes.map(d => {
                            const badgeColor = {
                                OPEN: 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                INVESTIGATING: 'bg-blue-100 text-blue-800 border-blue-200',
                                RESOLVED: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            }[d.status] || 'bg-slate-100 text-slate-800';

                            const stText = statusLabels[d.status] || d.status;
                            const decisionText = d.keputusan ? `<p class="text-[10px] text-emerald-700 font-bold mt-0.5">Keputusan: ${decisionLabels[d.keputusan] || d.keputusan}</p>` : '';

                            return `
                                <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl space-y-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-black text-slate-700 text-xs">Order #${d.order?.kode_order || '—'}</span>
                                        <span class="text-[9px] px-2 py-0.5 rounded-full border font-bold ${badgeColor}">${stText}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500"><strong>Alasan:</strong> ${d.alasan}</p>
                                    ${decisionText}
                                    ${d.resolusi ? `<p class="text-[10px] text-slate-600 bg-white p-1.5 rounded border border-slate-100 italic">"${d.resolusi}"</p>` : ''}
                                    <p class="text-[9px] text-slate-400">${new Date(d.created_at).toLocaleString('id-ID')}</p>
                                </div>
                            `;
                        }).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function triggerTopup() {
            const { value: amount } = await Swal.fire({
                title: 'Top-Up Saldo Dompet',
                html: `
                    <div class="space-y-3 text-left px-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nominal Top-Up (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 text-sm">Rp</span>
                                <input type="text" id="topupAmountInput" class="w-full text-base font-black pl-10 pr-3 py-3 border border-slate-300 rounded-xl outline-none focus:border-brandBlue transition" placeholder="50.000">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Minimal top-up Rp 10.000</p>
                        </div>
                        <div class="pt-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilihan Cepat</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" onclick="setTopupPreset(20000)" class="text-xs font-bold py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">20.000</button>
                                <button type="button" onclick="setTopupPreset(50000)" class="text-xs font-bold py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">50.000</button>
                                <button type="button" onclick="setTopupPreset(100000)" class="text-xs font-bold py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">100.000</button>
                                <button type="button" onclick="setTopupPreset(200000)" class="text-xs font-bold py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">200.000</button>
                                <button type="button" onclick="setTopupPreset(500000)" class="text-xs font-bold py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">500.000</button>
                                <button type="button" onclick="setTopupPreset(1000000)" class="text-xs font-bold py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">1.000.000</button>
                            </div>
                        </div>
                    </div>
                `,
                background: '#FFFFFF',
                confirmButtonColor: '#FF6A00',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan Top-Up',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const input = document.getElementById('topupAmountInput');
                    input.focus();
                    input.addEventListener('input', function() {
                        let val = this.value.replace(/[^0-9]/g, '');
                        this.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
                    });
                },
                preConfirm: () => {
                    const input = document.getElementById('topupAmountInput');
                    const raw = input.value.replace(/[^0-9]/g, '');
                    const num = parseInt(raw, 10);
                    if (!num || num < 10000) {
                        Swal.showValidationMessage('Minimal nominal top-up adalah Rp 10.000');
                        return false;
                    }
                    return num;
                }
            });

            if (amount) {
                try {
                    const res = await fetch('/api/wallet/topup', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ jumlah: amount, amount: amount, role: 'pelanggan' })
                    });
                    const result = await res.json();
                    if (res.ok && result.snap_token) {
                        const currentOrderId = result.order_id;

                        localStorage.setItem('revaro_active_topup_order_id', currentOrderId);
                        startDashboardPaymentAutoPolling(currentOrderId);

                        if (window.snap) {
                            window.snap.pay(result.snap_token, {
                                language: 'id',
                                onSuccess: async function(snapRes) {
                                    await verifyMidtransPayment(currentOrderId, false);
                                },
                                onPending: async function(snapRes) {
                                    await verifyMidtransPayment(currentOrderId, true);
                                    await fetchUserProfile();
                                    await loadWalletLedger();
                                },
                                onError: function(snapRes) {
                                    stopDashboardPaymentAutoPolling();
                                    Swal.fire('Pembayaran Gagal', 'Silakan coba lagi.', 'error');
                                },
                                onClose: async function() {
                                    await verifyMidtransPayment(currentOrderId, false);
                                    await fetchUserProfile();
                                    await loadWalletLedger();
                                }
                            });
                        } else if (result.redirect_url) {
                            window.open(result.redirect_url, '_blank');
                        }
                    } else {
                        Swal.fire('Gagal', result.message || 'Periksa input Anda.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Gagal', 'Terjadi kesalahan sistem saat menghubungi gerbang pembayaran.', 'error');
                }
            }
        }

        let dashPaymentPollTimer = null;
        function startDashboardPaymentAutoPolling(orderId) {
            stopDashboardPaymentAutoPolling();
            let attempts = 0;
            const maxAttempts = 45; // 45 * 2s = 90 detik
            dashPaymentPollTimer = setInterval(async () => {
                attempts++;
                if (attempts > maxAttempts) {
                    stopDashboardPaymentAutoPolling();
                    return;
                }
                const activeId = localStorage.getItem('revaro_active_topup_order_id');
                if (!activeId) {
                    stopDashboardPaymentAutoPolling();
                    return;
                }
                await verifyMidtransPayment(activeId, true);
            }, 2000);
        }

        function stopDashboardPaymentAutoPolling() {
            if (dashPaymentPollTimer) {
                clearInterval(dashPaymentPollTimer);
                dashPaymentPollTimer = null;
            }
        }

        async function checkActiveDashboardTopup() {
            const activeId = localStorage.getItem('revaro_active_topup_order_id');
            if (activeId) {
                await verifyMidtransPayment(activeId, true);
            }
        }

        window.addEventListener('focus', checkActiveDashboardTopup);
        window.addEventListener('pageshow', checkActiveDashboardTopup);
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                checkActiveDashboardTopup();
            }
        });

        async function verifyMidtransPayment(orderId, isSilent = false) {
            try {
                if (!isSilent) {
                    Swal.fire({
                        title: 'Memverifikasi Pembayaran...',
                        text: 'Memeriksa status pembayaran langsung dari Midtrans...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                }

                const res = await fetch('/api/wallet/check-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_id: orderId })
                });

                const data = await res.json();
                const isPaidSuccess = res.ok && (data.success || data.status === 'success' || data.transaction_status === 'settlement' || data.transaction_status === 'capture');

                if (isPaidSuccess) {
                    stopDashboardPaymentAutoPolling();
                    localStorage.removeItem('revaro_active_topup_order_id');

                    // Langsung update angka saldo di DOM seketika
                    const newBal = data.new_balance ?? data.saldo;
                    if (newBal !== undefined && newBal !== null) {
                        const formatted = 'Rp ' + parseFloat(newBal).toLocaleString('id-ID');
                        const sVal = document.getElementById('saldoValue');
                        if (sVal) sVal.innerText = formatted;
                        const sSnap = document.getElementById('pelangganSaldoSnapshot');
                        if (sSnap) sSnap.innerText = formatted;
                        const sFull = document.getElementById('pelangganSaldoFull');
                        if (sFull) sFull.innerText = formatted;
                    }

                    const snapModal = document.getElementById('snap-midtrans');
                    if (snapModal) snapModal.remove();

                    if (data.is_newly_settled || !isSilent) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            text: data.message || 'Saldo Anda telah berhasil ditambahkan.',
                            confirmButtonColor: '#0A4C8A'
                        });
                    }
                    fetchUserProfile();
                    loadWalletLedger();
                } else if (!isSilent && data.transaction_status === 'pending') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Menunggu Pembayaran',
                        text: 'Silakan selesaikan pembayaran Anda di Midtrans.',
                        confirmButtonColor: '#0A4C8A'
                    });
                    fetchUserProfile();
                    loadWalletLedger();
                } else if (!isSilent && !res.ok) {
                    Swal.fire('Info', data.message || 'Pembayaran belum diselesaikan atau sedang diproses.', 'info');
                }
            } catch (err) {
                if (!isSilent) {
                    Swal.fire('Error', 'Gagal memverifikasi status pembayaran.', 'error');
                }
            }
        }

        function setTopupPreset(val) {
            const input = document.getElementById('topupAmountInput');
            if (input) {
                input.value = val.toLocaleString('id-ID');
            }
        }

        async function triggerWithdraw() {
            const { value: formValues } = await Swal.fire({
                title: 'Tarik Saldo ke Rekening Bank',
                html: `
                    <div class="space-y-3 text-left px-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Bank Tujuan</label>
                            <select id="wdBank" class="w-full text-sm border border-slate-300 rounded-xl p-3 outline-none focus:border-brandBlue transition">
                                <option value="">— Pilih Bank —</option>
                                <option value="BCA">Bank BCA</option>
                                <option value="Mandiri">Bank Mandiri</option>
                                <option value="BRI">Bank BRI</option>
                                <option value="BNI">Bank BNI</option>
                                <option value="CIMB Niaga">CIMB Niaga</option>
                                <option value="Permata">Bank Permata</option>
                                <option value="Bank Jago">Bank Jago</option>
                                <option value="SeaBank">SeaBank</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pemilik Rekening</label>
                            <input type="text" id="wdAccountHolder" class="w-full text-sm border border-slate-300 rounded-xl p-3 outline-none focus:border-brandBlue transition" placeholder="Nama sesuai buku tabungan">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                            <input type="text" id="wdAccountNumber" class="w-full text-sm font-mono border border-slate-300 rounded-xl p-3 outline-none focus:border-brandBlue transition" placeholder="1234567890">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Penarikan (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 text-sm">Rp</span>
                                <input type="text" id="wdAmount" class="w-full text-base font-black pl-10 pr-3 py-3 border border-slate-300 rounded-xl outline-none focus:border-brandBlue transition" placeholder="50.000">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Minimal penarikan Rp 10.000</p>
                        </div>
                    </div>
                `,
                background: '#FFFFFF',
                confirmButtonColor: '#FF6A00',
                showCancelButton: true,
                confirmButtonText: 'Ajukan Penarikan',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const input = document.getElementById('wdAmount');
                    input.addEventListener('input', function() {
                        let val = this.value.replace(/[^0-9]/g, '');
                        this.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
                    });
                },
                preConfirm: () => {
                    const bank_name = document.getElementById('wdBank').value;
                    const account_holder_name = document.getElementById('wdAccountHolder').value.trim();
                    const account_number = document.getElementById('wdAccountNumber').value.trim();
                    const amountRaw = document.getElementById('wdAmount').value.replace(/[^0-9]/g, '');
                    const amount = parseInt(amountRaw, 10);

                    if (!bank_name) {
                        Swal.showValidationMessage('Silakan pilih nama bank tujuan.');
                        return false;
                    }
                    if (!account_holder_name) {
                        Swal.showValidationMessage('Nama pemilik rekening wajib diisi.');
                        return false;
                    }
                    if (!account_number) {
                        Swal.showValidationMessage('Nomor rekening tujuan wajib diisi.');
                        return false;
                    }
                    if (!amount || amount < 10000) {
                        Swal.showValidationMessage('Minimal penarikan adalah Rp 10.000.');
                        return false;
                    }

                    return {
                        bank_name,
                        account_holder_name,
                        account_number,
                        amount
                    };
                }
            });

            if (formValues) {
                try {
                    const res = await fetch('/api/wallet/withdraw', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formValues)
                    });
                    const result = await res.json();
                    if (res.ok) {
                        Swal.fire('Permintaan Terkirim!', result.message, 'success');
                        fetchUserProfile();
                        loadWalletLedger();
                    } else {
                        Swal.fire('Gagal Penarikan', result.message || 'Saldo tidak mencukupi atau data salah.', 'error');
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        }

        // --- Interactive Map & GOAPI Places Suggestion System ---
        let GOAPI_KEY = 'ff44d139-e2e0-51a5-9f35-312a2392';
        let leafletMap = null;
        let leafletMarker = null;
        let goApiDebounceTimer = null;
        let selectedRadiusKm = 7;
        let dynamicPremiumFee = 25000;

        async function fetchClientMapsSettings() {
            try {
                const res = await fetch('/api/settings/maps');
                if (res.ok) {
                    const data = await res.json();
                    if (data.maps_goapi_key) {
                        GOAPI_KEY = data.maps_goapi_key;
                    }
                    if (data.maps_premium_fee) {
                        dynamicPremiumFee = data.maps_premium_fee;
                        const label = document.getElementById('premiumFeeLabel');
                        if (label) label.textContent = Number(dynamicPremiumFee).toLocaleString('id-ID');
                    }
                }
            } catch(e) {
                console.warn('Gagal memuat setting maps client:', e);
            }
        }

        function toggleRadiusOption(radius) {
            selectedRadiusKm = parseInt(radius, 10);
            const btn = document.getElementById('btnSubmitSOS');
            if (btn) {
                if (selectedRadiusKm >= 20) {
                    const feeFmt = Number(dynamicPremiumFee).toLocaleString('id-ID');
                    btn.innerHTML = `<span>👑 Panggil Teknisi Sekarang (Prioritas 20 KM • Tambahan Rp ${feeFmt})</span> <i class="fas fa-arrow-right"></i>`;
                    btn.className = "w-full revaro-btn-orange font-extrabold text-base py-4 px-6 rounded-xl transition flex items-center justify-center space-x-2";
                } else {
                    btn.innerHTML = `<span>Panggil Teknisi Sekarang (SOS — Radius 7 KM)</span> <i class="fas fa-arrow-right"></i>`;
                    btn.className = "w-full revaro-btn font-extrabold text-base py-4 px-6 rounded-xl transition flex items-center justify-center space-x-2";
                }
            }
        }

        // Helper untuk memastikan nama wilayah seluruhnya dalam Bahasa Indonesia murni
        function cleanIndonesianAddress(raw) {
            if (!raw) return '';
            return raw
                .replace(/\bWest Jakarta\b/gi, 'Jakarta Barat')
                .replace(/\bCentral Jakarta\b/gi, 'Jakarta Pusat')
                .replace(/\bSouth Jakarta\b/gi, 'Jakarta Selatan')
                .replace(/\bNorth Jakarta\b/gi, 'Jakarta Utara')
                .replace(/\bEast Jakarta\b/gi, 'Jakarta Timur')
                .replace(/\bSpecial Capital Region of Jakarta\b/gi, 'DKI Jakarta')
                .replace(/\bWest Java\b/gi, 'Jawa Barat')
                .replace(/\bCentral Java\b/gi, 'Jawa Tengah')
                .replace(/\bEast Java\b/gi, 'Jawa Timur')
                .replace(/\bSpecial Region of Yogyakarta\b/gi, 'DI Yogyakarta')
                .replace(/\bNorth Sumatra\b/gi, 'Sumatera Utara')
                .replace(/\bWest Sumatra\b/gi, 'Sumatera Barat')
                .replace(/\bSouth Sumatra\b/gi, 'Sumatera Selatan')
                .replace(/\bWest Kalimantan\b/gi, 'Kalimantan Barat')
                .replace(/\bEast Kalimantan\b/gi, 'Kalimantan Timur')
                .replace(/\bSouth Kalimantan\b/gi, 'Kalimantan Selatan')
                .replace(/\bCentral Kalimantan\b/gi, 'Kalimantan Tengah')
                .replace(/\bNorth Sulawesi\b/gi, 'Sulawesi Utara')
                .replace(/\bSouth Sulawesi\b/gi, 'Sulawesi Selatan');
        }

        // Helper untuk normalisasi pencarian alamat detail Indonesia (RT/RW, No. Rumah, K.H., dsb)
        function prepareAddressVariants(raw) {
            if (!raw) return [];
            let clean = raw;
            // 1. Bersihkan format RT / RW
            clean = clean.replace(/\b(?:RT|RW)\.?\s*\d+(?:\s*[\/\-]\s*(?:RW|RT)\.?\s*\d+)?\b/gi, '');
            clean = clean.replace(/\b(?:RT|RW)\.?\s*\d+\b/gi, '');
            // 2. Bersihkan nomor rumah / nomor blok
            clean = clean.replace(/\bNo\.?\s*\d+(\s*[a-zA-Z0-9]+)?\b/gi, '');
            clean = clean.replace(/\bBlok\s*[a-zA-Z0-9\-\.]+\b/gi, '');
            // 3. Bersihkan kode pos 5 digit
            clean = clean.replace(/\b\d{5}\b/g, '');
            // 4. Normalisasi singkatan jalan dan gelar
            clean = clean.replace(/\bJl\.?\b/gi, 'Jalan');
            clean = clean.replace(/\bKp\.?\b/gi, 'Kampung');
            clean = clean.replace(/\bK\.?H\.?\b/gi, 'KH');
            clean = clean.replace(/\bKel\.?\b/gi, 'Kelurahan');
            clean = clean.replace(/\bKec\.?\b/gi, 'Kecamatan');
            clean = clean.replace(/\bDaerah Khusus Ibukota Jakarta\b/gi, 'Jakarta');
            clean = clean.replace(/\bDKI Jakarta\b/gi, 'Jakarta');
            clean = clean.replace(/\bKota Jakarta Pusat\b/gi, 'Jakarta Pusat');
            clean = clean.replace(/\bKota Jakarta Barat\b/gi, 'Jakarta Barat');
            clean = clean.replace(/\bKota Jakarta Selatan\b/gi, 'Jakarta Selatan');
            clean = clean.replace(/\bKota Jakarta Timur\b/gi, 'Jakarta Timur');
            clean = clean.replace(/\bKota Jakarta Utara\b/gi, 'Jakarta Utara');
            // 5. Bersihkan koma dan spasi ganda
            clean = clean.replace(/[,]+/g, ' ').replace(/\s+/g, ' ').trim();

            // Varian sederhana (menghilangkan kata kunci administratif)
            let simplified = clean.replace(/\b(?:Kecamatan|Kelurahan|Kampung|Daerah|Khusus|Ibukota)\b/gi, '').replace(/\s+/g, ' ').trim();

            const variants = [clean, simplified, raw];
            return [...new Set(variants.filter(v => v && v.length >= 3))];
        }

        function initMap() {
            const initialLat = parseFloat(document.getElementById('geoLat').value) || -6.2088;
            const initialLong = parseFloat(document.getElementById('geoLong').value) || 106.8456;

            if (typeof L === 'undefined') {
                console.warn('Leaflet library is not loaded');
                return;
            }

            try {
                leafletMap = L.map('map').setView([initialLat, initialLong], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(leafletMap);

                leafletMarker = L.marker([initialLat, initialLong], { draggable: true }).addTo(leafletMap);

                leafletMarker.on('dragend', function (e) {
                    const pos = e.target.getLatLng();
                    updateCoordinates(pos.lat, pos.lng);
                    reverseGeocode(pos.lat, pos.lng);
                });

                leafletMap.on('click', function(e) {
                    leafletMarker.setLatLng(e.latlng);
                    updateCoordinates(e.latlng.lat, e.latlng.lng);
                    reverseGeocode(e.latlng.lat, e.latlng.lng);
                });

                // Tutup dropdown saran saat klik di luar
                document.addEventListener('click', function(e) {
                    const box = document.getElementById('goapiSuggestions');
                    const input = document.getElementById('searchMapInput');
                    if (box && !box.contains(e.target) && e.target !== input) {
                        box.classList.add('hidden');
                    }
                });

                // Update badge alamat tersimpan & muat kategori master
                if (typeof updateSavedAddressBadges === 'function') {
                    updateSavedAddressBadges();
                }
                loadServiceCategoriesFromDb();
            } catch(e) {
                console.error('Error initializing map:', e);
            }
        }

        let globalMasterServices = [];

        async function loadServiceCategoriesFromDb() {
            const select = document.getElementById('bookingCategory');
            if (!select) return;

            try {
                const token = localStorage.getItem('token') || localStorage.getItem('admin_token');
                const res = await fetch('/api/jasa-servis', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': token ? `Bearer ${token}` : ''
                    }
                });

                if (!res.ok) throw new Error('Gagal mengambil kategori master');
                const data = await res.json();
                const activeServices = (Array.isArray(data) ? data : (data.data || [])).filter(s => s.is_active !== false);

                if (activeServices.length > 0) {
                    globalMasterServices = activeServices;
                    select.innerHTML = activeServices.map((s, idx) => {
                        const priceFmt = new Intl.NumberFormat('id-ID').format(s.harga_baseline || 0);
                        const warranty = s.garansi_hari || 30;
                        const label = `${s.nama_jasa} (Baseline Rp ${priceFmt} — Garansi ${warranty} Hari)`;
                        return `<option value="${s.kategori || s.nama_jasa}" data-id="${s.id}" data-price="${s.harga_baseline}" data-warranty="${warranty}" data-name="${s.nama_jasa}" ${idx === 0 ? 'selected' : ''}>${label}</option>`;
                    }).join('');

                    handleCategoryChange();
                } else {
                    select.innerHTML = `
                        <option value="AC" data-price="150000" data-warranty="30" selected>Servis AC &amp; Pendingin (Baseline Rp 150.000 — Garansi 30 Hari)</option>
                        <option value="Kulkas" data-price="250000" data-warranty="45">Perbaikan Kulkas &amp; Freezer (Baseline Rp 250.000 — Garansi 45 Hari)</option>
                        <option value="Mesin Cuci" data-price="200000" data-warranty="30">Servis Mesin Cuci (Baseline Rp 200.000 — Garansi 30 Hari)</option>
                    `;
                    handleCategoryChange();
                }
            } catch (err) {
                console.warn('Fallback loading categories:', err);
                select.innerHTML = `
                    <option value="AC" data-price="150000" data-warranty="30" selected>Servis AC &amp; Pendingin (Baseline Rp 150.000 — Garansi 30 Hari)</option>
                    <option value="Kulkas" data-price="250000" data-warranty="45">Perbaikan Kulkas &amp; Freezer (Baseline Rp 250.000 — Garansi 45 Hari)</option>
                    <option value="Mesin Cuci" data-price="200000" data-warranty="30">Servis Mesin Cuci (Baseline Rp 200.000 — Garansi 30 Hari)</option>
                `;
                handleCategoryChange();
            }
        }

        function handleCategoryChange() {
            const select = document.getElementById('bookingCategory');
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            if (!opt) return;

            const warranty = opt.getAttribute('data-warranty') || '30';
            const price = parseFloat(opt.getAttribute('data-price')) || 150000;
            const priceFmt = new Intl.NumberFormat('id-ID').format(price);

            const badge = document.getElementById('categoryWarrantyBadge');
            if (badge) {
                badge.innerText = `🛡️ Garansi ${warranty} Hari`;
            }

            const orderPriceEl = document.getElementById('orderPrice');
            if (orderPriceEl) {
                orderPriceEl.innerText = `Rp ${priceFmt}`;
            }
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('geoLat').value = lat.toFixed(6);
            document.getElementById('geoLong').value = lng.toFixed(6);
        }

        async function reverseGeocode(lat, lng) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=id`);
                const data = await res.json();
                if (data && data.display_name) {
                    const addressField = document.getElementById('bookingAddress');
                    if (addressField) {
                        addressField.value = cleanIndonesianAddress(data.display_name);
                    }
                }
            } catch (err) {
                console.warn('Reverse geocode error:', err);
            }
        }

        // GOAPI & Indonesian Geocoding Auto-suggest
        function handleGoApiInput(val) {
            const trimmed = val.trim();
            const box = document.getElementById('goapiSuggestions');
            clearTimeout(goApiDebounceTimer);

            if (trimmed.length < 3) {
                box.classList.add('hidden');
                box.innerHTML = '';
                return;
            }

            goApiDebounceTimer = setTimeout(() => {
                fetchGoApiSuggestions(trimmed);
            }, 300);
        }

        async function fetchGoApiSuggestions(query) {
            const spinner = document.getElementById('goapiLoading');
            const box = document.getElementById('goapiSuggestions');
            if (spinner) spinner.classList.remove('hidden');

            try {
                const variants = prepareAddressVariants(query);
                let items = [];

                // 1. Coba Nominatim dengan query dan varian normalisasi
                for (const v of variants.slice(0, 2)) {
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(v)}&countrycodes=id&limit=4&addressdetails=1&accept-language=id`);
                        const results = await res.json();
                        if (results && results.length > 0) {
                            items = results.map(item => ({
                                lat: parseFloat(item.lat),
                                lon: parseFloat(item.lon),
                                displayName: cleanIndonesianAddress(item.display_name)
                            }));
                            break;
                        }
                    } catch (e) {}
                }

                // 2. Jika hasil sedikit / kosong, fallback ke Photon (fuzzy search jalan & tempat di Indonesia)
                if (items.length < 2) {
                    for (const v of variants.slice(0, 2)) {
                        try {
                            const pres = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(v)}&limit=4`);
                            const pdata = await pres.json();
                            if (pdata && pdata.features && pdata.features.length > 0) {
                                pdata.features.forEach(f => {
                                    const p = f.properties;
                                    const parts = [p.name, p.street, p.district, p.city, p.state].filter(Boolean);
                                    const fullName = cleanIndonesianAddress(parts.join(', '));
                                    if (!items.some(it => Math.abs(it.lat - f.geometry.coordinates[1]) < 0.001 && Math.abs(it.lon - f.geometry.coordinates[0]) < 0.001)) {
                                        items.push({
                                            lat: f.geometry.coordinates[1],
                                            lon: f.geometry.coordinates[0],
                                            displayName: fullName
                                        });
                                    }
                                });
                                if (items.length > 0) break;
                            }
                        } catch (e) {}
                    }
                }

                if (items && items.length > 0) {
                    box.innerHTML = items.slice(0, 5).map(item => {
                        const cleanName = item.displayName;
                        const parts = cleanName.split(',');
                        const mainTitle = parts.slice(0, 2).join(',').trim();
                        const subTitle = parts.slice(2).join(',').trim();

                        return `
                            <div onclick="selectGoApiLocation(${item.lat}, ${item.lon}, '${cleanName.replace(/'/g, "\\'")}')" 
                                 class="p-3 hover:bg-accent/40 cursor-pointer transition flex items-start gap-2.5">
                                <span class="text-secondary text-sm mt-0.5 shrink-0"><i class="fas fa-map-marker-alt"></i></span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="text-xs font-bold text-primary truncate">${mainTitle}</p>
                                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-accent text-primary border border-primary/20 shrink-0">📍 Lokasi</span>
                                    </div>
                                    <p class="text-[11px] text-textLight truncate">${subTitle || mainTitle}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                    box.classList.remove('hidden');
                } else {
                    box.innerHTML = `
                        <div class="p-3 text-xs text-textLight text-center font-medium">
                            Tidak ada saran tempat untuk "<strong>${query}</strong>"
                        </div>
                    `;
                    box.classList.remove('hidden');
                }
            } catch (err) {
                console.error('Error fetching suggestions:', err);
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }

        function selectGoApiLocation(lat, lon, cleanName) {
            if (leafletMap && leafletMarker) {
                leafletMap.setView([lat, lon], 16);
                leafletMarker.setLatLng([lat, lon]);
            }
            updateCoordinates(lat, lon);
            
            const bookingAddr = document.getElementById('bookingAddress');
            const searchInput = document.getElementById('searchMapInput');
            // Jika bookingAddress kosong atau generik, isi dengan cleanName
            if (bookingAddr && (!bookingAddr.value || !bookingAddr.value.trim())) {
                bookingAddr.value = cleanName;
            }
            if (searchInput) searchInput.value = cleanName;

            const box = document.getElementById('goapiSuggestions');
            if (box) {
                box.classList.add('hidden');
                box.innerHTML = '';
            }

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Lokasi & Titik GPS Diterapkan!',
                showConfirmButton: false,
                timer: 1800
            });
        }

        async function searchLocation() {
            const rawQuery = document.getElementById('searchMapInput').value.trim();
            if (!rawQuery) {
                Swal.fire('Input Kosong', 'Ketikkan nama jalan, kelurahan, atau daerah yang ingin dicari.', 'warning');
                return;
            }

            const variants = prepareAddressVariants(rawQuery);
            let found = null;

            // 1. Coba pencarian bertahap ke Nominatim
            for (const v of variants) {
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(v)}&countrycodes=id&limit=1&accept-language=id`);
                    const results = await res.json();
                    if (results && results.length > 0) {
                        found = {
                            lat: parseFloat(results[0].lat),
                            lon: parseFloat(results[0].lon),
                            displayName: cleanIndonesianAddress(results[0].display_name)
                        };
                        break;
                    }
                } catch (e) {}
            }

            // 2. Jika Nominatim belum menemukan karena detail RT/RW/No Rumah, fallback ke Photon
            if (!found) {
                for (const v of variants) {
                    try {
                        const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(v)}&limit=1`);
                        const data = await res.json();
                        if (data && data.features && data.features.length > 0) {
                            const feat = data.features[0];
                            const props = feat.properties;
                            const parts = [props.name, props.street, props.district, props.city, props.state].filter(Boolean);
                            found = {
                                lat: feat.geometry.coordinates[1],
                                lon: feat.geometry.coordinates[0],
                                displayName: cleanIndonesianAddress(parts.join(', ')) || rawQuery
                            };
                            break;
                        }
                    } catch (e) {}
                }
            }

            if (found) {
                // Pastikan alamat pesanan tetap menggunakan detail lengkap yang diinput pengguna
                const bookingAddr = document.getElementById('bookingAddress');
                if (bookingAddr) {
                    bookingAddr.value = rawQuery;
                }
                selectGoApiLocation(found.lat, found.lon, rawQuery);
            } else {
                Swal.fire('Tidak Ditemukan', 'Lokasi tidak ditemukan. Coba ketikkan nama jalan atau nama daerah utama.', 'info');
            }
        }

        function getCurrentLocation() {
            if (!navigator.geolocation) {
                Swal.fire('GPS Tidak Didukung', 'Browser Anda tidak mendukung Geolocation GPS.', 'error');
                return;
            }

            Swal.fire({
                title: 'Mendeteksi Lokasi GPS...',
                text: 'Mohon tunggu sebentar selagi mendeteksi koordinat akurat Anda.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (leafletMap && leafletMarker) {
                        leafletMap.setView([lat, lng], 16);
                        leafletMarker.setLatLng([lat, lng]);
                    }
                    updateCoordinates(lat, lng);
                    await reverseGeocode(lat, lng);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'GPS Terkini Berhasil Diterapkan!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
                (err) => {
                    console.error(err);
                    Swal.fire('Gagal GPS', 'Gagal mendapatkan lokasi. Pastikan izin lokasi telah diaktifkan pada browser Anda.', 'warning');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        // --- Fitur Penanda Alamat Tersimpan & Riwayat Lokasi Pesanan ---
        const SAVED_ADDRESSES_STORAGE_KEY = 'revaro_saved_addresses';

        function getSavedAddresses() {
            try {
                const raw = localStorage.getItem(SAVED_ADDRESSES_STORAGE_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) {
                return [];
            }
        }

        function updateSavedAddressBadges() {
            const list = getSavedAddresses();
            const badge = document.getElementById('savedAddressCountBadge');
            const tabBadge = document.getElementById('savedTabBadge');
            if (badge) {
                if (list.length > 0) {
                    badge.innerText = list.length;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            if (tabBadge) tabBadge.innerText = list.length;
        }

        function openSavedAddressModal() {
            const modal = document.getElementById('savedAddressModal');
            if (modal) {
                modal.classList.remove('hidden');
                renderSavedAddressesList();
                fetchHistoryOrderAddresses();
                updateSavedAddressBadges();
            }
        }

        function closeSavedAddressModal() {
            const modal = document.getElementById('savedAddressModal');
            if (modal) modal.classList.add('hidden');
        }

        function switchAddressTab(tab) {
            const btnSaved = document.getElementById('tabBtnSaved');
            const btnHistory = document.getElementById('tabBtnHistory');
            const contentSaved = document.getElementById('tabContentSaved');
            const contentHistory = document.getElementById('tabContentHistory');

            if (tab === 'saved') {
                btnSaved.className = 'flex-1 pb-3 text-xs font-black border-b-2 border-primary text-primary transition flex items-center justify-center gap-2';
                btnHistory.className = 'flex-1 pb-3 text-xs font-bold border-b-2 border-transparent text-textLight hover:text-primary transition flex items-center justify-center gap-2';
                contentSaved.classList.remove('hidden');
                contentHistory.classList.add('hidden');
            } else {
                btnHistory.className = 'flex-1 pb-3 text-xs font-black border-b-2 border-primary text-primary transition flex items-center justify-center gap-2';
                btnSaved.className = 'flex-1 pb-3 text-xs font-bold border-b-2 border-transparent text-textLight hover:text-primary transition flex items-center justify-center gap-2';
                contentHistory.classList.remove('hidden');
                contentSaved.classList.add('hidden');
            }
        }

        function renderSavedAddressesList() {
            const container = document.getElementById('savedAddressesList');
            const list = getSavedAddresses();
            updateSavedAddressBadges();

            if (!container) return;

            if (list.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-textLight text-xs bg-accent/20 rounded-2xl border-2 border-dashed border-primary/20 p-6">
                        <p class="text-2xl mb-1">⭐</p>
                        <p class="font-bold text-textDark text-sm">Belum Ada Titik Alamat Tersimpan</p>
                        <p class="text-[11px] text-textLight mt-1">
                            Pasang pin lokasi Anda pada peta lalu klik tombol <strong>"⭐ Simpan Titik Ini"</strong> untuk menyimpan alamat rumah, kantor, atau tempat usaha Anda.
                        </p>
                    </div>
                `;
                return;
            }

            container.innerHTML = list.map((item, idx) => {
                const labelIcon = item.label.toLowerCase().includes('rumah') ? '🏠' : 
                                  item.label.toLowerCase().includes('kantor') ? '🏢' : 
                                  item.label.toLowerCase().includes('toko') ? '🏪' : 
                                  item.label.toLowerCase().includes('kost') ? '🛏️' : '📍';

                return `
                    <div class="p-3.5 rounded-2xl border-2 border-primary/20 hover:border-primary bg-white hover:bg-accent/20 transition flex items-start justify-between gap-3 shadow-sm">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-accent text-primary border border-primary/30 flex items-center justify-center text-base shrink-0">
                                ${labelIcon}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-extrabold text-primary truncate">${item.label}</h4>
                                    <span class="text-[9px] font-bold px-2 py-0.2 rounded-full bg-accent text-primary border border-primary/20">Favorit</span>
                                </div>
                                <p class="text-[11px] text-textDark font-medium line-clamp-2 mt-0.5">${item.address}</p>
                                <p class="text-[9px] text-textLight font-mono mt-1">Koordinat: ${parseFloat(item.lat).toFixed(4)}, ${parseFloat(item.lng).toFixed(4)} • ${item.saved_at || 'Tersimpan'}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="applySavedAddress(${item.lat}, ${item.lng}, '${item.address.replace(/'/g, "\\'")}', '${item.label.replace(/'/g, "\\'")}')" class="revaro-btn text-xs font-extrabold px-3 py-1.5 rounded-xl shadow-btn transition flex items-center gap-1" title="Gunakan Alamat Ini">
                                <span>Pilih</span> →
                            </button>
                            <button type="button" onclick="deleteSavedAddress(${item.id})" class="p-1.5 rounded-xl text-red-500 hover:bg-red-50 hover:text-red-700 transition" title="Hapus Penanda">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function fetchHistoryOrderAddresses() {
            const container = document.getElementById('historyAddressesList');
            const tabBadge = document.getElementById('historyTabBadge');
            if (!container) return;

            try {
                const token = localStorage.getItem('token');
                const res = await fetch('/api/orders?role=pelanggan', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': token ? `Bearer ${token}` : ''
                    }
                });

                if (!res.ok) throw new Error('Gagal mengambil data pesanan');
                const orders = await res.json();

                // Group & deduplicate unique addresses
                const uniqueAddresses = [];
                const seenAddresses = new Set();

                (Array.isArray(orders) ? orders : (orders.data || [])).forEach(ord => {
                    const rawAddr = (ord.alamat_lengkap || ord.alamat || '').trim();
                    const lat = parseFloat(ord.latitude || ord.lat) || null;
                    const lng = parseFloat(ord.longitude || ord.long || ord.lng) || null;

                    if (rawAddr && !seenAddresses.has(rawAddr.toLowerCase())) {
                        seenAddresses.add(rawAddr.toLowerCase());
                        uniqueAddresses.push({
                            orderCode: ord.kode_order || `REV-ORD-${ord.id}`,
                            category: ord.kategori_servis || ord.kategori || 'Servis Perangkat',
                            address: rawAddr,
                            lat: lat || -6.2088,
                            lng: lng || 106.8456,
                            date: ord.created_at ? new Date(ord.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Pesanan Sebelumnya'
                        });
                    }
                });

                if (tabBadge) tabBadge.innerText = uniqueAddresses.length;

                if (uniqueAddresses.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-textLight text-xs bg-gray-50 rounded-2xl border border-gray-200 p-6">
                            <p class="text-2xl mb-1">🕒</p>
                            <p class="font-bold text-textDark text-sm">Belum Ada Riwayat Pemesanan</p>
                            <p class="text-[11px] text-textLight mt-1">Setelah Anda membuat pesanan servis pertama kali, lokasi pesanan akan otomatis tersimpan di riwayat ini.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = uniqueAddresses.map(item => `
                    <div class="p-3.5 rounded-2xl border-2 border-primary/20 hover:border-primary bg-white hover:bg-accent/20 transition flex items-start justify-between gap-3 shadow-sm">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-secondary border border-secondary/30 flex items-center justify-center text-base shrink-0">
                                🕒
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-extrabold text-primary truncate">${item.category}</h4>
                                    <span class="text-[9px] font-bold px-2 py-0.2 rounded-full bg-gray-100 text-textDark border border-gray-200 font-mono">${item.orderCode}</span>
                                </div>
                                <p class="text-[11px] text-textDark font-medium line-clamp-2 mt-0.5">${item.address}</p>
                                <p class="text-[9px] text-textLight font-mono mt-1">📅 Dipesan pada ${item.date}</p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <button type="button" onclick="applySavedAddress(${item.lat}, ${item.lng}, '${item.address.replace(/'/g, "\\'")}', 'Riwayat: ${item.category}')" class="revaro-btn-orange text-xs font-extrabold px-3 py-1.5 rounded-xl shadow-btn transition flex items-center gap-1" title="Gunakan Lokasi Riwayat Ini">
                                <span>Pakai</span> →
                            </button>
                        </div>
                    </div>
                `).join('');

            } catch (err) {
                console.error('Error fetching order history locations:', err);
                container.innerHTML = `
                    <div class="p-4 text-center text-xs text-textLight">
                        <p class="text-amber-600 font-bold">Tidak dapat memuat riwayat order dari server.</p>
                        <p class="text-[10px]">Pastikan Anda telah login.</p>
                    </div>
                `;
            }
        }

        async function saveCurrentAddressPrompt() {
            const currentAddr = (document.getElementById('bookingAddress')?.value || '').trim();
            const lat = parseFloat(document.getElementById('geoLat')?.value) || -6.2088;
            const lng = parseFloat(document.getElementById('geoLong')?.value) || 106.8456;

            if (!currentAddr) {
                Swal.fire({
                    title: 'Alamat Masih Kosong',
                    text: 'Tentukan lokasi pada peta atau ketikkan alamat lengkap terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonColor: '#0B4A8F'
                });
                return;
            }

            const { value: labelName } = await Swal.fire({
                title: '⭐ Simpan Penanda Alamat',
                html: `
                    <div class="text-left space-y-3">
                        <p class="text-xs text-textLight">Berikan nama label untuk titik lokasi alamat ini (contoh: <strong>Rumah, Kantor, Toko Cabang, Kost</strong>):</p>
                        <input id="swalInputLabel" class="swal2-input !m-0 !w-full text-xs font-bold" placeholder="Contoh: Rumah Utama / Kantor Slipi" value="Rumah">
                        <div class="p-2.5 bg-accent/30 rounded-xl border border-primary/20 text-[11px] text-textDark">
                            <span class="font-bold text-primary">Detail Alamat:</span> ${currentAddr}
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '💾 Simpan Penanda',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#FF7F00',
                cancelButtonColor: '#64748B',
                preConfirm: () => {
                    const input = document.getElementById('swalInputLabel').value.trim();
                    if (!input) {
                        Swal.showValidationMessage('Nama label tidak boleh kosong');
                        return false;
                    }
                    return input;
                }
            });

            if (labelName) {
                const list = getSavedAddresses();
                const newItem = {
                    id: Date.now(),
                    label: labelName,
                    address: currentAddr,
                    lat: lat,
                    lng: lng,
                    saved_at: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                };

                list.unshift(newItem);
                localStorage.setItem(SAVED_ADDRESSES_STORAGE_KEY, JSON.stringify(list));
                updateSavedAddressBadges();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Penanda "${labelName}" Berhasil Disimpan!`,
                    showConfirmButton: false,
                    timer: 2200
                });

                renderSavedAddressesList();
            }
        }

        function deleteSavedAddress(id) {
            const list = getSavedAddresses().filter(item => item.id !== id);
            localStorage.setItem(SAVED_ADDRESSES_STORAGE_KEY, JSON.stringify(list));
            renderSavedAddressesList();
            updateSavedAddressBadges();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Penanda alamat dihapus',
                showConfirmButton: false,
                timer: 1500
            });
        }

        function applySavedAddress(lat, lng, addressText, label) {
            selectGoApiLocation(lat, lng, addressText);
            closeSavedAddressModal();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `📍 Lokasi ${label || 'Tersimpan'} Diterapkan!`,
                showConfirmButton: false,
                timer: 2000
            });
        }

        function toggleBookingType() {
            const bookingType = document.querySelector('input[name="bookingType"]:checked').value;
            const otherFields = document.getElementById('otherPersonFields');
            const namaInput = document.getElementById('pemesanNama');
            const hpInput = document.getElementById('pemesanHp');

            if (bookingType === 'orang_lain') {
                otherFields.classList.remove('hidden');
                namaInput.required = true;
                hpInput.required = true;
            } else {
                otherFields.classList.add('hidden');
                namaInput.required = false;
                hpInput.required = false;
            }
        }

        function updateCharCount() {
            const textarea = document.getElementById('bookingComplaint');
            const counter = document.getElementById('charCount');
            const len = textarea.value.length;
            counter.innerText = `${len}/500`;

            if (len >= 480) {
                counter.className = 'text-[10px] text-red-500 font-bold';
            } else if (len >= 400) {
                counter.className = 'text-[10px] text-amber-500 font-semibold';
            } else {
                counter.className = 'text-[10px] text-slate-400';
            }
        }

        async function submitSOSOrder(e) {
            e.preventDefault();
            const keluhan = document.getElementById('bookingComplaint').value.trim();
            const alamat_servis = document.getElementById('bookingAddress').value.trim();
            const latitude = document.getElementById('geoLat').value;
            const longitude = document.getElementById('geoLong').value;
            
            if (keluhan.length > 500) {
                Swal.fire('Peringatan', 'Deskripsi masalah maksimal 500 karakter.', 'warning');
                return;
            }

            const bookingType = document.querySelector('input[name="bookingType"]:checked').value;
            let pemesan_nama = null;
            let pemesan_hp = null;
            
            if (bookingType === 'orang_lain') {
                pemesan_nama = document.getElementById('pemesanNama').value.trim();
                pemesan_hp = document.getElementById('pemesanHp').value.trim();
                if (!pemesan_nama || !pemesan_hp) {
                    Swal.fire('Wajib Diisi', 'Silakan masukkan nama dan nomor HP orang yang Anda pesankan.', 'warning');
                    return;
                }
            }

            try {
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        kategori_servis: document.getElementById('bookingCategory')?.value || 'AC',
                        kategori: document.getElementById('bookingCategory')?.value || 'AC',
                        keluhan,
                        alamat_servis,
                        latitude,
                        longitude,
                        pemesan_nama,
                        pemesan_hp,
                        search_radius: selectedRadiusKm,
                        is_premium: selectedRadiusKm >= 20
                    })
                });

                const result = await response.json();
                if (!response.ok) {
                    Swal.fire('Gagal', result.message || JSON.stringify(result.errors), 'error');
                    return;
                }

                document.getElementById('bookingFormPanel').classList.add('hidden');
                currentOrder = result.order;
                showLiveOrderPanel(result.order);
                checkActiveOrders();
                Swal.fire('Berhasil!', 'Pesanan SOS berhasil dikirim ke teknisi terdekat.', 'success');
            } catch (err) {
                console.error(err);
                Swal.fire('Gagal', 'Terjadi gangguan jaringan internet.', 'error');
            }
        }

        async function checkActiveOrders() {
            try {
                const response = await fetch('/api/orders?role=pelanggan', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                
                // Active in-progress orders (exclude completed, cancelled, resolved, disputed, or already rated)
                const active = result.orders && result.orders.find(o => !['COMPLETED', 'CANCELLED', 'RESOLVED', 'DISPUTED'].includes(o.status) && !o.rating);
                // Completed or resolved order that still needs customer rating
                const unratedCompleted = result.orders && result.orders.find(o => 
                    ['COMPLETED', 'RESOLVED'].includes(o.status) && !o.rating
                );

                const target = active || unratedCompleted;

                if (target) {
                    currentOrder = target;
                    showLiveOrderPanel(target);
                    fetchChats();
                } else {
                    currentOrder = null;
                    document.getElementById('liveOrderPanel').classList.add('hidden');
                    document.getElementById('bookingFormPanel').classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
            }
        }

        function showLiveOrderPanel(order) {
            document.getElementById('bookingFormPanel').classList.add('hidden');
            document.getElementById('liveOrderPanel').classList.remove('hidden');
            const orderStatusMap = {
                'SEARCHING': '🔍 Mencari Teknisi Terdekat',
                'DISPATCHED': '🛵 Teknisi Ditemukan',
                'ON_THE_WAY': '🛵 Teknisi Sedang Menuju Lokasi',
                'IN_PROGRESS': '🔧 Servis Sedang Dikerjakan',
                'AWAITING_PAYMENT': '💳 Menunggu Pembayaran',
                'COMPLETED': '✅ Servis Selesai',
                'CANCELLED': '❌ Dibatalkan',
                'DISPUTED': '⚖️ Dalam Investigasi Sengketa',
                'RESOLVED': '🤝 Kasus Selesai'
            };
            document.getElementById('orderBadge').innerText = orderStatusMap[order.status] || order.status;
            
            // Handle cancel button visibility
            const cancelBtn = document.getElementById('cancelOrderBtn');
            if (['COMPLETED', 'CANCELLED', 'AWAITING_PAYMENT', 'DISPUTED', 'RESOLVED'].includes(order.status)) {
                cancelBtn.classList.add('hidden');
            } else {
                cancelBtn.classList.remove('hidden');
            }

            // Status blocks
            const searching = document.getElementById('searchingState');
            const techInfo = document.getElementById('techAssignedDetails');
            const chatBox = document.getElementById('chatBoxContainer');
            const sparepartsBlock = document.getElementById('sparepartApprovalBlock');
            const paymentBlock = document.getElementById('paymentBlock');
            const escrowRelease = document.getElementById('escrowReleaseBlock');
            const ratingBlock = document.getElementById('ratingReviewBlock');
            const disputeNotice = document.getElementById('disputeActiveNotice');

            searching.classList.add('hidden');
            techInfo.classList.add('hidden');
            chatBox.classList.add('hidden');
            sparepartsBlock.classList.add('hidden');
            paymentBlock.classList.add('hidden');
            escrowRelease.classList.add('hidden');
            ratingBlock.classList.add('hidden');
            if (disputeNotice) disputeNotice.classList.add('hidden');

            if (order.status === 'SEARCHING') {
                searching.classList.remove('hidden');
            } else {
                techInfo.classList.remove('hidden');
                chatBox.classList.remove('hidden');
                if (order.teknisi) {
                    document.getElementById('assignedTechName').innerText = order.teknisi.nama;
                    document.getElementById('assignedTechHP').innerText = 'WA: ' + order.teknisi.hp;
                }

                const badgeEl = document.getElementById('assignedTechStatusBadge');
                if (badgeEl) {
                    if (order.status === 'ON_THE_WAY') {
                        badgeEl.innerText = 'Teknisi Meluncur';
                        badgeEl.className = 'bg-secondary text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-sm';
                    } else if (order.status === 'IN_PROGRESS') {
                        badgeEl.innerText = 'Dalam Perbaikan';
                        badgeEl.className = 'bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-sm';
                    } else if (order.status === 'AWAITING_PAYMENT') {
                        badgeEl.innerText = 'Menunggu Pembayaran';
                        badgeEl.className = 'bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-sm';
                    } else if (order.status === 'DISPUTED') {
                        badgeEl.innerText = 'Dalam Sengketa';
                        badgeEl.className = 'bg-amber-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-sm';
                        if (disputeNotice) disputeNotice.classList.remove('hidden');
                    } else {
                        badgeEl.innerText = 'Teknisi Ditugaskan';
                        badgeEl.className = 'bg-primary text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-sm';
                    }
                }
            }

            // Check spareparts
            if (order.spareparts && order.spareparts.length > 0) {
                const pendingCustomerParts = order.spareparts.filter(p => p.pivot.status === 'PENDING_CUSTOMER');
                const hasRejectedParts = order.spareparts.some(p => p.pivot.status === 'REJECTED');

                // If technician requested, show approval list
                if (pendingCustomerParts.length > 0) {
                    sparepartsBlock.classList.remove('hidden');
                    const tbody = document.getElementById('sparepartsApproveList');
                    tbody.innerHTML = order.spareparts.map(p => `
                        <tr>
                            <td class="py-1">${p.nama} (${p.merk})</td>
                            <td class="py-1 text-center">${p.pivot.qty}</td>
                            <td class="py-1 text-right">Rp ${parseFloat(p.pivot.harga).toLocaleString('id-ID')}</td>
                            <td class="py-1 text-right">Rp ${parseFloat(p.pivot.subtotal).toLocaleString('id-ID')}</td>
                        </tr>
                    `).join('');
                }
            }

            // Transaction / invoice state
            if (order.status === 'AWAITING_PAYMENT' && order.transaksi) {
                paymentBlock.classList.remove('hidden');
                document.getElementById('invoiceJasa').innerText = 'Rp ' + parseFloat(order.transaksi.biaya_jasa).toLocaleString('id-ID');
                document.getElementById('invoiceSparepart').innerText = 'Rp ' + parseFloat(order.transaksi.biaya_part).toLocaleString('id-ID');
                document.getElementById('invoiceTotal').innerText = 'Rp ' + parseFloat(order.transaksi.total).toLocaleString('id-ID');

                // Render approved spareparts list inside invoice card
                const approvedParts = order.spareparts ? order.spareparts.filter(p => ['APPROVED', 'CONFIRMED', 'DELIVERED', 'RECEIVED'].includes(p.pivot.status)) : [];
                const itemsContainer = document.getElementById('invoiceItemsListContainer');
                const itemsList = document.getElementById('invoiceItemsList');
                if (approvedParts.length > 0) {
                    itemsContainer.classList.remove('hidden');
                    itemsList.innerHTML = approvedParts.map(p => `
                        <div class="flex justify-between text-[11px] text-slate-400">
                            <span>• ${p.nama} (${p.merk}) x ${p.pivot.qty}</span>
                            <span>Rp ${parseFloat(p.pivot.subtotal).toLocaleString('id-ID')}</span>
                        </div>
                    `).join('');
                } else {
                    itemsContainer.classList.add('hidden');
                }
            }

            // Check Escrow & Rating
            const isCompletedOrPaid = ['COMPLETED', 'RESOLVED'].includes(order.status) || (order.transaksi && order.transaksi.status_pembayaran === 'PAID');
            if (isCompletedOrPaid) {
                if (order.transaksi && order.transaksi.status_escrow === 'HELD' && !['COMPLETED', 'RESOLVED'].includes(order.status)) {
                    escrowRelease.classList.remove('hidden');
                } else {
                    // Check if rating exists
                    if (!order.rating) {
                        ratingBlock.classList.remove('hidden');
                    }
                }
            }
        }

        async function fetchChats() {
            if (!currentOrder) return;
            try {
                const res = await fetch(`/api/orders/${currentOrder.id}/chats`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const messages = await res.json();
                const box = document.getElementById('chatMessagesBox');
                
                const currentUserRaw = localStorage.getItem('user');
                const currentUserId = currentUserRaw ? JSON.parse(currentUserRaw).id : null;

                box.innerHTML = messages.map(m => {
                    const isMe = m.dari_user_id === currentUserId;
                    const alignment = isMe ? 'text-right' : 'text-left';
                    const bubbleBg = isMe ? 'bg-brandBlue text-white ml-auto' : 'bg-slate-200 text-slate-800 mr-auto';
                    return `
                        <div class="${alignment}">
                            <div class="inline-block p-2 rounded-xl text-xs max-w-xs ${bubbleBg}">
                                ${escapeHtml(m.pesan)}
                            </div>
                        </div>
                    `;
                }).join('');
                box.scrollTop = box.scrollHeight;
            } catch (err) {
                console.error(err);
            }
        }

        async function sendChatMessage(e) {
            e.preventDefault();
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg || !currentOrder) return;

            try {
                const res = await fetch(`/api/orders/${currentOrder.id}/chats`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ pesan: msg })
                });
                if (res.ok) {
                    input.value = '';
                    fetchChats();
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function respondSpareparts(approve) {
            if (!currentOrder) return;
            try {
                const res = await fetch(`/api/orders/${currentOrder.id}/spareparts/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ approve })
                });
                const d = await res.json();
                Swal.fire({
                    title: approve ? 'Persetujuan Dikirim' : 'Ditolak',
                    text: d.message,
                    icon: approve ? 'success' : 'info',
                    confirmButtonColor: '#FF6A00'
                });
                checkActiveOrders();
            } catch (e) {
                console.error(e);
            }
        }

        async function payInvoice() {
            if (!currentOrder) return;
            try {
                const res = await fetch(`/api/orders/${currentOrder.id}/pay`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire({ title: 'Sukses!', text: d.message, icon: 'success', confirmButtonColor: '#FF6A00' });
                    fetchUserProfile();
                    loadWalletLedger();
                    checkActiveOrders();
                } else {
                    Swal.fire('Pembayaran Gagal', d.message || 'Saldo tidak mencukupi.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        async function confirmReleaseEscrow() {
            if (!currentOrder) return;
            try {
                const res = await fetch(`/api/orders/${currentOrder.id}/confirm-release`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire({ title: 'Dana Escrow Dicairkan!', text: d.message, icon: 'success', confirmButtonColor: '#FF6A00' });
                    checkActiveOrders();
                }
            } catch (e) { console.error(e); }
        }

        function selectRating(score) {
            document.getElementById('ratingScore').value = score;
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((star, index) => {
                if (index < score) {
                    star.classList.add('text-yellow-400');
                    star.classList.remove('text-slate-300');
                } else {
                    star.classList.add('text-slate-300');
                    star.classList.remove('text-yellow-400');
                }
            });
        }

        async function submitRating() {
            if (!currentOrder) return;
            const score = parseInt(document.getElementById('ratingScore').value, 10);
            const comment = document.getElementById('ratingComment').value.trim();

            if (!score || score < 1 || score > 5) {
                Swal.fire('Rating Wajib', 'Silakan klik salah satu bintang (1-5) terlebih dahulu.', 'warning');
                return;
            }

            if (score <= 2 && !comment) {
                const confirmLow = await Swal.fire({
                    title: 'Bintang Rendah',
                    text: 'Anda memberikan ulasan bintang ' + score + '. Mohon sertakan alasan/keluhan agar tim Admin Resolusi dapat menindaklanjuti kinerja teknisi.',
                    input: 'textarea',
                    inputPlaceholder: 'Tuliskan keluhan pekerjaan teknisi...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim Ulasan & Laporkan',
                    cancelButtonText: 'Batal'
                });
                if (!confirmLow.isConfirmed || !confirmLow.value) {
                    return;
                }
                document.getElementById('ratingComment').value = confirmLow.value;
            }

            const finalComment = document.getElementById('ratingComment').value.trim();

            try {
                const res = await fetch(`/api/orders/${currentOrder.id}/rating`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        skor: score,
                        komentar: finalComment
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    if (score <= 2) {
                        Swal.fire({
                            title: 'Ulasan & Eskalasi Tersimpan',
                            text: 'Rating rendah Anda telah disimpan. Kasus performa teknisi otomatis dilaporkan ke antrian investigasi Admin Resolusi.',
                            icon: 'warning'
                        });
                    } else {
                        Swal.fire('Terima Kasih!', 'Ulasan bintang ' + score + ' Anda berhasil dikirim ke teknisi.', 'success');
                    }
                    document.getElementById('ratingReviewBlock').classList.add('hidden');
                    checkActiveOrders();
                } else {
                    Swal.fire('Perhatian', data.message || 'Gagal mengirim ulasan.', 'info');
                    document.getElementById('ratingReviewBlock').classList.add('hidden');
                    checkActiveOrders();
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function cancelActiveOrder() {
            if (!currentOrder) return;
            const confirmCancel = await Swal.fire({
                title: 'Batalkan Pesanan?',
                text: 'Membatalkan pesanan yang sedang berjalan dapat dikenakan biaya kompensasi Rp 20.000 kepada teknisi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Batalkan'
            });

            if (confirmCancel.isConfirmed) {
                try {
                    const res = await fetch(`/api/orders/${currentOrder.id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const d = await res.json();
                    if (res.ok) {
                        Swal.fire('Dibatalkan!', d.message, 'success');
                        fetchUserProfile();
                        loadWalletLedger();
                        checkActiveOrders();
                    } else {
                        Swal.fire('Gagal', d.message, 'error');
                    }
                } catch (e) { console.error(e); }
            }
        }

        function triggerDisputeForm() {
            document.getElementById('disputeFormPanel').classList.remove('hidden');
            document.getElementById('paymentBlock').classList.add('hidden');
        }

        function closeDisputeForm() {
            document.getElementById('disputeFormPanel').classList.add('hidden');
            document.getElementById('paymentBlock').classList.remove('hidden');
        }

        async function submitDispute() {
            if (!currentOrder) return;
            const reason = document.getElementById('disputeReason').value.trim();
            if (!reason) {
                Swal.fire('Wajib', 'Alasan sengketa tidak boleh kosong.', 'warning');
                return;
            }

            try {
                const res = await fetch('/api/disputes', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: currentOrder.id,
                        alasan: reason
                    })
                });

                const d = await res.json();
                if (res.ok) {
                    Swal.fire('Dispute Diajukan!', d.message, 'success');
                    closeDisputeForm();
                    loadDisputesList();
                    checkActiveOrders();
                } else {
                    Swal.fire('Gagal', d.message || 'Gagal mengajukan sengketa.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        function showPendingNotice(role) {
            const title = role === 'teknisi' ? 'Mitra Teknisi' : 'Toko Sparepart';
            Swal.fire({
                icon: 'info',
                title: 'Menunggu Persetujuan Admin ⏳',
                html: `
                    <div class="text-left space-y-2.5 text-xs text-textDark leading-relaxed">
                        <p>Pendaftaran akun <strong>${title}</strong> Anda sedang dalam antrean pemeriksaan oleh <strong>Admin Verifikator REVARO</strong>.</p>
                        <div class="p-3 bg-amber-50 border-2 border-amber-300 rounded-xl text-amber-900 font-semibold">
                            ⏳ <strong>Status: Menunggu ACC Admin Verifikator</strong><br>
                            Sesuai SOP keamanan, akses ke dasbor kerja mitra baru akan dibuka setelah berkas dan dokumen Anda disetujui (ACC).
                        </div>
                        <p class="text-slate-500">Anda tetap dapat menggunakan dasbor Pelanggan ini secara normal selama menunggu proses verifikasi.</p>
                    </div>
                `,
                confirmButtonColor: '#0B4A8F',
                confirmButtonText: 'Mengerti'
            });
        }

        function openRevisionModal(role) {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            document.getElementById('revTargetType').value = role;

            let catatan = 'Dokumen perlu diperbaiki, harap unggah ulang berkas yang valid dan jelas.';
            if (role === 'teknisi') {
                document.getElementById('revModalTitle').textContent = 'Revisi Berkas Mitra Teknisi';
                document.getElementById('revFieldTechCategory').classList.remove('hidden');
                document.getElementById('revFieldCert').classList.remove('hidden');
                document.getElementById('revFieldKtp').classList.remove('hidden');
                document.getElementById('revFieldShopDetails').classList.add('hidden');
                document.getElementById('revFieldShopDoc').classList.add('hidden');

                if (user.teknisi) {
                    document.getElementById('revInputPhone').value = user.teknisi.hp || user.hp || '';
                    populatePelangganRevTechCategories(user.teknisi.kategori || '');
                    if (user.teknisi.pendaftaran_verifikasi && user.teknisi.pendaftaran_verifikasi.length) {
                        const lastVerif = user.teknisi.pendaftaran_verifikasi[user.teknisi.pendaftaran_verifikasi.length - 1];
                        if (lastVerif.catatan) catatan = lastVerif.catatan;
                    }
                } else {
                    populatePelangganRevTechCategories('');
                }
            } else {
                document.getElementById('revModalTitle').textContent = 'Revisi Berkas Toko Sparepart';
                document.getElementById('revFieldTechCategory').classList.add('hidden');
                document.getElementById('revFieldCert').classList.add('hidden');
                document.getElementById('revFieldKtp').classList.add('hidden');
                document.getElementById('revFieldShopDetails').classList.remove('hidden');
                document.getElementById('revFieldShopDoc').classList.remove('hidden');

                if (user.toko) {
                    document.getElementById('revInputPhone').value = user.toko.telepon || user.hp || '';
                    document.getElementById('revInputIzinUsaha').value = user.toko.izin_usaha || '';
                    document.getElementById('revInputShopAddress').value = user.toko.alamat || '';
                    if (user.toko.pendaftaran_verifikasi && user.toko.pendaftaran_verifikasi.length) {
                        const lastVerif = user.toko.pendaftaran_verifikasi[user.toko.pendaftaran_verifikasi.length - 1];
                        if (lastVerif.catatan) catatan = lastVerif.catatan;
                    }
                }
            }

            document.getElementById('revAdminNotes').textContent = catatan;
            document.getElementById('partnerRevisionModal').classList.remove('hidden');
        }

        function closeRevisionModal() {
            document.getElementById('partnerRevisionModal').classList.add('hidden');
        }

        async function populatePelangganRevTechCategories(selectedCat = '') {
            const sel = document.getElementById('revInputTechCategory');
            if (!sel) return;

            let categories = [];
            if (globalMasterServices && globalMasterServices.length > 0) {
                categories = [...new Set(globalMasterServices.map(s => (s.kategori || s.nama_jasa || '').trim()).filter(Boolean))];
            } else {
                try {
                    let res = await fetch('/api/jasa-servis/categories');
                    if (res.ok) {
                        const data = await res.json();
                        if (Array.isArray(data.categories) && data.categories.length > 0) {
                            categories = data.categories.map(c => String(c).trim()).filter(Boolean);
                        } else if (Array.isArray(data.services) && data.services.length > 0) {
                            categories = [...new Set(data.services.map(s => (s.kategori || s.nama_jasa || '').trim()).filter(Boolean))];
                        }
                    }

                    if (!categories.length) {
                        const res2 = await fetch('/api/jasa-servis');
                        if (res2.ok) {
                            const data2 = await res2.json();
                            if (Array.isArray(data2) && data2.length > 0) {
                                categories = [...new Set(data2.map(s => (s.kategori || s.nama_jasa || '').trim()).filter(Boolean))];
                            }
                        }
                    }
                } catch(e) {}
            }

            const selTrim = (selectedCat || '').trim();
            if (!categories.length && selTrim) {
                categories = [selTrim];
            } else if (selTrim && !categories.some(c => c.toLowerCase() === selTrim.toLowerCase())) {
                categories.unshift(selTrim);
            }

            sel.innerHTML = categories.map(cat => {
                const isSel = selTrim && (
                    cat.toLowerCase() === selTrim.toLowerCase() ||
                    (selTrim.toLowerCase().includes('ac') && cat.toLowerCase().includes('ac')) ||
                    (selTrim.toLowerCase().includes('kulkas') && cat.toLowerCase().includes('kulkas')) ||
                    (selTrim.toLowerCase().includes('cuci') && cat.toLowerCase().includes('cuci')) ||
                    (selTrim.toLowerCase().includes('pompa') && cat.toLowerCase().includes('pompa')) ||
                    (selTrim.toLowerCase().includes('tv') && cat.toLowerCase().includes('tv'))
                );
                return `<option value="${cat}" ${isSel ? 'selected' : ''}>${cat}</option>`;
            }).join('');

            if (selTrim) {
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value.toLowerCase() === selTrim.toLowerCase()) {
                        sel.selectedIndex = i;
                        break;
                    }
                }
            }
        }

        async function submitPartnerRevision(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitRevision');
            btn.disabled = true;
            btn.textContent = 'Mengirim...';

            const role = document.getElementById('revTargetType').value;
            const formData = new FormData();
            formData.append('target_type', role);

            if (role === 'teknisi') {
                formData.append('hp', document.getElementById('revInputPhone').value);
                formData.append('kategori', document.getElementById('revInputTechCategory').value);
                const cert = document.getElementById('revInputCert').files[0];
                if (cert) formData.append('sertifikat', cert);
                const ktp = document.getElementById('revInputKtp').files[0];
                if (ktp) formData.append('ktp', ktp);
            } else {
                formData.append('telepon', document.getElementById('revInputPhone').value);
                formData.append('izin_usaha', document.getElementById('revInputIzinUsaha').value);
                formData.append('alamat', document.getElementById('revInputShopAddress').value);
                const doc = document.getElementById('revInputShopDoc').files[0];
                if (doc) formData.append('dokumen_izin', doc);
            }

            try {
                const res = await fetch('/api/profile/resubmit', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    body: formData
                });
                const result = await res.json();
                btn.disabled = false;
                btn.textContent = 'Kirim Ulang Dokumen';

                if (res.ok) {
                    closeRevisionModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Dokumen Berhasil Dikirim Ulang! ⏳',
                        text: 'Berkas revisi Anda telah masuk kembali ke antrean Admin Verifikator. Harap menunggu proses ACC.',
                        confirmButtonColor: '#0B4A8F'
                    });
                    fetchUserProfile();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || JSON.stringify(result.errors),
                        confirmButtonColor: '#0B4A8F'
                    });
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
                btn.textContent = 'Kirim Ulang Dokumen';
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            }
        }

        async function handleLogout() {
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
            } catch(e) {
                console.error('Logout error:', e);
            }
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = '/';
        }

        // Realtime Maintenance & Session Heartbeat
        setInterval(async () => {
            try {
                if (token) {
                    const ping = await fetch('/api/auth/me?role=pelanggan', {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    if (ping.status === 401) {
                        localStorage.clear();
                        sessionStorage.clear();
                        window.location.href = '/session-expired';
                        return;
                    }
                    if (ping.status === 503) {
                        window.location.reload();
                    }
                }
            } catch(e) {}
        }, 10000);
    </script>

    <!-- Modal Revisi Dokumen Mitra (Teknisi / Toko) -->
    <div id="partnerRevisionModal" class="hidden fixed inset-0 bg-primary/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white border-2 border-primary rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 md:p-8 shadow-[8px_8px_0px_0px_#0B4A8F] space-y-5">
            <div class="flex items-center justify-between border-b-2 border-accent pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">📝</span>
                    <div>
                        <h3 class="text-base font-extrabold text-primary" id="revModalTitle">Revisi Berkas Mitra</h3>
                        <p class="text-[11px] text-textLight">Kirim ulang dokumen yang diminta oleh Admin Verifikator</p>
                    </div>
                </div>
                <button onclick="closeRevisionModal()" type="button" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-textDark font-bold flex items-center justify-center transition">✕</button>
            </div>

            <!-- Catatan Admin Box -->
            <div class="p-3.5 bg-red-50 border-2 border-red-200 rounded-2xl space-y-1">
                <p class="text-[10px] font-black uppercase text-red-600 tracking-wider flex items-center gap-1.5">
                    <i class="fas fa-exclamation-triangle"></i> Catatan Admin Verifikator:
                </p>
                <p id="revAdminNotes" class="text-xs text-red-800 font-semibold leading-relaxed">
                    Dokumen Anda memerlukan revisi. Harap unggah ulang berkas yang valid.
                </p>
            </div>

            <!-- Form Pengajuan Ulang -->
            <form id="partnerRevisionForm" onsubmit="submitPartnerRevision(event)" class="space-y-4 text-xs font-medium">
                <input type="hidden" id="revTargetType" value="teknisi">

                <div id="revFieldPhone">
                    <label class="block text-[11px] font-bold text-primary uppercase mb-1">Nomor HP / WhatsApp Aktif *</label>
                    <input type="text" id="revInputPhone" required class="w-full revaro-input rounded-xl p-2.5 bg-white text-xs font-bold" placeholder="08123456789">
                </div>

                <div id="revFieldTechCategory">
                    <label class="block text-[11px] font-bold text-primary uppercase mb-1">Kategori Keahlian Teknisi *</label>
                    <select id="revInputTechCategory" class="w-full revaro-input rounded-xl p-2.5 bg-white text-xs font-bold">
                        <option value="Air Conditioner (AC)">Air Conditioner (AC)</option>
                        <option value="Kulkas / Mesin Pendingin">Kulkas / Mesin Pendingin</option>
                        <option value="Mesin Cuci">Mesin Cuci</option>
                        <option value="Pompa Air">Pompa Air</option>
                        <option value="Elektronik Rumah Tangga">Elektronik Rumah Tangga</option>
                    </select>
                </div>

                <div id="revFieldShopDetails" class="hidden space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-primary uppercase mb-1">Nomor Izin Usaha (NIB / SIUP) *</label>
                        <input type="text" id="revInputIzinUsaha" class="w-full revaro-input rounded-xl p-2.5 bg-white text-xs font-bold" placeholder="NIB-1234567890">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-primary uppercase mb-1">Alamat Toko Fisik *</label>
                        <textarea id="revInputShopAddress" rows="2" class="w-full revaro-input rounded-xl p-2.5 bg-white text-xs font-bold" placeholder="Alamat lengkap toko"></textarea>
                    </div>
                </div>

                <!-- Dokumen File Uploads -->
                <div class="space-y-3 pt-1 border-t border-gray-100">
                    <div id="revFieldCert">
                        <label class="block text-[11px] font-bold text-primary uppercase mb-1">Unggah Sertifikat / Dokumen Baru (.PDF, .JPG, .PNG)</label>
                        <input type="file" id="revInputCert" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-[11px] bg-white border border-gray-300 rounded-xl p-2 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary file:text-white">
                        <p class="text-[10px] text-textLight mt-0.5">Biarkan kosong jika tidak ada perubahan sertifikat.</p>
                    </div>

                    <div id="revFieldKtp">
                        <label class="block text-[11px] font-bold text-primary uppercase mb-1">Unggah Foto KTP Baru (.JPG, .PNG)</label>
                        <input type="file" id="revInputKtp" accept=".jpg,.jpeg,.png" class="w-full text-[11px] bg-white border border-gray-300 rounded-xl p-2 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary file:text-white">
                        <p class="text-[10px] text-textLight mt-0.5">Biarkan kosong jika tidak ada perubahan KTP.</p>
                    </div>

                    <div id="revFieldShopDoc" class="hidden">
                        <label class="block text-[11px] font-bold text-primary uppercase mb-1">Unggah Dokumen Izin Toko (.PDF, .JPG, .PNG)</label>
                        <input type="file" id="revInputShopDoc" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-[11px] bg-white border border-gray-300 rounded-xl p-2 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary file:text-white">
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button type="button" onclick="closeRevisionModal()" class="w-1/3 py-2.5 rounded-xl border-2 border-primary/40 font-bold text-textDark text-xs hover:bg-gray-100 transition">Batal</button>
                    <button type="submit" id="btnSubmitRevision" class="w-2/3 revaro-btn py-2.5 rounded-xl text-xs font-black shadow-btn">
                        Kirim Ulang Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Penanda Alamat Tersimpan & Riwayat Lokasi Pesanan -->
    <div id="savedAddressModal" class="hidden fixed inset-0 bg-primary/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white border-2 border-primary rounded-3xl max-w-xl w-full max-h-[90vh] flex flex-col shadow-[8px_8px_0px_0px_#0B4A8F] overflow-hidden">
            <!-- Modal Header -->
            <div class="p-5 md:p-6 bg-accent/40 border-b-2 border-primary flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center text-lg font-black shadow-sm">
                        📍
                    </div>
                    <div>
                        <h3 class="text-base font-black text-primary">Penanda &amp; Riwayat Lokasi Servis</h3>
                        <p class="text-[11px] text-textLight">Pilih alamat tersimpan atau lokasi pesanan terdahulu</p>
                    </div>
                </div>
                <button onclick="closeSavedAddressModal()" type="button" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 text-textDark border border-primary/30 font-bold flex items-center justify-center transition">✕</button>
            </div>

            <!-- Modal Tab Navigation -->
            <div class="flex border-b-2 border-primary/20 bg-white shrink-0 px-4 pt-3 gap-2">
                <button type="button" onclick="switchAddressTab('saved')" id="tabBtnSaved" class="flex-1 pb-3 text-xs font-black border-b-2 border-primary text-primary transition flex items-center justify-center gap-2">
                    <span>⭐</span> <span>Alamat Tersimpan</span>
                    <span id="savedTabBadge" class="px-1.5 py-0.2 rounded-full text-[10px] bg-primary text-white font-mono">0</span>
                </button>
                <button type="button" onclick="switchAddressTab('history')" id="tabBtnHistory" class="flex-1 pb-3 text-xs font-bold border-b-2 border-transparent text-textLight hover:text-primary transition flex items-center justify-center gap-2">
                    <span>🕒</span> <span>Riwayat Pesanan</span>
                    <span id="historyTabBadge" class="px-1.5 py-0.2 rounded-full text-[10px] bg-accent text-primary font-mono">0</span>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 overflow-y-auto flex-1 space-y-4">
                
                <!-- Tab Content 1: Saved Bookmarks -->
                <div id="tabContentSaved" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-textLight uppercase tracking-wider">Daftar Titik Penanda Favorit</span>
                        <button type="button" onclick="saveCurrentAddressPrompt()" class="text-xs font-extrabold text-secondary hover:underline flex items-center gap-1">
                            <span>+</span> Tambah Titik Saat Ini
                        </button>
                    </div>

                    <div id="savedAddressesList" class="space-y-2.5">
                        <!-- Injected via JavaScript -->
                        <div class="text-center py-8 text-textLight text-xs">
                            <p class="text-2xl mb-2">⭐</p>
                            <p class="font-bold text-textDark">Belum ada alamat tersimpan</p>
                            <p class="text-[11px] text-textLight mt-1">Gunakan tombol "Simpan Titik Ini" di formulir booking untuk menyimpan alamat rumah, kantor, atau toko.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab Content 2: Order History Addresses -->
                <div id="tabContentHistory" class="space-y-3 hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-textLight uppercase tracking-wider">Lokasi dari Pesanan yang Pernah Dibuat</span>
                        <span class="text-[10px] text-textLight font-mono">Otomatis dari Riwayat Servis</span>
                    </div>

                    <div id="historyAddressesList" class="space-y-2.5">
                        <!-- Injected via JavaScript -->
                        <div class="text-center py-8 text-textLight text-xs">
                            <p class="text-2xl mb-2">🕒</p>
                            <p class="font-bold text-textDark">Memuat riwayat lokasi pesanan...</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-gray-50 border-t-2 border-primary/20 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-textLight flex items-center gap-1">
                    <span>💡</span> <span>Klik alamat untuk langsung memasang pin &amp; memindahkan peta</span>
                </span>
                <button type="button" onclick="closeSavedAddressModal()" class="px-4 py-2 rounded-xl bg-white hover:bg-gray-100 border border-primary/40 text-xs font-extrabold text-textDark transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @include('components.dock')
</body>
</html>
