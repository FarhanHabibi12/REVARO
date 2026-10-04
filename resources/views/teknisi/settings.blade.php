<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun Mitra Teknisi — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Leaflet Interactive Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .revaro-card {
            background-color: #ffffff;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #0B4A8F;
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
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: #ffffff;
            border: 2px solid #0B4A8F;
            box-shadow: 2px 2px 0px 0px #0B4A8F;
            border-radius: 0.75rem;
            color: #1E293B;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .revaro-input:focus {
            outline: none;
            box-shadow: 1px 1px 0px 0px #FF7F00;
            border-color: #FF7F00;
            transform: translate(1px, 1px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-textDark flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b-2 border-primary sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center">
            <a href="/teknisi/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-secondary text-white font-extrabold shadow-sm hidden sm:inline-block">Konsol Teknisi</span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="/teknisi/dashboard" class="revaro-btn-outline text-xs px-3.5 py-1.5 rounded-xl font-bold flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> <span>Kembali ke Dasbor</span>
                </a>
                <button onclick="handleLogout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3.5 py-1.5 rounded-xl font-bold border-2 border-red-700 shadow-[2px_2px_0px_0px_#991B1B] transition">Keluar</button>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8 md:pl-20 pb-28">
        <!-- Top Action Banner -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="text-xs text-textLight font-bold uppercase tracking-wider flex items-center gap-2">
                <span>REVARO</span> / <span>MITRA TEKNISI</span> / <span class="text-secondary font-black">PENGATURAN KONSOL</span>
            </div>
            <div id="topVerifBadgeWrapper">
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border-2 border-emerald-600 flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>MITRA TEKNISI AKTIF TERVERIFIKASI</span>
                </span>
            </div>
        </div>

        <!-- Pending Review Banner (Dynamic) -->
        <div id="pendingAlertBanner" class="hidden bg-amber-50 border-2 border-amber-400 text-amber-900 px-5 py-4 rounded-2xl flex items-start gap-3.5 shadow-sm">
            <span class="text-2xl mt-0.5">⏳</span>
            <div class="space-y-1">
                <h4 class="font-extrabold text-sm text-amber-900">Pendaftaran Menunggu Persetujuan Admin Verifikator</h4>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Data domisili tempat tinggal dan profil teknisi Anda telah tersimpan dan saat ini sedang menunggu tinjauan oleh <strong>Admin Verifikator REVARO</strong>. Fitur penerimaan order panggilan dari pelanggan akan dibuka otomatis setelah status Anda disetujui (ACC).
                </p>
            </div>
        </div>

        <!-- 2 Column Section: Profil & Sandi Teknisi + Spesifikasi Lingkungan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Box (7 cols): Profil & Sandi Mitra Teknisi -->
            <div class="lg:col-span-7 revaro-card p-6 md:p-8 space-y-6">
                <div class="border-b-2 border-accent pb-3">
                    <h3 class="text-lg font-extrabold text-primary">Profil &amp; Keamanan Akun Teknisi</h3>
                    <p class="text-xs text-textLight mt-1">Perbarui informasi identitas, nomor telepon aktif, dan keahlian spesialisasi teknisi Anda.</p>
                </div>

                <form id="teknisiProfileForm" onsubmit="event.preventDefault(); saveTeknisiProfile();" class="space-y-5">
                    <div>
                        <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Nama Lengkap Teknisi *</label>
                        <input type="text" id="teknisiNama" required class="revaro-input font-bold" placeholder="Budi Teknisi">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Alamat Email Login *</label>
                        <input type="email" id="teknisiEmail" required class="revaro-input font-medium" placeholder="teknisi@revaro.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Nomor HP / WhatsApp Teknisi</label>
                            <input type="text" id="teknisiPhone" class="revaro-input font-medium" placeholder="081234567893">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Spesialisasi Kategori Servis</label>
                            <input type="text" id="teknisiSkill" class="revaro-input font-medium" placeholder="AC, Kulkas, Mesin Cuci, Elektronik">
                        </div>
                    </div>

                    <!-- Section Patokan Titik Domisili Tempat Tinggal Teknisi (Leaflet Map) -->
                    <div class="p-5 rounded-2xl border-2 border-primary bg-accent/20 space-y-3.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-primary/20 pb-2">
                            <div>
                                <h4 class="text-xs font-black text-primary uppercase flex items-center gap-1.5">
                                    <i class="fas fa-map-marked-alt text-secondary"></i>
                                    <span>Titik Patokan Domisili Tinggal Teknisi</span>
                                </h4>
                                <p class="text-[11px] text-textLight">Pusat radius radar penugasan <strong>⚡ Standar (7 KM)</strong> &amp; <strong>👑 Prioritas (20 KM)</strong></p>
                            </div>
                            <button type="button" onclick="getTechCurrentLocation()" class="text-[11px] font-black text-secondary hover:underline flex items-center gap-1">
                                <i class="fas fa-location-arrow"></i> <span>Gunakan GPS Domisili Saya</span>
                            </button>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-primary uppercase tracking-wider block mb-1">Alamat Domisili Tempat Tinggal</label>
                            <input type="text" id="teknisiAlamat" class="revaro-input text-xs" placeholder="Contoh: Jalan Slipi IX, Slipi, Palmerah, Jakarta Barat">
                        </div>

                        <!-- Pencarian cepat lokasi di peta -->
                        <div class="flex gap-2">
                            <input type="text" id="techSearchMapInput" placeholder="Ketik nama jalan atau daerah untuk mencari di peta..." class="revaro-input text-xs py-2 flex-grow">
                            <button type="button" onclick="searchTechLocation()" class="revaro-btn-outline text-xs px-3.5 font-bold rounded-xl whitespace-nowrap">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>

                        <!-- Map Container -->
                        <div id="techLocationMap" class="w-full h-56 rounded-xl border-2 border-primary z-0 shadow-sm"></div>

                        <!-- Koordinat info & Simpan cepat -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white p-3 rounded-xl border border-primary/30 text-xs">
                            <div class="font-mono text-[11px] text-textDark">
                                <span class="text-textLight">Koordinat Patokan:</span>
                                <strong id="techCoordDisplay" class="text-primary font-bold ml-1">-6.208800, 106.845600</strong>
                            </div>
                            <button type="button" onclick="saveOnlyDomicileLocation()" class="revaro-btn-orange text-[11px] font-black py-1.5 px-3 rounded-lg shadow-sm">
                                📍 Simpan Titik Domisili Saja
                            </button>
                        </div>

                        <input type="hidden" id="teknisiLat" value="-6.208800">
                        <input type="hidden" id="teknisiLng" value="106.845600">
                    </div>

                    <!-- Password change sub-section -->
                    <div class="pt-4 border-t-2 border-accent space-y-3">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider block">Ubah Kata Sandi (Opsional)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" id="teknisiNewPassword" minlength="6" class="revaro-input font-medium" placeholder="Kata sandi baru">
                            <input type="password" id="teknisiConfirmPassword" minlength="6" class="revaro-input font-medium" placeholder="Ulangi kata sandi">
                        </div>
                    </div>

                    <button type="submit" class="w-full revaro-btn font-extrabold text-sm py-4 px-6 rounded-xl flex items-center justify-center gap-2">
                        <span>✓ Simpan Perubahan Profil Teknisi</span>
                    </button>
                </form>
            </div>

            <!-- Right Box (5 cols): Spesifikasi Lingkungan Platform & Status Role -->
            <div class="lg:col-span-5 revaro-card p-6 md:p-8 space-y-6">
                <div class="border-b-2 border-accent pb-3">
                    <h3 class="text-lg font-extrabold text-primary">Status Kemitraan Teknisi</h3>
                    <p class="text-xs text-textLight mt-1">Informasi status verifikasi dan modul dispatch GPS REVARO.</p>
                </div>

                <!-- Profile Header Card -->
                <div class="p-4 bg-orange-50 rounded-2xl border-2 border-secondary flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-secondary text-white flex items-center justify-center font-black text-2xl border-2 border-primary shadow-btn shrink-0" id="badgeTeknisiInitial">
                        T
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-base font-extrabold text-primary" id="displayTeknisiName">Budi Teknisi</h4>
                        <span id="displayTechRoleBadge" class="px-2.5 py-0.5 rounded text-[10px] font-black bg-white text-secondary border border-secondary uppercase tracking-wider inline-block">
                            TEKNISI RESMI TERVERIFIKASI
                        </span>
                    </div>
                </div>

                <!-- Key-Value Specs -->
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-textLight font-medium uppercase">Platform</span>
                        <span class="text-primary font-bold">REVARO Marketplace</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-textLight font-medium uppercase">Status Verifikasi</span>
                        <span id="displayVerifStatus" class="text-green-600 font-extrabold uppercase flex items-center gap-1.5">
                            <i class="fas fa-check-circle"></i> Disetujui (Active)
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-textLight font-medium uppercase">Tier Reputasi</span>
                        <span class="text-secondary font-black">Bronze / Gold Level</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-textLight font-medium uppercase">Rantai Pasok</span>
                        <span class="text-primary font-bold">Toko Sparepart Terintegrasi</span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-textLight font-medium uppercase">Zona Waktu</span>
                        <span class="text-primary font-bold">WIB (Asia/Jakarta)</span>
                    </div>
                </div>

                <!-- Active Controller Footer Box -->
                <div id="radarStatusBox" class="p-4 bg-accent/40 rounded-xl border-2 border-primary/20 space-y-1">
                    <p id="radarStatusTitle" class="text-[11px] font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-satellite-dish"></i> RADAR DISPATCH AKTIF
                    </p>
                    <p id="radarStatusDesc" class="text-xs text-textDark leading-relaxed">
                        Akun Anda aktif menerima feed order servis panggilan SOS terdekat dalam radius kerja dan pencairan dana langsung ke rekening bank.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 2: Pintasan Cepat Modul Teknisi -->
        <div class="space-y-4">
            <h3 class="text-base font-extrabold text-primary flex items-center gap-2">
                <span>Pintasan Cepat Teknisi</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shortcut 1: Dasbor Teknisi -->
                <a href="/teknisi/dashboard" class="revaro-card p-5 hover:-translate-y-1 transition group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-secondary flex items-center justify-center text-xl border-2 border-secondary shadow-btn group-hover:scale-105 transition">
                        🧰
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-primary group-hover:text-secondary transition">Konsol Order Teknisi</h4>
                        <p class="text-xs text-textLight">Buka feed order, update status servis, dan ajukan sparepart</p>
                    </div>
                </a>

                <!-- Shortcut 2: Buka Toko Sparepart -->
                <a href="/partner/register" class="revaro-card p-5 hover:-translate-y-1 transition group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-accent text-primary flex items-center justify-center text-xl border-2 border-primary shadow-btn group-hover:scale-105 transition">
                        🏪
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-primary group-hover:text-secondary transition">Buka Toko Sparepart Resmi</h4>
                        <p class="text-xs text-textLight">Daftarkan etalase suku cadang Anda ke ribuan teknisi</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        const token = localStorage.getItem('token') || localStorage.getItem('admin_token');
        const user = JSON.parse(localStorage.getItem('user') || localStorage.getItem('admin_user') || '{}');

        let techMap = null;
        let techMarker = null;

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

        function updateVerificationUI(status) {
            const topBadgeWrapper = document.getElementById('topVerifBadgeWrapper');
            const roleBadge = document.getElementById('displayTechRoleBadge');
            const verifStatus = document.getElementById('displayVerifStatus');
            const radarBox = document.getElementById('radarStatusBox');
            const radarTitle = document.getElementById('radarStatusTitle');
            const radarDesc = document.getElementById('radarStatusDesc');
            const pendingBanner = document.getElementById('pendingAlertBanner');

            const normalizedStatus = (status || 'PENDING_REVIEW').toUpperCase();

            if (normalizedStatus === 'PENDING_REVIEW') {
                if (pendingBanner) pendingBanner.classList.remove('hidden');
                if (topBadgeWrapper) {
                    topBadgeWrapper.innerHTML = `
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-amber-50 text-amber-800 border-2 border-amber-600 flex items-center gap-2 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>⏳ MENUNGGU VERIFIKASI ADMIN VERIFIKATOR</span>
                        </span>
                    `;
                }
                if (roleBadge) {
                    roleBadge.className = 'px-2.5 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-500 uppercase tracking-wider inline-block';
                    roleBadge.textContent = '⏳ MENUNGGU ACC ADMIN';
                }
                if (verifStatus) {
                    verifStatus.className = 'text-amber-600 font-extrabold uppercase flex items-center gap-1.5';
                    verifStatus.innerHTML = '<i class="fas fa-clock"></i> Menunggu Peninjauan Admin (Pending)';
                }
                if (radarBox) {
                    radarBox.className = 'p-4 bg-amber-50 rounded-xl border-2 border-amber-400 space-y-1';
                }
                if (radarTitle) {
                    radarTitle.className = 'text-[11px] font-black text-amber-900 uppercase tracking-wider flex items-center gap-1.5';
                    radarTitle.innerHTML = '<i class="fas fa-hourglass-half"></i> RADAR DISPATCH BELUM AKTIF (MENUNGGU ACC ADMIN)';
                }
                if (radarDesc) {
                    radarDesc.className = 'text-xs text-amber-800 leading-relaxed';
                    radarDesc.textContent = 'Pendaftaran kemitraan teknisi Anda sedang dalam antrean pemeriksaan dokumen oleh tim Admin Verifikator. Fitur dispatch order SOS & penerimaan pesanan akan aktif otomatis setelah akun disetujui (ACC).';
                }
            } else if (normalizedStatus === 'NEEDS_REVISION') {
                if (pendingBanner) pendingBanner.classList.add('hidden');
                if (topBadgeWrapper) {
                    topBadgeWrapper.innerHTML = `
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-red-50 text-red-800 border-2 border-red-600 flex items-center gap-2 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span>⚠️ PERLU REVISI DOKUMEN</span>
                        </span>
                    `;
                }
                if (roleBadge) {
                    roleBadge.className = 'px-2.5 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-900 border border-red-500 uppercase tracking-wider inline-block';
                    roleBadge.textContent = '⚠️ PERLU REVISI';
                }
                if (verifStatus) {
                    verifStatus.className = 'text-red-600 font-extrabold uppercase flex items-center gap-1.5';
                    verifStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Perlu Revisi Dokumen';
                }
                if (radarBox) {
                    radarBox.className = 'p-4 bg-red-50 rounded-xl border-2 border-red-400 space-y-1';
                }
                if (radarTitle) {
                    radarTitle.className = 'text-[11px] font-black text-red-900 uppercase tracking-wider flex items-center gap-1.5';
                    radarTitle.innerHTML = '<i class="fas fa-exclamation-circle"></i> DOKUMEN PERLU DIPERBAIKI';
                }
                if (radarDesc) {
                    radarDesc.className = 'text-xs text-red-800 leading-relaxed';
                    radarDesc.textContent = 'Admin Verifikator meminta perbaikan berkas sertifikat atau identitas Anda. Silakan buka Dasbor Teknisi untuk memperbarui data.';
                }
            } else {
                // ACTIVE / APPROVED
                if (pendingBanner) pendingBanner.classList.add('hidden');
                if (topBadgeWrapper) {
                    topBadgeWrapper.innerHTML = `
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border-2 border-emerald-600 flex items-center gap-2 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>MITRA TEKNISI AKTIF TERVERIFIKASI</span>
                        </span>
                    `;
                }
                if (roleBadge) {
                    roleBadge.className = 'px-2.5 py-0.5 rounded text-[10px] font-black bg-white text-secondary border border-secondary uppercase tracking-wider inline-block';
                    roleBadge.textContent = 'TEKNISI RESMI TERVERIFIKASI';
                }
                if (verifStatus) {
                    verifStatus.className = 'text-green-600 font-extrabold uppercase flex items-center gap-1.5';
                    verifStatus.innerHTML = '<i class="fas fa-check-circle"></i> Disetujui (Active)';
                }
                if (radarBox) {
                    radarBox.className = 'p-4 bg-accent/40 rounded-xl border-2 border-primary/20 space-y-1';
                }
                if (radarTitle) {
                    radarTitle.className = 'text-[11px] font-black text-primary uppercase tracking-wider flex items-center gap-1.5';
                    radarTitle.innerHTML = '<i class="fas fa-satellite-dish"></i> RADAR DISPATCH AKTIF';
                }
                if (radarDesc) {
                    radarDesc.className = 'text-xs text-textDark leading-relaxed';
                    radarDesc.textContent = 'Akun Anda aktif menerima feed order servis panggilan SOS terdekat dalam radius kerja dan pencairan dana langsung ke rekening bank.';
                }
            }
        }

        async function loadData() {
            document.getElementById('teknisiNama').value = user.nama || 'Budi Teknisi';
            document.getElementById('teknisiEmail').value = user.email || 'teknisi@revaro.com';
            document.getElementById('displayTeknisiName').textContent = user.nama || 'Budi Teknisi';
            document.getElementById('badgeTeknisiInitial').textContent = (user.nama || 'T').charAt(0).toUpperCase();
            document.getElementById('teknisiPhone').value = user.telepon || user.hp || '081234567893';
            document.getElementById('teknisiSkill').value = (user.teknisi && user.teknisi.kategori) ? user.teknisi.kategori : 'AC, Kulkas, Mesin Cuci, Elektronik';

            let currentStatus = (user.teknisi && user.teknisi.status) ? user.teknisi.status : 'PENDING_REVIEW';
            updateVerificationUI(currentStatus);

            // Fetch live location & domicile from server
            try {
                const res = await fetch('/api/teknisi/location', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.teknisi && data.teknisi.status) {
                        currentStatus = data.teknisi.status;
                        updateVerificationUI(currentStatus);
                    }
                    if (data.latitude && data.longitude) {
                        document.getElementById('teknisiLat').value = data.latitude;
                        document.getElementById('teknisiLng').value = data.longitude;
                        document.getElementById('techCoordDisplay').textContent = `${parseFloat(data.latitude).toFixed(6)}, ${parseFloat(data.longitude).toFixed(6)}`;
                    }
                    if (data.alamat) {
                        document.getElementById('teknisiAlamat').value = data.alamat;
                    }
                }
            } catch(e) {
                console.warn('Gagal load lokasi teknisi:', e);
            }

            initTechSettingsMap();
        }

        function initTechSettingsMap() {
            const mapEl = document.getElementById('techLocationMap');
            if (!mapEl || typeof L === 'undefined') return;

            const initLat = parseFloat(document.getElementById('teknisiLat').value) || -6.2088;
            const initLng = parseFloat(document.getElementById('teknisiLng').value) || 106.8456;

            if (techMap) {
                techMap.remove();
            }

            techMap = L.map('techLocationMap').setView([initLat, initLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(techMap);

            techMarker = L.marker([initLat, initLng], { draggable: true }).addTo(techMap);
            techMarker.bindPopup('<b>🏠 Titik Domisili Tempat Tinggal</b><br>Pusat radar 7 KM / 20 KM').openPopup();

            techMarker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                updateCoords(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });

            techMap.on('click', function(e) {
                techMarker.setLatLng(e.latlng);
                updateCoords(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
        }

        function updateCoords(lat, lng) {
            document.getElementById('teknisiLat').value = lat.toFixed(6);
            document.getElementById('teknisiLng').value = lng.toFixed(6);
            document.getElementById('techCoordDisplay').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        async function reverseGeocode(lat, lng) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=id`);
                const data = await res.json();
                if (data && data.display_name) {
                    const cleanName = cleanIndonesianAddress(data.display_name);
                    document.getElementById('teknisiAlamat').value = cleanName;
                }
            } catch(e) {
                console.warn(e);
            }
        }

        function getTechCurrentLocation() {
            if (!navigator.geolocation) {
                Swal.fire('GPS Tidak Didukung', 'Browser tidak mendukung GPS Geolocation.', 'error');
                return;
            }

            Swal.fire({
                title: 'Mendeteksi GPS...',
                text: 'Mengambil posisi domisili tempat tinggal Anda saat ini.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    if (techMap && techMarker) {
                        techMap.setView([lat, lng], 16);
                        techMarker.setLatLng([lat, lng]);
                    }
                    updateCoords(lat, lng);
                    await reverseGeocode(lat, lng);
                    Swal.close();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Lokasi GPS Diterapkan!',
                        showConfirmButton: false,
                        timer: 1800
                    });
                },
                (err) => {
                    console.error(err);
                    Swal.fire('Gagal GPS', 'Gagal mendeteksi lokasi GPS secara otomatis.', 'warning');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        async function searchTechLocation() {
            const query = document.getElementById('techSearchMapInput').value.trim();
            if (!query) {
                Swal.fire('Input Kosong', 'Ketikkan nama jalan atau wilayah untuk dicari.', 'warning');
                return;
            }

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=1&accept-language=id`);
                const results = await res.json();
                if (results && results.length > 0) {
                    const item = results[0];
                    const lat = parseFloat(item.lat);
                    const lon = parseFloat(item.lon);
                    if (techMap && techMarker) {
                        techMap.setView([lat, lon], 16);
                        techMarker.setLatLng([lat, lon]);
                    }
                    updateCoords(lat, lon);
                    const cleanName = cleanIndonesianAddress(item.display_name);
                    document.getElementById('teknisiAlamat').value = cleanName;

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Lokasi Ditemukan di Peta!',
                        showConfirmButton: false,
                        timer: 1800
                    });
                } else {
                    Swal.fire('Tidak Ditemukan', `Tidak ada hasil pencarian untuk "${query}".`, 'info');
                }
            } catch(e) {
                console.error(e);
            }
        }

        async function saveOnlyDomicileLocation() {
            const lat = document.getElementById('teknisiLat').value;
            const lng = document.getElementById('teknisiLng').value;
            const alamat = document.getElementById('teknisiAlamat').value;

            try {
                Swal.fire({ title: 'Menyimpan Lokasi Domisili...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                const res = await fetch('/api/teknisi/location', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                        alamat: alamat
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    Swal.fire({
                        title: 'Titik Domisili Tersimpan!',
                        text: 'Titik patokan tempat tinggal berhasil diperbarui. Radar pencarian reguler 7 KM dan prioritas 20 KM kini berpusat di titik ini.',
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F'
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal menyimpan lokasi domisili.', 'error');
                }
            } catch(e) {
                console.error(e);
                Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
            }
        }

        async function saveTeknisiProfile() {
            const nama = document.getElementById('teknisiNama').value.trim();
            const email = document.getElementById('teknisiEmail').value.trim();
            const hp = document.getElementById('teknisiPhone').value.trim();
            const kategori = document.getElementById('teknisiSkill').value.trim();
            const lat = document.getElementById('teknisiLat').value;
            const lng = document.getElementById('teknisiLng').value;
            const alamat = document.getElementById('teknisiAlamat').value;
            const newPassword = document.getElementById('teknisiNewPassword').value;
            const confirmPassword = document.getElementById('teknisiConfirmPassword').value;

            if (newPassword && newPassword !== confirmPassword) {
                Swal.fire({ title: 'Password Tidak Cocok', text: 'Konfirmasi kata sandi baru tidak sama.', icon: 'warning', confirmButtonColor: '#0B4A8F' });
                return;
            }

            try {
                Swal.fire({ title: 'Menyimpan Profil...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                // Update full profile via specialized endpoint
                const res = await fetch('/api/teknisi/profile', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nama: nama,
                        email: email,
                        hp: hp,
                        kategori: kategori,
                        latitude: lat,
                        longitude: lng,
                        alamat: alamat,
                        new_password: newPassword || undefined
                    })
                });

                if (res.ok) {
                    const data = await res.json();
                    user.nama = data.user.nama;
                    user.telepon = data.user.telepon;
                    user.teknisi = data.teknisi;
                    localStorage.setItem('user', JSON.stringify(user));
                    document.getElementById('displayTeknisiName').textContent = data.user.nama;
                    document.getElementById('teknisiNewPassword').value = '';
                    document.getElementById('teknisiConfirmPassword').value = '';

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Profil dan pengaturan akun teknisi berhasil diperbarui.',
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F'
                    });
                } else {
                    const errData = await res.json();
                    Swal.fire('Gagal', errData.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            }
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
            window.location.href = '/';
        }

        document.addEventListener('DOMContentLoaded', loadData);
    </script>
    @include('components.dock')
</body>
</html>
