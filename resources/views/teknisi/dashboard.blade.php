<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
</head>
<body class="bg-gray-50 min-h-screen text-textDark flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b-2 border-primary sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center">
            <a href="/teknisi/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-secondary text-white font-extrabold shadow-sm hidden sm:inline-block">Konsol Teknisi</span>
            </a>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Tombol Cepat: Pesan Servis (Pindah ke Pelanggan) -->
                <a href="/pelanggan/dashboard#pesan" class="revaro-btn-orange text-xs px-3 py-1.5 rounded-xl font-black flex items-center gap-1.5 shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition" title="Pesan Layanan Servis sebagai Pelanggan">
                    <i class="fa-solid fa-wrench"></i>
                    <span class="hidden md:inline">Pesan Servis</span>
                </a>

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
                            <a href="/pelanggan/dashboard" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-accent/50 transition">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">👤</span>
                                    <div>
                                        <p class="text-xs font-bold text-textDark">Dasbor Pelanggan</p>
                                        <p class="text-[10px] text-textLight">Pesan servis SOS &amp; lacak pesanan</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-primary font-bold">Buka →</span>
                            </a>
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-accent border-2 border-primary">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">🔧</span>
                                    <div>
                                        <p class="text-xs font-bold text-primary">Dasbor Teknisi</p>
                                        <p class="text-[10px] text-textLight">Terima &amp; eksekusi order servis</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black bg-primary text-white px-2 py-0.5 rounded-full">Aktif</span>
                            </div>
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

                <span id="navUserName" class="text-xs font-bold bg-accent text-primary px-3 py-1.5 rounded-xl border-2 border-primary hidden sm:inline-block">Teknisi</span>
                <button onclick="handleLogout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3.5 py-1.5 rounded-xl font-bold border-2 border-red-700 shadow-[2px_2px_0px_0px_#991B1B] transition">Keluar</button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 md:pl-20 pb-28">
        
        <!-- Document Onboarding Verifications Notices -->
        <div id="verifStatusBanner" class="hidden mb-6">
            <!-- Notices injection -->
        </div>

        <!-- Low Reputation / Escalation Alert Banner -->
        <div id="lowReputationAlert" class="hidden mb-6 p-5 rounded-2xl bg-red-50 border-2 border-red-600 shadow-[4px_4px_0px_0px_#DC2626] flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-600 text-white flex items-center justify-center text-2xl font-black shrink-0 shadow-sm">
                ⚠️
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h4 class="text-sm font-black text-red-950 uppercase tracking-wide">Peringatan Kinerja: Reputasi Kritis (<span id="alertRatingScore">0.0</span> / 5.0)</h4>
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full bg-red-600 text-white">Eskalasi Resolusi</span>
                </div>
                <p class="text-xs text-red-900 leading-relaxed font-medium">
                    Akun kemitraan Anda menerima ulasan tidak memuaskan dari pelanggan. Sistem otomatis mencatat log pelanggaran mutu dan meneruskan status akun Anda ke <strong>Admin Resolusi</strong> untuk investigasi kepatuhan SOP servis. Segera tingkatkan kualitas pengerjaan untuk memulihkan reputasi dan mencegah sanksi pembekuan akun (banned).
                </p>
            </div>
        </div>

        <!-- Dashboard Main Grid -->
        <div id="dashboardMainGrid" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Available Jobs & Active Job Tracker (8 Cols) -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Active Job Tracker -->
                    <div id="activeJobPanel" class="hidden revaro-card p-6 md:p-8 space-y-6">
                        <div class="flex justify-between items-center border-b-2 border-accent pb-3">
                            <div>
                                <span id="activeJobBadge" class="bg-secondary text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">SEDANG BERJALAN</span>
                                <h3 id="activeJobCode" class="text-base font-extrabold text-primary mt-1.5">REV-ORDER-0000</h3>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-textLight font-bold uppercase">Estimasi Biaya Jasa Servis</p>
                                <p class="text-base font-black text-secondary">Rp 150.000</p>
                            </div>
                        </div>

                        <div class="p-4 bg-accent/40 border-2 border-primary/20 rounded-xl space-y-2 text-xs font-medium">
                            <p><strong>Nama Pelanggan:</strong> <span id="activeCustomerName" class="font-bold text-primary">—</span></p>
                            <p><strong>Nomor HP Pelanggan:</strong> <span id="activeCustomerHP" class="font-bold text-primary">—</span></p>
                            <p><strong>Alamat Lokasi:</strong> <span id="activeCustomerAddress">—</span></p>
                            <p><strong>Keluhan Kerusakan:</strong> <span id="activeCustomerComplaint" class="text-textLight font-normal">—</span></p>
                        </div>

                        <!-- Interactive Navigation Route Map to Customer -->
                        <div class="space-y-2.5 bg-white p-4 rounded-2xl border-2 border-primary shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <label class="text-xs font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🗺️ Peta Navigasi Rute ke Rumah Pelanggan</span>
                                </label>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span id="techRouteDistanceBadge" class="text-[11px] font-black bg-secondary text-white px-2.5 py-1 rounded-full shadow-sm">
                                        Jarak: Menghitung...
                                    </span>
                                    <button onclick="updateTechnicianGpsLive()" type="button" class="revaro-btn-outline text-[11px] px-2.5 py-1 rounded-xl font-bold flex items-center gap-1" title="Perbarui Lokasi GPS Teknisi">
                                        <i class="fas fa-location-arrow text-secondary"></i> <span>Update GPS</span>
                                    </button>
                                    <a id="btnOpenGoogleMapsRoute" href="#" target="_blank" class="revaro-btn text-[11px] px-3 py-1 rounded-xl font-bold flex items-center gap-1 shadow-sm">
                                        <i class="fas fa-directions"></i> <span>Google Maps</span>
                                    </a>
                                </div>
                            </div>
                            <div id="techRouteMap" class="w-full h-72 rounded-xl border-2 border-primary z-0 relative shadow-sm"></div>
                            <div class="flex items-center justify-between text-[11px] text-textLight font-medium pt-1">
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-primary inline-block"></span> <strong>Posisi Teknisi</strong></span>
                                    <span>➔</span>
                                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-secondary inline-block"></span> <strong>Rumah Pelanggan</strong></span>
                                </div>
                                <span class="text-[10px] text-textLight italic">Rute jalan raya terpendek &amp; real-time</span>
                            </div>
                        </div>

                        <!-- Steps Timeline / Actions -->
                        <div class="bg-gray-50 p-5 rounded-2xl border-2 border-primary space-y-3 shadow-sm">
                            <h4 class="text-xs font-extrabold text-primary uppercase tracking-wider">Tahapan Pengerjaan Servis</h4>
                            <div class="flex flex-wrap gap-2.5" id="jobActionSteps">
                                <!-- Action buttons -->
                            </div>
                        </div>

                        <!-- Request Spareparts Form -->
                        <div id="techSparepartsRequestBlock" class="hidden bg-orange-50 p-6 rounded-2xl border-2 border-secondary space-y-4 shadow-form">
                            <div class="flex items-center space-x-2">
                                <span class="text-xl">📦</span>
                                <h4 class="text-xs font-black text-primary uppercase tracking-wider">Ajukan Suku Cadang Resmi ke Toko Terdekat</h4>
                            </div>
                            <p class="text-[11px] text-textDark">Pilih suku cadang resmi dari katalog toko sparepart terverifikasi untuk dipasang pada perangkat pelanggan:</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                                <div class="md:col-span-2">
                                    <select id="sparepartSelector" class="w-full text-xs bg-white revaro-input rounded-xl p-3 outline-none font-bold">
                                        <option value="">— Pilih Suku Cadang Resmi —</option>
                                        <!-- Populated via API search -->
                                    </select>
                                </div>
                                <div>
                                    <input type="number" id="sparepartQty" class="w-full text-xs bg-white revaro-input rounded-xl p-3 outline-none font-bold" value="1" min="1" placeholder="Jumlah (Qty)">
                                </div>
                            </div>

                            <button onclick="addSparepartToList()" class="w-full revaro-btn text-xs font-extrabold py-2.5 rounded-xl transition">+ Tambah ke Keranjang Servis</button>

                            <div class="overflow-x-auto pt-2 bg-white rounded-xl border border-secondary/30 p-2">
                                <table class="w-full text-xs text-left">
                                    <thead>
                                        <tr class="text-textLight border-b border-orange-200 text-[10px] uppercase font-black">
                                            <th class="pb-1.5">Nama Barang</th>
                                            <th class="pb-1.5 text-center">Qty</th>
                                            <th class="pb-1.5 text-right">Harga</th>
                                            <th class="pb-1.5 text-right">Subtotal</th>
                                            <th class="pb-1.5 text-right">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="requestedSparepartsListBody" class="divide-y divide-orange-100 font-semibold text-textDark">
                                        <!-- Items -->
                                    </tbody>
                                </table>
                            </div>

                            <button onclick="submitSparepartsRequest()" class="w-full revaro-btn-orange text-xs font-black py-3 rounded-xl transition shadow">
                                Kirim Rincian Suku Cadang ke Pelanggan untuk Disetujui
                            </button>
                        </div>

                        <!-- Chat with Customer -->
                        <div id="techChatBoxBlock" class="border-2 border-primary rounded-2xl overflow-hidden flex flex-col h-56 bg-white shadow-form">
                            <div class="bg-primary text-white px-4 py-2.5 flex justify-between items-center text-xs">
                                <span class="font-extrabold flex items-center gap-1.5"><i class="fas fa-comments text-secondary"></i> Chat Langsung Dengan Pelanggan</span>
                                <span class="text-[10px] text-green-300 font-bold flex items-center space-x-1">
                                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block animate-ping"></span>
                                    <span>Online</span>
                                </span>
                            </div>
                            <div id="techChatMessagesBox" class="flex-grow p-3 overflow-y-auto space-y-2 text-xs bg-slate-50">
                                <!-- Chats here -->
                            </div>
                            <form onsubmit="sendTechChatMessage(event)" class="border-t-2 border-primary flex p-1.5 bg-white">
                                <input id="techChatInput" type="text" class="flex-grow text-xs px-3 outline-none font-medium" placeholder="Ketik pesan konfirmasi ke pelanggan...">
                                <button type="submit" class="revaro-btn text-xs px-4 py-2 rounded-lg font-extrabold">Kirim</button>
                            </form>
                        </div>

                        <button onclick="cancelOrder()" class="text-red-500 hover:text-red-700 text-xs font-extrabold transition">Batalkan Pesanan Sepihak (Catatan Pelanggaran Sistem)</button>
                    </div>

                    <!-- Available Jobs Feed -->
                    <div id="jobsFeedPanel" class="revaro-card p-6 md:p-8 space-y-4">
                        <div class="border-b-2 border-accent pb-3 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-extrabold text-primary flex items-center space-x-2">
                                    <span>🚀 Feed Order Servis SOS Tersedia</span>
                                </h3>
                                <p class="text-xs text-textLight mt-0.5">Daftar order cepat di sekitar wilayah jangkauan Anda yang siap dikerjakan.</p>
                            </div>
                            <button onclick="loadAvailableJobs()" class="revaro-btn-outline text-xs px-3.5 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5">
                                <i class="fas fa-sync-alt"></i> <span>Refresh</span>
                            </button>
                        </div>

                        <div id="jobsListContainer" class="space-y-4">
                            <p class="text-textLight text-center py-8">Memuat daftar order aktif...</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Wallet & Profile Info (4 Cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- User Profile info card -->
                    <div class="revaro-card p-6 text-center space-y-3">
                        <div id="techProfileCard" class="space-y-3">
                            <div class="w-14 h-14 bg-secondary text-white border-2 border-primary rounded-2xl flex items-center justify-center mx-auto text-xl font-black shadow-btn">
                                🔧
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-primary">Teknisi REVARO</h4>
                                <p class="text-xs text-textLight mt-0.5 font-medium">Memuat profil mitra...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Saldo Snapshot Card -->
                    <div class="revaro-card p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-black text-primary uppercase tracking-wider">Saldo Dompet</h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">Siap Tarik</span>
                        </div>
                        <div class="bg-accent/40 p-4 rounded-xl border-2 border-primary/20 text-center">
                            <p class="text-[10px] uppercase font-bold text-textLight">Pendapatan Siap Tarik</p>
                            <p id="techSaldoSnapshot" class="text-2xl font-black text-primary mt-1">Rp 0</p>
                        </div>
                        <a href="/teknisi/dompet" class="w-full revaro-btn text-xs py-2.5 rounded-xl font-extrabold transition flex items-center justify-center gap-1.5 text-center block">
                            <span>Buka Dompet &amp; Tarik Dana →</span>
                        </a>
                    </div>

                    <!-- Quick Link to Riwayat Card -->
                    <div class="bg-white border-2 border-primary rounded-2xl p-5 shadow-form space-y-2.5">
                        <h4 class="text-xs font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                            <span>📋</span> <span>Catatan Mutasi &amp; Riwayat</span>
                        </h4>
                        <p class="text-xs text-textLight leading-relaxed">
                            Cek seluruh riwayat order servis yang telah selesai Anda tangani beserta rincian pembagian fee.
                        </p>
                        <a href="/teknisi/riwayat" class="w-full revaro-btn-outline text-xs py-2 rounded-xl font-bold transition text-center block">
                            Lihat Riwayat Pekerjaan →
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Client-Side State Controller Logic -->
    <script>
        const token = localStorage.getItem('token');
        let currentActiveJob = null;
        let selectedSpareparts = [];
        let globalTechProfile = null;
        let activeJobPollInterval = null;

        if (!token) {
            window.location.href = '/session-expired';
        }

        window.addEventListener('load', () => {
            fetchProfile();
            loadWalletLedger();
            checkActiveJob();
            loadAvailableJobs();
            loadSparepartsList();
            fetchNotifications();

            activeJobPollInterval = setInterval(() => {
                checkActiveJob();
                if (!currentActiveJob) {
                    loadAvailableJobs();
                }
            }, 5000);
            setInterval(fetchNotifications, 8000);
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
                                    <h5 class="text-xs font-bold ${isUnread ? 'text-primary' : 'text-slate-700'}">${n.judul}</h5>
                                    <span class="text-[10px] text-textLight whitespace-nowrap shrink-0">${timeStr}</span>
                                </div>
                                <p class="text-xs text-textDark mt-1 leading-snug">${n.pesan}</p>
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
            await markNotificationRead(id);
            const dd = document.getElementById('notifDropdown');
            if (dd) dd.classList.add('hidden');

            const title = n.judul || 'Notifikasi';
            const body = n.pesan || '';
            const lowerBody = body.toLowerCase();
            const lowerTitle = title.toLowerCase();

            // Skenario Saldo / Dompet
            if (lowerTitle.includes('dompet') || lowerTitle.includes('saldo') || lowerTitle.includes('penarikan') || lowerBody.includes('saldo') || lowerBody.includes('escrow')) {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: body,
                    showCancelButton: true,
                    confirmButtonText: '💳 Buka Dompet Teknisi',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#0B4A8F',
                    cancelButtonColor: '#64748B'
                }).then((r) => {
                    if (r.isConfirmed) window.location.href = '/teknisi/dompet';
                });
                return;
            }

            // Skenario Order / Riwayat
            if (lowerTitle.includes('pesanan') || lowerTitle.includes('order') || lowerBody.includes('#ord-')) {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: body,
                    showCancelButton: true,
                    confirmButtonText: '📋 Buka Riwayat Kerja',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#0B4A8F',
                    cancelButtonColor: '#64748B'
                }).then((r) => {
                    if (r.isConfirmed) window.location.href = '/teknisi/riwayat';
                });
                return;
            }

            // Default
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

        async function fetchProfile() {
            try {
                const res = await fetch('/api/auth/me?role=teknisi', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (res.status === 401) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    localStorage.removeItem('profile');
                    sessionStorage.clear();
                    window.location.href = '/session-expired';
                    return;
                }
                if (res.status === 503) {
                    window.location.reload();
                    return;
                }
                if (res.ok) {
                    const d = await res.json();
                    const user = d.user;
                    const profile = d.profile;
                    const techProfile = d.teknisi || user.teknisi || profile;
                    globalTechProfile = techProfile;

                    // Sync session details
                    localStorage.setItem('user', JSON.stringify(user));
                    localStorage.setItem('profile', JSON.stringify(techProfile));

                    // Check if user has technician access
                    const roleName = user.role ? user.role.nama : '';
                    const isTeknisi = !!techProfile || user.teknisi || roleName === 'Teknisi';
                    if (!isTeknisi) {
                        Swal.fire('Belum Terdaftar', 'Anda belum terdaftar sebagai Teknisi. Silakan daftar mitra terlebih dahulu.', 'info')
                            .then(() => { window.location.href = '/partner/register'; });
                        return;
                    }

                    // Check verification status: PENDING_REVIEW, NEEDS_REVISION, or REJECTED
                    const banner = document.getElementById('verifStatusBanner');
                    const mainGrid = document.getElementById('dashboardMainGrid');
                    const techStatus = (techProfile && techProfile.status) ? techProfile.status : 'PENDING_REVIEW';

                    if (techStatus === 'PENDING_REVIEW' || (techProfile && techStatus !== 'ACTIVE' && techStatus !== 'APPROVED')) {
                        if (techStatus === 'REJECTED') {
                            document.body.innerHTML = `
                                <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
                                    <div class="max-w-md w-full bg-white border-2 border-red-500 rounded-3xl p-8 shadow-[6px_6px_0px_0px_#EF4444] text-center space-y-4">
                                        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-3xl mx-auto border-2 border-red-500 shadow-sm">
                                            ❌
                                        </div>
                                        <h3 class="text-lg font-black text-red-600">Pendaftaran Ditolak</h3>
                                        <div class="p-3.5 bg-red-50 border border-red-300 rounded-2xl text-xs text-red-900 leading-relaxed font-semibold text-left">
                                            Mohon maaf, pengajuan pendaftaran kemitraan Teknisi Anda tidak disetujui oleh tim Admin Verifikator REVARO.
                                        </div>
                                        <div class="pt-2">
                                            <a href="/?stay=1" class="w-full inline-block revaro-btn py-3 px-6 rounded-xl text-xs font-black shadow-btn">
                                                Kembali ke Beranda
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            return;
                        } else if (techStatus === 'NEEDS_REVISION') {
                            banner.classList.remove('hidden');
                            mainGrid.classList.add('hidden');
                            // Fall through to display revision banner below
                        } else {
                            // PENDING_REVIEW
                            const registeredCategory = (techProfile && techProfile.kategori) ? techProfile.kategori : 'Air Conditioner (AC)';
                            document.body.innerHTML = `
                                <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
                                    <div class="max-w-md w-full bg-white border-2 border-primary rounded-3xl p-8 shadow-[6px_6px_0px_0px_#0B4A8F] text-center space-y-4">
                                        <div class="w-16 h-16 bg-amber-100 text-amber-800 rounded-2xl flex items-center justify-center text-3xl mx-auto border-2 border-amber-500 shadow-sm">
                                            ⏳
                                        </div>
                                        <h3 class="text-lg font-black text-primary">Akses Dasbor Belum Disetujui</h3>
                                        <div class="p-3.5 bg-amber-50 border border-amber-300 rounded-2xl text-xs text-amber-900 leading-relaxed font-semibold text-left">
                                            Akun kemitraan Teknisi Anda sedang dalam antrean pemeriksaan oleh <strong>Admin Verifikator REVARO</strong>.
                                            <div class="mt-2 pt-2 border-t border-amber-200">
                                                <strong>Kategori Spesialisasi:</strong> <span class="text-primary font-bold">${registeredCategory}</span>
                                            </div>
                                            <p class="mt-2">Sesuai ketentuan platform REVARO, berkas kemitraan Anda <u>harus disetujui (ACC) terlebih dahulu</u> oleh Admin Verifikator sebelum Anda dapat mengakses konsol dasbor teknisi dan menerima order pekerjaan.</p>
                                        </div>
                                        <p class="text-xs text-textLight">Harap menunggu konfirmasi verifikasi dari tim admin kami.</p>
                                        <div class="pt-2">
                                            <a href="/?stay=1" class="w-full inline-block revaro-btn py-3 px-6 rounded-xl text-xs font-black shadow-btn">
                                                Kembali ke Beranda
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            return;
                        }
                    }

                    // Update shop slot in multi-role widget if user is also a shop
                    if (user.toko) {
                        const shopSlot = document.getElementById('techShopRoleSlot');
                        if (shopSlot) {
                            shopSlot.innerHTML = `
                                <a href="/toko/dashboard" class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 hover:bg-slate-100 transition">
                                    <span class="text-xs font-bold text-slate-700">🏪 Dasbor Toko Sparepart</span>
                                    <span class="text-[9px] text-brandBlue font-bold">Buka →</span>
                                </a>
                            `;
                        }
                    }

                    const ratingVal = techProfile ? parseFloat(techProfile.rating || 0) : 0;
                    let reputationBadge = '';
                    if (ratingVal >= 4.5) {
                        reputationBadge = '<span class="inline-block mt-1 text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">🌟 Reputasi Sangat Baik</span>';
                    } else if (ratingVal >= 3.0) {
                        reputationBadge = '<span class="inline-block mt-1 text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-300">👍 Reputasi Baik</span>';
                    } else if (ratingVal > 0) {
                        reputationBadge = '<span class="inline-block mt-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-300 animate-pulse">⚠️ Reputasi Kritis — Resolusi Aktif</span>';
                    } else {
                        reputationBadge = '<span class="inline-block mt-1 text-[9px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-300">Mitra Baru</span>';
                    }

                    // Check if rating drops severely to trigger escalation banner
                    if (ratingVal > 0 && ratingVal < 3.0) {
                        const alertBox = document.getElementById('lowReputationAlert');
                        const alertScore = document.getElementById('alertRatingScore');
                        if (alertBox) alertBox.classList.remove('hidden');
                        if (alertScore) alertScore.innerText = ratingVal.toFixed(1);
                    }

                    const specDisplay = (techProfile && techProfile.kategori) ? techProfile.kategori : (profile ? profile.kategori : 'Air Conditioner (AC)');
                    const tierDisplay = (techProfile && techProfile.tier) ? techProfile.tier : (profile ? profile.tier : 'Bronze');

                    document.getElementById('navUserName').innerText = user.nama;
                    document.getElementById('techProfileCard').innerHTML = `
                        <div class="w-12 h-12 bg-orange-100 text-brandOrange rounded-full flex items-center justify-center mx-auto text-lg font-black">
                            ${user.nama.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">${user.nama}</h4>
                            <p class="text-[10px] text-brandOrange font-bold mt-0.5">Spesialisasi: ${specDisplay}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5 font-bold">Tier: <span class="text-primary">${tierDisplay}</span> | Rating: ⭐️ <span class="${ratingVal > 0 && ratingVal < 3.0 ? 'text-red-600 font-black' : 'text-amber-500'}">${ratingVal.toFixed(1)}</span> / 5.0</p>
                            <div>${reputationBadge}</div>
                        </div>
                    `;

                    const unifiedSaldoNumber = d.unified_saldo !== undefined ? d.unified_saldo : (d.saldo !== undefined ? d.saldo : (user.teknisi ? user.teknisi.saldo : (techProfile ? techProfile.saldo : 0)));
                    const formattedSaldo = 'Rp ' + parseFloat(unifiedSaldoNumber || 0).toLocaleString('id-ID');
                    const saldoValEl = document.getElementById('techSaldoValue');
                    if (saldoValEl) saldoValEl.innerText = formattedSaldo;
                    const saldoSnapEl = document.getElementById('techSaldoSnapshot');
                    if (saldoSnapEl) saldoSnapEl.innerText = formattedSaldo;

                    if (techProfile && techProfile.status === 'NEEDS_REVISION') {
                        // Retrieve latest verification notes
                        banner.classList.remove('hidden');
                        mainGrid.classList.add('hidden');
                        
                        banner.innerHTML = `
                            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 space-y-4">
                                <div class="flex items-center space-x-2 text-red-600 font-extrabold text-sm">
                                    <span>📝</span> <span>Dokumen Anda Perlu Direvisi</span>
                                </div>
                                <div class="p-3 bg-red-100/50 border border-red-200 text-xs text-red-700 rounded-xl leading-normal">
                                    <strong>Catatan Admin Verifikator:</strong> <span id="revisionNotes">Dokumen KTP buram, harap unggah ulang.</span>
                                </div>
                                
                                <form id="resubmitForm" onsubmit="handleResubmit(event)" class="space-y-3 text-xs max-w-xl">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Nomor WhatsApp/HP</label>
                                            <input required type="text" id="resubPhone" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 outline-none" value="${profile.hp}">
                                        </div>
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Kategori Spesialisasi</label>
                                            <select required id="resubCategory" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 outline-none font-semibold text-slate-800">
                                                <option value="">Memuat kategori dari database...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Unggah Sertifikat Baru (.PDF/.JPG)</label>
                                            <input type="file" id="resubCert" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-[10px]">
                                        </div>
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Unggah KTP Baru (.JPG/.PNG)</label>
                                            <input type="file" id="resubKtp" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-[10px]">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold py-2.5 rounded-lg transition">Kirim Ulang Dokumen Onboarding</button>
                                </form>
                            </div>
                        `;
                        // Fetch latest verification record notes & load dynamic categories from DB
                        fetchRevisionNotes(profile.id);
                        loadCategoriesForResubmit(specDisplay);
                    } else {
                        banner.classList.add('hidden');
                        mainGrid.classList.remove('hidden');
                    }
                }
            } catch (err) { console.error(err); }
        }

        async function fetchRevisionNotes(techId) {
            try {
                // Read verification notes via audit trace or endpoint if any
                const res = await fetch('/api/admin/verifications', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const myVerif = data.find(v => v.teknisi_id === techId && v.status === 'NEEDS_REVISION');
                    if (myVerif && myVerif.catatan) {
                        document.getElementById('revisionNotes').innerText = myVerif.catatan;
                    }
                }
            } catch (e) {}
        }

        async function loadCategoriesForResubmit(selectedCategory = '') {
            const select = document.getElementById('resubCategory');
            if (!select) return;

            try {
                const res = await fetch('/api/jasa-servis/categories');
                let categories = [];
                if (res.ok) {
                    const data = await res.json();
                    categories = data.categories || [];
                    if (!categories.length && Array.isArray(data.services)) {
                        categories = [...new Set(data.services.map(s => (s.kategori || s.nama_jasa || '').trim()).filter(Boolean))];
                    }
                }

                if (!categories.length) {
                    categories = ['Air Conditioner (AC)', 'Lemari Es / Kulkas', 'Mesin Cuci', 'Pompa Air', 'TV & Perangkat Audio'];
                }

                const selCatTrim = (selectedCategory || '').trim();
                const catMap = categories.map(c => c.trim());

                if (selCatTrim && !catMap.some(c => c.toLowerCase() === selCatTrim.toLowerCase())) {
                    catMap.unshift(selCatTrim);
                }

                select.innerHTML = catMap.map(cat => {
                    const isMatch = selCatTrim && (
                        cat.toLowerCase() === selCatTrim.toLowerCase() ||
                        (selCatTrim.toLowerCase().includes('ac') && cat.toLowerCase().includes('ac')) ||
                        (selCatTrim.toLowerCase().includes('kulkas') && cat.toLowerCase().includes('kulkas')) ||
                        (selCatTrim.toLowerCase().includes('cuci') && cat.toLowerCase().includes('cuci')) ||
                        (selCatTrim.toLowerCase().includes('pompa') && cat.toLowerCase().includes('pompa')) ||
                        (selCatTrim.toLowerCase().includes('tv') && cat.toLowerCase().includes('tv'))
                    );
                    return `<option value="${cat}" ${isMatch ? 'selected' : ''}>${cat}</option>`;
                }).join('');

                if (selCatTrim) {
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value.toLowerCase() === selCatTrim.toLowerCase()) {
                            select.selectedIndex = i;
                            break;
                        }
                    }
                }
            } catch (err) {
                console.error('Gagal mengambil kategori untuk form revisi:', err);
                select.innerHTML = `
                    <option value="Air Conditioner (AC)">Air Conditioner (AC)</option>
                    <option value="Lemari Es / Kulkas">Lemari Es / Kulkas</option>
                    <option value="Mesin Cuci">Mesin Cuci</option>
                    <option value="Pompa Air">Pompa Air</option>
                    <option value="TV & Perangkat Audio">TV & Perangkat Audio</option>
                `;
            }
        }

        async function handleResubmit(e) {
            e.preventDefault();
            const hp = document.getElementById('resubPhone').value;
            const kategori = document.getElementById('resubCategory').value;
            
            const formData = new FormData();
            formData.append('hp', hp);
            formData.append('kategori', kategori);
            formData.append('latitude', -6.2088);
            formData.append('longitude', 106.8456);

            const cert = document.getElementById('resubCert').files[0];
            if (cert) formData.append('sertifikat', cert);

            const ktp = document.getElementById('resubKtp').files[0];
            if (ktp) formData.append('ktp', ktp);

            try {
                const res = await fetch('/api/profile/resubmit', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });
                const result = await res.json();
                if (res.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: 'Dokumen revisi Anda berhasil diajukan ulang ke antrean Admin Verifikator. Harap menunggu proses ACC.',
                        confirmButtonColor: '#0B4A8F'
                    }).then(() => {
                        window.location.href = '/pelanggan/dashboard';
                    });
                } else {
                    Swal.fire('Gagal', result.message || JSON.stringify(result.errors), 'error');
                }
            } catch (e) { console.error(e); }
        }

        async function loadWalletLedger() {
            try {
                const res = await fetch('/api/wallet/balance?role=teknisi', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const list = document.getElementById('techLedgerHistory');
                    if (data.transactions.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-4">Belum ada mutasi</p>';
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

        async function triggerWithdraw() {
            const { value: formValues } = await Swal.fire({
                title: 'Tarik Pendapatan ke Bank',
                html: `
                    <div class="space-y-3 text-left px-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Bank</label>
                            <input type="text" id="wdBank" class="w-full text-sm border border-slate-300 rounded-xl p-3 outline-none" placeholder="BCA / Mandiri / BNI / BRI">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Rekening</label>
                            <input type="text" id="wdName" class="w-full text-sm border border-slate-300 rounded-xl p-3 outline-none" placeholder="Nama pemilik rekening">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                            <input type="text" id="wdNumber" class="w-full text-sm font-mono border border-slate-300 rounded-xl p-3 outline-none" placeholder="123456789">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Penarikan (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 text-sm">Rp</span>
                                <input type="text" id="wdAmount" class="w-full text-base font-black pl-10 pr-3 py-3 border border-slate-300 rounded-xl outline-none" placeholder="50.000">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Minimal penarikan Rp 50.000</p>
                        </div>
                    </div>
                `,
                background: '#FFFFFF',
                confirmButtonColor: '#FF6A00',
                showCancelButton: true,
                didOpen: () => {
                    const input = document.getElementById('wdAmount');
                    input.addEventListener('input', function() {
                        let val = this.value.replace(/[^0-9]/g, '');
                        this.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
                    });
                },
                preConfirm: () => {
                    const bank = document.getElementById('wdBank').value;
                    const name = document.getElementById('wdName').value;
                    const number = document.getElementById('wdNumber').value;
                    const amountRaw = document.getElementById('wdAmount').value.replace(/[^0-9]/g, '');
                    if (!bank || !name || !number || !amountRaw || parseInt(amountRaw) < 50000) {
                        Swal.showValidationMessage('Harap lengkapi semua data. Minimal penarikan Rp 50.000');
                        return false;
                    }
                    return { bank_name: bank, account_name: name, account_number: number, amount: amountRaw };
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
                        Swal.fire('Berhasil!', result.message, 'success');
                        fetchProfile();
                        loadWalletLedger();
                    } else {
                        Swal.fire('Gagal', result.message || 'Saldo tidak mencukupi.', 'error');
                    }
                } catch (e) { console.error(e); }
            }
        }

        async function checkActiveJob() {
            try {
                const res = await fetch('/api/orders?role=teknisi', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                
                // An order is only active on-site if not finished/cancelled/resolved, and NOT already rated!
                const active = d.orders && d.orders.find(o => 
                    !['COMPLETED', 'CANCELLED', 'RESOLVED'].includes(o.status) &&
                    !o.rating &&
                    !(o.status === 'DISPUTED' && (o.dispute?.status === 'RESOLVED' || o.transaksi?.status_escrow === 'RELEASED'))
                );
                if (active) {
                    currentActiveJob = active;
                    showActiveJobPanel(active);
                    fetchChats();
                } else {
                    currentActiveJob = null;
                    document.getElementById('activeJobPanel').classList.add('hidden');
                    document.getElementById('jobsFeedPanel').classList.remove('hidden');
                    loadAvailableJobs();
                }
            } catch (e) { console.error(e); }
        }

        // --- Peta Navigasi Rute Teknisi Menuju Pelanggan ---
        let techRouteMapInstance = null;
        let techMarkerInstance = null;
        let custMarkerInstance = null;
        let routePolylineInstance = null;
        let currentTechLat = -6.2088;
        let currentTechLng = 106.8456;

        function initOrUpdateTechRouteMap(order) {
            const custLat = parseFloat(order.latitude) || -6.2088;
            const custLng = parseFloat(order.longitude) || 106.8456;

            if (globalTechProfile && globalTechProfile.latitude && globalTechProfile.longitude) {
                currentTechLat = parseFloat(globalTechProfile.latitude);
                currentTechLng = parseFloat(globalTechProfile.longitude);
            }

            const mapContainer = document.getElementById('techRouteMap');
            if (!mapContainer || typeof L === 'undefined') return;

            if (!techRouteMapInstance) {
                techRouteMapInstance = L.map('techRouteMap').setView([currentTechLat, currentTechLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(techRouteMapInstance);
            } else {
                techRouteMapInstance.invalidateSize();
            }

            // Marker Teknisi (Motor/Posisi Teknisi)
            const techIconHtml = `
                <div style="background:#0B4A8F;color:white;width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;border:2px solid white;box-shadow:2px 2px 0px 0px #FF7F00;font-size:16px;">
                    🛵
                </div>
            `;
            const techIcon = L.divIcon({ html: techIconHtml, className: '', iconSize: [34, 34], iconAnchor: [17, 17] });
            
            if (techMarkerInstance) {
                techMarkerInstance.setLatLng([currentTechLat, currentTechLng]);
            } else {
                techMarkerInstance = L.marker([currentTechLat, currentTechLng], { icon: techIcon }).addTo(techRouteMapInstance)
                    .bindPopup('<b>Posisi Anda (Teknisi)</b>');
            }

            // Marker Pelanggan (Rumah / Lokasi Servis)
            const custIconHtml = `
                <div style="background:#FF7F00;color:white;width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;border:2px solid white;box-shadow:2px 2px 0px 0px #0B4A8F;font-size:16px;">
                    🏠
                </div>
            `;
            const custIcon = L.divIcon({ html: custIconHtml, className: '', iconSize: [34, 34], iconAnchor: [17, 17] });

            if (custMarkerInstance) {
                custMarkerInstance.setLatLng([custLat, custLng]);
            } else {
                custMarkerInstance = L.marker([custLat, custLng], { icon: custIcon }).addTo(techRouteMapInstance)
                    .bindPopup(`<b>Lokasi Pelanggan</b><br>${order.alamat_servis || ''}`);
            }

            // Gambar rute jalan raya navigasi Leaflet / OSRM
            fetchRouteDirections(currentTechLat, currentTechLng, custLat, custLng);

            // Set link eksternal Google Maps
            const gmapsBtn = document.getElementById('btnOpenGoogleMapsRoute');
            if (gmapsBtn) {
                gmapsBtn.href = `https://www.google.com/maps/dir/?api=1&origin=${currentTechLat},${currentTechLng}&destination=${custLat},${custLng}&travelmode=driving`;
            }
        }

        async function fetchRouteDirections(startLat, startLng, endLat, endLng) {
            const badge = document.getElementById('techRouteDistanceBadge');
            try {
                const res = await fetch(`https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`);
                const data = await res.json();

                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    const route = data.routes[0];
                    const distanceKm = (route.distance / 1000).toFixed(1);
                    const durationMin = Math.round(route.duration / 60);

                    if (badge) {
                        badge.innerText = `Jarak: ${distanceKm} KM (~${durationMin} mnt)`;
                    }

                    const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);

                    if (routePolylineInstance) {
                        techRouteMapInstance.removeLayer(routePolylineInstance);
                    }

                    routePolylineInstance = L.polyline(coordinates, {
                        color: '#0B4A8F',
                        weight: 5,
                        opacity: 0.9
                    }).addTo(techRouteMapInstance);

                    const bounds = L.latLngBounds([[startLat, startLng], [endLat, endLng]]);
                    techRouteMapInstance.fitBounds(bounds, { padding: [45, 45] });
                    return;
                }
            } catch (e) {
                console.warn('OSRM routing fetch warning, fallback to straight line:', e);
            }

            // Fallback straight line polyline
            if (routePolylineInstance) {
                techRouteMapInstance.removeLayer(routePolylineInstance);
            }
            routePolylineInstance = L.polyline([[startLat, startLng], [endLat, endLng]], {
                color: '#FF7F00',
                weight: 4,
                dashArray: '6, 8',
                opacity: 0.85
            }).addTo(techRouteMapInstance);

            const bounds = L.latLngBounds([[startLat, startLng], [endLat, endLng]]);
            techRouteMapInstance.fitBounds(bounds, { padding: [40, 40] });

            const dist = calculateHaversine(startLat, startLng, endLat, endLng).toFixed(1);
            if (badge) {
                badge.innerText = `Jarak: ± ${dist} KM`;
            }
        }

        function calculateHaversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function updateTechnicianGpsLive() {
            if (!navigator.geolocation) {
                Swal.fire('GPS Tidak Didukung', 'Browser tidak mendukung deteksi GPS.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Mendeteksi GPS...',
                text: 'Mengambil koordinat live posisi Anda saat ini...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    currentTechLat = pos.coords.latitude;
                    currentTechLng = pos.coords.longitude;
                    Swal.close();
                    if (currentActiveJob) {
                        initOrUpdateTechRouteMap(currentActiveJob);
                    }
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Posisi GPS Teknisi Diperbarui!',
                        showConfirmButton: false,
                        timer: 1800
                    });
                },
                (err) => {
                    console.error(err);
                    Swal.fire('Gagal GPS', 'Pastikan izin lokasi GPS diaktifkan pada browser Anda.', 'warning');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        function showActiveJobPanel(order) {
            document.getElementById('jobsFeedPanel').classList.add('hidden');
            document.getElementById('activeJobPanel').classList.remove('hidden');
            const techStatusMap = {
                'DISPATCHED': '🛵 Pesanan Diterima',
                'ON_THE_WAY': '🛵 Menuju Lokasi Pelanggan',
                'IN_PROGRESS': '🔧 Pengerjaan Servis',
                'AWAITING_PAYMENT': '💳 Menunggu Pembayaran Pelanggan',
                'COMPLETED': '✅ Servis Selesai',
                'CANCELLED': '❌ Dibatalkan',
                'DISPUTED': '⚖️ Dalam Penanganan Sengketa',
                'RESOLVED': '🤝 Selesai Diputuskan'
            };
            document.getElementById('activeJobBadge').innerText = techStatusMap[order.status] || order.status;

            if (order.pelanggan) {
                document.getElementById('activeCustomerName').innerText = order.pelanggan.nama;
                document.getElementById('activeCustomerHP').innerText = order.pelanggan.hp;
                document.getElementById('activeCustomerAddress').innerText = order.alamat_servis;
                document.getElementById('activeCustomerComplaint').innerText = order.keluhan;
            }

            // Inisialisasi peta rute navigasi menuju lokasi pelanggan
            setTimeout(() => {
                initOrUpdateTechRouteMap(order);
            }, 200);

            const stepActions = document.getElementById('jobActionSteps');
            const sparepartsBlock = document.getElementById('techSparepartsRequestBlock');

            stepActions.innerHTML = '';
            sparepartsBlock.classList.add('hidden');

            if (order.status === 'DISPATCHED') {
                stepActions.innerHTML = `<button onclick="changeJobStatus('ontheway')" class="revaro-btn font-bold py-2.5 px-5 rounded-xl text-xs transition">🚗 Mulai Perjalanan (OTW ke Lokasi)</button>`;
            } else if (order.status === 'ON_THE_WAY') {
                stepActions.innerHTML = `<button onclick="changeJobStatus('arrive')" class="revaro-btn-orange font-bold py-2.5 px-5 rounded-xl text-xs transition">📍 Tiba di Lokasi & Diagnosa</button>`;
            } else if (order.status === 'IN_PROGRESS') {
                sparepartsBlock.classList.remove('hidden');
                stepActions.innerHTML = `<button onclick="changeJobStatus('complete')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition border-2 border-emerald-700 shadow-[2px_2px_0px_0px_#065F46]">✅ Selesaikan Perbaikan (Terbitkan Nota)</button>`;
                renderRequestedSparepartsList(order.spareparts || []);
            } else if (order.status === 'AWAITING_PAYMENT') {
                stepActions.innerHTML = `<p class="text-xs text-secondary font-black">Menunggu konfirmasi pembayaran faktur dari Pelanggan...</p>`;
            } else if (order.status === 'DISPUTED') {
                stepActions.innerHTML = `<div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-800 text-xs font-bold flex items-center gap-2"><span>⚖️</span> Pesanan ini sedang dalam investigasi sengketa oleh Admin Resolusi. Proses dibekukan sementara hingga ada keputusan final.</div>`;
            }
        }

        async function changeJobStatus(endpoint) {
            if (!currentActiveJob) return;
            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire({
                        title: 'Status Diperbarui',
                        text: d.message,
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F'
                    });
                    checkActiveJob();
                } else {
                    Swal.fire('Gagal', d.message || 'Gagal merubah status.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        async function loadAvailableJobs() {
            try {
                const res = await fetch('/api/orders/available', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                if (res.ok) {
                    const list = document.getElementById('jobsListContainer');
                    const orders = d.orders || [];
                    if (orders.length === 0) {
                        list.innerHTML = '<p class="text-textLight text-center py-8">Tidak ada order SOS yang masuk saat ini...</p>';
                    } else {
                        list.innerHTML = orders.map(o => `
                            <div class="p-5 border-2 border-primary/20 rounded-2xl hover:border-secondary transition bg-accent/20 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                                <div class="space-y-1.5">
                                    <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                        <span class="bg-red-100 text-red-800 text-[10px] font-black uppercase px-2 py-0.5 rounded-full border border-red-200">SOS REQUEST</span>
                                        <span class="text-xs text-primary font-mono font-bold">${o.kode_order}</span>
                                        <span class="bg-blue-100 text-blue-900 text-[10px] font-black uppercase px-2 py-0.5 rounded-full border border-blue-300">
                                            🛠️ ${o.kategori || 'Air Conditioner (AC)'}
                                        </span>
                                        <span class="bg-primary/10 text-primary text-[10px] font-black uppercase px-2 py-0.5 rounded-full border border-primary/20">
                                            📍 Radius: ${o.distance_km ? o.distance_km + ' KM' : 'Dalam Jangkauan'}
                                        </span>
                                        ${o.distance_km && o.distance_km > 7 ? '<span class="bg-secondary text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-full shadow-sm">👑 PRIORITAS PREMIUM</span>' : ''}
                                    </div>
                                    <h4 class="text-sm font-black text-primary">${o.pelanggan ? o.pelanggan.nama : 'Pelanggan'}</h4>
                                    <p class="text-xs text-textLight"><strong>Alamat:</strong> ${o.alamat_servis}</p>
                                    <p class="text-xs text-textDark italic bg-white p-2 rounded-lg border border-primary/10">"${o.keluhan}"</p>
                                    <p class="text-[11px] text-secondary font-black uppercase pt-1">Estimasi Jasa Servis: Rp 150.000</p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    <button onclick="acceptJob(${o.id})" class="revaro-btn-orange text-xs font-black py-2.5 px-6 rounded-xl transition">Terima Kerja</button>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function acceptJob(id) {
            const confirmAccept = await Swal.fire({
                title: 'Terima Pekerjaan Ini?',
                text: 'Pastikan Anda siap berangkat langsung ke lokasi pelanggan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6A00',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Terima!'
            });

            if (confirmAccept.isConfirmed) {
                try {
                    const res = await fetch(`/api/orders/${id}/accept`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const d = await res.json();
                    if (res.ok) {
                        Swal.fire({
                            title: 'Diterima!',
                            text: d.message,
                            icon: 'success',
                            confirmButtonColor: '#0B4A8F',
                            timer: 1800
                        });
                        if (d.order) {
                            currentActiveJob = d.order;
                            showActiveJobPanel(d.order);
                            fetchChats();
                        }
                        checkActiveJob();
                    } else {
                        Swal.fire('Gagal Menerima', d.message || 'Pekerjaan telah diambil teknisi lain.', 'error');
                        loadAvailableJobs();
                    }
                } catch (e) { console.error(e); }
            }
        }

        async function loadSparepartsList() {
            try {
                // Load catalog spareparts search
                const res = await fetch('/api/spareparts/search', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                const selector = document.getElementById('sparepartSelector');
                selector.innerHTML = '<option value="">— Pilih Suku Cadang —</option>';
                data.forEach(item => {
                    selector.innerHTML += `<option value="${item.id}">${item.nama} - Rp ${parseFloat(item.harga).toLocaleString('id-ID')} (${item.merk}) [Toko: ${item.toko ? item.toko.nama : 'Mitra'}]</option>`;
                });
            } catch (e) { console.error(e); }
        }

        function addSparepartToList() {
            const select = document.getElementById('sparepartSelector');
            const qty = parseInt(document.getElementById('sparepartQty').value) || 1;
            const val = select.value;
            if (!val) return;

            // Mock get details
            const optText = select.options[select.selectedIndex].text;
            const parts = optText.split(' - Rp ');
            const nama = parts[0];
            const priceParts = parts[1].split(' (');
            const harga = parseFloat(priceParts[0].replace(/\./g, ''));
            
            selectedSpareparts.push({
                sparepart_id: parseInt(val),
                nama: nama,
                qty: qty,
                harga: harga,
                subtotal: harga * qty
            });

            renderLocalSpareparts();
        }

        function removeSparepartLocal(index) {
            selectedSpareparts.splice(index, 1);
            renderLocalSpareparts();
        }

        function renderLocalSpareparts() {
            const tbody = document.getElementById('requestedSparepartsListBody');
            tbody.innerHTML = selectedSpareparts.map((p, idx) => `
                <tr>
                    <td class="py-1">${p.nama}</td>
                    <td class="py-1 text-center">${p.qty}</td>
                    <td class="py-1 text-right">Rp ${p.harga.toLocaleString('id-ID')}</td>
                    <td class="py-1 text-right">Rp ${p.subtotal.toLocaleString('id-ID')}</td>
                    <td class="py-1 text-right">
                        <button onclick="removeSparepartLocal(${idx})" class="text-red-500 font-bold hover:text-red-600">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        function renderRequestedSparepartsList(list) {
            if (selectedSpareparts.length > 0) return; // Don't override local draft
            const tbody = document.getElementById('requestedSparepartsListBody');
            tbody.innerHTML = list.map(p => {
                let badge = `<span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-full">${p.pivot.status}</span>`;
                if (p.pivot.status === 'PENDING_CUSTOMER') {
                    badge = `<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-300">⏳ Menunggu ACC Pelanggan</span>`;
                } else if (p.pivot.status === 'PENDING_SHOP') {
                    badge = `<span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-300">📦 Menunggu Stok Toko</span>`;
                } else if (p.pivot.status === 'CONFIRMED') {
                    badge = `<span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-300">✅ Disetujui Toko</span>`;
                } else if (p.pivot.status === 'REJECTED') {
                    badge = `<span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-red-300">❌ Ditolak</span>`;
                }
                return `
                    <tr>
                        <td class="py-1 font-medium">${p.nama}</td>
                        <td class="py-1 text-center">${p.pivot.qty}</td>
                        <td class="py-1 text-right">Rp ${parseFloat(p.pivot.harga).toLocaleString('id-ID')}</td>
                        <td class="py-1 text-right">Rp ${parseFloat(p.pivot.subtotal).toLocaleString('id-ID')}</td>
                        <td class="py-1 text-right">
                            ${badge}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function submitSparepartsRequest() {
            if (selectedSpareparts.length === 0 || !currentActiveJob) {
                Swal.fire('Form Kosong', 'Tambahkan minimal 1 sparepart ke dalam draf pengajuan.', 'warning');
                return;
            }

            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/spareparts`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ spareparts: selectedSpareparts })
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire('Dikirim!', d.message, 'success');
                    selectedSpareparts = [];
                    checkActiveJob();
                } else {
                    Swal.fire('Gagal', d.message || 'Terjadi kesalahan.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        async function fetchChats() {
            if (!currentActiveJob) return;
            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/chats`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const messages = await res.json();
                const box = document.getElementById('techChatMessagesBox');
                
                const currentUserRaw = localStorage.getItem('user');
                const currentUserId = currentUserRaw ? JSON.parse(currentUserRaw).id : null;

                box.innerHTML = messages.map(m => {
                    const isMe = m.dari_user_id === currentUserId;
                    const alignment = isMe ? 'text-right' : 'text-left';
                    const bubbleBg = isMe ? 'bg-brandOrange text-white ml-auto' : 'bg-slate-200 text-slate-800 mr-auto';
                    return `
                        <div class="${alignment}">
                            <div class="inline-block p-2 rounded-xl text-xs max-w-xs ${bubbleBg}">
                                ${m.pesan}
                            </div>
                        </div>
                    `;
                }).join('');
                box.scrollTop = box.scrollHeight;
            } catch (err) { console.error(err); }
        }

        async function sendTechChatMessage(e) {
            e.preventDefault();
            const input = document.getElementById('techChatInput');
            const msg = input.value.trim();
            if (!msg || !currentActiveJob) return;

            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/chats`, {
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
            } catch (err) { console.error(err); }
        }

        async function cancelOrder() {
            if (!currentActiveJob) return;
            const confirmCancel = await Swal.fire({
                title: 'Batalkan Pesanan Sepihak?',
                text: 'Tindakan ini akan memicu penalti WARNING dan dicatat ke log pelanggaran Anda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Batalkan'
            });

            if (confirmCancel.isConfirmed) {
                try {
                    const res = await fetch(`/api/orders/${currentActiveJob.id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const d = await res.json();
                    if (res.ok) {
                        Swal.fire('Dibatalkan!', d.message, 'success');
                        checkActiveJob();
                    } else {
                        Swal.fire('Gagal', d.message, 'error');
                    }
                } catch (e) { console.error(e); }
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
            } catch(e) {}
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = '/';
        }

        // Realtime Maintenance & Session Heartbeat
        setInterval(async () => {
            try {
                if (token) {
                    const ping = await fetch('/api/auth/me?role=teknisi', {
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
    @include('components.dock')
</body>
</html>
