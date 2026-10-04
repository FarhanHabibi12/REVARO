<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun Toko Sparepart — REVARO</title>
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
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('js/app-i18n.js') }}"></script>
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
            <a href="/toko/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-secondary text-white font-extrabold shadow-sm hidden sm:inline-block">Konsol Toko Sparepart</span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="/toko/dashboard" class="revaro-btn-outline text-xs px-3.5 py-1.5 rounded-xl font-bold flex items-center gap-1.5">
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
                <span>REVARO</span> / <span>MITRA TOKO</span> / <span class="text-secondary font-black">PENGATURAN KONSOL</span>
            </div>
            <div>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border-2 border-emerald-600 flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>SHOP PARTNER ACTIVE</span>
                </span>
            </div>
        </div>

        <!-- 2 Column Section: Profil & Sandi Toko + Spesifikasi Lingkungan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Box (7 cols): Profil & Sandi Toko Sparepart -->
            <div class="lg:col-span-7 revaro-card p-6 md:p-8 space-y-6">
                <div class="border-b-2 border-accent pb-3">
                    <h3 class="text-lg font-extrabold text-primary">Profil &amp; Sandi Toko Sparepart</h3>
                    <p class="text-xs text-textLight mt-1">Perbarui informasi identitas usaha, kontak layanan toko, dan keamanan akun mitra Anda.</p>
                </div>

                <form id="tokoProfileForm" onsubmit="event.preventDefault(); saveTokoProfile();" class="space-y-5">
                    <div>
                        <label class="text-[11px] font-black text-primary uppercase tracking-wider block mb-1.5">NAMA TOKO / PEMILIK *</label>
                        <input type="text" id="tokoNama" required class="revaro-input font-bold" placeholder="Toko Sparepart Revaro">
                    </div>

                    <div>
                        <label class="text-[11px] font-black text-primary uppercase tracking-wider block mb-1.5">ALAMAT SUREL LOGIN *</label>
                        <input type="email" id="tokoEmail" required class="revaro-input font-mono" placeholder="toko@revaro.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-black text-primary uppercase tracking-wider block mb-1.5">NOMOR TELEPON / WA TOKO</label>
                            <input type="text" id="tokoPhone" class="revaro-input font-mono" placeholder="081234567892">
                        </div>
                        <div>
                            <label class="text-[11px] font-black text-primary uppercase tracking-wider block mb-1.5">ALAMAT FISIK TOKO</label>
                            <input type="text" id="tokoAddress" class="revaro-input" placeholder="Jl. Otomotif Raya No. 88, Jakarta">
                        </div>
                    </div>

                    <!-- Password change sub-section -->
                    <div class="pt-4 border-t-2 border-accent space-y-3">
                        <label class="text-[11px] font-black text-secondary uppercase tracking-wider block">UBAH KATA SANDI (OPSIONAL)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" id="tokoNewPassword" minlength="6" class="revaro-input" placeholder="Kata sandi baru">
                            <input type="password" id="tokoConfirmPassword" minlength="6" class="revaro-input" placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>

                    <button type="submit" class="w-full revaro-btn font-extrabold text-xs py-3.5 px-6 rounded-xl transition flex items-center justify-center gap-2">
                        <span>✓</span> <span>Simpan Perubahan Profil &amp; Password</span>
                    </button>
                </form>
            </div>

            <!-- Right Box (5 cols): Spesifikasi Lingkungan Platform & Status Role -->
            <div class="lg:col-span-5 revaro-card p-6 md:p-8 space-y-6">
                <div class="border-b-2 border-accent pb-3">
                    <h3 class="text-lg font-extrabold text-primary">Spesifikasi Lingkungan Platform</h3>
                    <p class="text-xs text-textLight mt-1">Status dan modul lingkungan operasional toko sparepart terverifikasi.</p>
                </div>

                <!-- Profile Header Card -->
                <div class="p-4 bg-accent/30 rounded-2xl border-2 border-primary/20 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-secondary text-white flex items-center justify-center font-black text-2xl border-2 border-primary shadow-sm shrink-0" id="badgeTokoInitial">
                        T
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-extrabold text-primary" id="displayTokoName">Toko Sparepart Revaro</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-secondary/15 text-secondary border border-secondary/30 uppercase tracking-wider inline-block">
                            VERIFIED SHOP PARTNER
                        </span>
                    </div>
                </div>

                <!-- Key-Value Specs -->
                <div class="space-y-3 font-mono text-xs">
                    <div class="flex items-center justify-between py-2 border-b-2 border-accent">
                        <span class="text-textLight font-sans font-bold uppercase">NAMA PLATFORM</span>
                        <span class="text-primary font-black">REVARO Ecosystem</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b-2 border-accent">
                        <span class="text-textLight font-sans font-bold uppercase">VERSI FRAMEWORK</span>
                        <span class="text-textDark font-bold">Laravel {{ app()->version() }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b-2 border-accent">
                        <span class="text-textLight font-sans font-bold uppercase">STATUS VERIFIKASI</span>
                        <span class="text-emerald-600 font-black uppercase">Disetujui (Active)</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b-2 border-accent">
                        <span class="text-textLight font-sans font-bold uppercase">PERAN AKSES</span>
                        <span class="text-secondary font-black uppercase">Mitra Toko Sparepart</span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-textLight font-sans font-bold uppercase">ZONA WAKTU</span>
                        <span class="text-textDark font-bold">{{ config('app.timezone', 'Asia/Jakarta') }} (WIB)</span>
                    </div>
                </div>

                <!-- Active Controller Footer Box -->
                <div class="p-4 bg-accent/40 rounded-xl border-2 border-primary/20 space-y-1">
                    <p class="text-[11px] font-black text-primary uppercase tracking-wider flex items-center gap-1.5">
                        <span>🛡️ SHOP CONTROLLER ACTIVE</span>
                    </p>
                    <p class="text-xs text-textLight leading-relaxed font-sans">
                        Seluruh etalase produk sparepart, penerimaan pesanan suku cadang, dan penarikan saldo wallet toko beroperasi normal secara real-time.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 2: Pintasan Cepat Modul Toko -->
        <div class="space-y-4">
            <h3 class="text-base font-extrabold text-primary flex items-center gap-2">
                <span>Pintasan Cepat Modul Toko</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Shortcut 1: Dasbor Toko -->
                <a href="/toko/dashboard" class="revaro-card p-5 hover:border-secondary transition group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-accent text-primary border-2 border-primary flex items-center justify-center text-xl font-black group-hover:scale-105 transition shadow-sm">
                        🏪
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-primary group-hover:text-secondary transition">Dasbor Toko</h4>
                        <p class="text-xs text-textLight">Katalog stok &amp; pesanan komponen</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        const token = localStorage.getItem('token') || localStorage.getItem('admin_token');
        const user = JSON.parse(localStorage.getItem('user') || localStorage.getItem('admin_user') || '{}');

        function loadData() {
            document.getElementById('tokoNama').value = user.nama || 'Toko Sparepart Revaro';
            document.getElementById('tokoEmail').value = user.email || 'toko@revaro.com';
            document.getElementById('displayTokoName').textContent = user.nama || 'Toko Sparepart Revaro';
            document.getElementById('badgeTokoInitial').textContent = (user.nama || 'T').charAt(0).toUpperCase();
            document.getElementById('tokoPhone').value = user.telepon || '081234567892';
            document.getElementById('tokoAddress').value = 'Jl. Otomotif Raya No. 88, Jakarta';
        }


        async function saveTokoProfile() {
            const nama = document.getElementById('tokoNama').value.trim();
            const email = document.getElementById('tokoEmail').value.trim();
            const phone = document.getElementById('tokoPhone').value.trim();
            const address = document.getElementById('tokoAddress').value.trim();
            const newPassword = document.getElementById('tokoNewPassword').value;
            const confirmPassword = document.getElementById('tokoConfirmPassword').value;

            if (newPassword && newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Password Tidak Cocok',
                    text: 'Konfirmasi kata sandi baru tidak sama.',
                    icon: 'warning',
                    confirmButtonColor: '#0B4A8F'
                });
                return;
            }

            const formData = new FormData();
            formData.append('nama', nama);
            formData.append('email', email);
            if (phone) formData.append('telepon', phone);
            if (address) formData.append('alamat', address);
            if (newPassword) {
                formData.append('new_password', newPassword);
                formData.append('password_confirmation', confirmPassword);
            }

            try {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    confirmButtonColor: '#0B4A8F',
                    didOpen: () => Swal.showLoading()
                });
                const res = await fetch('/api/user/profile', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (res.ok) {
                    user.nama = data.user.nama;
                    user.email = data.user.email;
                    if (data.profile) {
                        user.telepon = data.profile.telepon;
                        user.alamat = data.profile.alamat;
                    }
                    localStorage.setItem('user', JSON.stringify(user));
                    document.getElementById('displayTokoName').textContent = data.user.nama;
                    document.getElementById('tokoNewPassword').value = '';
                    document.getElementById('tokoConfirmPassword').value = '';
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Profil dan pengaturan akun toko berhasil diperbarui.',
                        icon: 'success',
                        confirmButtonColor: '#0B4A8F'
                    });
                } else {
                    let errMsg = data.message || 'Gagal menyimpan profil toko.';
                    if (data.errors) {
                        errMsg = Object.values(data.errors).flat().join('<br>');
                    }
                    Swal.fire({ title: 'Gagal', html: errMsg, icon: 'error', confirmButtonColor: '#0B4A8F' });
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
