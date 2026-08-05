<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVARO — Layanan Servis Elektronik On-Demand Instan</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <p class="text-[10px] text-blue-200 font-medium">Repair & Restore</p>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center space-x-6">
                <a href="/" class="text-sm font-semibold hover:text-brandOrange transition">Pesan Jasa</a>
                <a href="/partner/register" class="text-sm font-semibold hover:text-brandOrange transition">Gabung Mitra</a>
                <a href="/admin/login" class="text-sm font-semibold hover:text-brandOrange transition">Admin Portal</a>
            </nav>

            <div class="flex items-center space-x-3">
                <div id="userProfileSection" class="hidden flex items-center space-x-2">
                    <span id="navUserName" class="text-xs font-bold bg-white/10 px-3 py-1.5 rounded-full border border-white/20"></span>
                    <button onclick="handleLogout()" class="bg-red-500/80 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">Logout</button>
                </div>
                <div id="authNavButtons" class="flex space-x-2">
                    <button onclick="openAuthModal('login')" class="bg-white/10 hover:bg-white/20 text-white text-xs px-3.5 py-2 rounded-lg font-bold border border-white/10 transition">Masuk</button>
                    <button onclick="openAuthModal('register')" class="bg-brandOrange hover:bg-orange-600 text-white text-xs px-3.5 py-2 rounded-lg font-bold transition shadow-sm">Daftar</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-grow max-w-6xl mx-auto w-full px-4 py-8 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Gojek-like Booking & Order Status Panel (8 Cols) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Welcome Hero -->
            <div class="bg-gradient-to-r from-brandBlue to-blue-800 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
                <div class="relative z-10 space-y-2">
                    <span class="bg-brandOrange/90 text-white text-[10px] uppercase font-bold tracking-widest px-2.5 py-1 rounded-full">Platform On-Demand</span>
                    <h2 class="text-2xl font-black leading-tight">Servis Elektronik Instan<br>Semudah Pesan Ojek Online</h2>
                    <p class="text-xs text-blue-100 max-w-md">Teknisi terdekat akan langsung datang ke rumah Anda. Transparan, bergaransi resmi, dan pembayaran aman via Escrow.</p>
                </div>
                <div class="absolute -right-10 -bottom-10 opacity-15">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m-8 14H5v-2h6v2m6-4H5v-2h12v2m0-4H5V7h12v2Z"/></svg>
                </div>
            </div>

            <!-- Panel 1: Booking Form (Form Pemesanan) -->
            <div id="bookingFormPanel" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-brandBlue flex items-center space-x-2">
                        <span>🚀 SOS Order Jasa Servis Cepat</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Isi formulir kerusakan di bawah ini untuk mencari Teknisi di sekitar Anda.</p>
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

            <!-- Panel 2: Live Tracking & Dispatch Status (Hidden by Default) -->
            <div id="liveOrderPanel" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <span id="orderBadge" class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Menghubungkan...</span>
                        <h3 id="orderCodeTitle" class="text-base font-extrabold text-brandBlue mt-1">REV-ORDER-0000</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Estimasi Biaya</p>
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
                            T
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
                    <div id="chatMessages" class="flex-grow p-3 space-y-2.5 overflow-y-auto text-xs">
                        <!-- Message logs -->
                    </div>
                    <form onsubmit="handleSendChatMessage(event)" class="border-t border-slate-200 bg-white p-2 flex">
                        <input id="chatInput" required type="text" class="flex-grow text-xs outline-none px-2.5 py-2 rounded-lg bg-slate-50 border border-slate-200 focus:border-brandBlue focus:bg-white transition" placeholder="Ketik pesan untuk teknisi...">
                        <button type="submit" class="bg-brandBlue text-white px-4 py-2 ml-2 rounded-lg text-xs font-bold">Kirim</button>
                    </form>
                </div>

                <!-- Payment actions panel -->
                <div id="orderPaymentPanel" class="hidden p-4 rounded-xl border border-orange-100 bg-orange-50/50 space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <h4 class="font-bold text-slate-800">💳 Menunggu Pembayaran</h4>
                            <p class="text-[10px] text-slate-500">Pekerjaan selesai. Silakan lakukan pembayaran escrow untuk melepas dana.</p>
                        </div>
                        <p id="payPrice" class="font-black text-brandOrange text-sm">Rp 100.000</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="handleEscrowPayment()" class="flex-1 bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-2.5 rounded-lg transition shadow">Bayar Sekarang (Escrow)</button>
                    </div>
                </div>

                <!-- Dispute filing trigger -->
                <div id="orderDisputePanel" class="hidden p-4 rounded-xl border border-red-100 bg-red-50/50 space-y-2.5">
                    <div class="text-xs">
                        <h4 class="font-bold text-slate-800">⚠️ Garansi & Sengketa Servis</h4>
                        <p class="text-[10px] text-slate-500 mt-0.5">Jika Anda tidak puas atau kerusakan terulang dalam masa garansi, Anda dapat mengajukan sengketa.</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="openDisputeModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded-lg transition">Ajukan Sengketa (Dispute)</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Info & Active Orders Feed (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- User Status Card -->
            <div id="loginStatusCard" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 text-center space-y-3">
                <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mx-auto text-lg font-bold">
                    P
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Halo, Pelanggan!</h4>
                    <p class="text-[10px] text-slate-400 mt-0.5">Masuk untuk memesan perbaikan elektronik.</p>
                </div>
                <div id="saldoSection" class="hidden bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                    <p class="text-[9px] uppercase font-bold text-slate-400">Saldo Dompet Pelanggan</p>
                    <p id="saldoValue" class="text-sm font-black text-brandBlue">Rp 500.000</p>
                </div>
            </div>

            <!-- Standard On-Demand Instructions -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h4 class="text-xs font-black uppercase text-brandBlue tracking-wider">💡 SOP Servis On-Demand</h4>
                
                <ul class="space-y-3 text-xs">
                    <li class="flex items-start space-x-2">
                        <span class="text-brandOrange font-bold">1.</span>
                        <p class="text-slate-600">Teknisi terdekat akan ditugaskan secara instan dalam radius 15 KM.</p>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-brandOrange font-bold">2.</span>
                        <p class="text-slate-600">Masa garansi bergantung kategori (AC 30 hari, kategori lain 7 hari).</p>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-brandOrange font-bold">3.</span>
                        <p class="text-slate-600">Dana ditahan di rekening penampung (Escrow) demi keamanan Anda.</p>
                    </li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Global Auth Modal (Login / Register as Pelanggan) -->
    <div id="authModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl relative animate-scale">
            <button onclick="closeAuthModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 font-extrabold text-sm">✕</button>
            
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 id="authModalTitle" class="text-base font-black text-brandBlue">Masuk Akun Pelanggan</h3>
                <p id="authModalSubtitle" class="text-[11px] text-slate-400 mt-0.5">Silakan masuk menggunakan kredensial Anda.</p>
            </div>

            <!-- Switch auth tab -->
            <div class="flex space-x-2 bg-slate-100 p-1 rounded-xl mb-4 text-xs font-semibold">
                <button onclick="switchAuthTab('login')" id="authTabLogin" class="flex-1 py-1.5 rounded-lg text-center bg-white text-brandBlue shadow-sm">Masuk</button>
                <button onclick="switchAuthTab('register')" id="authTabRegister" class="flex-1 py-1.5 rounded-lg text-center text-slate-500 hover:text-slate-700">Daftar</button>
            </div>

            <form id="authForm" onsubmit="handleAuthSubmit(event)" class="space-y-4">
                <input type="hidden" id="authMode" value="login">

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Alamat Email</label>
                    <input required type="email" id="authEmail" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: budi@gmail.com">
                </div>

                <div id="authNameField" class="hidden">
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" id="authName" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: Budi Prasetyo">
                </div>

                <div id="authPhoneField" class="hidden">
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Nomor WhatsApp/HP</label>
                    <input type="text" id="authPhone" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: 08123456789">
                </div>

                <div id="authAddressField" class="hidden">
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Alamat Lengkap</label>
                    <input type="text" id="authAddress" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Contoh: Jl. Sudirman No. 45">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Password</label>
                    <input required type="password" id="authPassword" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Minimal 6 karakter">
                </div>

                <button type="submit" class="w-full bg-brandBlue hover:bg-blue-800 text-white font-bold text-xs py-2.5 rounded-lg transition shadow-md shadow-blue-800/10">
                    Kirim Kredensial
                </button>
            </form>
        </div>
    </div>

    <!-- Dispute Filing Modal -->
    <div id="disputeModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl relative animate-scale">
            <button onclick="closeDisputeModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 font-extrabold text-sm">✕</button>
            
            <div class="border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-base font-black text-red-600">Ajukan Sengketa (Dispute)</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Beri tahu kami masalah servis agar Admin Resolusi dapat melakukan investigasi.</p>
            </div>

            <form onsubmit="handleDisputeSubmit(event)" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Alasan Pengajuan Sengketa</label>
                    <textarea required id="disputeReason" rows="3" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2.5 outline-none focus:border-red-500 focus:bg-white transition" placeholder="Contoh: Teknisi menyervis AC tapi keesokan harinya AC langsung mati total dan tidak mau bertanggung jawab..."></textarea>
                </div>

                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-2.5 rounded-lg transition shadow-md shadow-red-800/10">
                    Kirim Laporan Sengketa
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-500 text-center py-6 mt-8 border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 text-xs space-y-2">
            <p class="font-bold text-slate-400">© 2026 REVARO — Repair & Restore Marketplace Platform.</p>
            <p>Sistem ini terintegrasi penuh menggunakan multi-role dan rate limiter dinamis.</p>
        </div>
    </footer>

    <!-- Client-Side State Controller Logic (Vanilla JS) -->
    <script>
        let currentOrder = null;
        let orderPollInterval = null;

        // Check login state on load
        window.addEventListener('load', () => {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));
            const profile = JSON.parse(localStorage.getItem('profile'));

            if (token && user) {
                document.getElementById('authNavButtons').classList.add('hidden');
                document.getElementById('userProfileSection').classList.remove('hidden');
                document.getElementById('navUserName').innerText = user.nama;
                
                // Show user card info
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
                        <p id="saldoValue" class="text-sm font-black text-brandBlue">Rp ${(profile && profile.saldo) ? parseFloat(profile.saldo).toLocaleString('id-ID') : '0'}</p>
                    </div>
                `;
                
                // Fetch active order if exists
                checkActiveOrders();
            }
        });

        // Randomize mock coordinates
        function randomizeCoords() {
            // Random point near Jakarta Central
            const offsetLat = (Math.random() - 0.5) * 0.05;
            const offsetLong = (Math.random() - 0.5) * 0.05;
            document.getElementById('geoLat').value = (-6.2088 + offsetLat).toFixed(4);
            document.getElementById('geoLong').value = (106.8456 + offsetLong).toFixed(4);
        }

        // Open Auth Modal
        function openAuthModal(mode) {
            document.getElementById('authModal').classList.remove('hidden');
            switchAuthTab(mode);
        }

        // Close Auth Modal
        function closeAuthModal() {
            document.getElementById('authModal').classList.add('hidden');
        }

        // Switch Auth Tab
        function switchAuthTab(mode) {
            document.getElementById('authMode').value = mode;
            const tabLogin = document.getElementById('authTabLogin');
            const tabRegister = document.getElementById('authTabRegister');
            
            const nameField = document.getElementById('authNameField');
            const phoneField = document.getElementById('authPhoneField');
            const addressField = document.getElementById('authAddressField');

            if (mode === 'login') {
                tabLogin.className = "flex-1 py-1.5 rounded-lg text-center bg-white text-brandBlue shadow-sm";
                tabRegister.className = "flex-1 py-1.5 rounded-lg text-center text-slate-500 hover:text-slate-700";
                
                document.getElementById('authModalTitle').innerText = "Masuk Akun Pelanggan";
                document.getElementById('authModalSubtitle').innerText = "Silakan masuk untuk melanjutkan pesanan Anda.";

                nameField.classList.add('hidden');
                phoneField.classList.add('hidden');
                addressField.classList.add('hidden');
                
                document.getElementById('authName').required = false;
                document.getElementById('authPhone').required = false;
                document.getElementById('authAddress').required = false;
            } else {
                tabRegister.className = "flex-1 py-1.5 rounded-lg text-center bg-white text-brandBlue shadow-sm";
                tabLogin.className = "flex-1 py-1.5 rounded-lg text-center text-slate-500 hover:text-slate-700";
                
                document.getElementById('authModalTitle').innerText = "Daftar Akun Pelanggan";
                document.getElementById('authModalSubtitle').innerText = "Daftar gratis untuk memesan perbaikan elektronik.";

                nameField.classList.remove('hidden');
                phoneField.classList.remove('hidden');
                addressField.classList.remove('hidden');
                
                document.getElementById('authName').required = true;
                document.getElementById('authPhone').required = true;
                document.getElementById('authAddress').required = true;
            }
        }

        // Handle Register / Login
        async function handleAuthSubmit(e) {
            e.preventDefault();
            const mode = document.getElementById('authMode').value;
            const email = document.getElementById('authEmail').value;
            const password = document.getElementById('authPassword').value;

            try {
                let response;
                if (mode === 'login') {
                    response = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email, password })
                    });
                } else {
                    const nama = document.getElementById('authName').value;
                    const hp = document.getElementById('authPhone').value;
                    const alamat = document.getElementById('authAddress').value;

                    response = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            role_slug: 'pelanggan',
                            nama,
                            email,
                            password,
                            hp,
                            alamat
                        })
                    });
                }

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || JSON.stringify(result.errors));
                    return;
                }

                // Check role, admins cannot log in here
                if (result.role && result.role !== 'Pelanggan') {
                    alert("Akses dibatasi. Halaman ini hanya untuk Pelanggan. Admin silakan login melalui portal khusus admin.");
                    return;
                }

                // Save to local storage
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                localStorage.setItem('profile', JSON.stringify(result.profile));

                alert(result.message || 'Berhasil masuk.');
                closeAuthModal();
                window.location.reload();

            } catch (err) {
                console.error(err);
                alert("Kesalahan koneksi jaringan.");
            }
        }

        // Handle Logout
        function handleLogout() {
            localStorage.clear();
            window.location.reload();
        }

        // Submit SOS Order
        async function submitSOSOrder(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            if (!token) {
                alert("Anda harus masuk (login) terlebih dahulu sebelum dapat memesan jasa perbaikan!");
                openAuthModal('login');
                return;
            }

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
                    body: JSON.stringify({
                        keluhan,
                        alamat_servis,
                        latitude,
                        longitude
                    })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || JSON.stringify(result.errors));
                    return;
                }

                alert("Order berhasil dikirim! Memulai pencarian teknisi terdekat...");
                currentOrder = result.order;
                renderOrderPanel(result.order);

            } catch (err) {
                console.error(err);
                alert("Kesalahan jaringan saat mengirim order.");
            }
        }

        // Check if user has active order on page load
        async function checkActiveOrders() {
            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                // Fetch the latest customer order
                // For demo/simplicity, we pull from the system
                const response = await fetch('/api/orders', {
                    method: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.orders && result.orders.length > 0) {
                        // Find the first active/disputed/completed order
                        const activeOrder = result.orders.find(o => !['COMPLETED_RELEASED', 'CANCELLED'].includes(o.status));
                        if (activeOrder) {
                            currentOrder = activeOrder;
                            renderOrderPanel(activeOrder);
                        }
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Render Order Panel State
        function renderOrderPanel(order) {
            document.getElementById('bookingFormPanel').classList.add('hidden');
            const panel = document.getElementById('liveOrderPanel');
            panel.classList.remove('hidden');

            document.getElementById('orderCodeTitle').innerText = order.kode_order;
            document.getElementById('orderBadge').innerText = order.status;

            // Price mapping
            const fee = parseFloat(order.transaksi ? order.transaksi.total : 100000);
            document.getElementById('orderPrice').innerText = 'Rp ' + fee.toLocaleString('id-ID');
            document.getElementById('payPrice').innerText = 'Rp ' + fee.toLocaleString('id-ID');

            const searching = document.getElementById('searchingState');
            const techDetails = document.getElementById('techAssignedDetails');
            const chatContainer = document.getElementById('chatBoxContainer');
            const paymentPanel = document.getElementById('orderPaymentPanel');
            const disputePanel = document.getElementById('orderDisputePanel');

            searching.classList.add('hidden');
            techDetails.classList.add('hidden');
            chatContainer.classList.add('hidden');
            paymentPanel.classList.add('hidden');
            disputePanel.classList.add('hidden');

            if (order.status === 'SEARCHING') {
                searching.classList.remove('hidden');
            } else if (order.status === 'DISPATCHED' || order.status === 'ON_THE_WAY' || order.status === 'IN_PROGRESS') {
                techDetails.classList.remove('hidden');
                chatContainer.classList.remove('hidden');
                
                if (order.teknisi) {
                    document.getElementById('assignedTechName').innerText = order.teknisi.nama;
                    document.getElementById('assignedTechHP').innerText = '📞 ' + order.teknisi.hp;
                } else {
                    document.getElementById('assignedTechName').innerText = "Mencocokkan Teknisi...";
                }

                // Poll messages
                fetchChatMessages(order.id);
            } else if (order.status === 'AWAITING_PAYMENT') {
                techDetails.classList.remove('hidden');
                paymentPanel.classList.remove('hidden');
                
                if (order.teknisi) {
                    document.getElementById('assignedTechName').innerText = order.teknisi.nama;
                    document.getElementById('assignedTechHP').innerText = '📞 ' + order.teknisi.hp;
                }
            } else if (order.status === 'COMPLETED' || order.status === 'RESOLVED') {
                techDetails.classList.remove('hidden');
                disputePanel.classList.remove('hidden');

                if (order.teknisi) {
                    document.getElementById('assignedTechName').innerText = order.teknisi.nama;
                    document.getElementById('assignedTechHP').innerText = 'Status Perbaikan: Selesai';
                }
            } else if (order.status === 'DISPUTED') {
                techDetails.classList.remove('hidden');
                document.getElementById('assignedTechName').innerText = "Status Perbaikan: SENGKETA (DISPUTED)";
                document.getElementById('assignedTechHP').innerText = "Penyelesaian sedang diinvestigasi Admin Resolusi.";
            }

            // Start polling status
            startStatusPolling(order.id);
        }

        // Poll Order Status
        function startStatusPolling(orderId) {
            if (orderPollInterval) clearInterval(orderPollInterval);

            orderPollInterval = setInterval(async () => {
                const token = localStorage.getItem('token');
                if (!token) return;

                try {
                    const response = await fetch(`/api/orders/${orderId}`, {
                        method: 'GET',
                        headers: { 'Authorization': `Bearer ${token}` }
                    });

                    if (response.ok) {
                        const result = await response.json();
                        const order = result.order;
                        currentOrder = order;
                        
                        // Update UI status badge
                        document.getElementById('orderBadge').innerText = order.status;

                        // Re-render subpanels if status has transitioned
                        renderOrderPanel(order);
                        
                        // If fully closed or cancelled, clear interval and return to normal booking
                        if (['CANCELLED'].includes(order.status)) {
                            clearInterval(orderPollInterval);
                            alert("Pesanan telah dibatalkan.");
                            window.location.reload();
                        }
                    }
                } catch (err) {
                    console.error(err);
                }
            }, 3000);
        }

        // Fetch Chat Messages
        async function fetchChatMessages(orderId) {
            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                const response = await fetch(`/api/orders/${orderId}/chats`, {
                    method: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (response.ok) {
                    const result = await response.json();
                    const container = document.getElementById('chatMessages');
                    container.innerHTML = '';
                    
                    if (result.messages && result.messages.length > 0) {
                        result.messages.forEach(msg => {
                            const isMe = msg.pengirim_id === JSON.parse(localStorage.getItem('user')).id;
                            const div = document.createElement('div');
                            div.className = isMe 
                                ? 'bg-blue-100 text-blue-900 rounded-lg p-2.5 max-w-[80%] ml-auto text-right' 
                                : 'bg-slate-200 text-slate-800 rounded-lg p-2.5 max-w-[80%]';
                            div.innerHTML = `
                                <p class="font-bold text-[9px] text-slate-500">${msg.pengirim.nama}</p>
                                <p class="mt-0.5">${msg.pesan}</p>
                            `;
                            container.appendChild(div);
                        });
                        // Scroll to bottom
                        container.scrollTop = container.scrollHeight;
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Send Chat Message
        async function handleSendChatMessage(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            if (!token || !currentOrder) return;

            const input = document.getElementById('chatInput');
            const pesan = input.value;

            try {
                const response = await fetch(`/api/orders/${currentOrder.id}/chats`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ pesan })
                });

                if (response.ok) {
                    input.value = '';
                    fetchChatMessages(currentOrder.id);
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Escrow Payment
        async function handleEscrowPayment() {
            const token = localStorage.getItem('token');
            if (!token || !currentOrder) return;

            try {
                const response = await fetch(`/api/orders/${currentOrder.id}/pay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || "Gagal memproses pembayaran.");
                    return;
                }

                alert("Pembayaran Escrow HELD berhasil disalurkan ke sistem penampung!");
                window.location.reload();

            } catch (err) {
                console.error(err);
                alert("Kesalahan jaringan.");
            }
        }

        // Open Dispute Modal
        function openDisputeModal() {
            document.getElementById('disputeModal').classList.remove('hidden');
        }

        // Close Dispute Modal
        function closeDisputeModal() {
            document.getElementById('disputeModal').classList.add('hidden');
        }

        // Submit Dispute
        async function handleDisputeSubmit(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            if (!token || !currentOrder) return;

            const alasan = document.getElementById('disputeReason').value;

            try {
                const response = await fetch('/api/disputes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        order_id: currentOrder.id,
                        alasan
                    })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || "Gagal mengajukan sengketa.");
                    return;
                }

                alert("Sengketa perbaikan Anda berhasil didaftarkan! Admin Resolusi segera bertindak.");
                closeDisputeModal();
                window.location.reload();

            } catch (err) {
                console.error(err);
                alert("Kesalahan jaringan.");
            }
        }
    </script>

</body>
</html>
