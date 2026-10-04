<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung Sebagai Mitra REVARO — Teknisi & Toko Sparepart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Leaflet Interactive Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function togglePwdVis(inputId, iconId) {
            const inp = document.getElementById(inputId);
            const ico = document.getElementById(iconId);
            if (!inp) return;
            inp.type = inp.type === 'password' ? 'text' : 'password';
            if (ico) {
                if (inp.type === 'password') {
                    ico.innerHTML = `<i class="far fa-eye text-primary"></i>`;
                } else {
                    ico.innerHTML = `<i class="far fa-eye-slash text-secondary"></i>`;
                }
            }
        }
    </script>
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
            width: 100%;
            border: 2px solid #0B4A8F;
            box-shadow: 2px 2px 0px 0px #0B4A8F;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #1E293B;
            background-color: #ffffff;
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
<body class="bg-gray-50 min-h-screen text-textDark flex flex-col justify-between">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b-2 border-primary sticky top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <a href="/" id="headerLogoLink" class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent text-primary font-bold border border-primary hidden sm:inline-block">Portal Mitra</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="/" id="backHomeLink" class="revaro-btn-outline text-xs px-3.5 py-1.5 rounded-xl font-bold flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> <span id="backHomeText">Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Registration Section -->
    <main class="flex-grow flex items-center justify-center p-4 py-10">
        <div class="w-full max-w-2xl revaro-card p-6 md:p-8 space-y-6">
            
            <div class="border-b-2 border-accent pb-4 text-center">
                <span class="bg-secondary/15 text-secondary border border-secondary/30 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                    Portal Registrasi Mitra Resmi
                </span>
                <h2 class="text-2xl font-black text-primary mt-3">Mulai Hubungkan Keahlian &amp; Usaha Anda</h2>
                <p class="text-xs text-textLight mt-1.5 max-w-md mx-auto">
                    Bergabunglah bersama jaringan teknisi profesional dan toko suku cadang terverifikasi di ekosistem REVARO.
                </p>
            </div>

            <!-- Multi-role Partner Notification Banner -->
            <div id="mitraStatusBanner" class="hidden"></div>

            <!-- Tab switch register type -->
            <div class="flex space-x-3 p-1.5 bg-accent/40 rounded-2xl border-2 border-primary/20 text-xs font-bold" id="partnerTabsWrapper">
                <button type="button" onclick="switchTab('teknisi')" id="tabTech" class="flex-grow py-2.5 rounded-xl text-center bg-primary text-white border-2 border-primary shadow-[2px_2px_0px_0px_#FF7F00] transition">
                    👨‍🔧 Daftar sebagai Teknisi
                </button>
                <button type="button" onclick="switchTab('toko-sparepart')" id="tabShop" class="flex-grow py-2.5 rounded-xl text-center text-textLight hover:text-primary transition">
                    🏪 Daftar sebagai Toko Sparepart
                </button>
            </div>

            <!-- Registration Form -->
            <form id="regForm" onsubmit="handleRegistrationSubmit(event)" class="space-y-4">
                <input type="hidden" id="roleSlugInput" value="teknisi">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label id="nameLabel" class="block text-xs font-black text-primary mb-1.5 uppercase">Nama Lengkap Teknisi</label>
                        <input required type="text" id="regName" class="revaro-input" placeholder="Joko Susanto">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-primary mb-1.5 uppercase">Alamat Email Aktif</label>
                        <input required type="email" id="regEmail" class="revaro-input font-mono" placeholder="joko@gmail.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="passwordFieldBlock">
                        <label class="block text-xs font-black text-primary mb-1.5 uppercase">Password Akun</label>
                        <div class="relative">
                            <input required type="password" id="regPassword" class="revaro-input pr-10" placeholder="Minimal 6 karakter">
                            <button type="button" onclick="togglePwdVis('regPassword','eyePartner')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                                <span id="eyePartner">
                                    <i class="far fa-eye text-primary"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div id="phoneFieldBlock">
                        <label id="phoneLabel" class="block text-xs font-black text-primary mb-1.5 uppercase">Nomor WhatsApp/HP</label>
                        <input required type="text" id="regPhone" class="revaro-input font-mono" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <!-- Conditional technician fields -->
                <div id="techFieldsBlock" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-primary mb-1.5 uppercase">Kategori Spesialisasi</label>
                            <select id="regCategory" class="revaro-input font-semibold">
                                <option value="Air Conditioner (AC)">Air Conditioner (AC)</option>
                                <option value="Lemari Es / Kulkas">Lemari Es / Kulkas</option>
                                <option value="Mesin Cuci">Mesin Cuci</option>
                                <option value="TV & Audio Visual">TV &amp; Audio Visual</option>
                                <option value="Komputer & Laptop">Komputer &amp; Laptop</option>
                                <option value="Pompa Air Rumah Tangga">Pompa Air Rumah Tangga</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-primary mb-1.5 uppercase">Sertifikat / Portofolio Keahlian (.PDF / .JPG)</label>
                            <input required type="file" id="regCert" class="revaro-input text-xs py-2">
                            <p class="text-[10px] text-textLight mt-1">Sertifikat BNSP, kursus teknisi, atau surat referensi kompetensi.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-primary mb-1.5 uppercase">Foto KTP Pemohon (.JPG / .PNG)</label>
                        <input required type="file" id="regKtp" class="revaro-input text-xs py-2">
                    </div>

                    <!-- Patokan Titik Domisili Tempat Tinggal Teknisi (Leaflet Map Picker) -->
                    <div class="p-4 rounded-xl border-2 border-primary bg-accent/20 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <label class="text-xs font-black text-primary uppercase flex items-center gap-1.5">
                                <i class="fas fa-map-marked-alt text-secondary"></i>
                                <span>Titik Patokan Domisili Tinggal (Pusat Radar Jangkauan) *</span>
                            </label>
                            <button type="button" onclick="getTechCurrentLocation()" class="text-[11px] font-black text-secondary hover:underline flex items-center gap-1">
                                <i class="fas fa-location-arrow"></i> <span>Gunakan Lokasi Domisili GPS Saya</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-textLight leading-relaxed">
                            Tentukan titik rumah atau pangkalan tempat tinggal Anda sebagai pusat patokan radar jangkauan <strong>⚡ Standar 7 KM</strong> dan <strong>👑 Prioritas 20 KM</strong> saat order masuk. <em>(Titik ini dapat Anda ubah kapan saja melalui Pengaturan Akun Teknisi).</em>
                        </p>

                        <!-- Input Alamat Domisili -->
                        <div>
                            <label class="block text-[11px] font-bold text-primary mb-1 uppercase">Alamat Domisili Tempat Tinggal</label>
                            <input required type="text" id="regTechAddress" class="revaro-input text-xs" placeholder="Contoh: Jalan Slipi IX, Slipi, Palmerah, Jakarta Barat">
                        </div>

                        <!-- Pencarian cepat lokasi -->
                        <div class="flex gap-2">
                            <input type="text" id="techSearchMapInput" placeholder="Ketik nama jalan/daerah tempat tinggal Anda..." class="revaro-input text-xs py-2 flex-grow">
                            <button type="button" onclick="searchTechLocation()" class="revaro-btn-outline text-xs px-3.5 font-bold rounded-xl whitespace-nowrap">
                                <i class="fas fa-search"></i> Cari Peta
                            </button>
                        </div>

                        <!-- Container Peta Leaflet -->
                        <div id="techDomicileMap" class="w-full h-56 rounded-xl border-2 border-primary z-0 shadow-sm"></div>

                        <!-- Koordinat terpilih info bar -->
                        <div class="flex items-center justify-between text-[11px] text-textDark font-mono bg-white px-3 py-2 rounded-lg border border-primary/30">
                            <span>Titik GPS Patokan: <strong id="techCoordDisplay" class="text-primary font-bold">-6.208800, 106.845600</strong></span>
                            <span class="text-[10px] font-black uppercase text-secondary bg-orange-100 px-2 py-0.5 rounded border border-orange-300">Pusat Radar Siap</span>
                        </div>

                        <input type="hidden" id="regLatitude" value="-6.208800">
                        <input type="hidden" id="regLongitude" value="106.845600">
                    </div>
                </div>

                <!-- Conditional shop fields (Hidden initially) -->
                <div id="shopFieldsBlock" class="hidden space-y-4">
                    <div>
                        <label class="block text-xs font-black text-primary mb-1.5 uppercase">Alamat Lengkap Toko Fisik</label>
                        <input type="text" id="regAddress" class="revaro-input" placeholder="Jl. Sukajadi No. 45, Bandung Barat">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-primary mb-1.5 uppercase">Nomor SIUP / NIB (Izin Usaha)</label>
                            <input type="text" id="regIzinUsaha" class="revaro-input font-mono" placeholder="NIB-1234-5678-90">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-primary mb-1.5 uppercase">Unggah Berkas NIB/SIUP (.PDF)</label>
                            <input type="file" id="regShopDoc" class="revaro-input text-xs py-2">
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl border-2 border-primary/20 bg-accent/30 text-xs text-textLight leading-relaxed">
                    🔒 <strong>Kebijakan Privasi &amp; Verifikasi:</strong> Berkas sertifikat kompetensi dan NIB/SIUP yang Anda lampirkan diverifikasi ketat oleh tim Verifikator REVARO. Akun Anda akan berstatus <strong>PENDING_REVIEW</strong> dan diaktivasi setelah verifikasi dinyatakan sah.
                </div>

                <button type="submit" class="w-full revaro-btn font-extrabold text-sm py-3.5 rounded-xl transition flex items-center justify-center gap-2">
                    <span>Daftar &amp; Kirim Berkas Onboarding</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t-2 border-primary text-textLight text-center py-6 mt-10">
        <div class="max-w-6xl mx-auto px-4 text-xs font-semibold">
            <p>© 2026 REVARO — Ekosistem Reparasi &amp; Restorasi Terpercaya Indonesia.</p>
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
                    regEmail.classList.add('bg-gray-100', 'cursor-not-allowed', 'text-gray-500');
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
                tabTech.className = "flex-grow py-2.5 rounded-xl text-center bg-primary text-white border-2 border-primary shadow-[2px_2px_0px_0px_#FF7F00] transition";
                tabShop.className = "flex-grow py-2.5 rounded-xl text-center text-textLight hover:text-primary transition";
                
                techBlock.classList.remove('hidden');
                shopBlock.classList.add('hidden');

                nameLabel.innerText = "Nama Lengkap Teknisi";
                phoneLabel.innerText = "Nomor WhatsApp/HP";
                regName.placeholder = "Joko Susanto";
                regPhone.placeholder = "08xxxxxxxxxx";

                regCert.required = true;
                regKtp.required = true;
                regAddress.required = false;
                regIzinUsaha.required = false;
                regShopDoc.required = false;
                document.getElementById('regTechAddress').required = true;

                // Refresh leaflet map size when switching to teknisi
                setTimeout(() => {
                    if (window.techMapInstance) {
                        window.techMapInstance.invalidateSize();
                    } else {
                        initTechDomicileMap();
                    }
                }, 200);
            } else {
                tabShop.className = "flex-grow py-2.5 rounded-xl text-center bg-primary text-white border-2 border-primary shadow-[2px_2px_0px_0px_#FF7F00] transition";
                tabTech.className = "flex-grow py-2.5 rounded-xl text-center text-textLight hover:text-primary transition";

                techBlock.classList.add('hidden');
                shopBlock.classList.remove('hidden');

                nameLabel.innerText = "Nama Resmi Toko";
                phoneLabel.innerText = "Nomor Telepon Toko";
                regName.placeholder = "Toko Sukajadi Sparepart Jaya";
                regPhone.placeholder = "08xxxxxxxxxx / 021-xxxxxxx";

                regCert.required = false;
                regKtp.required = false;
                regAddress.required = true;
                regIzinUsaha.required = true;
                regShopDoc.required = true;
                document.getElementById('regTechAddress').required = false;
            }
        }

        // --- Technician Domicile Map Picker System ---
        let techMapInstance = null;
        let techMarkerInstance = null;

        function cleanIndonesianAddress(raw) {
            if (!raw) return '';
            return raw
                .replace(/\bWest Jakarta\b/gi, 'Jakarta Barat')
                .replace(/\bCentral Jakarta\b/gi, 'Jakarta Pusat')
                .replace(/\bSouth Jakarta\b/gi, 'Jakarta Selatan')
                .replace(/\bNorth Jakarta\b/gi, 'Jakarta Utara')
                .replace(/\bEast Jakarta\b/gi, 'Jakarta Timur')
                .replace(/\bSpecial Capital Region of Jakarta\b/gi, 'DKI Jakarta')
                .replace(/\bWest Java\b/gi, 'Jawa Barat')
                .replace(/\bCentral Java\b/gi, 'Jawa Tengah')
                .replace(/\bEast Java\b/gi, 'Jawa Timur')
                .replace(/\bSpecial Region of Yogyakarta\b/gi, 'DI Yogyakarta')
                .replace(/\bNorth Sumatra\b/gi, 'Sumatera Utara')
                .replace(/\bWest Sumatra\b/gi, 'Sumatera Barat')
                .replace(/\bSouth Sumatra\b/gi, 'Sumatera Selatan')
                .replace(/\bWest Kalimantan\b/gi, 'Kalimantan Barat')
                .replace(/\bEast Kalimantan\b/gi, 'Kalimantan Timur')
                .replace(/\bSouth Kalimantan\b/gi, 'Kalimantan Selatan')
                .replace(/\bCentral Kalimantan\b/gi, 'Kalimantan Tengah')
                .replace(/\bNorth Sulawesi\b/gi, 'Sulawesi Utara')
                .replace(/\bSouth Sulawesi\b/gi, 'Sulawesi Selatan');
        }

        function initTechDomicileMap() {
            const mapEl = document.getElementById('techDomicileMap');
            if (!mapEl || typeof L === 'undefined') return;

            const initLat = parseFloat(document.getElementById('regLatitude').value) || -6.2088;
            const initLng = parseFloat(document.getElementById('regLongitude').value) || 106.8456;

            if (techMapInstance) {
                techMapInstance.remove();
            }

            techMapInstance = L.map('techDomicileMap').setView([initLat, initLng], 14);
            window.techMapInstance = techMapInstance;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(techMapInstance);

            techMarkerInstance = L.marker([initLat, initLng], { draggable: true }).addTo(techMapInstance);
            techMarkerInstance.bindPopup('<b>🏠 Titik Domisili Teknisi</b><br>Pusat radar 7 KM / 20 KM.').openPopup();

            techMarkerInstance.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                updateTechCoords(pos.lat, pos.lng);
                reverseGeocodeTech(pos.lat, pos.lng);
            });

            techMapInstance.on('click', function(e) {
                techMarkerInstance.setLatLng(e.latlng);
                updateTechCoords(e.latlng.lat, e.latlng.lng);
                reverseGeocodeTech(e.latlng.lat, e.latlng.lng);
            });
        }

        function updateTechCoords(lat, lng) {
            document.getElementById('regLatitude').value = lat.toFixed(6);
            document.getElementById('regLongitude').value = lng.toFixed(6);
            const display = document.getElementById('techCoordDisplay');
            if (display) display.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        async function reverseGeocodeTech(lat, lng) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=id`);
                const data = await res.json();
                if (data && data.display_name) {
                    const cleanName = cleanIndonesianAddress(data.display_name);
                    const addrField = document.getElementById('regTechAddress');
                    if (addrField) addrField.value = cleanName;
                }
            } catch(e) {
                console.warn('Reverse geocode error:', e);
            }
        }

        function getTechCurrentLocation() {
            if (!navigator.geolocation) {
                Swal.fire('GPS Tidak Didukung', 'Browser tidak mendukung GPS Geolocation.', 'error');
                return;
            }

            Swal.fire({
                title: 'Mendeteksi Titik GPS...',
                text: 'Mengambil koordinat domisili tempat tinggal Anda.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    if (techMapInstance && techMarkerInstance) {
                        techMapInstance.setView([lat, lng], 16);
                        techMarkerInstance.setLatLng([lat, lng]);
                    }
                    updateTechCoords(lat, lng);
                    await reverseGeocodeTech(lat, lng);
                    Swal.close();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Lokasi Domisili Diterapkan!',
                        showConfirmButton: false,
                        timer: 1800
                    });
                },
                (err) => {
                    console.error(err);
                    Swal.fire('Gagal GPS', 'Gagal mendeteksi lokasi otomatis. Silakan klik atau ketik nama lokasi di peta.', 'warning');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        async function searchTechLocation() {
            const query = document.getElementById('techSearchMapInput').value.trim();
            if (!query) {
                Swal.fire('Input Kosong', 'Ketikkan nama jalan atau daerah domisili Anda.', 'warning');
                return;
            }

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=1&accept-language=id`);
                const results = await res.json();
                if (results && results.length > 0) {
                    const item = results[0];
                    const lat = parseFloat(item.lat);
                    const lon = parseFloat(item.lon);
                    if (techMapInstance && techMarkerInstance) {
                        techMapInstance.setView([lat, lon], 16);
                        techMarkerInstance.setLatLng([lat, lon]);
                    }
                    updateTechCoords(lat, lon);
                    const cleanName = cleanIndonesianAddress(item.display_name);
                    const addrField = document.getElementById('regTechAddress');
                    if (addrField) addrField.value = cleanName;

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Lokasi Ditemukan di Peta!',
                        showConfirmButton: false,
                        timer: 1800
                    });
                } else {
                    Swal.fire('Lokasi Tidak Ditemukan', `Tidak ada hasil pencarian untuk "${query}". Coba ketik nama kelurahan atau jalan utama.`, 'info');
                }
            } catch(e) {
                console.error(e);
            }
        }

        let existingMitraState = { hasTeknisi: false, hasToko: false };

        async function checkExistingMitra() {
            const token = localStorage.getItem('token');
            if (!token) return;
            try {
                const res = await fetch('/api/auth/me', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    const user = data.user || data;
                    const roleName = user.role ? (user.role.nama || user.role) : '';
                    
                    const hasTeknisi = !!(data.has_teknisi || user.teknisi);
                    const hasToko = !!(data.has_toko || user.toko);
                    existingMitraState = { hasTeknisi, hasToko };

                    // Update header links to user dashboard
                    let userDashUrl = '/';
                    if (hasToko) userDashUrl = '/toko/dashboard';
                    else if (hasTeknisi) userDashUrl = '/teknisi/dashboard';
                    else if (roleName.toLowerCase().includes('pelanggan')) userDashUrl = '/pelanggan/dashboard';
                    
                    const headerLogoLink = document.getElementById('headerLogoLink');
                    if (headerLogoLink) headerLogoLink.href = userDashUrl;
                    const backHomeLink = document.getElementById('backHomeLink');
                    if (backHomeLink) {
                        backHomeLink.href = userDashUrl;
                        const backHomeText = document.getElementById('backHomeText');
                        if (backHomeText) backHomeText.innerText = 'Kembali ke Dasbor';
                    }

                    const tabTech = document.getElementById('tabTech');
                    const tabShop = document.getElementById('tabShop');
                    const banner = document.getElementById('mitraStatusBanner');

                    // Pre-fill user data
                    const regName = document.getElementById('regName');
                    if (regName && user.nama) regName.value = user.nama;
                    
                    const regEmail = document.getElementById('regEmail');
                    if (regEmail && user.email) {
                        regEmail.value = user.email;
                        regEmail.readOnly = true;
                        regEmail.classList.add('bg-gray-100', 'cursor-not-allowed');
                    }

                    const phoneVal = user.hp || (data.toko && data.toko.telepon) || (data.teknisi && data.teknisi.hp);
                    const regPhone = document.getElementById('regPhone');
                    if (regPhone && phoneVal) regPhone.value = phoneVal;

                    const pwdBlock = document.getElementById('passwordFieldBlock');
                    if (pwdBlock) {
                        pwdBlock.classList.add('hidden');
                        const pwdInput = document.getElementById('regPassword');
                        if (pwdInput) pwdInput.required = false;
                    }

                    if (hasToko && !hasTeknisi) {
                        // User is already a Toko Sparepart -> LOCK Toko tab, switch to Teknisi!
                        tabShop.disabled = true;
                        tabShop.classList.remove('hover:text-primary');
                        tabShop.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-100', 'border-gray-300', 'text-gray-500');
                        tabShop.innerHTML = '🏪 Toko Sparepart <span class="ml-1 text-[10px] bg-emerald-100 text-emerald-800 font-extrabold px-2 py-0.5 rounded-full border border-emerald-300">✅ Sudah Terdaftar</span>';
                        tabShop.onclick = (e) => {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'info',
                                title: 'Peran Toko Sudah Terdaftar',
                                text: 'Akun Anda sudah memiliki izin Toko Sparepart. Anda hanya dapat mendaftar sebagai Mitra Teknisi.',
                                confirmButtonColor: '#0B4A8F'
                            });
                        };

                        // Auto switch to Teknisi
                        switchTab('teknisi');

                        if (banner) {
                            banner.className = "p-4 bg-blue-50 border-2 border-primary rounded-xl text-primary space-y-1 text-xs block";
                            banner.innerHTML = `
                                <div class="font-extrabold flex items-center gap-2">
                                    <i class="fas fa-lock text-secondary"></i>
                                    <span>Pendaftaran Toko Sparepart Dikunci (Sudah Terdaftar)</span>
                                </div>
                                <p class="text-textDark">Halo <strong>${user.nama}</strong>! Akun Anda telah terdaftar sebagai <strong>Mitra Toko Sparepart</strong>. Anda dapat melengkapi formulir di bawah ini untuk mendaftar peran ganda sebagai <strong>Mitra Teknisi</strong>.</p>
                            `;
                        }

                        Swal.fire({
                            icon: 'info',
                            title: 'Akun Toko Sparepart Sudah Terdaftar',
                            html: `
                                <div class="text-left space-y-2 text-xs text-textDark">
                                    <p>Halo <strong>${user.nama}</strong>, Anda saat ini sudah memiliki profil <strong>Toko Sparepart REVARO</strong>.</p>
                                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 font-semibold">
                                        🔒 Pendaftaran Toko Sparepart telah dikunci agar tidak ganda.<br>
                                        👨‍🔧 Anda sekarang dapat mendaftar sebagai <strong>Mitra Teknisi</strong> untuk peran ganda.
                                    </div>
                                </div>
                            `,
                            confirmButtonText: '👨‍🔧 Lanjut Daftar Teknisi',
                            confirmButtonColor: '#0B4A8F',
                            showCancelButton: true,
                            cancelButtonText: '🏪 Masuk ke Dasbor Toko',
                            cancelButtonColor: '#64748B'
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href = '/toko/dashboard';
                            }
                        });

                    } else if (hasTeknisi && !hasToko) {
                        // User is already a Teknisi -> LOCK Teknisi tab, switch to Toko!
                        tabTech.disabled = true;
                        tabTech.classList.remove('hover:text-primary');
                        tabTech.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-100', 'border-gray-300', 'text-gray-500');
                        tabTech.innerHTML = '👨‍🔧 Teknisi <span class="ml-1 text-[10px] bg-emerald-100 text-emerald-800 font-extrabold px-2 py-0.5 rounded-full border border-emerald-300">✅ Sudah Terdaftar</span>';
                        tabTech.onclick = (e) => {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'info',
                                title: 'Peran Teknisi Sudah Terdaftar',
                                text: 'Akun Anda sudah memiliki izin Mitra Teknisi. Anda hanya dapat mendaftar sebagai Toko Sparepart.',
                                confirmButtonColor: '#0B4A8F'
                            });
                        };

                        // Auto switch to Toko
                        switchTab('toko-sparepart');

                        if (banner) {
                            banner.className = "p-4 bg-blue-50 border-2 border-primary rounded-xl text-primary space-y-1 text-xs block";
                            banner.innerHTML = `
                                <div class="font-extrabold flex items-center gap-2">
                                    <i class="fas fa-lock text-secondary"></i>
                                    <span>Pendaftaran Teknisi Dikunci (Sudah Terdaftar)</span>
                                </div>
                                <p class="text-textDark">Halo <strong>${user.nama}</strong>! Akun Anda telah terdaftar sebagai <strong>Mitra Teknisi</strong>. Anda dapat melengkapi formulir di bawah ini untuk mendaftar peran ganda sebagai <strong>Toko Sparepart</strong>.</p>
                            `;
                        }

                        Swal.fire({
                            icon: 'info',
                            title: 'Akun Teknisi Sudah Terdaftar',
                            html: `
                                <div class="text-left space-y-2 text-xs text-textDark">
                                    <p>Halo <strong>${user.nama}</strong>, Anda saat ini sudah memiliki profil <strong>Teknisi REVARO</strong>.</p>
                                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 font-semibold">
                                        🔒 Pendaftaran Teknisi telah dikunci agar tidak ganda.<br>
                                        🏪 Anda sekarang dapat mendaftar sebagai <strong>Toko Sparepart</strong> untuk peran ganda.
                                    </div>
                                </div>
                            `,
                            confirmButtonText: '🏪 Lanjut Daftar Toko Sparepart',
                            confirmButtonColor: '#0B4A8F',
                            showCancelButton: true,
                            cancelButtonText: '👨‍🔧 Masuk ke Dasbor Teknisi',
                            cancelButtonColor: '#64748B'
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href = '/teknisi/dashboard';
                            }
                        });

                    } else if (hasTeknisi && hasToko) {
                        // User has BOTH roles -> LOCK BOTH tabs!
                        tabTech.disabled = true;
                        tabTech.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-100');
                        tabShop.disabled = true;
                        tabShop.classList.add('opacity-60', 'cursor-not-allowed', 'bg-gray-100');

                        const regForm = document.getElementById('regForm');
                        if (regForm) regForm.classList.add('hidden');

                        if (banner) {
                            banner.className = "p-6 bg-emerald-50 border-2 border-emerald-500 rounded-2xl text-center space-y-4 block";
                            banner.innerHTML = `
                                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-emerald-900">Akun Anda Telah Terdaftar Lengkap!</h3>
                                    <p class="text-xs text-emerald-700 mt-1">Anda sudah memiliki peran ganda sebagai <strong>Mitra Teknisi</strong> dan <strong>Toko Sparepart</strong> di ekosistem REVARO.</p>
                                </div>
                                <div class="flex justify-center gap-3 pt-2">
                                    <a href="/teknisi/dashboard" class="revaro-btn text-xs px-4 py-2 rounded-xl font-bold">👨‍🔧 Buka Dasbor Teknisi</a>
                                    <a href="/toko/dashboard" class="revaro-btn-orange text-xs px-4 py-2 rounded-xl font-bold">🏪 Buka Dasbor Toko</a>
                                </div>
                            `;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Pendaftaran Lengkap',
                            text: 'Akun Anda sudah terdaftar sebagai Mitra Teknisi dan Toko Sparepart di REVARO.',
                            confirmButtonText: 'Buka Dasbor',
                            confirmButtonColor: '#0B4A8F'
                        }).then(() => {
                            window.location.href = '/teknisi/dashboard';
                        });
                    }
                }
            } catch(e) {
                console.error(e);
            }
        }

        window.addEventListener('load', () => {
            initTechDomicileMap();
            checkExistingMitra();
        });

        async function handleRegistrationSubmit(e) {
            e.preventDefault();

            const role_slug = document.getElementById('roleSlugInput').value;

            if (role_slug === 'toko-sparepart' && existingMitraState.hasToko) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pendaftaran Ditolak',
                    text: 'Anda sudah terdaftar sebagai Mitra Toko Sparepart. Anda hanya dapat mendaftar sebagai Teknisi.',
                    confirmButtonColor: '#0B4A8F'
                });
                return;
            }
            if (role_slug === 'teknisi' && existingMitraState.hasTeknisi) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pendaftaran Ditolak',
                    text: 'Anda sudah terdaftar sebagai Mitra Teknisi. Anda hanya dapat mendaftar sebagai Toko Sparepart.',
                    confirmButtonColor: '#0B4A8F'
                });
                return;
            }

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
                formData.append('latitude', document.getElementById('regLatitude').value || -6.2088);
                formData.append('longitude', document.getElementById('regLongitude').value || 106.8456);
                formData.append('alamat', document.getElementById('regTechAddress').value);

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

                if (response.status === 429) {
                    window.location.href = '/error/429';
                    return;
                }

                const result = await response.json();
                if (!response.ok) {
                    Swal.fire({
                        title: 'Gagal',
                        text: result.message || JSON.stringify(result.errors),
                        icon: 'error',
                        confirmButtonColor: '#0B4A8F'
                    });
                    return;
                }

                // Jangan izinkan masuk dashboard otomatis sebelum akun disetujui (ACC) oleh Admin Verifikator!
                const existingToken = localStorage.getItem('token');
                
                // Jika pendaftaran baru, bersihkan token agar tidak ada auto-login ke dashboard
                if (!existingToken) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    localStorage.removeItem('profile');
                }

                Swal.fire({
                    icon: 'info',
                    title: 'Pendaftaran Berhasil Dikirim! ⏳',
                    html: `
                        <div class="text-left space-y-3 text-xs text-textDark">
                            <p>Terima kasih telah mendaftar kemitraan <strong>${role_slug === 'teknisi' ? 'Mitra Teknisi' : 'Toko Sparepart'} REVARO</strong>.</p>
                            <div class="p-3.5 bg-amber-50 border-2 border-amber-300 rounded-xl text-amber-900 leading-relaxed font-semibold">
                                ⏳ <strong>Status: Menunggu Persetujuan Admin Verifikator (Pending)</strong><br>
                                Data pendaftaran Anda telah berhasil dikirim ke antrean verifikasi. Sesuai prosedur, akun Anda <u>harus di-ACC (disetujui)</u> terlebih dahulu oleh <strong>Admin Verifikator</strong> sebelum Anda dapat masuk ke dasbor kerja.
                            </div>
                            <p class="text-slate-500">Silakan tunggu proses verifikasi dokumen oleh tim kami. Anda akan dapat masuk setelah akun resmi disetujui.</p>
                        </div>
                    `,
                    confirmButtonText: 'Kembali ke Beranda',
                    confirmButtonColor: '#0B4A8F',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = '/?stay=1';
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    title: 'Gagal',
                    text: 'Gagal menghubungi server pendaftaran.',
                    icon: 'error',
                    confirmButtonColor: '#0B4A8F'
                });
            }
        }
    </script>
</body>
</html>
