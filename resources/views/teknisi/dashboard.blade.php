<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard — REVARO</title>
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
        .shimmer {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 106, 0, 0.2);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 106, 0, 0.4);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="glass-nav text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-2.5">
                <div class="bg-brandOrange p-2 rounded-xl font-extrabold text-sm tracking-widest text-white shadow-sm">
                    REV
                </div>
                <div>
                    <h1 class="text-lg font-extrabold tracking-tight">REVARO</h1>
                    <p class="text-[10px] text-blue-200 font-medium">Technician Mobile Console</p>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center space-x-6">
                <a href="/teknisi/dashboard" class="text-sm font-semibold text-brandOrange transition">Dashboard</a>
            </nav>

            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <span id="navUserName" class="text-xs font-bold bg-white/10 px-3 py-1.5 rounded-full border border-white/20">Teknisi</span>
                    <button onclick="handleLogout()" class="bg-red-500/80 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-6xl mx-auto w-full px-4 py-8">
        
        <!-- Document Onboarding Verifications Notices -->
        <div id="verifStatusBanner" class="hidden mb-6">
            <!-- Notices injection -->
        </div>

        <div id="dashboardMainGrid" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Available Jobs & Active Job Tracker (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Active Job Tracker -->
                <div id="activeJobPanel" class="hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-6">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                            <span id="activeJobBadge" class="bg-brandOrange text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">SEDANG BERJALAN</span>
                            <h3 id="activeJobCode" class="text-base font-extrabold text-brandBlue mt-1.5">REV-ORDER-0000</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] text-slate-400 font-bold uppercase">Biaya Jasa Servis</p>
                            <p class="text-sm font-black text-brandOrange">Rp 100.000</p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-2 text-xs">
                        <p><strong>Nama Pelanggan:</strong> <span id="activeCustomerName">—</span></p>
                        <p><strong>Nomor HP Pelanggan:</strong> <span id="activeCustomerHP">—</span></p>
                        <p><strong>Alamat Lokasi:</strong> <span id="activeCustomerAddress">—</span></p>
                        <p><strong>Keluhan Kerusakan:</strong> <span id="activeCustomerComplaint" class="text-slate-500">—</span></p>
                    </div>

                    <!-- Steps Timeline / Actions -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-3">
                        <h4 class="text-xs font-bold text-slate-800">Ubah Tahapan Pengerjaan</h4>
                        <div class="flex flex-wrap gap-2" id="jobActionSteps">
                            <!-- Action buttons -->
                        </div>
                    </div>

                    <!-- Request Spareparts Form -->
                    <div id="techSparepartsRequestBlock" class="hidden bg-slate-50 p-6 rounded-xl border border-slate-100 space-y-4">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">📦 Ajukan Suku Cadang Tambahan</h4>
                        <p class="text-[10px] text-slate-400">Pilih sparepart yang dibutuhkan dari toko di sekitar lokasi Anda:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div class="md:col-span-2">
                                <select id="sparepartSelector" class="w-full text-xs bg-white border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue">
                                    <option value="">— Pilih Suku Cadang —</option>
                                    <!-- Populated via API search -->
                                </select>
                            </div>
                            <div>
                                <input type="number" id="sparepartQty" class="w-full text-xs bg-white border border-slate-200 rounded-lg p-2.5 outline-none focus:border-brandBlue" value="1" min="1">
                            </div>
                        </div>

                        <button onclick="addSparepartToList()" class="w-full bg-brandBlue hover:bg-blue-800 text-white text-xs font-bold py-2 rounded-lg transition">+ Tambah Suku Cadang</button>

                        <div class="overflow-x-auto pt-2">
                            <table class="w-full text-xs text-left">
                                <thead>
                                    <tr class="text-slate-500 border-b border-slate-200 text-[10px] uppercase font-bold">
                                        <th class="pb-1.5">Nama Barang</th>
                                        <th class="pb-1.5 text-center">Qty</th>
                                        <th class="pb-1.5 text-right">Harga</th>
                                        <th class="pb-1.5 text-right">Subtotal</th>
                                        <th class="pb-1.5 text-right">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody id="requestedSparepartsListBody" class="divide-y divide-slate-100">
                                    <!-- Items -->
                                </tbody>
                            </table>
                        </div>

                        <button onclick="submitSparepartsRequest()" class="w-full bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold py-3 rounded-lg transition shadow">
                            Kirim Pengajuan Suku Cadang ke Pelanggan
                        </button>
                    </div>

                    <!-- Chat with Customer -->
                    <div id="techChatBoxBlock" class="border border-slate-100 rounded-xl overflow-hidden flex flex-col h-48 bg-slate-50">
                        <div class="bg-brandBlue text-white px-4 py-2 flex justify-between items-center text-xs">
                            <span class="font-bold">💬 Chat Instan Dengan Pelanggan</span>
                            <span class="text-[10px] text-emerald-400 font-bold flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-ping"></span>
                                <span>Online</span>
                            </span>
                        </div>
                        <div id="techChatMessagesBox" class="flex-grow p-3 overflow-y-auto space-y-2 text-xs">
                            <!-- Chats here -->
                        </div>
                        <form onsubmit="sendTechChatMessage(event)" class="border-t border-slate-200 flex p-1 bg-white">
                            <input id="techChatInput" type="text" class="flex-grow text-xs px-3 outline-none" placeholder="Ketik pesan kepada pelanggan...">
                            <button type="submit" class="bg-brandBlue hover:bg-blue-800 text-white text-xs px-4 py-2 rounded-lg font-bold">Kirim</button>
                        </form>
                    </div>

                    <button onclick="cancelOrder()" class="text-red-500 hover:text-red-600 text-xs font-bold transition">Batalkan Pesanan Sepihak (Penalti WARNING)</button>
                </div>

                <!-- Available Jobs Feed -->
                <div id="jobsFeedPanel" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-extrabold text-brandBlue flex items-center space-x-2">
                                <span>🚀 Feed Order Servis SOS Tersedia</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftar order cepat di sekitar wilayah jangkauan Anda yang siap dikerjakan.</p>
                        </div>
                        <button onclick="loadAvailableJobs()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-3 py-1.5 rounded-xl font-bold transition">🔄 Refresh</button>
                    </div>

                    <div id="jobsListContainer" class="space-y-4">
                        <p class="text-slate-400 text-center py-8">Memuat daftar order...</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Wallet & Profile Info (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- User Profile info card -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 text-center space-y-3">
                    <div id="techProfileCard" class="space-y-3">
                        <div class="w-12 h-12 bg-orange-100 text-brandOrange rounded-full flex items-center justify-center mx-auto text-lg font-black">
                            T
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Teknisi REVARO</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Memuat profil...</p>
                        </div>
                    </div>
                </div>

                <!-- Wallet Balance & Penarikan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <h4 class="text-xs font-black text-brandBlue uppercase tracking-wider">Kelola Saldo Dompet Pendapatan</h4>
                    <div id="techSaldoBlock" class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center">
                        <p class="text-[9px] uppercase font-bold text-slate-400">Saldo Pendapatan Saya</p>
                        <p id="techSaldoValue" class="text-lg font-black text-brandBlue">Rp 0</p>
                    </div>
                    <button onclick="triggerWithdraw()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2.5 rounded-xl font-bold transition">🏧 Tarik Pendapatan ke Bank</button>
                </div>

                <!-- Wallet transactions list -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <h4 class="text-xs font-black text-brandBlue uppercase tracking-wider">Mutasi Rekening & Cair</h4>
                    <div id="techLedgerHistory" class="space-y-3 max-h-64 overflow-y-auto pr-1 text-xs">
                        <p class="text-slate-400 text-center py-4">Belum ada mutasi masuk/keluar</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Client-Side State Controller Logic -->
    <script>
        const token = localStorage.getItem('token');
        let currentActiveJob = null;
        let selectedSpareparts = [];
        let globalTechProfile = null;
        let activeJobPollInterval = null;

        if (!token) {
            alert('Akses dibatasi. Silakan login.');
            window.location.href = '/';
        }

        window.addEventListener('load', () => {
            fetchProfile();
            loadWalletLedger();
            checkActiveJob();
            loadAvailableJobs();
            loadSparepartsList();
            
            activeJobPollInterval = setInterval(() => {
                checkActiveJob();
                if (!currentActiveJob) {
                    loadAvailableJobs();
                }
            }, 5000);
        });

        async function fetchProfile() {
            try {
                const res = await fetch('/api/auth/me', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                if (res.ok) {
                    const user = d.user;
                    const profile = d.profile;
                    globalTechProfile = profile;

                    // Sync session details
                    localStorage.setItem('user', JSON.stringify(user));
                    localStorage.setItem('profile', JSON.stringify(profile));

                    // Redirection check based on role
                    const roleName = user.role ? user.role.nama : '';
                    if (roleName === 'Pelanggan') {
                        window.location.href = '/pelanggan/dashboard';
                        return;
                    } else if (roleName === 'Toko Sparepart') {
                        window.location.href = '/toko/dashboard';
                        return;
                    }

                    document.getElementById('navUserName').innerText = user.nama;
                    document.getElementById('techProfileCard').innerHTML = `
                        <div class="w-12 h-12 bg-orange-100 text-brandOrange rounded-full flex items-center justify-center mx-auto text-lg font-black">
                            ${user.nama.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">${user.nama}</h4>
                            <p class="text-[10px] text-brandOrange font-bold mt-0.5">Spesialisasi: ${profile ? profile.kategori : 'Teknisi'}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Tier: ${profile ? profile.tier : 'Bronze'} | Rating: ⭐️ ${profile ? parseFloat(profile.rating).toFixed(1) : '0.0'}</p>
                        </div>
                    `;

                    document.getElementById('techSaldoValue').innerText = 'Rp ' + (profile ? parseFloat(profile.saldo).toLocaleString('id-ID') : '0');

                    // Check status PENDING_REVIEW / NEEDS_REVISION
                    const banner = document.getElementById('verifStatusBanner');
                    const mainGrid = document.getElementById('dashboardMainGrid');

                    if (profile && profile.status === 'PENDING_REVIEW') {
                        banner.classList.remove('hidden');
                        banner.innerHTML = `
                            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center space-y-3">
                                <span class="text-3xl">⏳</span>
                                <h3 class="text-sm font-black text-yellow-800">Akun Anda Dalam Peninjauan Verifikasi</h3>
                                <p class="text-xs text-yellow-700 max-w-lg mx-auto">Dokumen pendaftaran kompetensi, KTP, dan status Anda sedang diantrekan ke antrean peninjauan Admin Verifikator. Harap bersabar menunggu pengerjaan selesai.</p>
                            </div>
                        `;
                        mainGrid.classList.add('hidden');
                    } else if (profile && profile.status === 'NEEDS_REVISION') {
                        // Retrieve latest verification notes
                        banner.classList.remove('hidden');
                        mainGrid.classList.add('hidden');
                        
                        banner.innerHTML = `
                            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 space-y-4">
                                <div class="flex items-center space-x-2 text-red-600 font-extrabold text-sm">
                                    <span>📝</span> <span>Dokumen Anda Perlu Direvisi</span>
                                </div>
                                <div class="p-3 bg-red-100/50 border border-red-200 text-xs text-red-700 rounded-xl leading-normal">
                                    <strong>Catatan Admin Verifikator:</strong> <span id="revisionNotes">Dokumen KTP buram, harap unggah ulang.</span>
                                </div>
                                
                                <form id="resubmitForm" onsubmit="handleResubmit(event)" class="space-y-3 text-xs max-w-xl">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Nomor WhatsApp/HP</label>
                                            <input required type="text" id="resubPhone" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 outline-none" value="${profile.hp}">
                                        </div>
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Kategori Spesialisasi</label>
                                            <select required id="resubCategory" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 outline-none">
                                                <option value="AC Pilihan">Air Conditioner (AC)</option>
                                                <option value="Mesin Cuci">Mesin Cuci</option>
                                                <option value="Kulkas">Lemari Es / Kulkas</option>
                                                <option value="Pompa Air">Pompa Air</option>
                                                <option value="TV & Audio">TV & Perangkat Audio</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Unggah Sertifikat Baru (.PDF/.JPG)</label>
                                            <input type="file" id="resubCert" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-[10px]">
                                        </div>
                                        <div>
                                            <label class="block text-slate-600 font-semibold mb-1">Unggah KTP Baru (.JPG/.PNG)</label>
                                            <input type="file" id="resubKtp" class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-[10px]">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold py-2.5 rounded-lg transition">Kirim Ulang Dokumen Onboarding</button>
                                </form>
                            </div>
                        `;
                        // Fetch latest verification record notes
                        fetchRevisionNotes(profile.id);
                    } else {
                        banner.classList.add('hidden');
                        mainGrid.classList.remove('hidden');
                    }
                }
            } catch (err) { console.error(err); }
        }

        async function fetchRevisionNotes(techId) {
            try {
                // Read verification notes via audit trace or endpoint if any
                const res = await fetch('/api/admin/verifications', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                if (res.ok) {
                    const myVerif = data.find(v => v.teknisi_id === techId && v.status === 'NEEDS_REVISION');
                    if (myVerif && myVerif.catatan) {
                        document.getElementById('revisionNotes').innerText = myVerif.catatan;
                    }
                }
            } catch (e) {}
        }

        async function handleResubmit(e) {
            e.preventDefault();
            const hp = document.getElementById('resubPhone').value;
            const kategori = document.getElementById('resubCategory').value;
            
            const formData = new FormData();
            formData.append('hp', hp);
            formData.append('kategori', kategori);
            formData.append('latitude', -6.2088);
            formData.append('longitude', 106.8456);

            const cert = document.getElementById('resubCert').files[0];
            if (cert) formData.append('sertifikat', cert);

            const ktp = document.getElementById('resubKtp').files[0];
            if (ktp) formData.append('ktp', ktp);

            try {
                const res = await fetch('/api/profile/resubmit', {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });
                const result = await res.json();
                if (res.ok) {
                    Swal.fire('Sukses!', 'Dokumen revisi Anda berhasil diajukan ulang.', 'success');
                    fetchProfile();
                } else {
                    Swal.fire('Gagal', result.message || JSON.stringify(result.errors), 'error');
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
                    const list = document.getElementById('techLedgerHistory');
                    if (data.transactions.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-4">Belum ada mutasi</p>';
                    } else {
                        list.innerHTML = data.transactions.map(t => {
                            const badge = t.tipe === 'IN' ? 'text-emerald-500 font-bold' : 'text-red-500 font-bold';
                            const prefix = t.tipe === 'IN' ? '+' : '-';
                            return `
                                <div class="p-2 bg-slate-50 border border-slate-100 rounded-lg space-y-1">
                                    <div class="flex justify-between">
                                        <span class="font-bold">${t.deskripsi}</span>
                                        <span class="${badge}">${prefix} Rp ${parseFloat(t.jumlah).toLocaleString('id-ID')}</span>
                                    </div>
                                    <p class="text-[9px] text-slate-400 font-mono">${new Date(t.created_at).toLocaleString('id-ID')}</p>
                                </div>
                            `;
                        }).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function triggerWithdraw() {
            const { value: formValues } = await Swal.fire({
                title: 'Tarik Pendapatan',
                html:
                    '<input id="swal-amount" class="swal2-input" placeholder="Jumlah penarikan (Min Rp 10.000)" type="number">' +
                    '<input id="swal-bank" class="swal2-input" placeholder="Nama Bank (e.g. BCA, Mandiri)">' +
                    '<input id="swal-norek" class="swal2-input" placeholder="Nomor Rekening">',
                focusConfirm: false,
                confirmButtonColor: '#FF6A00',
                showCancelButton: true,
                preConfirm: () => {
                    return [
                        document.getElementById('swal-amount').value,
                        document.getElementById('swal-bank').value,
                        document.getElementById('swal-norek').value
                    ]
                }
            });

            if (formValues) {
                const [jumlah, bank, no_rekening] = formValues;
                if (!jumlah || !bank || !no_rekening) {
                    Swal.fire('Error', 'Semua form penarikan wajib diisi.', 'error');
                    return;
                }

                try {
                    const res = await fetch('/api/wallet/withdraw', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ jumlah, bank, no_rekening })
                    });
                    const result = await res.json();
                    if (res.ok) {
                        Swal.fire('Berhasil!', result.message, 'success');
                        fetchProfile();
                        loadWalletLedger();
                    } else {
                        Swal.fire('Gagal', result.message || 'Saldo tidak mencukupi.', 'error');
                    }
                } catch (e) { console.error(e); }
            }
        }

        async function checkActiveJob() {
            try {
                const res = await fetch('/api/orders', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                
                const active = d.orders.find(o => o.status !== 'COMPLETED' && o.status !== 'CANCELLED');
                if (active) {
                    currentActiveJob = active;
                    showActiveJobPanel(active);
                    fetchChats();
                } else {
                    currentActiveJob = null;
                    document.getElementById('activeJobPanel').classList.add('hidden');
                    document.getElementById('jobsFeedPanel').classList.remove('hidden');
                }
            } catch (e) { console.error(e); }
        }

        function showActiveJobPanel(order) {
            document.getElementById('jobsFeedPanel').classList.add('hidden');
            document.getElementById('activeJobPanel').classList.remove('hidden');
            document.getElementById('activeJobCode').innerText = order.kode_order;
            document.getElementById('activeJobBadge').innerText = order.status;

            if (order.pelanggan) {
                document.getElementById('activeCustomerName').innerText = order.pelanggan.nama;
                document.getElementById('activeCustomerHP').innerText = order.pelanggan.hp;
                document.getElementById('activeCustomerAddress').innerText = order.alamat_servis;
                document.getElementById('activeCustomerComplaint').innerText = order.keluhan;
            }

            const stepActions = document.getElementById('jobActionSteps');
            const sparepartsBlock = document.getElementById('techSparepartsRequestBlock');

            stepActions.innerHTML = '';
            sparepartsBlock.classList.add('hidden');

            if (order.status === 'DISPATCHED') {
                stepActions.innerHTML = `<button onclick="changeJobStatus('ontheway')" class="bg-brandBlue text-white font-bold py-2.5 px-4 rounded-xl text-xs hover:bg-blue-800 transition">🚗 Mulai Perjalanan (OTW)</button>`;
            } else if (order.status === 'ON_THE_WAY') {
                stepActions.innerHTML = `<button onclick="changeJobStatus('arrive')" class="bg-brandOrange text-white font-bold py-2.5 px-4 rounded-xl text-xs hover:bg-orange-600 transition">📍 Tiba di Lokasi & Diagnosa</button>`;
            } else if (order.status === 'IN_PROGRESS') {
                sparepartsBlock.classList.remove('hidden');
                stepActions.innerHTML = `<button onclick="changeJobStatus('complete')" class="bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-xl text-xs hover:bg-emerald-700 transition">✅ Selesaikan Perbaikan (Terbitkan Nota)</button>`;
                renderRequestedSparepartsList(order.spareparts || []);
            } else if (order.status === 'AWAITING_PAYMENT') {
                stepActions.innerHTML = `<p class="text-xs text-orange-600 font-bold">Menunggu pembayaran faktur dari Pelanggan...</p>`;
            }
        }

        async function changeJobStatus(endpoint) {
            if (!currentActiveJob) return;
            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire('Status Diperbarui', d.message, 'success');
                    checkActiveJob();
                } else {
                    Swal.fire('Gagal', d.message || 'Gagal merubah status.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        async function loadAvailableJobs() {
            try {
                const res = await fetch('/api/orders/available', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const d = await res.json();
                if (res.ok) {
                    const list = document.getElementById('jobsListContainer');
                    const orders = d.orders || [];
                    if (orders.length === 0) {
                        list.innerHTML = '<p class="text-slate-400 text-center py-8">Tidak ada order SOS yang masuk saat ini...</p>';
                    } else {
                        list.innerHTML = orders.map(o => `
                            <div class="p-5 border border-slate-200 rounded-2xl hover:border-brandOrange transition bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-red-100 text-red-800 text-[9px] font-black uppercase px-2 py-0.5 rounded border border-red-200">SOS REQUEST</span>
                                        <span class="text-xs text-slate-400 font-mono font-bold">${o.kode_order}</span>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-800">${o.pelanggan ? o.pelanggan.nama : 'Pelanggan'}</h4>
                                    <p class="text-xs text-slate-500"><strong>Alamat:</strong> ${o.alamat_servis}</p>
                                    <p class="text-xs text-slate-500 italic">"${o.keluhan}"</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase pt-1">Estimasi Pendapatan: Rp 100.000</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="acceptJob(${o.id})" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-2 px-5 rounded-xl transition shadow">Terima Kerja</button>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            } catch (e) { console.error(e); }
        }

        async function acceptJob(id) {
            const confirmAccept = await Swal.fire({
                title: 'Terima Pekerjaan Ini?',
                text: 'Pastikan Anda siap berangkat langsung ke lokasi pelanggan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6A00',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Terima!'
            });

            if (confirmAccept.isConfirmed) {
                try {
                    const res = await fetch(`/api/orders/${id}/accept`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const d = await res.json();
                    if (res.ok) {
                        Swal.fire('Diterima!', d.message, 'success');
                        checkActiveJob();
                    } else {
                        Swal.fire('Gagal Menerima', d.message || 'Pekerjaan telah diambil teknisi lain.', 'error');
                        loadAvailableJobs();
                    }
                } catch (e) { console.error(e); }
            }
        }

        async function loadSparepartsList() {
            try {
                // Load catalog spareparts search
                const res = await fetch('/api/spareparts/search', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                const selector = document.getElementById('sparepartSelector');
                selector.innerHTML = '<option value="">— Pilih Suku Cadang —</option>';
                data.forEach(item => {
                    selector.innerHTML += `<option value="${item.id}">${item.nama} - Rp ${parseFloat(item.harga).toLocaleString('id-ID')} (${item.merk}) [Toko: ${item.toko ? item.toko.nama : 'Mitra'}]</option>`;
                });
            } catch (e) { console.error(e); }
        }

        function addSparepartToList() {
            const select = document.getElementById('sparepartSelector');
            const qty = parseInt(document.getElementById('sparepartQty').value) || 1;
            const val = select.value;
            if (!val) return;

            // Mock get details
            const optText = select.options[select.selectedIndex].text;
            const parts = optText.split(' - Rp ');
            const nama = parts[0];
            const priceParts = parts[1].split(' (');
            const harga = parseFloat(priceParts[0].replace(/\./g, ''));
            
            selectedSpareparts.push({
                sparepart_id: parseInt(val),
                nama: nama,
                qty: qty,
                harga: harga,
                subtotal: harga * qty
            });

            renderLocalSpareparts();
        }

        function removeSparepartLocal(index) {
            selectedSpareparts.splice(index, 1);
            renderLocalSpareparts();
        }

        function renderLocalSpareparts() {
            const tbody = document.getElementById('requestedSparepartsListBody');
            tbody.innerHTML = selectedSpareparts.map((p, idx) => `
                <tr>
                    <td class="py-1">${p.nama}</td>
                    <td class="py-1 text-center">${p.qty}</td>
                    <td class="py-1 text-right">Rp ${p.harga.toLocaleString('id-ID')}</td>
                    <td class="py-1 text-right">Rp ${p.subtotal.toLocaleString('id-ID')}</td>
                    <td class="py-1 text-right">
                        <button onclick="removeSparepartLocal(${idx})" class="text-red-500 font-bold hover:text-red-600">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        function renderRequestedSparepartsList(list) {
            if (selectedSpareparts.length > 0) return; // Don't override local draft
            const tbody = document.getElementById('requestedSparepartsListBody');
            tbody.innerHTML = list.map(p => `
                <tr>
                    <td class="py-1">${p.nama}</td>
                    <td class="py-1 text-center">${p.pivot.qty}</td>
                    <td class="py-1 text-right">Rp ${parseFloat(p.pivot.harga).toLocaleString('id-ID')}</td>
                    <td class="py-1 text-right">Rp ${parseFloat(p.pivot.subtotal).toLocaleString('id-ID')}</td>
                    <td class="py-1 text-right text-slate-400">
                        ${p.pivot.status}
                    </td>
                </tr>
            `).join('');
        }

        async function submitSparepartsRequest() {
            if (selectedSpareparts.length === 0 || !currentActiveJob) {
                Swal.fire('Form Kosong', 'Tambahkan minimal 1 sparepart ke dalam draf pengajuan.', 'warning');
                return;
            }

            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/spareparts`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ spareparts: selectedSpareparts })
                });
                const d = await res.json();
                if (res.ok) {
                    Swal.fire('Dikirim!', d.message, 'success');
                    selectedSpareparts = [];
                    checkActiveJob();
                } else {
                    Swal.fire('Gagal', d.message || 'Terjadi kesalahan.', 'error');
                }
            } catch (e) { console.error(e); }
        }

        async function fetchChats() {
            if (!currentActiveJob) return;
            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/chats`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const messages = await res.json();
                const box = document.getElementById('techChatMessagesBox');
                
                const currentUserRaw = localStorage.getItem('user');
                const currentUserId = currentUserRaw ? JSON.parse(currentUserRaw).id : null;

                box.innerHTML = messages.map(m => {
                    const isMe = m.dari_user_id === currentUserId;
                    const alignment = isMe ? 'text-right' : 'text-left';
                    const bubbleBg = isMe ? 'bg-brandOrange text-white ml-auto' : 'bg-slate-200 text-slate-800 mr-auto';
                    return `
                        <div class="${alignment}">
                            <div class="inline-block p-2 rounded-xl text-xs max-w-xs ${bubbleBg}">
                                ${m.pesan}
                            </div>
                        </div>
                    `;
                }).join('');
                box.scrollTop = box.scrollHeight;
            } catch (err) { console.error(err); }
        }

        async function sendTechChatMessage(e) {
            e.preventDefault();
            const input = document.getElementById('techChatInput');
            const msg = input.value.trim();
            if (!msg || !currentActiveJob) return;

            try {
                const res = await fetch(`/api/orders/${currentActiveJob.id}/chats`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ pesan: msg })
                });
                if (res.ok) {
                    input.value = '';
                    fetchChats();
                }
            } catch (err) { console.error(err); }
        }

        async function cancelOrder() {
            if (!currentActiveJob) return;
            const confirmCancel = await Swal.fire({
                title: 'Batalkan Pesanan Sepihak?',
                text: 'Tindakan ini akan memicu penalti WARNING dan dicatat ke log pelanggaran Anda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Batalkan'
            });

            if (confirmCancel.isConfirmed) {
                try {
                    const res = await fetch(`/api/orders/${currentActiveJob.id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    const d = await res.json();
                    if (res.ok) {
                        Swal.fire('Dibatalkan!', d.message, 'success');
                        checkActiveJob();
                    } else {
                        Swal.fire('Gagal', d.message, 'error');
                    }
                } catch (e) { console.error(e); }
            }
        }

        function handleLogout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('profile');
            window.location.href = '/';
        }
    </script>
</body>
</html>
