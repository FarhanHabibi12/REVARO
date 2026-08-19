<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun Pelanggan — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#0A4C8A',
                        brandOrange: '#FF6A00',
                        darkNavy: '#0F172A',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('js/app-i18n.js') }}"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0B132B; color: #E2E8F0; }
        .card { background-color: #1E293B; border: 1px solid #334155; border-radius: 1rem; }
        .input-field { width: 100%; padding: 0.65rem 0.85rem; background-color: #0F172A; border: 1px solid #334155; border-radius: 0.65rem; color: #F8FAFC; font-size: 0.825rem; }
        .input-field:focus { outline: none; border-color: #FF6A00; }
    </style>
</head>
<body class="min-h-screen p-6 md:p-10 space-y-8 max-w-7xl mx-auto">

    <!-- Navbar Header -->
    <header class="flex items-center justify-between p-4 card bg-slate-900/90">
        <div class="flex items-center gap-3">
            <div class="bg-brandOrange p-2 rounded-xl font-black text-xs text-white">REV</div>
            <div>
                <h1 class="text-sm font-black text-white">PORTAL PELANGGAN REVARO</h1>
                <p class="text-[9px] text-brandOrange font-bold uppercase tracking-widest">AKUN PENGGUNA TERVERIFIKASI</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="/pelanggan/dashboard" class="text-xs font-bold text-slate-300 hover:text-white transition">🚗 Dasbor Pelanggan</a>
            <button onclick="handleLogout()" class="text-xs font-bold text-red-400 hover:text-red-300 transition">🚪 Keluar</button>
        </div>
    </header>

    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>REV</span> / <span>PELANGGAN</span> / <span class="text-brandOrange">PENGATURAN</span>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                <span>USER ACCOUNT ACTIVE</span>
            </span>
        </div>
    </div>

    <!-- 2 Column Section: Profil & Sandi Pelanggan + Spesifikasi Lingkungan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Box (7 cols): Profil & Sandi Pelanggan -->
        <div class="lg:col-span-7 card p-6 md:p-8 space-y-6 border-l-4 border-l-blue-500">
            <div>
                <h3 class="text-lg font-black text-white">Profil & Sandi Akun Pelanggan</h3>
                <p class="text-xs text-slate-400 mt-1">Perbarui informasi identitas, kontak, dan kata sandi akun pelanggan Anda.</p>
            </div>

            <form id="pelangganProfileForm" onsubmit="event.preventDefault(); savePelangganProfile();" class="space-y-5">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NAMA LENGKAP PELANGGAN *</label>
                    <input type="text" id="pelangganNama" required class="input-field font-bold" placeholder="Pelanggan Revaro">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ALAMAT SUREL LOGIN *</label>
                    <input type="email" id="pelangganEmail" required class="input-field font-mono" placeholder="pelanggan@revaro.com">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NOMOR TELEPON / WHATSAPP</label>
                        <input type="text" id="pelangganPhone" class="input-field font-mono" placeholder="081234567894">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ALAMAT DOMISILI UTAMA</label>
                        <input type="text" id="pelangganAddress" class="input-field" placeholder="Jl. Sudirman No. 12, Jakarta">
                    </div>
                </div>

                <!-- Password change sub-section -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <label class="text-[11px] font-bold text-blue-400 uppercase tracking-wider block">UBAH KATA SANDI (OPSIONAL)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="password" id="pelangganNewPassword" minlength="6" class="input-field" placeholder="Kata sandi baru">
                        <input type="password" id="pelangganConfirmPassword" minlength="6" class="input-field" placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs py-3.5 px-6 rounded-xl border border-slate-700 transition shadow-lg flex items-center justify-center gap-2">
                    <span>✓</span> Simpan Perubahan Profil & Password
                </button>
            </form>
        </div>

        <!-- Right Box (5 cols): Spesifikasi Lingkungan Platform & Status Role -->
        <div class="lg:col-span-5 card p-6 md:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-black text-white">Spesifikasi Lingkungan Platform</h3>
                <p class="text-xs text-slate-400 mt-1">Status akun dan fasilitas layanan pelanggan.</p>
            </div>

            <!-- Profile Header Card -->
            <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xl border border-blue-500/30 shrink-0" id="badgePelangganInitial">
                    P
                </div>
                <div class="space-y-0.5">
                    <h4 class="text-sm font-extrabold text-white" id="displayPelangganName">Pelanggan Revaro</h4>
                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-blue-500/15 text-blue-400 border border-blue-500/30 uppercase tracking-wider inline-block">
                        REGISTERED CUSTOMER
                    </span>
                </div>
            </div>

            <!-- Key-Value Specs -->
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">NAMA PLATFORM</span>
                    <span class="text-white font-bold">REVARO Platform</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">VERSI FRAMEWORK</span>
                    <span class="text-white font-bold">Laravel {{ app()->version() }}</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">STATUS AKUN</span>
                    <span class="text-emerald-400 font-bold uppercase">Aktif Terverifikasi</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">PERAN AKSES</span>
                    <span class="text-blue-400 font-bold uppercase">Pelanggan / Pengguna</span>
                </div>

                <div class="flex items-center justify-between py-2">
                    <span class="text-slate-400 uppercase">ZONA WAKTU</span>
                    <span class="text-white font-bold">{{ config('app.timezone', 'Asia/Jakarta') }} (WIB)</span>
                </div>
            </div>

            <!-- Active Controller Footer Box -->
            <div class="p-4 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                <p class="text-[10px] font-extrabold text-blue-400 uppercase tracking-widest flex items-center gap-1.5">
                    <span>CUSTOMER ACCOUNT ACTIVE</span>
                </p>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Akun Anda siap digunakan untuk pemesanan jasa teknisi panggilan, pembelian sparepart otomotif, dan garansi layanan.
                </p>
            </div>
        </div>
    </div>

    <!-- Section 2: Pintasan Cepat Modul Pelanggan -->
    <div class="space-y-4">
        <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span>Pintasan Cepat Modul Pelanggan</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Shortcut 1: Dasbor Pelanggan -->
            <a href="/pelanggan/dashboard" class="card p-5 hover:border-blue-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl border border-blue-500/20 group-hover:scale-110 transition">
                    🚗
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-blue-400 transition">Dasbor Pelanggan</h4>
                    <p class="text-xs text-slate-400">Overview pesanan & servis</p>
                </div>
            </a>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token') || localStorage.getItem('admin_token');
        const user = JSON.parse(localStorage.getItem('user') || localStorage.getItem('admin_user') || '{}');

        function loadData() {
            document.getElementById('pelangganNama').value = user.nama || 'Pelanggan Revaro';
            document.getElementById('pelangganEmail').value = user.email || 'pelanggan@revaro.com';
            document.getElementById('displayPelangganName').textContent = user.nama || 'Pelanggan Revaro';
            document.getElementById('badgePelangganInitial').textContent = (user.nama || 'P').charAt(0).toUpperCase();
            document.getElementById('pelangganPhone').value = user.telepon || '081234567894';
            document.getElementById('pelangganAddress').value = 'Jl. Sudirman No. 12, Jakarta';
        }

        async function savePelangganProfile() {
            const nama = document.getElementById('pelangganNama').value.trim();
            const email = document.getElementById('pelangganEmail').value.trim();
            const newPassword = document.getElementById('pelangganNewPassword').value;
            const confirmPassword = document.getElementById('pelangganConfirmPassword').value;

            if (newPassword && newPassword !== confirmPassword) {
                Swal.fire({ title: 'Password Mismatch', text: 'Konfirmasi kata sandi baru tidak cocok.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
                return;
            }

            const formData = new FormData();
            formData.append('nama', nama);
            formData.append('email', email);

            try {
                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, background: '#1E293B', color: '#E2E8F0', didOpen: () => Swal.showLoading() });
                const res = await fetch('/api/admin/profile', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token },
                    body: formData
                });
                if (res.ok) {
                    const data = await res.json();
                    user.nama = data.user.nama;
                    user.email = data.user.email;
                    localStorage.setItem('user', JSON.stringify(user));
                    document.getElementById('displayPelangganName').textContent = data.user.nama;
                    Swal.fire({ title: 'Berhasil!', text: 'Profil pelanggan berhasil diperbarui.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
                }
            } catch (e) {
                console.error(e);
            }
        }

        function handleLogout() {
            localStorage.clear();
            window.location.href = '/admin/login';
        }

        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>
