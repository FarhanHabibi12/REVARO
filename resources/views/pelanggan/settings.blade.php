<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun Pelanggan — REVARO</title>
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
            <a href="/pelanggan/dashboard" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent text-primary font-bold border border-primary hidden sm:inline-block">Pelanggan</span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="/pelanggan/dashboard" class="revaro-btn-outline text-xs px-3.5 py-1.5 rounded-xl font-bold flex items-center gap-1.5">
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
                <span>REVARO</span> / <span>PELANGGAN</span> / <span class="text-secondary font-black">PENGATURAN AKUN</span>
            </div>
            <div>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-accent text-primary border-2 border-primary flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span>AKUN PELANGGAN AKTIF</span>
                </span>
            </div>
        </div>

        <!-- 2 Column Section: Profil & Sandi Pelanggan + Spesifikasi Lingkungan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Box (7 cols): Profil & Sandi Pelanggan -->
            <div class="lg:col-span-7 revaro-card p-6 md:p-8 space-y-6">
                <div class="border-b-2 border-accent pb-3">
                    <h3 class="text-lg font-extrabold text-primary">Profil &amp; Kata Sandi Pelanggan</h3>
                    <p class="text-xs text-textLight mt-1">Perbarui informasi identitas diri, nomor kontak telepon/WhatsApp, dan kata sandi akun REVARO Anda.</p>
                </div>

                <form id="pelangganProfileForm" onsubmit="event.preventDefault(); savePelangganProfile();" class="space-y-5">
                    <div>
                        <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Nama Lengkap Pelanggan *</label>
                        <input type="text" id="pelangganNama" required class="revaro-input font-bold" placeholder="Pelanggan Revaro">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Alamat Email Login *</label>
                        <input type="email" id="pelangganEmail" required class="revaro-input font-medium" placeholder="pelanggan@revaro.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Nomor HP / WhatsApp</label>
                            <input type="text" id="pelangganPhone" class="revaro-input font-medium" placeholder="081234567894">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-primary uppercase tracking-wider block mb-1.5">Alamat Domisili Utama</label>
                            <input type="text" id="pelangganAddress" class="revaro-input font-medium" placeholder="Jl. Sudirman No. 12, Jakarta">
                        </div>
                    </div>

                    <!-- Password change sub-section -->
                    <div class="pt-4 border-t-2 border-accent space-y-3">
                        <label class="text-xs font-bold text-secondary uppercase tracking-wider block">Ubah Kata Sandi (Opsional)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" id="pelangganNewPassword" minlength="6" class="revaro-input font-medium" placeholder="Kata sandi baru">
                            <input type="password" id="pelangganConfirmPassword" minlength="6" class="revaro-input font-medium" placeholder="Ulangi kata sandi">
                        </div>
                    </div>

                    <button type="submit" class="w-full revaro-btn font-extrabold text-sm py-4 px-6 rounded-xl flex items-center justify-center gap-2">
                        <span>✓ Simpan Perubahan Profil &amp; Password</span>
                    </button>
                </form>
            </div>

            <!-- Right Box (5 cols): Ringkasan Status Akun -->
            <div class="lg:col-span-5 revaro-card p-6 md:p-8 space-y-6">
                <div class="border-b-2 border-accent pb-3">
                    <h3 class="text-lg font-extrabold text-primary">Status Akun Pelanggan</h3>
                    <p class="text-xs text-textLight mt-1">Fasilitas dan otentikasi akun pengguna REVARO.</p>
                </div>

                <!-- Profile Header Card -->
                <div class="p-4 bg-accent/40 rounded-2xl border-2 border-primary flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center font-black text-2xl border-2 border-primary shadow-btn shrink-0" id="badgePelangganInitial">
                        P
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-base font-extrabold text-primary" id="displayPelangganName">Pelanggan Revaro</h4>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-black bg-white text-secondary border border-secondary uppercase tracking-wider inline-block">
                            PELANGGAN TERDAFTAR
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
                        <span class="text-textLight font-medium uppercase">Perlindungan Transaksi</span>
                        <span class="text-secondary font-black">Escrow (Rekening Bersama)</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-textLight font-medium uppercase">Status Akun</span>
                        <span class="text-green-600 font-extrabold uppercase flex items-center gap-1.5">
                            <i class="fas fa-check-circle"></i> Aktif Terverifikasi
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-textLight font-medium uppercase">Masa Garansi Servis</span>
                        <span class="text-primary font-bold">30 — 45 Hari Resmi</span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-textLight font-medium uppercase">Zona Waktu</span>
                        <span class="text-primary font-bold">WIB (Asia/Jakarta)</span>
                    </div>
                </div>

                <!-- Active Controller Footer Box -->
                <div class="p-4 bg-orange-50 rounded-xl border-2 border-secondary/40 space-y-1">
                    <p class="text-[11px] font-black text-secondary uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-shield-alt"></i> AKUN TERLINDUNGI
                    </p>
                    <p class="text-xs text-textDark leading-relaxed">
                        Akun Anda siap digunakan untuk melakukan order servis cepat (SOS), pelacakan GPS teknisi, konfirmasi sparepart resmi, dan klaim garansi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 2: Pintasan Cepat Modul Pelanggan -->
        <div class="space-y-4">
            <h3 class="text-base font-extrabold text-primary flex items-center gap-2">
                <span>Pintasan Cepat Pelanggan</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shortcut 1: Dasbor Pelanggan -->
                <a href="/pelanggan/dashboard" class="revaro-card p-5 hover:-translate-y-1 transition group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-accent text-primary flex items-center justify-center text-xl border-2 border-primary shadow-btn group-hover:scale-105 transition">
                        🛠️
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-primary group-hover:text-secondary transition">Pesan Jasa Servis (SOS)</h4>
                        <p class="text-xs text-textLight">Panggil teknisi terdekat ke rumah Anda</p>
                    </div>
                </a>

                <!-- Shortcut 2: Gabung Mitra -->
                <a href="/partner/register" class="revaro-card p-5 hover:-translate-y-1 transition group flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-secondary flex items-center justify-center text-xl border-2 border-secondary shadow-btn group-hover:scale-105 transition">
                        🤝
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-primary group-hover:text-secondary transition">Gabung Jadi Mitra REVARO</h4>
                        <p class="text-xs text-textLight">Daftar sebagai teknisi independen atau toko sparepart</p>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        const token = localStorage.getItem('token') || localStorage.getItem('admin_token');
        const user = JSON.parse(localStorage.getItem('user') || localStorage.getItem('admin_user') || '{}');

        function loadData() {
            document.getElementById('pelangganNama').value = user.nama || 'Pelanggan Revaro';
            document.getElementById('pelangganEmail').value = user.email || 'pelanggan@revaro.com';
            document.getElementById('displayPelangganName').textContent = user.nama || 'Pelanggan Revaro';
            document.getElementById('badgePelangganInitial').textContent = (user.nama || 'P').charAt(0).toUpperCase();
            document.getElementById('pelangganPhone').value = user.telepon || user.hp || '081234567894';
            document.getElementById('pelangganAddress').value = user.alamat || 'Jl. Sudirman No. 12, Jakarta';
        }

        async function savePelangganProfile() {
            const nama = document.getElementById('pelangganNama').value.trim();
            const email = document.getElementById('pelangganEmail').value.trim();
            const hp = document.getElementById('pelangganPhone').value.trim();
            const alamat = document.getElementById('pelangganAddress').value.trim();
            const newPassword = document.getElementById('pelangganNewPassword').value;
            const confirmPassword = document.getElementById('pelangganConfirmPassword').value;

            if (newPassword && newPassword !== confirmPassword) {
                Swal.fire({ title: 'Password Tidak Cocok', text: 'Konfirmasi kata sandi baru tidak sama.', icon: 'warning', confirmButtonColor: '#0B4A8F' });
                return;
            }

            const formData = new FormData();
            formData.append('nama', nama);
            formData.append('email', email);
            if (hp) formData.append('hp', hp);
            if (alamat) formData.append('alamat', alamat);
            if (newPassword) {
                formData.append('new_password', newPassword);
                formData.append('password_confirmation', confirmPassword);
            }

            try {
                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
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
                        user.hp = data.profile.hp;
                        user.alamat = data.profile.alamat;
                    }
                    localStorage.setItem('user', JSON.stringify(user));
                    document.getElementById('displayPelangganName').textContent = data.user.nama;
                    document.getElementById('badgePelangganInitial').textContent = data.user.nama.charAt(0).toUpperCase();
                    document.getElementById('pelangganNewPassword').value = '';
                    document.getElementById('pelangganConfirmPassword').value = '';
                    Swal.fire({ title: 'Berhasil!', text: 'Profil dan kata sandi berhasil diperbarui.', icon: 'success', confirmButtonColor: '#0B4A8F' });
                } else {
                    let errMsg = data.message || 'Gagal menyimpan profil.';
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
