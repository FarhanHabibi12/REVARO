<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi &amp; Sengketa — Pelanggan REVARO</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/images/revaro-icon-transparent.png">
    
    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0B4A8F',
                        secondary: '#FF7F00',
                        accent: '#E6EFFF',
                        textDark: '#1E293B',
                        textLight: '#64748B'
                    },
                    boxShadow: {
                        'btn': '3px 3px 0px 0px #0B4A8F',
                        'card': '4px 4px 0px 0px #0B4A8F'
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome & SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .revaro-card {
            background-color: white;
            border: 2px solid #0B4A8F;
            border-radius: 1rem;
            box-shadow: 4px 4px 0px 0px #0B4A8F;
        }
        .revaro-btn {
            background-color: #0B4A8F;
            color: white;
            border: 2px solid #0B4A8F;
            box-shadow: 3px 3px 0px 0px #FF7F00;
            transition: all 0.15s ease;
        }
        .revaro-btn:hover {
            box-shadow: 1px 1px 0px 0px #FF7F00;
            transform: translate(2px, 2px);
        }
        .revaro-btn-orange {
            background-color: #FF7F00;
            color: white;
            border: 2px solid #FF7F00;
            box-shadow: 3px 3px 0px 0px #0B4A8F;
            transition: all 0.15s ease;
        }
        .revaro-btn-orange:hover {
            box-shadow: 1px 1px 0px 0px #0B4A8F;
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
    </style>
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
                <a href="/pelanggan/dashboard?action=pesan" class="revaro-btn-orange text-xs px-3 py-1.5 rounded-xl font-black flex items-center gap-1.5 shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition" title="Pesan Layanan Servis Cepat">
                    <i class="fa-solid fa-wrench"></i>
                    <span class="hidden md:inline">Pesan Servis</span>
                </a>

                <!-- Dropdown Switcher Dasbor -->
                <div class="relative inline-block text-left" id="globalRoleSwitcherWrapper">
                    <button onclick="toggleGlobalRoleDropdown()" type="button" class="relative text-xs font-extrabold text-primary bg-white hover:bg-accent/40 px-3 py-1.5 rounded-xl border-2 border-primary shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition flex items-center gap-1.5 focus:outline-none" title="Pindah Dasbor Peran">
                        <span>🔄</span>
                        <span class="hidden sm:inline">Pindah Dasbor</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-textLight"></i>
                    </button>

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

                <!-- Notifikasi Bell -->
                <div class="relative inline-block text-left" id="notifBellWrapper">
                    <button onclick="toggleNotificationDropdown()" type="button" class="relative p-2 text-primary hover:text-secondary rounded-xl border-2 border-primary bg-white shadow-[2px_2px_0px_0px_#0B4A8F] hover:shadow-none hover:translate-x-0.5 hover:translate-y-0.5 transition flex items-center justify-center focus:outline-none" title="Notifikasi">
                        <i class="fa-solid fa-bell text-base"></i>
                        <span id="notifBadge" class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-black px-1.5 py-0.2 rounded-full min-w-[18px] text-center border-2 border-white hidden animate-pulse">0</span>
                    </button>
                    
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

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 md:pl-20 pb-28">
        <div class="space-y-6">
            <!-- Header Page Banner -->
            <div class="revaro-card p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">📋</span>
                        <h2 class="text-xl font-extrabold text-primary tracking-tight">Riwayat Servis &amp; Status Kasus Sengketa</h2>
                    </div>
                    <p class="text-xs text-textLight">
                        Pantau status pengerjaan servis masa lalu, bukti pembayaran rekening bersama (escrow), serta investigasi perlindungan garansi.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="loadHistoryData()" type="button" class="revaro-btn text-xs font-bold py-2.5 px-4 rounded-xl flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> <span>Refresh Data</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Kolom Kiri: Riwayat Transaksi & Servis (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="revaro-card p-6 md:p-8 space-y-5">
                        <div class="flex justify-between items-center border-b-2 border-accent pb-3">
                            <h3 class="text-sm font-black text-primary uppercase tracking-wider flex items-center gap-2">
                                <span>📜</span> <span>Riwayat Mutasi &amp; Pembayaran</span>
                            </h3>
                            <a href="/pelanggan/dompet" class="text-xs font-bold text-secondary hover:underline">Buka Dompet →</a>
                        </div>
                        <div id="ledgerHistoryList" class="space-y-3 max-h-[550px] overflow-y-auto pr-1 text-xs">
                            <p class="text-textLight text-center py-10">Memuat riwayat mutasi...</p>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Status Garansi & Sengketa (5 Cols) -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="revaro-card p-6 md:p-8 space-y-5">
                        <div class="flex justify-between items-center border-b-2 border-accent pb-3">
                            <h3 class="text-sm font-black text-secondary uppercase tracking-wider flex items-center gap-2">
                                <span>🛡️</span> <span>Status Garansi &amp; Resolusi</span>
                            </h3>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-secondary/10 text-secondary border border-secondary/30">Investigasi Admin</span>
                        </div>
                        <div id="disputesStatusList" class="space-y-3 max-h-[550px] overflow-y-auto pr-1 text-xs">
                            <p class="text-textLight text-center py-10">Memuat status sengketa...</p>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <!-- Footer Pelanggan dengan Logo REVARO -->
    <footer class="bg-white border-t-2 border-primary py-6 text-center text-xs text-textLight">
        <div class="max-w-6xl mx-auto px-4 space-y-2">
            <div class="flex justify-center items-center">
                <img src="/images/revaro-logo-horizontal.png" alt="REVARO Logo" class="h-8 w-auto object-contain">
            </div>
            <p class="font-bold text-textDark">&copy; 2026 REVARO &mdash; Ekosistem Reparasi &amp; Restorasi Terpercaya Indonesia.</p>
        </div>
    </footer>

    @include('components.dock')

    <script>
        const token = localStorage.getItem('token');
        if (!token) {
            Swal.fire('Akses Ditolak', 'Silakan login terlebih dahulu.', 'warning').then(() => { window.location.href = '/'; });
            window.location.href = '/';
        }

        window.addEventListener('load', () => {
            fetchProfile();
            loadHistoryData();
            fetchNotifications();
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

        async function fetchProfile() {
            try {
                const res = await fetch('/api/auth/me', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                if (res.ok && d.user) {
                    document.getElementById('navUserName').innerText = d.user.nama;
                }
            } catch (e) {}
        }

        function loadHistoryData() {
            loadWalletLedger();
            loadDisputesList();
        }

        async function loadWalletLedger() {
            const list = document.getElementById('ledgerHistoryList');
            try {
                const res = await fetch('/api/wallet/balance', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    if (!data.transactions || data.transactions.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-10">Belum ada transaksi</p>';
                    } else {
                        list.innerHTML = data.transactions.map(t => {
                            const badge = t.tipe === 'IN' ? 'text-emerald-600 font-black' : 'text-red-500 font-black';
                            const prefix = t.tipe === 'IN' ? '+' : '-';
                            return `
                                <div class="p-3 bg-white border-2 border-primary/20 rounded-xl space-y-1 hover:border-primary transition">
                                    <div class="flex justify-between items-start">
                                        <span class="font-bold text-textDark text-xs leading-snug">${t.deskripsi}</span>
                                        <span class="${badge} text-xs shrink-0 ml-2">${prefix} Rp ${parseFloat(t.jumlah).toLocaleString('id-ID')}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-mono">${new Date(t.created_at).toLocaleString('id-ID')}</p>
                                </div>
                            `;
                        }).join('');
                    }
                }
            } catch (e) {
                list.innerHTML = `<p class="text-xs text-red-500 text-center py-6">Gagal memuat mutasi.</p>`;
            }
        }

        async function loadDisputesList() {
            const list = document.getElementById('disputesStatusList');
            try {
                const res = await fetch('/api/disputes', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const disputes = Array.isArray(data) ? data : (data.disputes || []);
                    if (disputes.length === 0) {
                        list.innerHTML = `
                            <div class="text-center py-10 text-textLight space-y-2">
                                <i class="fa-solid fa-shield-halved text-3xl text-emerald-300"></i>
                                <p class="text-xs font-semibold">Tidak ada sengketa atau klaim garansi yang sedang berjalan.</p>
                                <p class="text-[11px] text-slate-400">Seluruh pesanan Anda terlindungi sistem Rekber Escrow REVARO.</p>
                            </div>
                        `;
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
                                OPEN: 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                INVESTIGATING: 'bg-blue-100 text-blue-800 border-blue-300',
                                RESOLVED: 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            }[d.status] || 'bg-slate-100 text-slate-800';

                            const stText = statusLabels[d.status] || d.status;
                            const decisionText = d.keputusan ? `<p class="text-[10px] text-emerald-700 font-bold mt-0.5">Keputusan: ${decisionLabels[d.keputusan] || d.keputusan}</p>` : '';

                            return `
                                <div class="p-3 bg-white border-2 border-primary/20 rounded-xl space-y-1.5 hover:border-primary transition">
                                    <div class="flex justify-between items-center">
                                        <span class="font-black text-primary text-xs">Order #${d.order?.kode_order || '—'}</span>
                                        <span class="text-[9px] px-2 py-0.5 rounded-full border font-bold ${badgeColor}">${stText}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-600"><strong>Alasan:</strong> ${d.alasan}</p>
                                    ${decisionText}
                                    ${d.resolusi ? `<p class="text-[10px] text-slate-600 bg-accent/40 p-2 rounded-lg border border-primary/20 italic">"${d.resolusi}"</p>` : ''}
                                    <p class="text-[9px] text-slate-400 font-mono">${new Date(d.created_at).toLocaleString('id-ID')}</p>
                                </div>
                            `;
                        }).join('');
                    }
                }
            } catch (e) {
                list.innerHTML = `<p class="text-xs text-red-500 text-center py-6">Gagal memuat status sengketa.</p>`;
            }
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
                                    <h5 class="text-xs font-bold ${isUnread ? 'text-primary' : 'text-slate-700'}">${n.judul}</h5>
                                    <span class="text-[10px] text-textLight whitespace-nowrap shrink-0">${timeStr}</span>
                                </div>
                                <p class="text-xs text-textDark mt-1 leading-snug">${n.pesan}</p>
                            </div>
                        `;
                    }).join('');
                }
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

            if (lowerTitle.includes('verifikasi') || lowerTitle.includes('mitra') || lowerBody.includes('verifikasi')) {
                const isApproved = lowerBody.includes('disetujui') || lowerBody.includes('acc') || lowerBody.includes('aktif');
                Swal.fire({
                    icon: isApproved ? 'success' : 'info',
                    title: title,
                    text: body,
                    confirmButtonText: isApproved ? '🚀 Buka Dasbor Mitra' : 'Tutup',
                    confirmButtonColor: '#0B4A8F',
                    showCancelButton: isApproved
                }).then((r) => {
                    if (isApproved && r.isConfirmed) {
                        window.location.href = lowerBody.includes('toko') ? '/toko/dashboard' : '/teknisi/dashboard';
                    }
                });
                return;
            }

            if (lowerTitle.includes('dompet') || lowerTitle.includes('saldo') || lowerBody.includes('saldo')) {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: body,
                    confirmButtonText: '💳 Buka Dompet',
                    showCancelButton: true,
                    confirmButtonColor: '#0B4A8F'
                }).then((r) => {
                    if (r.isConfirmed) window.location.href = '/pelanggan/dompet';
                });
                return;
            }

            Swal.fire({ icon: 'info', title: title, text: body, confirmButtonColor: '#0B4A8F' });
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

        async function markAllNotificationsAsRead() {
            try {
                await fetch('/api/notifications/read-all', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                fetchNotifications();
            } catch (e) {}
        }

        async function handleLogout() {
            try {
                if (token) {
                    await fetch('/api/auth/logout', {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                    });
                }
            } catch(e) {}
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = '/';
        }
    </script>
</body>
</html>
