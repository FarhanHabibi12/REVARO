<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung Sebagai Mitra REVARO — Teknisi & Toko Sparepart</title>
    <script src="https://cdn.tailwindcss.com">
        function togglePwdVis(inputId, iconId) {
            const inp = document.getElementById(inputId);
            const ico = document.getElementById(iconId);
            inp.type = inp.type === 'password' ? 'text' : 'password';
            ico.innerText = inp.type === 'password' ? '???' : '??';
        }

        </script>
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between">

    <!-- Top Navigation Bar -->
    <header class="glass-nav text-white shadow-md">
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
            </nav>
            <div>
                <a href="/" class="bg-white/10 hover:bg-white/20 text-white text-xs px-3.5 py-2 rounded-lg font-bold border border-white/10 transition">Kembali</a>
            </div>
        </div>
    </header>

    <!-- Main Registration Section -->
    <main class="flex-grow flex items-center justify-center p-4 py-10">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
            
            <div class="border-b border-slate-100 pb-4 text-center">
                <span class="bg-brandOrange/10 text-brandOrange text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">Portal Pendaftaran Mitra</span>
                <h2 class="text-2xl font-black text-brandBlue mt-2.5">Mulai Hubungkan Usaha Anda</h2>
                <p class="text-xs text-slate-500 mt-1">Bergabunglah dengan ribuan teknisi dan toko sparepart resmi di platform REVARO.</p>
            </div>

            <!-- Tab switch register type -->
            <div class="flex space-x-3 bg-slate-100 p-1.5 rounded-xl text-xs font-bold">
                <button onclick="switchTab('teknisi')" id="tabTech" class="flex-grow py-2.5 rounded-lg text-center bg-white text-brandBlue shadow-sm transition">Daftar sebagai Teknisi</button>
                <button onclick="switchTab('toko-sparepart')" id="tabShop" class="flex-grow py-2.5 rounded-lg text-center text-slate-500 hover:text-slate-700 transition">Daftar sebagai Toko Sparepart</button>
            </div>

            <!-- Registration Form -->
            <form id="regForm" onsubmit="handleRegistrationSubmit(event)" class="space-y-4">
                <input type="hidden" id="roleSlugInput" value="teknisi">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label id="nameLabel" class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap Teknisi</label>
                        <input required type="text" id="regName" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Joko Susanto">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Email Aktif</label>
                        <input required type="email" id="regEmail" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="joko@gmail.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="passwordFieldBlock">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                        <div class="relative">
                            <input required type="password" id="regPassword" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 pr-10 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Minimal 6 karakter">
                            <button type="button" onclick="togglePwdVis('regPassword','eyePartner')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <span id="eyePartner">???</span>
                            </button>
                        </div>
                    </div>
                    <div id="phoneFieldBlock">
                        <label id="phoneLabel" class="block text-xs font-semibold text-slate-600 mb-1">Nomor WhatsApp/HP</label>
                        <input required type="text" id="regPhone" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="08xxxxxxxx">
                    </div>
                </div>

                <!-- Conditional technician fields -->
                <div id="techFieldsBlock" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Spesialisasi Servis</label>
                            <select id="regCategory" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition">
                                <option value="AC Pilihan">Air Conditioner (AC)</option>
                                <option value="Mesin Cuci">Mesin Cuci</option>
                                <option value="Kulkas">Lemari Es / Kulkas</option>
                                <option value="Pompa Air">Pompa Air</option>
                                <option value="TV & Audio">TV & Perangkat Audio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Unggah Sertifikat Kompetensi (.PDF / .JPG)</label>
                            <input required type="file" id="regCert" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 outline-none focus:border-brandBlue transition">
                            <p class="text-[9px] text-slate-400 mt-1">Sertifikat BNSP, sertifikasi keahlian, atau surat referensi kompetensi.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Unggah Foto KTP Pemohon (.JPG / .PNG)</label>
                        <input required type="file" id="regKtp" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 outline-none focus:border-brandBlue transition">
                    </div>
                </div>

                <!-- Conditional shop fields (Hidden initially) -->
                <div id="shopFieldsBlock" class="hidden space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Lengkap Toko Fisik</label>
                        <input type="text" id="regAddress" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="Glodok Jaya Plaza Blok C No. 12, Jakarta Barat">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor SIUP / NIB (Izin Usaha)</label>
                            <input type="text" id="regIzinUsaha" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl p-3 outline-none focus:border-brandBlue focus:bg-white transition" placeholder="NIB-1234-5678-90">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Unggah Berkas NIB/SIUP (.PDF)</label>
                            <input type="file" id="regShopDoc" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 outline-none focus:border-brandBlue transition">
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-[10px] text-slate-500 leading-relaxed">
                    🔒 <strong>Kebijakan Privasi & Kebijakan Verifikasi:</strong> Berkas sertifikat dan NIB/SIUP yang Anda lampirkan hanya digunakan untuk keperluan audit kompetensi oleh Admin Verifikator. Akun Anda akan berstatus <strong>PENDING_REVIEW</strong> terlebih dahulu dan akan segera diaktivasi setelah verifikasi disetujui.
                </div>

                <button type="submit" class="w-full bg-brandBlue hover:bg-blue-800 text-white font-bold text-sm py-3.5 rounded-xl transition shadow shadow-blue-800/10">
                    Daftar dan Kirim Dokumen Onboarding
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-500 text-center py-6 border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 text-xs">
            <p class="font-bold text-slate-400">© 2026 REVARO — Partner Registration Portal.</p>
        </div>
    </footer>

    <!-- Client Script -->
    <script>
        // Check if user is logged in to auto-fill details
        window.addEventListener('DOMContentLoaded', () => {
            const userRaw = localStorage.getItem('user');
            const profileRaw = localStorage.getItem('profile');

            if (userRaw) {
                const user = JSON.parse(userRaw);
                const regEmail = document.getElementById('regEmail');
                const regName = document.getElementById('regName');
                const passwordBlock = document.getElementById('passwordFieldBlock');
                const phoneBlock = document.getElementById('phoneFieldBlock');
                const regPassword = document.getElementById('regPassword');

                if (regEmail && user.email) {
                    regEmail.value = user.email;
                    regEmail.readOnly = true;
                    regEmail.classList.add('bg-slate-200', 'cursor-not-allowed', 'text-slate-500');
                    regEmail.classList.remove('bg-slate-50');
                }
                if (regName && user.nama) {
                    regName.value = user.nama;
                }
                if (passwordBlock) {
                    passwordBlock.classList.add('hidden');
                }
                if (phoneBlock) {
                    phoneBlock.classList.add('md:col-span-2');
                }
                if (regPassword) {
                    regPassword.required = false;
                }

                if (profileRaw) {
                    const profile = JSON.parse(profileRaw);
                    const regPhone = document.getElementById('regPhone');
                    if (regPhone) {
                        regPhone.value = profile.hp || profile.telepon || '';
                    }
                }
            }
        });

        function switchTab(role) {
            document.getElementById('roleSlugInput').value = role;
            
            const tabTech = document.getElementById('tabTech');
            const tabShop = document.getElementById('tabShop');
            
            const techBlock = document.getElementById('techFieldsBlock');
            const shopBlock = document.getElementById('shopFieldsBlock');

            const nameLabel = document.getElementById('nameLabel');
            const phoneLabel = document.getElementById('phoneLabel');

            const regName = document.getElementById('regName');
            const regPhone = document.getElementById('regPhone');
            const regCert = document.getElementById('regCert');
            const regKtp = document.getElementById('regKtp');
            const regAddress = document.getElementById('regAddress');
            const regIzinUsaha = document.getElementById('regIzinUsaha');
            const regShopDoc = document.getElementById('regShopDoc');

            if (role === 'teknisi') {
                tabTech.className = "flex-grow py-2.5 rounded-lg text-center bg-white text-brandBlue shadow-sm transition";
                tabShop.className = "flex-grow py-2.5 rounded-lg text-center text-slate-500 hover:text-slate-700 transition";
                
                techBlock.classList.remove('hidden');
                shopBlock.classList.add('hidden');

                nameLabel.innerText = "Nama Lengkap Teknisi";
                phoneLabel.innerText = "Nomor WhatsApp/HP";
                regName.placeholder = "Joko Susanto";
                regPhone.placeholder = "08xxxxxxxx";

                regCert.required = true;
                regKtp.required = true;
                regAddress.required = false;
                regIzinUsaha.required = false;
                regShopDoc.required = false;
            } else {
                tabShop.className = "flex-grow py-2.5 rounded-lg text-center bg-white text-brandBlue shadow-sm transition";
                tabTech.className = "flex-grow py-2.5 rounded-lg text-center text-slate-500 hover:text-slate-700 transition";

                techBlock.classList.add('hidden');
                shopBlock.classList.remove('hidden');

                nameLabel.innerText = "Nama Resmi Toko";
                phoneLabel.innerText = "Nomor Telepon Toko";
                regName.placeholder = "Glodok Sparepart Utama";
                regPhone.placeholder = "021-xxxxxxx";

                regCert.required = false;
                regKtp.required = false;
                regAddress.required = true;
                regIzinUsaha.required = true;
                regShopDoc.required = true;
            }
        }

        async function handleRegistrationSubmit(e) {
            e.preventDefault();

            const role_slug = document.getElementById('roleSlugInput').value;
            const nama = document.getElementById('regName').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;
            const hp = document.getElementById('regPhone').value;

            // Multipart Form Data construction
            const formData = new FormData();
            formData.append('role_slug', role_slug);
            formData.append('nama', nama);
            formData.append('email', email);
            if (password) {
                formData.append('password', password);
            }
            formData.append('hp', hp);

            if (role_slug === 'teknisi') {
                formData.append('kategori', document.getElementById('regCategory').value);
                formData.append('latitude', -6.2088); // Default mock
                formData.append('longitude', 106.8456);

                const certInput = document.getElementById('regCert');
                if (certInput.files[0]) {
                    formData.append('sertifikat', certInput.files[0]);
                }

                const ktpInput = document.getElementById('regKtp');
                if (ktpInput.files[0]) {
                    formData.append('ktp', ktpInput.files[0]);
                }
            } else {
                formData.append('alamat', document.getElementById('regAddress').value);
                formData.append('izin_usaha', document.getElementById('regIzinUsaha').value);
                formData.append('telepon', hp);
                formData.append('latitude', -6.2100);
                formData.append('longitude', 106.8500);

                const shopDocInput = document.getElementById('regShopDoc');
                if (shopDocInput.files[0]) {
                    formData.append('dokumen_izin', shopDocInput.files[0]);
                }
            }

            const headers = {};
            const token = localStorage.getItem('token');
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: headers,
                    body: formData
                });

                const result = await response.json();
                if (!response.ok) {
                    Swal.fire('Gagal', result.message || JSON.stringify(result.errors), 'error');
                    return;
                }

                // Update session details
                localStorage.setItem('user', JSON.stringify(result.user));
                localStorage.setItem('profile', JSON.stringify(result.profile));
                if (result.access_token) {
                    localStorage.setItem('token', result.access_token);
                }

                Swal.fire('Berhasil!', 'Pendaftaran berhasil diajukan. Menunggu verifikasi admin.', 'success');
                window.location.href = '/';

            } catch (err) {
                console.error(err);
                Swal.fire("Gagal", "Gagal menghubungi server pendaftaran.", "error");
            }
        }
    </script>
</body>
</html>
