<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dompet Saldo &amp; Top Up — Pelanggan REVARO</title>
    
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
            <a href="/pelanggan/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
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
                        <span class="text-2xl">💰</span>
                        <h2 class="text-xl font-extrabold text-primary tracking-tight">Dompet Saldo Pelanggan &amp; Escrow</h2>
                    </div>
                    <p class="text-xs text-textLight">
                        Isi ulang saldo untuk pembayaran servis aman melalui rekening bersama (escrow) terproteksi REVARO.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="loadWalletData()" type="button" class="revaro-btn text-xs font-bold py-2.5 px-4 rounded-xl flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> <span>Refresh Saldo</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Kolom Kiri: Saldo & Action Top-Up / Tarik (6 Cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="revaro-card p-6 md:p-8 space-y-5">
                        <div class="flex justify-between items-center border-b-2 border-accent pb-3">
                            <h4 class="text-sm font-black text-primary uppercase tracking-wider flex items-center gap-2">
                                <span>💵</span> <span>Total Saldo Aktif</span>
                            </h4>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">Escrow Ready</span>
                        </div>
                        <div class="bg-accent/40 p-6 rounded-2xl border-2 border-primary/20 text-center">
                            <p class="text-xs uppercase font-bold text-textLight">Saldo Dompet Anda</p>
                            <p id="pelangganSaldoFull" class="text-3xl font-black text-primary mt-1.5">Rp 0</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <button onclick="triggerTopup()" class="revaro-btn-orange text-xs py-3.5 rounded-xl font-black transition shadow-[3px_3px_0px_0px_#0B4A8F] flex items-center justify-center gap-2">
                                <span>💵</span> <span>Isi Saldo (Top Up)</span>
                            </button>
                            <button onclick="triggerWithdraw()" class="revaro-btn-outline text-xs py-3.5 rounded-xl font-bold transition flex items-center justify-center gap-2">
                                <span>🏧</span> <span>Tarik Dana</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-textLight text-center">
                            Saldo disimpan aman di Rekening Escrow Terproteksi REVARO dan hanya dilepaskan jika perbaikan selesai memuaskan.
                        </p>
                    </div>

                    <!-- Keunggulan Escrow Rekening Bersama -->
                    <div class="revaro-card p-6 md:p-8 space-y-4">
                        <h4 class="text-sm font-black text-primary uppercase tracking-wider flex items-center gap-2">
                            <span>🛡️</span> <span>Jaminan Keamanan Rekber (Escrow)</span>
                        </h4>
                        <div class="space-y-3 text-xs text-textDark">
                            <div class="p-3 bg-accent/40 border border-primary/20 rounded-xl flex items-start gap-2.5">
                                <span class="text-lg">🔒</span>
                                <div>
                                    <p class="font-bold text-primary">Dana Ditahan Aman</p>
                                    <p class="text-[11px] text-textLight">Uang pembayaran Anda tidak langsung dikirim ke teknisi sebelum Anda menyatakan puas dan melepas escrow.</p>
                                </div>
                            </div>
                            <div class="p-3 bg-orange-50 border border-secondary/20 rounded-xl flex items-start gap-2.5">
                                <span class="text-lg">⚖️</span>
                                <div>
                                    <p class="font-bold text-secondary">Mediasi Admin Resolusi</p>
                                    <p class="text-[11px] text-textLight">Jika terjadi komplain pengerjaan, tim resolusi REVARO siap melakukan mediasi atau refund 100%.</p>
                                </div>
                            </div>
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-2.5">
                                <span class="text-lg">🏷️</span>
                                <div>
                                    <p class="font-bold text-emerald-800">Garansi Layanan Servis</p>
                                    <p class="text-[11px] text-textLight">Perlindungan garansi resmi perbaikan hingga 30–45 hari.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Mutasi Transaksi (6 Cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="revaro-card p-6 md:p-8 space-y-4">
                        <div class="flex justify-between items-center border-b-2 border-accent pb-3">
                            <h3 class="text-sm font-black text-primary uppercase tracking-wider flex items-center gap-2">
                                <span>📜</span> <span>Riwayat Mutasi Saldo</span>
                            </h3>
                            <a href="/pelanggan/riwayat" class="text-xs font-bold text-secondary hover:underline">Riwayat Servis →</a>
                        </div>
                        <div id="ledgerHistoryList" class="space-y-3 max-h-[550px] overflow-y-auto pr-1 text-xs">
                            <p class="text-textLight text-center py-10">Memuat mutasi...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('components.dock')

    <script>
        const token = localStorage.getItem('token');
        if (!token) {
            Swal.fire('Akses Ditolak', 'Silakan login terlebih dahulu.', 'warning').then(() => { window.location.href = '/'; });
            window.location.href = '/';
        }

        window.addEventListener('load', () => {
            fetchProfile();
            loadWalletData();
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
                const res = await fetch('/api/auth/me?role=pelanggan', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                if (res.ok && d.user) {
                    document.getElementById('navUserName').innerText = d.user.nama;
                }
            } catch (e) {}
        }

        async function loadWalletData() {
            try {
                const res = await fetch('/api/wallet/balance?role=pelanggan', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const saldoVal = data.saldo ?? data.balance ?? 0;
                    document.getElementById('pelangganSaldoFull').innerText = 'Rp ' + parseFloat(saldoVal).toLocaleString('id-ID');

                    const list = document.getElementById('ledgerHistoryList');
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
                console.error(e);
            }
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
                                <input type="text" id="topupAmountInput" class="w-full text-base font-black pl-10 pr-3 py-3 border border-slate-300 rounded-xl outline-none focus:border-primary transition" placeholder="50.000">
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
                confirmButtonColor: '#FF7F00',
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
                    if (res.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Top-Up Berhasil!',
                            text: result.message || `Saldo Anda bertambah Rp ${amount.toLocaleString('id-ID')}`,
                            confirmButtonColor: '#0B4A8F'
                        });
                        loadWalletData();
                    } else {
                        Swal.fire('Gagal', result.message || 'Periksa input Anda.', 'error');
                    }
                } catch (e) {}
            }
        }

        function setTopupPreset(val) {
            const input = document.getElementById('topupAmountInput');
            if (input) input.value = val.toLocaleString('id-ID');
        }

        async function triggerWithdraw() {
            const { value: formValues } = await Swal.fire({
                title: 'Tarik Saldo ke Rekening Bank',
                html: `
                    <div class="space-y-3 text-left px-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Bank Tujuan</label>
                            <select id="wdBank" class="w-full text-sm border border-slate-300 rounded-xl p-3 outline-none focus:border-primary transition">
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
                            <input type="text" id="wdAccountHolder" class="w-full text-sm border border-slate-300 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="Nama sesuai buku tabungan">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                            <input type="text" id="wdAccountNumber" class="w-full text-sm font-mono border border-slate-300 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="1234567890">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Penarikan (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-500 text-sm">Rp</span>
                                <input type="text" id="wdAmount" class="w-full text-base font-black pl-10 pr-3 py-3 border border-slate-300 rounded-xl outline-none focus:border-primary transition" placeholder="50.000">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Minimal penarikan Rp 10.000</p>
                        </div>
                    </div>
                `,
                background: '#FFFFFF',
                confirmButtonColor: '#FF7F00',
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

                    return { bank_name, account_holder_name, account_number, amount };
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
                        loadWalletData();
                    } else {
                        Swal.fire('Gagal Penarikan', result.message || 'Saldo tidak mencukupi atau data salah.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                }
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
                        return `
                            <div onclick="markNotificationRead(${n.id})" class="p-3.5 transition cursor-pointer hover:bg-accent/30 ${isUnread ? 'bg-orange-50/70 border-l-4 border-secondary' : 'bg-white'}">
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
