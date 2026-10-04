<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Keuangan &amp; Penjualan — Mitra Toko REVARO</title>
    
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
            <a href="/toko/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-secondary text-white font-extrabold shadow-sm hidden sm:inline-block">Konsol Toko Sparepart</span>
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

                <span id="navUserName" class="text-xs font-bold bg-accent text-primary px-3 py-1.5 rounded-xl border-2 border-primary hidden sm:inline-block">Toko Mitra</span>
                <button onclick="handleLogout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3.5 py-1.5 rounded-xl font-bold border-2 border-red-700 shadow-[2px_2px_0px_0px_#991B1B] transition">Keluar</button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 md:pl-20 pb-28">
        <div class="space-y-8">
            <!-- Header Page Banner with Export Action -->
            <div class="revaro-card p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">💰</span>
                        <h2 class="text-xl font-extrabold text-primary tracking-tight">Pencatatan Keuangan &amp; Penjualan Toko</h2>
                        <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">Terhitung Otomatis</span>
                    </div>
                    <p class="text-xs text-textLight">
                        Rekapitulasi otomatis transaksi suku cadang, potongan komisi sistem, dan pembukuan hasil penjualan saldo dompet.
                    </p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <button onclick="loadTokoFinance()" type="button" class="revaro-btn-outline text-xs font-bold py-2.5 px-4 rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-arrows-rotate"></i> <span>Refresh Data</span>
                    </button>
                    <button onclick="exportFinanceExcel()" type="button" class="revaro-btn-orange text-xs font-black py-2.5 px-5 rounded-xl flex items-center gap-2 shadow-btn hover:translate-x-0.5 hover:translate-y-0.5 transition cursor-pointer">
                        <i class="fa-solid fa-file-excel text-base text-white"></i> <span>Export Excel (.xlsx)</span>
                    </button>
                </div>
            </div>

            <!-- Financial Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="revaro-card p-5 space-y-2 relative overflow-hidden">
                    <div class="flex justify-between items-center text-xs font-bold text-textLight uppercase tracking-wider">
                        <span>Total Omset Kotor</span>
                        <span class="text-lg">📈</span>
                    </div>
                    <h3 id="finKpiOmset" class="text-2xl font-black text-primary">Rp 0</h3>
                    <p class="text-[10px] text-textLight">Total nilai penjualan sparepart sebelum potongan</p>
                </div>
                <div class="revaro-card p-5 space-y-2 relative overflow-hidden">
                    <div class="flex justify-between items-center text-xs font-bold text-textLight uppercase tracking-wider">
                        <span>Biaya Platform (<span id="finKpiKomisiPersen">5</span>%)</span>
                        <span class="text-lg">🏷️</span>
                    </div>
                    <h3 id="finKpiFee" class="text-2xl font-black text-amber-600">Rp 0</h3>
                    <p class="text-[10px] text-textLight">Biaya layanan sistem pemrosesan transaksi</p>
                </div>
                <div class="revaro-card p-5 space-y-2 relative overflow-hidden border-2 border-emerald-600">
                    <div class="flex justify-between items-center text-xs font-bold text-emerald-800 uppercase tracking-wider">
                        <span>Pendapatan Bersih</span>
                        <span class="text-lg">💵</span>
                    </div>
                    <h3 id="finKpiBersih" class="text-2xl font-black text-emerald-600">Rp 0</h3>
                    <p class="text-[10px] text-emerald-700 font-medium">Omset kotor setelah potongan komisi platform</p>
                </div>
                <div class="revaro-card p-5 space-y-2 relative overflow-hidden border-2 border-secondary shadow-[4px_4px_0px_0px_#FF7F00]">
                    <div class="flex justify-between items-center text-xs font-bold text-secondary uppercase tracking-wider">
                        <span>Saldo Toko Tersedia</span>
                        <span class="text-lg">🏦</span>
                    </div>
                    <h3 id="finKpiSaldo" class="text-2xl font-black text-secondary">Rp 0</h3>
                    <p class="text-[10px] text-textLight">Saldo aktif saat ini yang dapat ditarik (withdraw)</p>
                </div>
            </div>

            <!-- Detailed Ledger Table -->
            <div class="revaro-card p-6 md:p-8 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-accent pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-primary flex items-center gap-2">
                            <span>📜</span> <span>Buku Besar Transaksi Keuangan Suku Cadang</span>
                        </h3>
                        <p class="text-xs text-textLight mt-0.5">Rincian seluruh pembelian suku cadang oleh teknisi/pelanggan yang terverifikasi</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="finRecordCount" class="text-xs font-bold px-3 py-1 rounded-full bg-accent text-primary border border-primary">
                            0 Transaksi
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-primary text-[11px] font-black text-primary uppercase tracking-wider bg-accent/40">
                                <th class="p-3 text-center">No</th>
                                <th class="p-3">Waktu Transaksi</th>
                                <th class="p-3">Kode Order</th>
                                <th class="p-3">Suku Cadang &amp; Merk</th>
                                <th class="p-3 text-center">Qty</th>
                                <th class="p-3 text-right">Harga Satuan</th>
                                <th class="p-3 text-right">Total Omset</th>
                                <th class="p-3 text-right">Fee Sistem</th>
                                <th class="p-3 text-right font-black text-emerald-700">Bersih Masuk</th>
                                <th class="p-3">Pemesan</th>
                                <th class="p-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="finLedgerTableBody" class="divide-y divide-gray-100 text-xs">
                            <tr>
                                <td colspan="11" class="py-8 text-center text-textLight">
                                    <i class="fa-solid fa-spinner fa-spin text-xl text-primary mb-2"></i>
                                    <p>Memuat catatan keuangan toko...</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot id="finLedgerTableFoot" class="hidden border-t-2 border-primary bg-amber-50 font-black text-xs text-textDark">
                            <tr>
                                <td colspan="6" class="p-3 text-right uppercase tracking-wider text-primary">TOTAL KESELURUHAN (IDR):</td>
                                <td id="finFootOmset" class="p-3 text-right text-primary">Rp 0</td>
                                <td id="finFootFee" class="p-3 text-right text-amber-700">Rp 0</td>
                                <td id="finFootBersih" class="p-3 text-right text-emerald-700">Rp 0</td>
                                <td colspan="2" class="p-3 text-center text-[10px] text-textLight font-semibold">Tercatat Otomatis di Sistem</td>
                            </tr>
                        </tfoot>
                    </table>
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
            loadTokoFinance();
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

        async function loadTokoFinance() {
            try {
                const res = await fetch('/api/toko/finance', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!res.ok) return;

                const summary = data.summary || {};
                const ledger = data.ledger || [];

                document.getElementById('finKpiOmset').innerText = 'Rp ' + Number(summary.total_omset || 0).toLocaleString('id-ID');
                document.getElementById('finKpiFee').innerText = 'Rp ' + Number(summary.total_fee_platform || 0).toLocaleString('id-ID');
                document.getElementById('finKpiBersih').innerText = 'Rp ' + Number(summary.pendapatan_bersih || 0).toLocaleString('id-ID');
                document.getElementById('finKpiSaldo').innerText = 'Rp ' + Number(summary.saldo_saat_ini || 0).toLocaleString('id-ID');
                if (document.getElementById('finKpiKomisiPersen')) {
                    document.getElementById('finKpiKomisiPersen').innerText = summary.persen_komisi || '5';
                }
                document.getElementById('finRecordCount').innerText = `${ledger.length} Transaksi`;

                const tbody = document.getElementById('finLedgerTableBody');
                const tfoot = document.getElementById('finLedgerTableFoot');

                if (!ledger.length) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="11" class="py-12 text-center text-textLight">
                                <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2 block"></i>
                                <p class="font-bold text-sm">Belum Ada Transaksi Suku Cadang</p>
                                <p class="text-xs text-slate-400 mt-1">Pembelian suku cadang dari teknisi akan terhitung otomatis di pembukuan ini.</p>
                            </td>
                        </tr>
                    `;
                    tfoot.classList.add('hidden');
                    return;
                }

                tbody.innerHTML = ledger.map((item, idx) => {
                    const statusColors = {
                        'CONFIRMED': 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'DELIVERED': 'bg-blue-100 text-blue-800 border-blue-300',
                        'RECEIVED': 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'PENDING_SHOP': 'bg-amber-100 text-amber-800 border-amber-300',
                        'REJECTED': 'bg-red-100 text-red-800 border-red-300'
                    };
                    const colorClass = statusColors[item.status_part] || 'bg-slate-100 text-slate-700 border-slate-300';

                    return `
                        <tr class="hover:bg-accent/20 transition">
                            <td class="p-3 text-center text-textLight font-bold">${idx + 1}</td>
                            <td class="p-3 text-textLight whitespace-nowrap text-[11px]">${item.tanggal}</td>
                            <td class="p-3 font-mono font-bold text-primary">${item.kode_order || '-'}</td>
                            <td class="p-3">
                                <span class="font-bold text-textDark block">${item.nama_sparepart}</span>
                                <span class="text-[10px] text-textLight">Merk: ${item.merk || '-'}</span>
                            </td>
                            <td class="p-3 text-center font-bold">${item.qty}</td>
                            <td class="p-3 text-right">Rp ${Number(item.harga).toLocaleString('id-ID')}</td>
                            <td class="p-3 text-right font-bold text-primary">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
                            <td class="p-3 text-right text-amber-700">Rp ${Number(item.fee_komisi).toLocaleString('id-ID')}</td>
                            <td class="p-3 text-right font-black text-emerald-700">Rp ${Number(item.pendapatan_bersih).toLocaleString('id-ID')}</td>
                            <td class="p-3">
                                <span class="font-semibold text-textDark block">${item.teknisi_nama}</span>
                                <span class="text-[10px] text-textLight">Plg: ${item.pelanggan_nama}</span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border ${colorClass}">
                                    ${item.status_part}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');

                tfoot.classList.remove('hidden');
                document.getElementById('finFootOmset').innerText = 'Rp ' + Number(summary.total_omset || 0).toLocaleString('id-ID');
                document.getElementById('finFootFee').innerText = 'Rp ' + Number(summary.total_fee_platform || 0).toLocaleString('id-ID');
                document.getElementById('finFootBersih').innerText = 'Rp ' + Number(summary.pendapatan_bersih || 0).toLocaleString('id-ID');

            } catch (e) {
                console.error('Error loading finance ledger:', e);
            }
        }

        async function exportFinanceExcel() {
            Swal.fire({
                title: 'Menyiapkan File Excel...',
                text: 'Sistem sedang menyusun pembukuan keuangan dan formula spreadsheet (.xlsx).',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('/api/toko/finance/export-excel', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengunduh berkas laporan.');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                const disposition = response.headers.get('content-disposition');
                let filename = 'Laporan_Keuangan_Toko_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Swal.fire({
                    title: 'Berhasil Diexport!',
                    text: 'Laporan keuangan format Excel asli (.xlsx) telah berhasil diunduh.',
                    icon: 'success',
                    confirmButtonColor: '#0B4A8F'
                });
            } catch (err) {
                console.error(err);
                Swal.fire({
                    title: 'Gagal Export',
                    text: err.message || 'Terjadi kesalahan saat memproses laporan Excel.',
                    icon: 'error',
                    confirmButtonColor: '#0B4A8F'
                });
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
