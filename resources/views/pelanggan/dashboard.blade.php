<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#0A4C8A',
                        brandOrange: '#FF6A00',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-nav {
            background: rgba(10, 76, 138, 0.95);
            backdrop-filter: blur(8px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .shimmer {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
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
            background: rgba(15, 23, 42, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 106, 0, 0.2);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 106, 0, 0.4);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="glass-nav text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-2.5">
                <div class="bg-brandOrange p-2 rounded-xl font-extrabold text-sm tracking-widest text-white shadow-sm">
                    REV
                </div>
                <div>
                    <h1 class="text-lg font-extrabold tracking-tight">REVARO</h1>
                    <p class="text-[10px] text-blue-200 font-medium">Customer Dashboard</p>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center space-x-6">
                <a href="/pelanggan/dashboard" class="text-sm font-semibold text-brandOrange transition">Pesan Jasa</a>
                <a href="/partner/register" class="text-sm font-semibold hover:text-brandOrange transition">Gabung Mitra</a>
            </nav>

            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <span id="navUserName" class="text-xs font-bold bg-white/10 px-3 py-1.5 rounded-full border border-white/20">Pelanggan</span>
                    <button onclick="handleLogout()" class="bg-red-500/80 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-grow max-w-6xl mx-auto w-full px-4 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: SOS Booking & Order Status Panel (8 Cols) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Booking Form Panel -->
            <div id="bookingFormPanel" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-brandBlue flex items-center space-x-2">
                        <span>🚀 SOS Order Jasa Servis Cepat</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Isi formulir kerusakan di bawah ini untuk mencari Teknisi di sekitar Anda secara instan.</p>
                </div>

                <form id="sosForm" onsubmit="submitSOSOrder(event)" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pemesan</label>
                            <input required type="text" id="bookingName" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: Budi Prasetyo">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Servis</label>
                            <select required id="bookingCategory" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition">
                                <option value="AC Pilihan">Servis AC (Garansi 30 Hari)</option>
                                <option value="Mesin Cuci">Servis Mesin Cuci (Garansi 7 Hari)</option>
                                <option value="Kulkas">Servis Kulkas (Garansi 7 Hari)</option>
                                <option value="Pompa Air">Servis Pompa Air (Garansi 7 Hari)</option>
                                <option value="TV & Audio">Servis TV & Audio (Garansi 7 Hari)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Keluhan / Deskripsi Kerusakan</label>
                        <textarea required id="bookingComplaint" rows="2" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: AC saya bocor meneteskan air terus dan tidak dingin..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Lengkap Servis</label>
                        <input required type="text" id="bookingAddress" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: Jl. Sudirman No. 45 Blok C, Jakarta Selatan">
                    </div>

                    <!-- Geolocation Mock Map coordinates -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="text-xs font-bold text-slate-700">📍 Geokode Lokasi (GPS)</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Gunakan titik koordinat mock di bawah untuk pengujian radius dispatch.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="text" id="geoLat" readonly value="-6.2088" class="bg-white border border-slate-200 text-xs px-2.5 py-1.5 rounded-lg w-20 text-center font-mono">
                            <input type="text" id="geoLong" readonly value="106.8456" class="bg-white border border-slate-200 text-xs px-2.5 py-1.5 rounded-lg w-20 text-center font-mono">
                            <button type="button" onclick="randomizeCoords()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Ganti Lokasi</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-brandBlue hover:bg-blue-800 text-white font-bold text-sm py-3.5 rounded-xl transition shadow-md shadow-blue-800/10">
                        Pesan Jasa Servis Sekarang
                    </button>
                </form>
            </div>

            <!-- Live Tracking & Dispatch Status -->
            <div id="liveOrderPanel" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <span id="orderBadge" class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Menghubungkan...</span>
                        <h3 id="orderCodeTitle" class="text-base font-extrabold text-brandBlue mt-1">REV-ORDER-0000</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Estimasi Biaya Jasa</p>
                        <p id="orderPrice" class="text-sm font-black text-brandOrange">Rp 100.000</p>
                    </div>
                </div>

                <!-- Searching state loader -->
                <div id="searchingState" class="space-y-4 py-4 text-center">
                    <div class="shimmer w-16 h-16 rounded-full mx-auto relative flex items-center justify-center">
                        <svg class="w-8 h-8 text-brandBlue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Mencari Teknisi Terdekat...</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Sistem sedang mencocokkan Anda dengan mitra berjarak maksimum 15 KM.</p>
                    </div>
                </div>

                <!-- Tech info details -->
                <div id="techAssignedDetails" class="hidden p-4 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold">
                            🔧
                        </div>
                        <div>
                            <h4 id="assignedTechName" class="text-sm font-bold text-slate-800">Nama Teknisi</h4>
                            <p id="assignedTechHP" class="text-xs text-slate-500"></p>
                        </div>
                    </div>
                    <div>
                        <span class="bg-brandBlue text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase">Teknisi SOS</span>
                    </div>
                </div>

                <!-- Chat simulation box -->
                <div id="chatBoxContainer" class="hidden border border-slate-100 rounded-xl overflow-hidden flex flex-col h-48 bg-slate-50">
                    <div class="bg-brandBlue text-white px-4 py-2 flex justify-between items-center text-xs">
                        <span class="font-bold">💬 Chat Instan Dengan Teknisi</span>
                        <span class="text-[10px] text-emerald-400 font-bold flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-ping"></span>
                            <span>Online</span>
                        </span>
                    </div>
                    <div id="chatMessagesBox" class="flex-grow p-3 overflow-y-auto space-y-2 text-xs">
                        <!-- Chats populate here -->
                    </div>
                    <form onsubmit="sendChatMessage(event)" class="border-t border-slate-200 flex p-1 bg-white">
                        <input id="chatInput" type="text" class="flex-grow text-xs px-3 outline-none" placeholder="Ketik pesan kepada teknisi...">
                        <button type="submit" class="bg-brandBlue hover:bg-blue-800 text-white text-xs px-4 py-2 rounded-lg font-bold">Kirim</button>
                    </form>
                </div>

                <!-- Sparepart Approval Card -->
                <div id="sparepartApprovalBlock" class="hidden bg-orange-50 border border-orange-200 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">⚙️</span>
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Persetujuan Suku Cadang dari Teknisi</h4>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-normal">Teknisi telah mengajukan suku cadang tambahan untuk perbaikan alat Anda. Silakan setujui atau tolak pengajuan ini:</p>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead>
                                <tr class="text-slate-500 border-b border-orange-200 text-[10px] uppercase font-bold">
                                    <th class="pb-1.5">Nama Suku Cadang</th>
                                    <th class="pb-1.5 text-center">Qty</th>
                                    <th class="pb-1.5 text-right">Harga</th>
                                    <th class="pb-1.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="sparepartsApproveList" class="divide-y divide-orange-100 font-semibold">
                                <!-- Spareparts here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button onclick="respondSpareparts(false)" class="bg-white border border-slate-200 text-slate-700 text-xs px-4 py-2 rounded-xl font-bold hover:bg-slate-50 transition">Tolak</button>
                        <button onclick="respondSpareparts(true)" class="bg-brandOrange text-white text-xs px-4 py-2 rounded-xl font-bold hover:bg-orange-600 transition shadow">Setujui & Pesan</button>
                    </div>
                </div>

                <!-- Payment Block -->
                <div id="paymentBlock" class="hidden bg-slate-900 text-white rounded-2xl p-6 space-y-4 shadow border border-slate-800">
                    <div class="border-b border-slate-800 pb-3 flex justify-between items-center">
                        <h4 class="text-sm font-black text-white flex items-center space-x-2">
                            <span>🧾</span> <span>Invoice Pembayaran Layanan</span>
                        </h4>
                        <span class="bg-red-500/20 text-red-400 text-[9px] font-black uppercase px-2 py-0.5 rounded border border-red-500/30">Belum Terbayar</span>
                    </div>

                    <div class="text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Biaya Jasa Servis</span>
                            <span id="invoiceJasa">Rp 100.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Biaya Suku Cadang (Sparepart)</span>
                            <span id="invoiceSparepart">Rp 0</span>
                        </div>
                        <hr class="border-slate-800">
                        <div class="flex justify-between text-sm font-black text-brandOrange">
                            <span>Total Invoice</span>
                            <span id="invoiceTotal">Rp 100.000</span>
                        </div>
                        <!-- Rincian sparepart -->
                        <div id="invoiceItemsListContainer" class="hidden pt-2 border-t border-slate-800/80 space-y-1.5">
                            <p class="text-[9px] uppercase font-bold text-slate-500">Suku Cadang yang Dibeli:</p>
                            <div id="invoiceItemsList" class="space-y-1.5">
                                <!-- items -->
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="payInvoice()" class="flex-grow bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold py-3 rounded-xl transition shadow">
                            Bayar Sekarang via Saldo Dompet
                        </button>
                        <button onclick="triggerDisputeForm()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-4 py-3 rounded-xl font-bold transition">
                            Ajukan Dispute / Komplain
                        </button>
                    </div>
                </div>

                <!-- Escrow Confirm Release / Selesai -->
                <div id="escrowReleaseBlock" class="hidden bg-emerald-950 text-emerald-100 rounded-2xl p-6 border border-emerald-900 space-y-3">
                    <h4 class="text-sm font-black text-white flex items-center space-x-2">
                        <span>🤝</span> <span>Pembayaran Sukses (Dana di Escrow REVARO)</span>
                    </h4>
                    <p class="text-xs text-emerald-200">Dana Anda aman ditahan di escrow REVARO. Jika pengerjaan teknisi sudah selesai secara fisik dan berfungsi normal, silakan konfirmasi untuk melepaskan dana ke Teknisi & Toko.</p>
                    
                    <button onclick="confirmReleaseEscrow()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold py-3 rounded-xl transition shadow">
                        Konfirmasi Pekerjaan Selesai & Cairkan Dana
                    </button>
                </div>

                <!-- Rating Review Block -->
                <div id="ratingReviewBlock" class="hidden bg-white border border-slate-200 rounded-2xl p-6 space-y-4">
                    <h4 class="text-sm font-black text-brandBlue flex items-center space-x-2">
                        <span>⭐️</span> <span>Beri Ulasan Pekerjaan Teknisi</span>
                    </h4>
                    <p class="text-xs text-slate-500">Bantu kami meningkatkan kualitas layanan dengan memberi rating kepada teknisi Anda.</p>
                    
                    <div class="flex items-center space-x-3 justify-center py-2">
                        <button onclick="selectRating(1)" class="star-btn text-2xl text-slate-300">★</button>
                        <button onclick="selectRating(2)" class="star-btn text-2xl text-slate-300">★</button>
                        <button onclick="selectRating(3)" class="star-btn text-2xl text-slate-300">★</button>
                        <button onclick="selectRating(4)" class="star-btn text-2xl text-slate-300">★</button>
                        <button onclick="selectRating(5)" class="star-btn text-2xl text-slate-300">★</button>
                    </div>
                    <input type="hidden" id="ratingScore" value="0">
                    <textarea id="ratingComment" rows="2" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Tuliskan komentar Anda..."></textarea>
                    
                    <button onclick="submitRating()" class="w-full bg-brandBlue hover:bg-blue-800 text-white text-xs font-bold py-2.5 rounded-xl transition">
                        Kirim Ulasan
                    </button>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-400 pt-2 border-t border-slate-100">
                    <button id="cancelOrderBtn" onclick="cancelActiveOrder()" class="text-red-500 hover:text-red-600 font-bold transition">Batalkan Pesanan</button>
                    <span id="liveUpdatesTime">Pembaruan otomatis aktif...</span>
                </div>
            </div>

            <!-- Dispute Form Panel (Komplain/Garansi) -->
            <div id="disputeFormPanel" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-extrabold text-red-600">⚖️ Form Sengketa / Klaim Garansi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Ajukan klaim komplain dana escrow atau garansi layanan jika terjadi wanprestasi.</p>
                    </div>
                    <button onclick="closeDisputeForm()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alasan Pengajuan Komplain (Wajib)</label>
                        <textarea id="disputeReason" rows="3" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-red-600 focus:bg-white transition" placeholder="Tuliskan alasan komplain Anda secara rinci..."></textarea>
                    </div>
                    <button onclick="submitDispute()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-sm py-3.5 rounded-xl transition shadow">
                        Kirim Sengketa ke Admin Resolusi
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: User Account, Wallet, & Transaction History (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- User Status Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 text-center space-y-4">
                <div id="loginStatusCard" class="space-y-3">
                    <div class="w-12 h-12 bg-blue-100 text-brandBlue rounded-full flex items-center justify-center mx-auto text-lg font-black">
                        P
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Pelanggan REVARO</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5">Memuat profil...</p>
                    </div>
                </div>
            </div>

            <!-- Wallet Actions -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h4 class="text-xs font-black text-brandBlue uppercase tracking-wider">Kelola Saldo Dompet</h4>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="triggerTopup()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2.5 rounded-xl font-bold transition">💵 Top-Up</button>
                    <button onclick="triggerWithdraw()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2.5 rounded-xl font-bold transition">🏧 Tarik Dana</button>
                </div>
            </div>

            <!-- Transactions Ledger History -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h4 class="text-xs font-black text-brandBlue uppercase tracking-wider">Riwayat Mutasi Saldo</h4>
                <div id="ledgerHistoryList" class="space-y-3 max-h-64 overflow-y-auto pr-1 text-xs">
                    <p class="text-slate-400 text-center py-4">Belum ada transaksi</p>
                </div>
            </div>
            
            <!-- Dispute Cases Status Tracker -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h4 class="text-xs font-black text-red-600 uppercase tracking-wider">Status Dispute & Sengketa Saya</h4>
                <div id="disputesStatusList" class="space-y-3 max-h-64 overflow-y-auto pr-1 text-xs">
                    <p class="text-slate-400 text-center py-4">Tidak ada sengketa berjalan</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Client-Side State Controller Logic -->
    <script>
        let currentOrder = null;
        let orderPollInterval = null;
        const token = localStorage.getItem('token');

        if (!token) {
            alert('Akses dibatasi. Silakan login terlebih dahulu.');
            window.location.href = '/';
        }

        // Initialize details on load
        window.addEventListener('load', () => {
            fetchUserProfile();
            loadWalletLedger();
            loadDisputesList();
            checkActiveOrders();
            orderPollInterval = setInterval(checkActiveOrders, 4000);
        });

        async function fetchUserProfile() {
            try {
                const response = await fetch('/api/auth/me', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                if (response.ok) {
                    const user = result.user;
                    const profile = result.profile;
                    
                    // Sync session details
                    localStorage.setItem('user', JSON.stringify(user));
                    localStorage.setItem('profile', JSON.stringify(profile));

                    // Redirection check based on role
                    const roleName = user.role ? user.role.nama : '';
                    if (roleName === 'Teknisi') {
                        window.location.href = '/teknisi/dashboard';
                        return;
                    } else if (roleName === 'Toko Sparepart') {
                        window.location.href = '/toko/dashboard';
                        return;
                    }

                    document.getElementById('navUserName').innerText = user.nama;
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
                            <p id="saldoValue" class="text-sm font-black text-brandBlue">Rp ${profile ? parseFloat(profile.saldo).toLocaleString('id-ID') : '0'}</p>
                        </div>
                    `;
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function loadWalletLedger() {
            try {
                const res = await fetch('/api/wallet/balance', {
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
                    if (data.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-4">Tidak ada sengketa berjalan</p>';
                    } else {
                        list.innerHTML = data.map(d => {
                            const badgeColor = {
                                OPEN: 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                INVESTIGATING: 'bg-blue-100 text-blue-800 border-blue-200',
                                RESOLVED: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            }[d.status] || 'bg-slate-100 text-slate-800';
                            return `
                                <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl space-y-1.5">
                                    <div class="flex justify-between items-center">
                                        <span class="font-black text-slate-700">Order: ${d.order?.kode_order || '—'}</span>
                                        <span class="text-[9px] px-2 py-0.5 rounded-full border ${badgeColor}">${d.status}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500"><strong>Alasan:</strong> ${d.alasan}</p>
                                    ${d.resolusi ? `<p class="text-[11px] text-emerald-700"><strong>Resolusi:</strong> ${d.resolusi}</p>` : ''}
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
                input: 'number',
                inputLabel: 'Masukkan Jumlah Top-Up (Rp)',
                inputPlaceholder: 'Minimal Rp 10.000',
                background: '#FFFFFF',
                color: '#1E293B',
                confirmButtonColor: '#FF6A00',
                showCancelButton: true
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
                        body: JSON.stringify({ jumlah: amount })
                    });
                    const result = await res.json();
                    if (res.ok) {
                        Swal.fire('Top-Up Sukses!', result.message, 'success');
                        fetchUserProfile();
                        loadWalletLedger();
                    } else {
                        Swal.fire('Gagal', result.message || 'Periksa input Anda.', 'error');
                    }
                } catch (e) { console.error(e); }
            }
        }

        async function triggerWithdraw() {
            const { value: formValues } = await Swal.fire({
                title: 'Tarik Saldo Dompet',
                html:
                    '<input id="swal-amount" class="swal2-input" placeholder="Jumlah penarikan (Min Rp 10.000)" type="number">' +
                    '<input id="swal-bank" class="swal2-input" placeholder="Nama Bank (e.g. BCA, Mandiri)">' +
                    '<input id="swal-norek" class="swal2-input" placeholder="Nomor Rekening">',
                focusConfirm: false,
                confirmButtonColor: '#FF6A00',
                showCancelButton: true,
                preConfirm: () => {
                    return [
                        document.getElementById('swal-amount').value,
                        document.getElementById('swal-bank').value,
                        document.getElementById('swal-norek').value
                    ]
                }
            });

            if (formValues) {
                const [jumlah, bank, no_rekening] = formValues;
                if (!jumlah || !bank || !no_rekening) {
                    Swal.fire('Error', 'Semua form penarikan wajib diisi.', 'error');
                    return;
                }

                try {
                    const res = await fetch('/api/wallet/withdraw', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ jumlah, bank, no_rekening })
                    });
                    const result = await res.json();
                    if (res.ok) {
                        Swal.fire('Berhasil!', result.message, 'success');
                        fetchUserProfile();
                        loadWalletLedger();
                    } else {
                        Swal.fire('Gagal', result.message || 'Saldo tidak mencukupi.', 'error');
                    }
                } catch (e) { console.error(e); }
            }
        }

        function randomizeCoords() {
            const offsetLat = (Math.random() - 0.5) * 0.05;
            const offsetLong = (Math.random() - 0.5) * 0.05;
            document.getElementById('geoLat').value = (-6.2088 + offsetLat).toFixed(4);
            document.getElementById('geoLong').value = (106.8456 + offsetLong).toFixed(4);
        }

        async function submitSOSOrder(e) {
            e.preventDefault();
            const keluhan = document.getElementById('bookingComplaint').value;
            const alamat_servis = document.getElementById('bookingAddress').value;
            const latitude = document.getElementById('geoLat').value;
            const longitude = document.getElementById('geoLong').value;
            const nama = document.getElementById('bookingName').value;

            try {
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ keluhan, alamat_servis, latitude, longitude })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || JSON.stringify(result.errors));
                    return;
                }

                currentOrder = result.order;
                showLiveOrderPanel(result.order);
            } catch (err) {
                console.error(err);
                alert("Gagal mengirim pesanan SOS.");
            }
        }

        async function checkActiveOrders() {
            try {
                const response = await fetch('/api/orders', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await response.json();
                
                const active = result.orders.find(o => o.status !== 'COMPLETED' && o.status !== 'CANCELLED');
                if (active) {
                    currentOrder = active;
                    showLiveOrderPanel(active);
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
            document.getElementById('orderCodeTitle').innerText = order.kode_order;
            document.getElementById('orderBadge').innerText = order.status;
            
            // Handle cancel button visibility
            const cancelBtn = document.getElementById('cancelOrderBtn');
            if (['COMPLETED', 'CANCELLED', 'AWAITING_PAYMENT'].includes(order.status)) {
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

            searching.classList.add('hidden');
            techInfo.classList.add('hidden');
            chatBox.classList.add('hidden');
            sparepartsBlock.classList.add('hidden');
            paymentBlock.classList.add('hidden');
            escrowRelease.classList.add('hidden');
            ratingBlock.classList.add('hidden');

            if (order.status === 'SEARCHING') {
                searching.classList.remove('hidden');
            } else {
                techInfo.classList.remove('hidden');
                chatBox.classList.remove('hidden');
                if (order.teknisi) {
                    document.getElementById('assignedTechName').innerText = order.teknisi.nama;
                    document.getElementById('assignedTechHP').innerText = 'WA: ' + order.teknisi.hp;
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

            // Check Escrow
            if (order.transaksi && order.transaksi.status_pembayaran === 'PAID') {
                if (order.transaksi.status_escrow === 'HELD') {
                    escrowRelease.classList.remove('hidden');
                } else if (order.transaksi.status_escrow === 'RELEASED') {
                    // Check if rating exists
                    ratingBlock.classList.remove('hidden');
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
                                ${m.pesan}
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
            const score = document.getElementById('ratingScore').value;
            const comment = document.getElementById('ratingComment').value.trim();

            if (score == 0) {
                Swal.fire('Rating Wajib', 'Silakan pilih bintang terlebih dahulu.', 'warning');
                return;
            }

            try {
                // Post rating via dispute/review mock endpoints or standard api
                // In Phase 5, we can use order ratings logic:
                const res = await fetch('/api/disputes', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: currentOrder.id,
                        alasan: `Ulasan Pelanggan Bintang ${score}: ${comment || 'Puas dengan layanan'}`
                    })
                });

                if (res.ok) {
                    Swal.fire('Terima Kasih!', 'Ulasan Anda berhasil dikirim.', 'success');
                    // Complete order sequence locally
                    document.getElementById('ratingReviewBlock').classList.add('hidden');
                    // Refresh
                    checkActiveOrders();
                }
            } catch (e) { console.error(e); }
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

        function handleLogout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('profile');
            window.location.href = '/';
        }
    </script>
</body>
</html>
