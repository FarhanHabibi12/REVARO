<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVARO — Layanan Servis Elektronik On-Demand Instan</title>
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
        .gradient-bg {
            background: linear-gradient(135deg, #0A4C8A 0%, #062F56 100%);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between">

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
                <a href="#fitur" class="text-sm font-semibold hover:text-brandOrange transition">Layanan Kami</a>
                <a href="/partner/register" id="navGabungMitra" class="text-sm font-semibold hover:text-brandOrange transition">Gabung Mitra</a>
            </nav>

            <div class="flex items-center space-x-3">
                <div id="userProfileSection" class="hidden flex items-center space-x-2">
                    <span id="navUserName" class="text-xs font-bold bg-white/10 px-3 py-1.5 rounded-full border border-white/20"></span>
                    <button id="dashboardBtn" class="bg-brandOrange hover:bg-orange-600 text-white text-xs px-3.5 py-2 rounded-lg font-bold transition">Ke Dashboard</button>
                    <button onclick="handleLogout()" class="bg-red-500/80 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">Logout</button>
                </div>
                <div id="authNavButtons" class="flex space-x-2">
                    <button onclick="openAuthModal('login')" class="bg-white/10 hover:bg-white/20 text-white text-xs px-3.5 py-2 rounded-lg font-bold border border-white/10 transition">Masuk</button>
                    <button onclick="openAuthModal('register')" class="bg-brandOrange hover:bg-orange-600 text-white text-xs px-3.5 py-2 rounded-lg font-bold transition shadow-sm">Daftar</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20 px-4 text-center relative overflow-hidden flex-grow flex flex-col justify-center">
        <!-- Glow effects -->
        <div class="absolute w-96 h-96 rounded-full bg-blue-500/10 blur-[120px] top-10 left-10"></div>
        <div class="absolute w-96 h-96 rounded-full bg-orange-500/10 blur-[120px] bottom-10 right-10"></div>

        <div class="max-w-4xl mx-auto space-y-6 relative z-10">
            <span class="bg-brandOrange/95 text-white text-xs uppercase font-bold tracking-widest px-3 py-1 rounded-full">Solusi Elektronik Terpercaya</span>
            <h2 class="text-3xl md:text-5xl font-black leading-tight">Servis Elektronik Instan &amp;<br>Aman Di Sekitar Anda</h2>
            <p class="text-sm md:text-base text-blue-100 max-w-2xl mx-auto">Cari teknisi terdekat untuk AC, Kulkas, Mesin Cuci, TV, dan Pompa Air secara real-time. Keamanan dana terjamin lewat sistem Escrow REVARO.</p>
            
            <div class="flex justify-center space-x-3 pt-4">
                <button onclick="openAuthModal('login')" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-sm px-6 py-3.5 rounded-xl transition shadow-lg shadow-orange-700/20">Pesan Layanan Sekarang</button>
                <a href="/partner/register" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-sm px-6 py-3.5 rounded-xl transition">Gabung Sebagai Mitra</a>
            </div>
        </div>
    </section>

    <!-- Marketing Features Grid -->
    <section id="fitur" class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto text-center space-y-12">
            <div class="space-y-2.5">
                <h3 class="text-2xl font-black text-brandBlue">Kategori Servis Elektronik</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Kami menyediakan penanganan cepat oleh teknisi bersertifikat untuk berbagai peralatan rumah tangga Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- AC -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 hover:border-brandOrange transition shadow-sm space-y-3">
                    <div class="text-3xl">❄️</div>
                    <h4 class="font-extrabold text-brandBlue text-sm">Servis AC</h4>
                    <p class="text-xs text-slate-500">Pencucian, tambah freon, perbaikan kebocoran dingin, garansi 30 hari.</p>
                </div>
                <!-- Kulkas -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 hover:border-brandOrange transition shadow-sm space-y-3">
                    <div class="text-3xl">❄️</div>
                    <h4 class="font-extrabold text-brandBlue text-sm">Servis Kulkas</h4>
                    <p class="text-xs text-slate-500">Perbaikan kompresor kulkas 1 atau 2 pintu, tidak dingin, garansi 7 hari.</p>
                </div>
                <!-- Mesin Cuci -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 hover:border-brandOrange transition shadow-sm space-y-3">
                    <div class="text-3xl">🧺</div>
                    <h4 class="font-extrabold text-brandBlue text-sm">Mesin Cuci</h4>
                    <p class="text-xs text-slate-500">Mengatasi modul error, mesin tidak berputar, air macet, garansi 7 hari.</p>
                </div>
                <!-- Pompa Air -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 hover:border-brandOrange transition shadow-sm space-y-3">
                    <div class="text-3xl">🚰</div>
                    <h4 class="font-extrabold text-brandBlue text-sm">Pompa Air</h4>
                    <p class="text-xs text-slate-500">Perbaikan pompa mati total, hisapan lemah, otomatis rusak, garansi 7 hari.</p>
                </div>
                <!-- TV -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 hover:border-brandOrange transition shadow-sm space-y-3">
                    <div class="text-3xl">📺</div>
                    <h4 class="font-extrabold text-brandBlue text-sm">TV &amp; Audio</h4>
                    <p class="text-xs text-slate-500">Servis layar LED/LCD bergaris, mati total, suara hilang, garansi 7 hari.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-500 text-center py-8 border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 text-xs space-y-2">
            <p class="font-bold text-slate-400">© 2026 REVARO — Repair &amp; Restore Marketplace Platform.</p>
            <p>Sistem ini terintegrasi penuh menggunakan multi-role dan rate limiter dinamis.</p>
        </div>
    </footer>

    <!-- Auth Modal (Masuk / Daftar Pelanggan) -->
    <div id="authModal" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl relative space-y-6">
            <button onclick="closeAuthModal()" class="absolute right-6 top-6 text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            
            <div class="text-center space-y-1.5">
                <h3 id="authModalTitle" class="text-xl font-black text-brandBlue">Masuk Akun Pelanggan</h3>
                <p id="authModalSubtitle" class="text-xs text-slate-500">Silakan masuk untuk menggunakan layanan.</p>
            </div>

            <!-- Tab login/register -->
            <div class="flex bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <button onclick="switchAuthTab('login')" id="authTabLogin" class="flex-1 py-1.5 rounded-lg text-center bg-white text-brandBlue shadow-sm">Masuk</button>
                <button onclick="switchAuthTab('register')" id="authTabRegister" class="flex-1 py-1.5 rounded-lg text-center text-slate-500 hover:text-slate-700">Daftar</button>
            </div>

            <form onsubmit="handleAuthSubmit(event)" class="space-y-4">
                <input type="hidden" id="authMode" value="login">

                <div id="authNameField" class="hidden">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                    <input type="text" id="authName" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Budi Santoso">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Alamat Email</label>
                    <input required type="email" id="authEmail" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="budi@gmail.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Kata Sandi</label>
                    <input required type="password" id="authPassword" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Minimal 6 karakter">
                </div>

                <div id="authPhoneField" class="hidden">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Nomor HP / WhatsApp</label>
                    <input type="text" id="authPhone" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="08xxxxxxxx">
                </div>

                <div id="authAddressField" class="hidden">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Alamat Rumah Lengkap</label>
                    <input type="text" id="authAddress" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Jl. Sudirman No. 25, Jakarta Selatan">
                </div>

                <button type="submit" class="w-full bg-brandBlue hover:bg-blue-800 text-white font-bold text-xs py-3.5 rounded-xl transition shadow">
                    Kirim &amp; Lanjutkan
                </button>
            </form>
        </div>
    </div>

    <!-- Client-Side Routing Logic -->
    <script>
        // Check session and redirect accordingly
        window.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('token');
            const userRaw = localStorage.getItem('user');

            if (token && userRaw) {
                const user = JSON.parse(userRaw);
                
                document.getElementById('authNavButtons').classList.add('hidden');
                document.getElementById('userProfileSection').classList.remove('hidden');
                document.getElementById('navUserName').innerText = user.nama;
                
                // Hide Gabung Mitra link if already a partner
                const roleName = user.role ? (user.role.nama || user.role) : '';
                if (roleName === 'Teknisi' || roleName === 'Toko Sparepart') {
                    const navMitra = document.getElementById('navGabungMitra');
                    if (navMitra) navMitra.classList.add('hidden');
                }

                // Dashboard button redirect link based on role
                const dashBtn = document.getElementById('dashboardBtn');
                let dashUrl = '/pelanggan/dashboard';
                if (roleName === 'Teknisi') {
                    dashUrl = '/teknisi/dashboard';
                } else if (roleName === 'Toko Sparepart') {
                    dashUrl = '/toko/dashboard';
                }
                
                dashBtn.addEventListener('click', () => {
                    window.location.href = dashUrl;
                });

                // Auto redirect immediately upon visiting home if logged in
                window.location.href = dashUrl;
            }
        });

        function openAuthModal(mode) {
            document.getElementById('authModal').classList.remove('hidden');
            switchAuthTab(mode);
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.add('hidden');
        }

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
                
                document.getElementById('authModalTitle').innerText = "Masuk Akun Mitra / Pelanggan";
                document.getElementById('authModalSubtitle').innerText = "Silakan masuk untuk melanjutkan.";

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
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ email, password })
                    });
                } else {
                    const nama = document.getElementById('authName').value;
                    const hp = document.getElementById('authPhone').value;
                    const alamat = document.getElementById('authAddress').value;

                    response = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
                    Swal.fire({ title: 'Gagal', text: result.message || JSON.stringify(result.errors), icon: 'error', confirmButtonColor: '#FF6A00' });
                    return;
                }

                // Save to local storage
                localStorage.setItem('token', result.access_token);
                localStorage.setItem('user', JSON.stringify(result.user));
                localStorage.setItem('profile', JSON.stringify(result.profile));

                let redirectUrl = '/pelanggan/dashboard';
                if (result.role === 'Teknisi' || (result.user.role && result.user.role.nama === 'Teknisi')) {
                    redirectUrl = '/teknisi/dashboard';
                } else if (result.role === 'Toko Sparepart' || (result.user.role && result.user.role.nama === 'Toko Sparepart')) {
                    redirectUrl = '/toko/dashboard';
                }

                Swal.fire({
                    title: 'Masuk Berhasil!',
                    text: 'Selamat datang kembali, ' + result.user.nama,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    closeAuthModal();
                    window.location.href = redirectUrl;
                });

            } catch (err) {
                console.error(err);
                Swal.fire('Gagal', 'Terjadi kesalahan koneksi.', 'error');
            }
        }

        function handleLogout() {
            localStorage.clear();
            window.location.reload();
        }
    </script>
</body>
</html>
