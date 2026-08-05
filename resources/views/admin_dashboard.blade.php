<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsol Admin REVARO — Manajemen Ekosistem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#0A4C8A',
                        brandOrange: '#FF6A00',
                        darkBg: '#0F172A',
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
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-100 flex flex-col justify-between">

    <!-- Navbar -->
    <header class="glass-nav text-white sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-brandOrange p-2 rounded-xl font-extrabold text-xs tracking-widest text-white shadow-md">
                    REV
                </div>
                <div>
                    <h1 class="text-sm font-black tracking-tight text-white">REVARO KONSOL</h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Dashboard Panel</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p id="adminNavName" class="text-xs font-bold text-white"></p>
                    <p id="adminNavRole" class="text-[9px] text-brandOrange font-bold uppercase tracking-wider"></p>
                </div>
                <button onclick="handleAdminLogout()" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3.5 py-2 rounded-xl font-bold transition shadow shadow-red-700/10">
                    Logout
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 py-8">
        
        <!-- Dynamic Dashboards Panel Wrapper -->
        <div id="dashboardWrapper" class="space-y-8">
            
            <!-- PANEL 1: Super Admin Pemilik (Owner) -->
            <div id="panelOwner" class="hidden space-y-6 animate-fade">
                <div class="border-b border-slate-800 pb-3 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-black text-white">👑 Super Admin Pemilik Dashboard</h2>
                        <p class="text-xs text-slate-400 mt-1">Kelola akun internal administrator dan audit aktivitas ekosistem.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Form Tambah Admin (5 Cols) -->
                    <div class="lg:col-span-5 bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                        <h3 class="text-sm font-extrabold text-white">➕ Tambah Akun Administrator Baru</h3>
                        <form onsubmit="handleCreateAdmin(event)" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Lengkap</label>
                                <input required type="text" id="newAdminName" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Contoh: Admin Verifikator C">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Alamat Email</label>
                                <input required type="email" id="newAdminEmail" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="verifC@revaro.com">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Password</label>
                                <input required type="password" id="newAdminPassword" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Minimal 6 karakter">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Peran Akses (Role)</label>
                                <select required id="newAdminRole" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition">
                                    <option value="admin-verifikator">Admin Verifikator (Gatekeeper)</option>
                                    <option value="admin-resolusi">Admin Resolusi (Penengah Sengketa)</option>
                                    <option value="super-admin-monitoring">Super Admin (Monitoring Kinerja)</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3 rounded-xl transition">
                                Daftarkan Akun Admin
                            </button>
                        </form>
                    </div>

                    <!-- Audit Trail / Admin Workloads (7 Cols) -->
                    <div class="lg:col-span-7 bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                        <h3 class="text-sm font-extrabold text-white">📋 Log Audit Aktivitas Manajemen</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-700 text-slate-400 font-bold">
                                        <th class="py-2.5">User</th>
                                        <th class="py-2.5">Aktivitas Audit</th>
                                        <th class="py-2.5 text-right">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody id="ownerAuditLogs" class="divide-y divide-slate-800 text-slate-300">
                                    <!-- Rendered dynamically -->
                                    <tr>
                                        <td class="py-2.5 text-slate-400" colspan="3">Memuat aktivitas log...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL 2: Admin Verifikator (Gatekeeper) -->
            <div id="panelVerifikator" class="hidden space-y-6 animate-fade">
                <div>
                    <h2 class="text-xl font-black text-white">🔎 Admin Verifikator Dashboard</h2>
                    <p class="text-xs text-slate-400 mt-1">Tinjau antrean pendaftaran dokumen sertifikat Teknisi dan Toko Sparepart.</p>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Antrean Teknisi -->
                    <div class="bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                        <h3 class="text-sm font-extrabold text-white flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span>Aplikasi Pendaftaran Teknisi (Pending)</span>
                        </h3>
                        <div id="techVerifQueue" class="space-y-4 overflow-y-auto max-h-[500px]">
                            <p class="text-xs text-slate-500">Memuat antrean...</p>
                        </div>
                    </div>

                    <!-- Antrean Toko -->
                    <div class="bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                        <h3 class="text-sm font-extrabold text-white flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                            <span>Aplikasi Pendaftaran Toko Sparepart (Pending)</span>
                        </h3>
                        <div id="shopVerifQueue" class="space-y-4 overflow-y-auto max-h-[500px]">
                            <p class="text-xs text-slate-500">Memuat antrean...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL 3: Admin Resolusi (Penengah) -->
            <div id="panelResolusi" class="hidden space-y-6 animate-fade">
                <div>
                    <h2 class="text-xl font-black text-white">⚖️ Admin Resolusi Dashboard</h2>
                    <p class="text-xs text-slate-400 mt-1">Lakukan investigasi sengketa order aktif dan tentukan putusan yang adil.</p>
                </div>

                <div class="bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                    <h3 class="text-sm font-extrabold text-white">🚨 Antrean Kasus Sengketa Aktif</h3>
                    <div id="disputeQueueList" class="space-y-4">
                        <p class="text-xs text-slate-500">Memuat daftar sengketa...</p>
                    </div>
                </div>
            </div>

            <!-- PANEL 4: Super Admin Monitoring (Performance Monitor) -->
            <div id="panelMonitoring" class="hidden space-y-6 animate-fade">
                <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-xl font-black text-white">📊 Super Admin Monitoring Kinerja</h2>
                        <p class="text-xs text-slate-400 mt-1">Pantau metrik performa agregat tim admin & kirimkan flag alert ke Owner.</p>
                    </div>
                </div>

                <!-- Metrik Scorecards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-slate-800/30 p-4 rounded-2xl border border-slate-700/50">
                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Mitra Aktif (Teknisi / Toko)</p>
                        <p id="metricActivePartners" class="text-xl font-black text-white mt-1">0 / 0</p>
                    </div>
                    <div class="bg-slate-800/30 p-4 rounded-2xl border border-slate-700/50">
                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Sengketa (Tutup / Buka)</p>
                        <p id="metricDisputes" class="text-xl font-black text-white mt-1">0 / 0</p>
                    </div>
                    <div class="bg-slate-800/30 p-4 rounded-2xl border border-slate-700/50">
                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Avg SLA Waktu Resolusi</p>
                        <p id="metricAvgSla" class="text-xl font-black text-brandOrange mt-1">0 Jam</p>
                    </div>
                    <div class="bg-slate-800/30 p-4 rounded-2xl border border-slate-700/50">
                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Pelanggaran SLA (>48 Jam)</p>
                        <p id="metricSlaBreach" class="text-xl font-black text-red-500 mt-1">0 Kasus</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Rate comparisons (6 Cols) -->
                    <div class="lg:col-span-6 bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                        <h3 class="text-sm font-extrabold text-white">📈 Rasio Onboarding Reject vs Freeze</h3>
                        <div class="space-y-4 text-xs">
                            <div>
                                <div class="flex justify-between font-bold mb-1">
                                    <span>Tingkat Penolakan Pendaftaran (Rejection Rate)</span>
                                    <span id="rateRejectText">0%</span>
                                </div>
                                <div class="w-full bg-slate-900 h-2.5 rounded-full overflow-hidden">
                                    <div id="rateRejectBar" class="bg-red-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between font-bold mb-1">
                                    <span>Tingkat Freeze Akun Akibat Pelanggaran (Freeze Rate)</span>
                                    <span id="rateFreezeText">0%</span>
                                </div>
                                <div class="w-full bg-slate-900 h-2.5 rounded-full overflow-hidden">
                                    <div id="rateFreezeBar" class="bg-orange-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dispatch Warning Flag (6 Cols) -->
                    <div class="lg:col-span-6 bg-slate-800/40 rounded-2xl p-6 border border-slate-700/50 space-y-4">
                        <h3 class="text-sm font-extrabold text-white">🚨 Kirim Alert Flag Peringatan Kinerja</h3>
                        <form onsubmit="handleSendFlag(event)" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Judul Peringatan</label>
                                <input required type="text" id="flagTitle" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Contoh: SLA Resolusi Menurun Drastis">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Tingkat Urgensi</label>
                                <select required id="flagUrgency" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition">
                                    <option value="LOW">LOW (Informasional)</option>
                                    <option value="MEDIUM">MEDIUM (Butuh Atensi)</option>
                                    <option value="HIGH">HIGH (Kritis/Segera Tindak)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pesan Deskripsi</label>
                                <textarea required id="flagMessage" rows="2" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Deskripsikan alasan pelaporan..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-3 rounded-xl transition">
                                Kirim Laporan Alert ke Owner
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Global cats/reason modal for verifications decision -->
    <div id="verifDecisionModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-800 w-full max-w-sm rounded-2xl p-6 border border-slate-700 text-white relative">
            <button onclick="closeDecisionModal()" class="absolute right-4 top-4 text-slate-400 hover:text-white font-extrabold text-sm">✕</button>
            <h3 id="verifModalTitle" class="text-sm font-extrabold">Keputusan Verifikasi</h3>
            
            <form onsubmit="submitVerifDecision(event)" class="mt-4 space-y-4">
                <input type="hidden" id="verifTargetType">
                <input type="hidden" id="verifTargetId">
                <input type="hidden" id="verifTargetStatus">

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Catatan Verifikator (Wajib)</label>
                    <textarea required id="verifCatatan" rows="3" class="w-full text-xs bg-slate-900 border border-slate-700 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Tuliskan catatan verifikasi..."></textarea>
                </div>

                <button type="submit" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-2.5 rounded-xl transition">
                    Kirim Keputusan Final
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-600 text-center py-6 border-t border-slate-900 text-xs">
        <p>© 2026 REVARO — Administration Management System.</p>
    </footer>

    <!-- Script dashboard controller -->
    <script>
        window.addEventListener('load', () => {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user'));
            const role = localStorage.getItem('role');

            if (!token || !user || !role) {
                alert("Anda tidak memiliki sesi masuk. Silakan masuk terlebih dahulu.");
                window.location.href = '/admin/login';
                return;
            }

            document.getElementById('adminNavName').innerText = user.nama;
            document.getElementById('adminNavRole').innerText = role;

            // Load panel based on role
            if (role === 'Super Admin (Pemilik Sistem)') {
                document.getElementById('panelOwner').classList.remove('hidden');
                loadOwnerDashboard();
            } else if (role === 'Admin Verifikator') {
                document.getElementById('panelVerifikator').classList.remove('hidden');
                loadVerifikatorDashboard();
            } else if (role === 'Admin Resolusi') {
                document.getElementById('panelResolusi').classList.remove('hidden');
                loadResolusiDashboard();
            } else if (role === 'Super Admin (Monitoring Kinerja)') {
                document.getElementById('panelMonitoring').classList.remove('hidden');
                loadMonitoringDashboard();
            }
        });

        // Logout
        function handleAdminLogout() {
            localStorage.clear();
            window.location.href = '/admin/login';
        }

        // ================= OWNER LOGIC =================
        async function loadOwnerDashboard() {
            const token = localStorage.getItem('token');
            try {
                const response = await fetch('/api/monitoring/metrics', {
                    method: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (response.ok) {
                    const metrics = await response.json();
                    // Owner can view stats too, but let's fetch activity logs
                    const logResponse = await fetch('/api/verifications/pending', { // Fetching verifications as a proxy workload for simplicity
                        method: 'GET',
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    
                    const logsContainer = document.getElementById('ownerAuditLogs');
                    logsContainer.innerHTML = `
                        <tr>
                            <td class="py-2.5">System</td>
                            <td class="py-2.5">Total Teknisi Terdaftar: ${metrics.total_active_technicians} Aktif</td>
                            <td class="py-2.5 text-right font-mono">Real-time</td>
                        </tr>
                        <tr>
                            <td class="py-2.5">System</td>
                            <td class="py-2.5">Total Toko Terdaftar: ${metrics.total_active_shops} Aktif</td>
                            <td class="py-2.5 text-right font-mono">Real-time</td>
                        </tr>
                        <tr>
                            <td class="py-2.5">Sengketa</td>
                            <td class="py-2.5">Dispute Aktif: ${metrics.open_disputes} Terbuka, ${metrics.resolved_disputes} Selesai</td>
                            <td class="py-2.5 text-right font-mono">Real-time</td>
                        </tr>
                    `;
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function handleCreateAdmin(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            
            const nama = document.getElementById('newAdminName').value;
            const email = document.getElementById('newAdminEmail').value;
            const password = document.getElementById('newAdminPassword').value;
            const role_slug = document.getElementById('newAdminRole').value;

            try {
                const response = await fetch('/api/admin/accounts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ nama, email, password, role_slug })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || JSON.stringify(result.errors));
                    return;
                }

                alert("Akun internal administrator berhasil dibuat!");
                e.target.reset();

            } catch (err) {
                console.error(err);
                alert("Kesalahan koneksi saat membuat admin.");
            }
        }

        // ================= VERIFIKATOR LOGIC =================
        async function loadVerifikatorDashboard() {
            const token = localStorage.getItem('token');
            try {
                const response = await fetch('/api/verifications/pending', {
                    method: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) return;
                const result = await response.json();

                const techContainer = document.getElementById('techVerifQueue');
                techContainer.innerHTML = '';
                if (result.technicians.length === 0) {
                    techContainer.innerHTML = '<p class="text-xs text-slate-500">Antrean kosong.</p>';
                } else {
                    result.technicians.forEach(t => {
                        const div = document.createElement('div');
                        div.className = 'bg-slate-900/60 p-4 rounded-xl border border-slate-700/30 text-xs space-y-2';
                        div.innerHTML = `
                            <div class="flex justify-between">
                                <h4 class="font-bold text-white text-sm">${t.nama}</h4>
                                <span class="bg-blue-900/60 text-blue-400 text-[9px] font-bold px-2 py-0.5 rounded">Spesialis: ${t.kategori}</span>
                            </div>
                            <p class="text-slate-400">📞 WhatsApp: ${t.hp}</p>
                            <p class="text-[10px] text-slate-500 font-mono">Lampiran Sertifikat: Sertifikat_Keahlian_${t.nama.replace(/\s+/g, '_')}.pdf</p>
                            <div class="flex space-x-2 pt-2">
                                <button onclick="openDecisionModal('teknisi', ${t.id}, 'APPROVED')" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 rounded-lg text-[10px]">Setujui (Approve)</button>
                                <button onclick="openDecisionModal('teknisi', ${t.id}, 'NEEDS_REVISION')" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-1.5 rounded-lg text-[10px]">Revisi (Revision)</button>
                                <button onclick="openDecisionModal('teknisi', ${t.id}, 'REJECTED')" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 rounded-lg text-[10px]">Tolak (Reject)</button>
                            </div>
                        `;
                        techContainer.appendChild(div);
                    });
                }

                const shopContainer = document.getElementById('shopVerifQueue');
                shopContainer.innerHTML = '';
                if (result.shops.length === 0) {
                    shopContainer.innerHTML = '<p class="text-xs text-slate-500">Antrean kosong.</p>';
                } else {
                    result.shops.forEach(s => {
                        const div = document.createElement('div');
                        div.className = 'bg-slate-900/60 p-4 rounded-xl border border-slate-700/30 text-xs space-y-2';
                        div.innerHTML = `
                            <div class="flex justify-between">
                                <h4 class="font-bold text-white text-sm">${s.nama}</h4>
                                <span class="bg-orange-950/60 text-orange-400 text-[9px] font-bold px-2 py-0.5 rounded">Izin Usaha: ${s.izin_usaha}</span>
                            </div>
                            <p class="text-slate-400">📞 Telepon Toko: ${s.telepon}</p>
                            <p class="text-slate-400">📍 Alamat Fisik: ${s.alamat}</p>
                            <div class="flex space-x-2 pt-2">
                                <button onclick="openDecisionModal('toko', ${s.id}, 'APPROVED')" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 rounded-lg text-[10px]">Setujui</button>
                                <button onclick="openDecisionModal('toko', ${s.id}, 'NEEDS_REVISION')" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-1.5 rounded-lg text-[10px]">Revisi</button>
                                <button onclick="openDecisionModal('toko', ${s.id}, 'REJECTED')" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 rounded-lg text-[10px]">Tolak</button>
                            </div>
                        `;
                        shopContainer.appendChild(div);
                    });
                }

            } catch (err) {
                console.error(err);
            }
        }

        function openDecisionModal(type, id, status) {
            document.getElementById('verifTargetType').value = type;
            document.getElementById('verifTargetId').value = id;
            document.getElementById('verifTargetStatus').value = status;
            document.getElementById('verifCatatan').value = '';
            
            document.getElementById('verifModalTitle').innerText = `Tinjau ${type === 'teknisi' ? 'Teknisi' : 'Toko'} — Keputusan: ${status}`;
            document.getElementById('verifDecisionModal').classList.remove('hidden');
        }

        function closeDecisionModal() {
            document.getElementById('verifDecisionModal').classList.add('hidden');
        }

        async function submitVerifDecision(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            
            const target_type = document.getElementById('verifTargetType').value;
            const target_id = parseInt(document.getElementById('verifTargetId').value);
            const status = document.getElementById('verifTargetStatus').value;
            const catatan = document.getElementById('verifCatatan').value;

            try {
                const response = await fetch('/api/verifications/decision', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ target_type, target_id, status, catatan })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || JSON.stringify(result.errors));
                    return;
                }

                alert("Keputusan verifikasi berhasil disimpan!");
                closeDecisionModal();
                loadVerifikatorDashboard();

            } catch (err) {
                console.error(err);
            }
        }

        // ================= RESOLUSI LOGIC =================
        async function loadResolusiDashboard() {
            const token = localStorage.getItem('token');
            try {
                const response = await fetch('/api/disputes', {
                    method: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) return;
                const result = await response.json();

                const container = document.getElementById('disputeQueueList');
                container.innerHTML = '';

                if (result.disputes.length === 0) {
                    container.innerHTML = '<p class="text-xs text-slate-500">Tidak ada sengketa aktif.</p>';
                } else {
                    result.disputes.forEach(d => {
                        const div = document.createElement('div');
                        div.className = 'bg-slate-900/60 p-4 rounded-xl border border-slate-700/30 text-xs space-y-3';
                        
                        // Decision options markup
                        div.innerHTML = `
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <h4 class="font-black text-white text-sm">Dispute Sengketa ID: ${d.id}</h4>
                                <span class="bg-red-950/60 text-red-400 text-[9px] font-bold px-2 py-0.5 rounded uppercase">${d.status}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-400">
                                <p>👤 <strong>Pelanggan:</strong> ${(d.order && d.order.pelanggan) ? d.order.pelanggan.nama : 'N/A'}</p>
                                <p>🛠️ <strong>Teknisi:</strong> ${(d.order && d.order.teknisi) ? d.order.teknisi.nama : 'N/A'}</p>
                            </div>
                            <p class="text-slate-300">🚨 <strong>Alasan Sengketa:</strong> "${d.alasan}"</p>
                            
                            ${d.status !== 'RESOLVED' ? `
                                <div class="bg-slate-950/50 p-3 rounded-lg border border-slate-800 space-y-3">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="flex-grow">
                                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Keputusan Akhir</label>
                                            <select id="decisionSelect_${d.id}" class="bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white outline-none w-full">
                                                <option value="REFUND_PELANGGAN">REFUND PELANGGAN (Kembalikan Saldo 100%)</option>
                                                <option value="RELEASE_TEKNISI">RELEASE TEKNISI (Cairkan Escrow ke Mitra)</option>
                                                <option value="KUNJUNGAN_ULANG">KUNJUNGAN ULANG (Teknisi Kembali Servis Gratis)</option>
                                                <option value="FREEZE_AKUN">FREEZE AKUN (Warning + Bekukan Akun Mitra Fraud)</option>
                                            </select>
                                        </div>
                                        <div class="flex-grow">
                                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Catatan Resolusi</label>
                                            <input required type="text" id="resolusiText_${d.id}" class="bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-white outline-none w-full" placeholder="Catatan alasan keputusan...">
                                        </div>
                                    </div>
                                    <button onclick="submitDisputeResolve(${d.id})" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold py-2 rounded-lg text-xs">
                                        Selesaikan Sengketa
                                    </button>
                                </div>
                            ` : `
                                <div class="bg-slate-950 p-2.5 rounded-lg border border-slate-800 text-[10px] text-emerald-400 font-bold">
                                    ✅ Selesai: Keputusan [${d.keputusan}] - "${d.resolusi}"
                                </div>
                            `}
                        `;
                        container.appendChild(div);
                    });
                }

            } catch (err) {
                console.error(err);
            }
        }

        async function submitDisputeResolve(disputeId) {
            const token = localStorage.getItem('token');
            const keputusan = document.getElementById(`decisionSelect_${disputeId}`).value;
            const resolusi = document.getElementById(`resolusiText_${disputeId}`).value;

            if (!resolusi) {
                alert("Catatan resolusi harus diisi.");
                return;
            }

            try {
                const response = await fetch(`/api/disputes/${disputeId}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ keputusan, resolusi })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || "Gagal menyelesaikan sengketa.");
                    return;
                }

                alert(result.message || "Sengketa berhasil diselesaikan!");
                loadResolusiDashboard();

            } catch (err) {
                console.error(err);
            }
        }

        // ================= MONITORING LOGIC =================
        async function loadMonitoringDashboard() {
            const token = localStorage.getItem('token');
            try {
                const response = await fetch('/api/monitoring/metrics', {
                    method: 'GET',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) return;
                const m = await response.json();

                document.getElementById('metricActivePartners').innerText = `${m.total_active_technicians} / ${m.total_active_shops}`;
                document.getElementById('metricDisputes').innerText = `${m.resolved_disputes} / ${m.open_disputes}`;
                document.getElementById('metricAvgSla').innerText = `${m.average_resolution_time_hours} Jam`;
                document.getElementById('metricSlaBreach').innerText = `${m.sla_breach_count} Kasus`;

                // Set rates
                document.getElementById('rateRejectText').innerText = `${m.onboarding_rejection_rate_percent}%`;
                document.getElementById('rateRejectBar').style.width = `${m.onboarding_rejection_rate_percent}%`;

                document.getElementById('rateFreezeText').innerText = `${m.operational_freeze_rate_percent}%`;
                document.getElementById('rateFreezeBar').style.width = `${m.operational_freeze_rate_percent}%`;

            } catch (err) {
                console.error(err);
            }
        }

        async function handleSendFlag(e) {
            e.preventDefault();
            const token = localStorage.getItem('token');
            
            const judul = document.getElementById('flagTitle').value;
            const urgency = document.getElementById('flagUrgency').value;
            const pesan = document.getElementById('flagMessage').value;

            try {
                const response = await fetch('/api/monitoring/flags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ judul, urgency, pesan })
                });

                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || JSON.stringify(result.errors));
                    return;
                }

                alert(result.message || "Alert Flag Peringatan Kinerja berhasil dikirimkan ke Owner!");
                e.target.reset();

            } catch (err) {
                console.error(err);
                alert("Gagal mengirimkan alert flag.");
            }
        }
    </script>
</body>
</html>
