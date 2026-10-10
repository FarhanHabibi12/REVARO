<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Partner Dashboard — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <a href="/toko/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-secondary text-white font-extrabold shadow-sm hidden sm:inline-block">Konsol Toko Sparepart</span>
            </a>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Tombol Cepat: Pesan Servis -->
                <a href="/pelanggan/dashboard#pesan" class="revaro-btn-orange text-xs px-3 py-1.5 rounded-xl font-black flex items-center gap-1.5 shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition" title="Pesan Layanan Servis Cepat">
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
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-accent border-2 border-primary">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">🏪</span>
                                    <div>
                                        <p class="text-xs font-bold text-primary">Dasbor Toko Sparepart</p>
                                        <p class="text-[10px] text-textLight">Katalog suku cadang &amp; omset</p>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black bg-primary text-white px-2 py-0.5 rounded-full">Aktif</span>
                            </div>
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

                <span id="navUserName" class="text-xs font-bold bg-accent text-primary px-3 py-1.5 rounded-xl border-2 border-primary hidden sm:inline-block">Toko Mitra</span>
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



        <div id="dashboardMainGrid" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Catalog Management & Incoming Orders (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Incoming Sparepart Orders -->
                <div class="revaro-card p-6 md:p-7 space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-accent pb-3">
                        <div class="flex items-center space-x-3">
                            <span class="w-10 h-10 rounded-xl bg-accent text-primary border-2 border-primary flex items-center justify-center text-lg font-black shadow-sm">
                                📦
                            </span>
                            <div>
                                <h3 class="text-base font-extrabold text-primary">Pesanan Suku Cadang Masuk</h3>
                                <p class="text-xs text-textLight">Konfirmasi ketersediaan stok komponen sebelum pengerjaan dilanjutkan oleh Teknisi.</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black uppercase bg-secondary/15 text-secondary border border-secondary/30 px-2.5 py-1 rounded-full">
                            Live Polling
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto rounded-xl border border-primary/20">
                        <table class="w-full text-xs text-left border-collapse min-w-[750px]">
                            <thead>
                                <tr class="bg-accent/40 text-primary border-b-2 border-primary/20 text-[11px] uppercase font-black tracking-wider">
                                    <th class="py-3 px-3">Kode Order</th>
                                    <th class="py-3 px-3">Nama Suku Cadang</th>
                                    <th class="py-3 px-3">Lokasi Pengiriman</th>
                                    <th class="py-3 px-3 text-center">Qty</th>
                                    <th class="py-3 px-3 text-right">Subtotal</th>
                                    <th class="py-3 px-3 text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody id="incomingOrdersList" class="divide-y divide-gray-100 font-semibold text-textDark bg-white">
                                <tr><td colspan="6" class="text-center text-textLight py-6">Memuat pesanan sparepart...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Catalog CRUD -->
                <div class="revaro-card p-6 md:p-7 space-y-6">
                    <div class="border-b-2 border-accent pb-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-10 h-10 rounded-xl bg-accent text-primary border-2 border-primary flex items-center justify-center text-lg font-black shadow-sm">
                                🛠️
                            </span>
                            <div>
                                <h3 class="text-base font-extrabold text-primary">Manajemen Katalog Sparepart</h3>
                                <p class="text-xs text-textLight">Kelola etalase suku cadang resmi, ketersediaan stok, harga jual, dan spesifikasi merek.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Add/Edit Catalog Item Form -->
                    <form id="sparepartForm" onsubmit="saveSparepartItem(event)" class="space-y-4 bg-accent/30 p-5 rounded-2xl border-2 border-primary/20">
                        <input type="hidden" id="editItemId">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-primary mb-1.5 uppercase">Nama Barang</label>
                                <input required type="text" id="partNama" class="revaro-input w-full text-xs bg-white rounded-xl p-2.5" placeholder="Kapasitor AC / Modul PCB">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-primary mb-1.5 uppercase">Harga Jual (Rp)</label>
                                <input required type="number" id="partHarga" class="revaro-input w-full text-xs bg-white rounded-xl p-2.5 font-bold" placeholder="75000" min="0">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-primary mb-1.5 uppercase">Stok Tersedia</label>
                                <input required type="number" id="partStok" class="revaro-input w-full text-xs bg-white rounded-xl p-2.5" placeholder="10" min="0">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-primary mb-1.5 uppercase">Merek</label>
                                <input required type="text" id="partMerk" class="revaro-input w-full text-xs bg-white rounded-xl p-2.5" placeholder="Panasonic / Sharp / Daikin">
                            </div>
                        </div>
                        <div class="flex justify-end items-center space-x-3 pt-2">
                            <button type="button" onclick="resetForm()" class="revaro-btn-outline text-xs px-4 py-2 rounded-xl font-bold">Reset</button>
                            <button type="submit" id="savePartBtn" class="revaro-btn text-xs px-6 py-2 rounded-xl font-bold">Tambah Item</button>
                        </div>
                    </form>

                    <!-- Catalog Items List -->
                    <div class="overflow-x-auto rounded-xl border border-primary/20">
                        <table class="w-full text-xs text-left border-collapse min-w-[620px]">
                            <thead>
                                <tr class="bg-accent/40 text-primary border-b-2 border-primary/20 text-[11px] uppercase font-black tracking-wider">
                                    <th class="py-3 px-3">Nama Barang</th>
                                    <th class="py-3 px-3">Merek</th>
                                    <th class="py-3 px-3 text-center">Harga</th>
                                    <th class="py-3 px-3 text-center">Stok</th>
                                    <th class="py-3 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="catalogItemsList" class="divide-y divide-gray-100 font-semibold text-textDark bg-white">
                                <tr><td colspan="5" class="text-center text-textLight py-6">Memuat katalog...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Wallet & Profile Info (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- User Profile info card -->
                <div class="revaro-card p-6 text-center space-y-4">
                    <div id="shopProfileCard" class="space-y-3">
                        <div class="w-14 h-14 bg-accent text-primary border-2 border-primary rounded-2xl flex items-center justify-center mx-auto text-2xl font-black shadow-sm">
                            🏪
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-primary">Toko Mitra REVARO</h4>
                            <p class="text-xs text-textLight mt-0.5">Memuat profil...</p>
                        </div>
                    </div>
                </div>

                <!-- Wallet Balance & Penarikan -->
                <div class="revaro-card p-6 space-y-4">
                    <h4 class="text-xs font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                        <span>💰 Saldo Penjualan Suku Cadang</span>
                    </h4>
                    <div id="shopSaldoBlock" class="bg-accent/40 p-4 rounded-xl border-2 border-primary/20 text-center">
                        <p class="text-[10px] uppercase font-bold text-textLight">Saldo Bersih Siap Cair</p>
                        <p id="shopSaldoValue" class="text-2xl font-black text-primary mt-0.5">Rp 0</p>
                    </div>
                    <button onclick="triggerWithdraw()" class="w-full revaro-btn-orange text-xs py-3 rounded-xl font-bold flex items-center justify-center gap-2">
                        <span>🏧</span> <span>Tarik Pendapatan ke Rekening Bank</span>
                    </button>
                </div>

                <!-- Wallet transactions list -->
                <div class="revaro-card p-6 space-y-4">
                    <h4 class="text-xs font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                        <span>📋 Mutasi Rekening Toko</span>
                    </h4>
                    <div id="shopLedgerHistory" class="space-y-2.5 max-h-64 overflow-y-auto pr-1 text-xs">
                        <p class="text-textLight text-center py-4">Belum ada mutasi masuk/keluar</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Client-Side State Controller Logic -->
    <script>
        const token = localStorage.getItem('token');
        let globalShopProfile = null;

        if (!token) {
            window.location.href = '/session-expired';
        }

        window.addEventListener('load', async () => {
            await fetchProfile();
            loadWalletLedger();
            loadCatalogItems();
            loadIncomingOrders();
            fetchNotifications();
            checkHashTab();
            setInterval(() => {
                loadIncomingOrders();
                fetchProfile();
                if (currentTokoTab === 'keuangan') {
                    loadTokoFinance();
                }
            }, 7000);
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

            // Saldo / Keuangan
            if (lowerTitle.includes('keuangan') || lowerTitle.includes('saldo') || lowerTitle.includes('penarikan') || lowerBody.includes('saldo')) {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: body,
                    showCancelButton: true,
                    confirmButtonText: '💳 Buka Keuangan Toko',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#0B4A8F',
                    cancelButtonColor: '#64748B'
                }).then((r) => {
                    if (r.isConfirmed) window.location.href = '/toko/keuangan';
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
                const res = await fetch('/api/auth/me', {
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
                const d = await res.json();
                if (res.status === 503 && d.is_maintenance) {
                    Swal.fire({
                        title: 'Mode Pemeliharaan',
                        text: d.message || 'Sistem sedang dalam pemeliharaan berkala oleh Super Admin.',
                        icon: 'info',
                        confirmButtonColor: '#0B4A8F',
                        allowOutsideClick: false
                    }).then(() => {
                        localStorage.clear();
                        window.location.href = '/';
                    });
                    return;
                }
                if (res.ok) {
                    const user = d.user;
                    const profile = d.toko || d.profile || d.user?.toko;
                    globalShopProfile = profile;

                    // Sync session details
                    localStorage.setItem('user', JSON.stringify(user));
                    localStorage.setItem('profile', JSON.stringify(profile));

                    // Check if user has shop partner access
                    const roleName = user.role ? user.role.nama : '';
                    const isToko = user.toko || roleName === 'Toko Sparepart';
                    if (!isToko) {
                        Swal.fire({
                            title: 'Belum Terdaftar',
                            text: 'Anda belum terdaftar sebagai Mitra Toko Sparepart. Silakan daftar terlebih dahulu.',
                            icon: 'info',
                            confirmButtonColor: '#0B4A8F'
                        }).then(() => { window.location.href = '/partner/register'; });
                        return;
                    }

                    // Update technician slot in multi-role widget if user is also a technician
                    if (user.teknisi) {
                        const techSlot = document.getElementById('shopTechRoleSlot');
                        if (techSlot) {
                            techSlot.innerHTML = `
                                <a href="/teknisi/dashboard" class="flex items-center justify-between bg-white border-2 border-primary/20 hover:border-primary rounded-xl px-4 py-2.5 transition">
                                    <span class="text-xs font-bold text-textDark">🔧 Dasbor Teknisi</span>
                                    <span class="text-[10px] text-primary font-black">Buka →</span>
                                </a>
                            `;
                        }
                    }

                    document.getElementById('navUserName').innerText = user.nama;
                    document.getElementById('shopProfileCard').innerHTML = `
                        <div class="w-14 h-14 bg-accent text-primary border-2 border-primary rounded-2xl flex items-center justify-center mx-auto text-2xl font-black shadow-sm">
                            🏪
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-primary">${user.nama}</h4>
                            <p class="text-xs text-secondary font-bold mt-0.5">NIB: ${profile ? profile.izin_usaha : '—'}</p>
                            <p class="text-[11px] text-textLight mt-0.5">${profile ? profile.alamat : '—'}</p>
                        </div>
                    `;

                    const unifiedSaldoNumber = d.unified_saldo !== undefined ? d.unified_saldo : (d.saldo !== undefined ? d.saldo : (profile ? profile.saldo : 0));
                    document.getElementById('shopSaldoValue').innerText = 'Rp ' + parseFloat(unifiedSaldoNumber || 0).toLocaleString('id-ID');

                    // Check status PENDING_REVIEW / NEEDS_REVISION
                    const banner = document.getElementById('verifStatusBanner');
                    const mainGrid = document.getElementById('dashboardMainGrid');

                    if (profile && profile.status === 'PENDING_REVIEW') {
                        document.body.innerHTML = `
                            <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
                                <div class="max-w-md w-full bg-white border-2 border-primary rounded-3xl p-8 shadow-[6px_6px_0px_0px_#0B4A8F] text-center space-y-4">
                                    <div class="w-16 h-16 bg-amber-100 text-amber-800 rounded-2xl flex items-center justify-center text-3xl mx-auto border-2 border-amber-500 shadow-sm">
                                        ⏳
                                    </div>
                                    <h3 class="text-lg font-black text-primary">Akses Dasbor Belum Disetujui</h3>
                                    <div class="p-3.5 bg-amber-50 border border-amber-300 rounded-2xl text-xs text-amber-900 leading-relaxed font-semibold text-left">
                                        Akun kemitraan Toko Sparepart Anda sedang dalam antrean pemeriksaan oleh <strong>Admin Verifikator REVARO</strong>. Sesuai ketentuan sistem, akun Anda <u>harus di-ACC terlebih dahulu</u> sebelum dapat mengakses fitur dan konsol dasbor toko.
                                    </div>
                                    <p class="text-xs text-textLight">Harap menunggu konfirmasi verifikasi dari tim admin.</p>
                                    <div class="pt-2">
                                        <a href="/?stay=1" class="w-full inline-block revaro-btn py-3 px-6 rounded-xl text-xs font-black shadow-btn">
                                            Kembali ke Beranda
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                        return;
                    } else if (profile && profile.status === 'NEEDS_REVISION') {
                        banner.classList.remove('hidden');
                        mainGrid.classList.add('hidden');
                        
                        banner.innerHTML = `
                            <div class="revaro-card p-6 md:p-8 space-y-4 border-2 border-red-500 bg-red-50/60">
                                <div class="flex items-center space-x-2 text-red-600 font-extrabold text-base">
                                    <span>📝</span> <span>Dokumen Toko Anda Perlu Direvisi</span>
                                </div>
                                <div class="p-3 bg-white border-2 border-red-300 text-xs text-red-700 rounded-xl leading-normal">
                                    <strong>Catatan Admin Verifikator:</strong> <span id="revisionNotes">Dokumen NIB tidak terdaftar, harap unggah ulang scan dokumen asli.</span>
                                </div>
                                
                                <form id="resubmitForm" onsubmit="handleResubmit(event)" class="space-y-4 text-xs max-w-xl">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-primary font-bold mb-1">Nomor Telepon Toko</label>
                                            <input required type="text" id="resubPhone" class="revaro-input w-full bg-white rounded-xl p-2.5" value="${profile.telepon}">
                                        </div>
                                        <div>
                                            <label class="block text-primary font-bold mb-1">Nomor SIUP / NIB</label>
                                            <input required type="text" id="resubNIB" class="revaro-input w-full bg-white rounded-xl p-2.5" value="${profile.izin_usaha}">
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-primary font-bold mb-1">Alamat Lengkap Toko</label>
                                            <input required type="text" id="resubAddress" class="revaro-input w-full bg-white rounded-xl p-2.5" value="${profile.alamat}">
                                        </div>
                                        <div>
                                            <label class="block text-primary font-bold mb-1">Unggah Berkas NIB Baru (.PDF/.JPG)</label>
                                            <input type="file" id="resubDoc" class="revaro-input w-full bg-white rounded-xl p-2.5 text-[11px]">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full revaro-btn-orange text-xs font-bold py-3 rounded-xl transition">Kirim Ulang Dokumen Toko</button>
                                </form>
                            </div>
                        `;
                        fetchRevisionNotes(profile.id);
                    } else {
                        banner.classList.add('hidden');
                        mainGrid.classList.remove('hidden');
                    }
                }
            } catch (err) { console.error(err); }
        }

        async function fetchRevisionNotes(shopId) {
            try {
                const res = await fetch('/api/admin/verifications', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const myVerif = data.find(v => v.toko_id === shopId && v.status === 'NEEDS_REVISION');
                    if (myVerif && myVerif.catatan) {
                        document.getElementById('revisionNotes').innerText = myVerif.catatan;
                    }
                }
            } catch (e) {}
        }

        async function handleResubmit(e) {
            e.preventDefault();
            const telepon = document.getElementById('resubPhone').value;
            const izin_usaha = document.getElementById('resubNIB').value;
            const alamat = document.getElementById('resubAddress').value;
            
            const formData = new FormData();
            formData.append('telepon', telepon);
            formData.append('izin_usaha', izin_usaha);
            formData.append('alamat', alamat);
            formData.append('latitude', -6.2100);
            formData.append('longitude', 106.8500);

            const doc = document.getElementById('resubDoc').files[0];
            if (doc) formData.append('dokumen_izin', doc);

            try {
                const res = await fetch('/api/profile/resubmit', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });
                const result = await res.json();
                if (res.ok) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Dokumen revisi Toko Anda berhasil diajukan ulang.',
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F'
                    });
                    fetchProfile();
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: result.message || JSON.stringify(result.errors),
                        icon: 'error',
                        confirmButtonColor: '#0B4A8F'
                    });
                }
            } catch (e) { console.error(e); }
        }

        async function loadWalletLedger() {
            try {
                const res = await fetch('/api/wallet/balance', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const list = document.getElementById('shopLedgerHistory');
                    if (!data.transactions || data.transactions.length === 0) {
                        list.innerHTML = '<p class="text-textLight text-center py-4">Belum ada mutasi rekening</p>';
                    } else {
                        list.innerHTML = data.transactions.map(t => {
                            const badge = t.tipe === 'IN' ? 'text-emerald-600 font-bold' : 'text-red-500 font-bold';
                            const prefix = t.tipe === 'IN' ? '+' : '-';
                            return `
                                <div class="p-3 bg-accent/20 border border-primary/20 rounded-xl space-y-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-textDark">${t.deskripsi}</span>
                                        <span class="${badge}">${prefix} Rp ${parseFloat(t.jumlah).toLocaleString('id-ID')}</span>
                                    </div>
                                    <p class="text-[10px] text-textLight font-mono">${new Date(t.created_at).toLocaleString('id-ID')}</p>
                                </div>
                            `;
                        }).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function triggerWithdraw() {
            const { value: formValues } = await Swal.fire({
                title: 'Tarik Saldo Toko ke Bank',
                html: `
                    <div class="space-y-3 text-left px-1">
                        <div>
                            <label class="block text-xs font-bold text-primary mb-1">Nama Bank</label>
                            <input type="text" id="wdBank" class="w-full text-xs border-2 border-primary rounded-xl p-2.5 outline-none font-semibold" placeholder="BCA / Mandiri / BNI / BRI">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-primary mb-1">Nama Pemilik Rekening</label>
                            <input type="text" id="wdName" class="w-full text-xs border-2 border-primary rounded-xl p-2.5 outline-none font-semibold" placeholder="Nama sesuai buku tabungan">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-primary mb-1">Nomor Rekening</label>
                            <input type="text" id="wdNumber" class="w-full text-xs font-mono border-2 border-primary rounded-xl p-2.5 outline-none" placeholder="1234567890">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-primary mb-1">Jumlah Penarikan (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-primary text-xs">Rp</span>
                                <input type="text" id="wdAmount" class="w-full text-sm font-black pl-9 pr-3 py-2.5 border-2 border-primary rounded-xl outline-none" placeholder="50.000">
                            </div>
                            <p class="text-[10px] text-textLight mt-1 font-semibold">Minimal penarikan Rp 50.000</p>
                        </div>
                    </div>
                `,
                background: '#FFFFFF',
                confirmButtonColor: '#FF7F00',
                confirmButtonText: 'Tarik Dana',
                cancelButtonText: 'Batal',
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
                        Swal.fire({
                            title: 'Berhasil!',
                            text: result.message,
                            icon: 'success',
                            confirmButtonColor: '#0B4A8F'
                        });
                        fetchProfile();
                        loadWalletLedger();
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: result.message || 'Saldo tidak mencukupi.',
                            icon: 'error',
                            confirmButtonColor: '#0B4A8F'
                        });
                    }
                } catch (e) { console.error(e); }
            }
        }

        async function loadCatalogItems() {
            try {
                const tokoId = globalShopProfile?.id || JSON.parse(localStorage.getItem('user') || '{}')?.toko?.id;
                const url = tokoId ? `/api/spareparts/search?toko_id=${tokoId}&all=1` : '/api/spareparts/search';
                const res = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                
                const list = document.getElementById('catalogItemsList');
                const myParts = Array.isArray(data) ? (tokoId ? data.filter(p => parseInt(p.toko_id) === parseInt(tokoId)) : data) : [];
                if (myParts.length === 0) {
                    list.innerHTML = '<tr><td colspan="5" class="text-center text-textLight py-6">Katalog masih kosong. Silakan tambahkan barang suku cadang.</td></tr>';
                } else {
                    list.innerHTML = myParts.map(p => `
                        <tr class="border-b border-gray-100 hover:bg-accent/20 transition">
                            <td class="py-3.5 px-3 font-bold text-primary">${p.nama}</td>
                            <td class="py-3.5 px-3 font-semibold text-textLight">${p.merk}</td>
                            <td class="py-3.5 px-3 text-center font-bold text-secondary whitespace-nowrap">Rp ${parseFloat(p.harga).toLocaleString('id-ID')}</td>
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-1 rounded-full bg-accent border border-primary text-primary font-bold text-[11px] whitespace-nowrap">${p.stok} pcs</span>
                            </td>
                            <td class="py-3.5 px-3 text-right whitespace-nowrap space-x-1.5">
                                <button onclick="editPartItem(${JSON.stringify(p).replace(/"/g, '&quot;')})" class="revaro-btn-outline text-[11px] px-2.5 py-1.5 rounded-lg font-bold">Edit</button>
                                <button onclick="deletePartItem(${p.id}, '${p.nama.replace(/'/g, "\\'")}')" class="revaro-btn-outline text-red-500 hover:text-red-700 text-[11px] px-2.5 py-1.5 rounded-lg font-bold">Hapus</button>
                            </td>
                        </tr>
                    `).join('');
                }
            } catch (e) { console.error(e); }
        }

        async function saveSparepartItem(e) {
            e.preventDefault();
            const id = document.getElementById('editItemId').value;
            const nama = document.getElementById('partNama').value.trim();
            const harga = document.getElementById('partHarga').value;
            const stok = document.getElementById('partStok').value;
            const merk = document.getElementById('partMerk').value.trim();

            const tokoId = globalShopProfile?.id || JSON.parse(localStorage.getItem('user') || '{}')?.toko?.id;
            if (!tokoId) {
                Swal.fire('Gagal', 'Profil toko belum terdeteksi. Silakan muat ulang halaman.', 'error');
                return;
            }

            const payload = {
                nama,
                harga: parseFloat(harga),
                stok: parseInt(stok),
                merk,
                toko_id: parseInt(tokoId)
            };
            if (id) {
                payload.id = parseInt(id);
            }

            try {
                const res = await fetch('/api/test/spareparts', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if (res.ok) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: result.message || 'Suku cadang berhasil disimpan ke etalase toko.',
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F',
                        timer: 1800
                    });
                    resetForm();
                    loadCatalogItems();
                } else {
                    let errMsg = result.message;
                    if (result.errors) {
                        errMsg = Object.values(result.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        title: 'Gagal',
                        html: errMsg || 'Terjadi kesalahan saat menyimpan data.',
                        icon: 'error',
                        confirmButtonColor: '#0B4A8F'
                    });
                }
            } catch (e) { console.error(e); }
        }

        async function deletePartItem(id, nama) {
            const conf = await Swal.fire({
                title: 'Hapus Suku Cadang?',
                text: `Apakah Anda yakin ingin menghapus "${nama}" dari etalase katalog toko?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus'
            });

            if (conf.isConfirmed) {
                try {
                    const res = await fetch(`/api/test/spareparts/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const d = await res.json();
                    if (res.ok) {
                        Swal.fire('Terhapus!', d.message, 'success');
                        loadCatalogItems();
                    } else {
                        Swal.fire('Gagal', d.message || 'Gagal menghapus produk.', 'error');
                    }
                } catch(e) { console.error(e); }
            }
        }

        function editPartItem(p) {
            document.getElementById('editItemId').value = p.id;
            document.getElementById('partNama').value = p.nama;
            document.getElementById('partHarga').value = parseInt(p.harga);
            document.getElementById('partStok').value = p.stok;
            document.getElementById('partMerk').value = p.merk;
            document.getElementById('savePartBtn').innerText = 'Simpan Perubahan';
        }

        function resetForm() {
            document.getElementById('editItemId').value = '';
            document.getElementById('partNama').value = '';
            document.getElementById('partHarga').value = '';
            document.getElementById('partStok').value = '';
            document.getElementById('partMerk').value = '';
            document.getElementById('savePartBtn').innerText = 'Tambah Item';
        }

        async function loadIncomingOrders() {
            try {
                const tokoId = globalShopProfile?.id || JSON.parse(localStorage.getItem('user') || '{}')?.toko?.id;
                const res = await fetch('/api/orders?role=toko', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                if (res.ok && d.orders) {
                    const list = document.getElementById('incomingOrdersList');
                    const incoming = [];
                    d.orders.forEach(order => {
                        const shopParts = order.spareparts ? order.spareparts.filter(p => parseInt(p.toko_id) === parseInt(tokoId) && p.pivot.status === 'PENDING_SHOP') : [];
                        shopParts.forEach(part => {
                            incoming.push({
                                order: order,
                                part: part
                            });
                        });
                    });

                    if (incoming.length === 0) {
                        list.innerHTML = '<tr><td colspan="6" class="text-center text-textLight py-6">Tidak ada pesanan suku cadang pending saat ini.</td></tr>';
                    } else {
                        list.innerHTML = incoming.map(item => `
                            <tr class="border-b border-gray-100 hover:bg-accent/20 transition">
                                <td class="py-3.5 px-3 font-mono text-primary font-bold whitespace-nowrap">${item.order.kode_order}</td>
                                <td class="py-3.5 px-3 font-semibold">
                                    <div class="text-xs font-bold text-textDark">${item.part.nama}</div>
                                    <div class="text-[10px] text-textLight font-medium">${item.part.merk}</div>
                                </td>
                                <td class="py-3.5 px-3 text-xs text-textLight max-w-xs leading-relaxed">${item.order.alamat_servis || '—'}</td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 rounded-full bg-accent border border-primary text-primary font-bold text-[11px] whitespace-nowrap">${item.part.pivot.qty} pcs</span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-black text-secondary whitespace-nowrap text-xs">Rp ${parseFloat(item.part.pivot.subtotal).toLocaleString('id-ID')}</td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick="confirmSparepartRequest(${item.order.id}, 'REJECTED')" class="revaro-btn-outline text-xs px-2.5 py-1 rounded-lg font-bold text-red-500 hover:text-red-700 transition">Tolak</button>
                                        <button onclick="confirmSparepartRequest(${item.order.id}, 'CONFIRMED')" class="revaro-btn-orange text-xs font-bold px-3 py-1 rounded-lg shadow-sm whitespace-nowrap">Konfirmasi Stok</button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function confirmSparepartRequest(orderId, status) {
            try {
                const res = await fetch(`/api/toko/orders/${orderId}/spareparts/confirm`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: d.message,
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F'
                    });
                    loadIncomingOrders();
                    loadCatalogItems();
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: d.message,
                        icon: 'error',
                        confirmButtonColor: '#0B4A8F'
                    });
                }
            } catch (e) { console.error(e); }
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
                    const ping = await fetch('/api/auth/me?role=toko', {
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
