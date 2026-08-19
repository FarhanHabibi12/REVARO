@extends('layouts.superadmin')

@section('title', 'Pengaturan Maintenance — REVARO')
@section('page-title', 'Pengaturan Mode Pemeliharaan Sistem')
@section('page-subtitle', 'Kelola sakelar pemeliharaan sistem secara global, per-role pengguna, serta tentukan IP bypass khusus.')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action & Status Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>DK</span> / <span>SUPERADMIN</span> / <span class="text-brandOrange">MAINTENANCE</span>
        </div>
        <div id="overallPlatformStatus">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>SEMUA AKSES PLATFORM NORMAL</span>
            </span>
        </div>
    </div>

    <form id="maintenanceForm" onsubmit="event.preventDefault(); saveMaintenanceSettings();" class="space-y-8">

        <!-- 1. Maintenance Global (Seluruh Platform Website) -->
        <div class="card p-6 space-y-6 border-l-4 border-l-orange-500">
            <div>
                <h3 class="text-base font-extrabold text-white">1. Maintenance Global (Seluruh Platform Website)</h3>
                <p class="text-xs text-slate-400 mt-1">Menutup seluruh halaman publik dan portal login (HTTP 503) kecuali Super Admin dan IP bypass.</p>
            </div>

            <!-- Sakelar Maintenance Global Box -->
            <div class="p-4 bg-slate-900/70 rounded-2xl border border-slate-800 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-extrabold text-white">Sakelar Maintenance Global</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5" id="labelGlobalStatus">
                        Status saat ini: <span class="text-emerald-400 font-bold">NONAKTIF — Website Normal</span>
                    </p>
                </div>
                <!-- Custom Toggle Switch -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="switchGlobalMaintenance" class="sr-only peer" onchange="toggleGlobalSwitchUI()">
                    <div class="w-14 h-7 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
            </div>

            <!-- Inputs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">PESAN MAINTENANCE *</label>
                    <textarea id="maintenanceMessage" rows="3" class="input-field leading-relaxed" placeholder="Sistem REVARO sedang dalam pemeliharaan berkala."></textarea>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ESTIMASI SELESAI (ETA)</label>
                    <input type="datetime-local" id="maintenanceEta" class="input-field font-mono">
                    <p class="text-[10px] text-slate-500 mt-1">Estimasi perkiraan waktu selesai perbaikan.</p>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">DAFTAR IP BYPASS (DIPISAHKAN KOMA)</label>
                    <input type="text" id="maintenanceIpBypass" class="input-field font-mono" placeholder="186.243.3.201, 127.0.0.1">
                    <p class="text-[10px] text-slate-500 mt-1">Alamat IP pengembang / admin yang diizinkan melewati maintenance.</p>
                </div>
            </div>
        </div>

        <!-- 2. Maintenance Selektif Per-Role Pengguna -->
        <div class="card p-6 space-y-6 border-l-4 border-l-blue-500">
            <div>
                <h3 class="text-base font-extrabold text-white">2. Maintenance Selektif Per-Role Pengguna</h3>
                <p class="text-xs text-slate-400 mt-1">Tutup akses khusus untuk role tertentu tanpa mengganggu pengguna role lainnya.</p>
            </div>

            <div class="space-y-3">
                <!-- Row 1: Admin Verifikator -->
                <div class="p-3.5 bg-slate-900/50 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white">Role Admin Verifikator</h4>
                        <p class="text-[11px] text-slate-400">Menutup portal admin verifikator (/admin/verifikator/*) — Status: <span id="statusRoleVerifikator" class="font-extrabold text-emerald-400">Normal Active</span></p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="switchRoleVerifikator" class="sr-only peer" onchange="updateSelectiveRoleUI()">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>

                <!-- Row 2: Admin Resolusi -->
                <div class="p-3.5 bg-slate-900/50 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white">Role Admin Resolusi</h4>
                        <p class="text-[11px] text-slate-400">Menutup portal admin resolusi (/admin/resolusi/*) — Status: <span id="statusRoleResolusi" class="font-extrabold text-emerald-400">Normal Active</span></p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="switchRoleResolusi" class="sr-only peer" onchange="updateSelectiveRoleUI()">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>

                <!-- Row 3: Mitra & Warga -->
                <div class="p-3.5 bg-slate-900/50 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white">Role Pengguna (Pelanggan, Teknisi & Toko)</h4>
                        <p class="text-[11px] text-slate-400">Menutup portal publik & mitra (/pelanggan/*, /teknisi/*, /toko/*) — Status: <span id="statusRolePelanggan" class="font-extrabold text-emerald-400">Normal Active</span></p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="switchRolePelanggan" class="sr-only peer" onchange="updateSelectiveRoleUI()">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>
            </div>

            <!-- Action Footer -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-800">
                <span class="text-xs text-slate-400">Perubahan langsung aktif seketika saat disimpan.</span>
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white border border-slate-700 font-extrabold text-xs px-6 py-3 rounded-xl transition shadow flex items-center gap-2">
                    <span>✓</span> Simpan Pengaturan Maintenance
                </button>
            </div>
        </div>

    </form>

    <!-- 3. Riwayat Aktivitas Pemeliharaan -->
    <div class="card p-6 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <h3 class="text-sm font-extrabold text-white">Riwayat Aktivitas Pemeliharaan</h3>
            <span class="text-xs text-slate-400">10 Log Terakhir</span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="w-48">WAKTU LOG</th>
                        <th class="w-64">OLEH ADMINISTRATOR</th>
                        <th class="w-40">IP ADDRESS</th>
                        <th class="text-right">AKSI MODE</th>
                    </tr>
                </thead>
                <tbody id="maintenanceHistoryBody" class="text-xs font-mono">
                    <tr><td colspan="4" class="text-center text-slate-500 py-8">Memuat riwayat aktivitas pemeliharaan...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('admin_token');

    function toggleGlobalSwitchUI() {
        const isChecked = document.getElementById('switchGlobalMaintenance').checked;
        const label = document.getElementById('labelGlobalStatus');
        const overall = document.getElementById('overallPlatformStatus');

        if (isChecked) {
            label.innerHTML = '<span class="text-red-400 font-bold">AKTIF — Mode Pemeliharaan (HTTP 503)</span>';
            overall.innerHTML = `
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-red-500/15 text-red-400 border border-red-500/30 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
                    <span>MODE PEMELIHARAAN GLOBAL AKTIF</span>
                </span>
            `;
        } else {
            label.innerHTML = '<span class="text-emerald-400 font-bold">NONAKTIF — Website Normal</span>';
            overall.innerHTML = `
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>SEMUA AKSES PLATFORM NORMAL</span>
                </span>
            `;
        }
    }

    function updateSelectiveRoleUI() {
        const verif = document.getElementById('switchRoleVerifikator').checked;
        const resol = document.getElementById('switchRoleResolusi').checked;
        const pel = document.getElementById('switchRolePelanggan').checked;

        document.getElementById('statusRoleVerifikator').className = verif ? 'font-extrabold text-red-400' : 'font-extrabold text-emerald-400';
        document.getElementById('statusRoleVerifikator').textContent = verif ? 'Maintenance Active' : 'Normal Active';

        document.getElementById('statusRoleResolusi').className = resol ? 'font-extrabold text-red-400' : 'font-extrabold text-emerald-400';
        document.getElementById('statusRoleResolusi').textContent = resol ? 'Maintenance Active' : 'Normal Active';

        document.getElementById('statusRolePelanggan').className = pel ? 'font-extrabold text-red-400' : 'font-extrabold text-emerald-400';
        document.getElementById('statusRolePelanggan').textContent = pel ? 'Maintenance Active' : 'Normal Active';
    }

    async function loadMaintenanceData() {
        try {
            const res = await fetch('/api/admin/maintenance', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const settings = data.settings || {};

            // Populate global switch
            const globalChecked = settings.maintenance_global === 'true';
            document.getElementById('switchGlobalMaintenance').checked = globalChecked;
            document.getElementById('maintenanceMessage').value = settings.maintenance_message || 'Sistem REVARO sedang dalam pemeliharaan berkala.';
            document.getElementById('maintenanceEta').value = settings.maintenance_eta || '';
            document.getElementById('maintenanceIpBypass').value = settings.maintenance_ip_bypass || '186.243.3.201, 127.0.0.1';

            // Populate selective switches
            document.getElementById('switchRoleVerifikator').checked = settings.maintenance_role_verifikator === 'true';
            document.getElementById('switchRoleResolusi').checked = settings.maintenance_role_resolusi === 'true';
            document.getElementById('switchRolePelanggan').checked = settings.maintenance_role_pelanggan === 'true';

            toggleGlobalSwitchUI();
            updateSelectiveRoleUI();

            // Populate history table
            renderHistoryTable(data.history || []);
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan saat memuat data maintenance.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    function renderHistoryTable(logs) {
        const tbody = document.getElementById('maintenanceHistoryBody');
        if (!logs || logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-slate-500 py-8">Belum ada riwayat aktivitas pemeliharaan.</td></tr>';
            return;
        }

        tbody.innerHTML = logs.map(log => {
            const d = new Date(log.created_at);
            const formattedDate = `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()} ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
            const userName = log.user ? log.user.nama : 'Super Admin Platform';

            const isGlobalOn = log.aktivitas.includes('Global Maintenance ON');
            const badgeClass = isGlobalOn ? 'bg-red-500/15 text-red-400 border border-red-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30';
            const badgeText = isGlobalOn ? 'Global Maintenance On' : 'Global Maintenance Off';

            return `
                <tr class="hover:bg-slate-800/30 transition">
                    <td class="text-slate-400 py-3">${formattedDate}</td>
                    <td class="font-bold text-white font-sans">${userName}</td>
                    <td class="text-slate-400">${log.ip_address || '127.0.0.1'}</td>
                    <td class="text-right">
                        <span class="px-2.5 py-1 rounded text-[10px] font-extrabold ${badgeClass}">
                            ${badgeText}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function saveMaintenanceSettings() {
        const payload = {
            maintenance_global: document.getElementById('switchGlobalMaintenance').checked ? 'true' : 'false',
            maintenance_message: document.getElementById('maintenanceMessage').value,
            maintenance_eta: document.getElementById('maintenanceEta').value,
            maintenance_ip_bypass: document.getElementById('maintenanceIpBypass').value,
            maintenance_role_verifikator: document.getElementById('switchRoleVerifikator').checked ? 'true' : 'false',
            maintenance_role_resolusi: document.getElementById('switchRoleResolusi').checked ? 'true' : 'false',
            maintenance_role_pelanggan: document.getElementById('switchRolePelanggan').checked ? 'true' : 'false',
        };

        try {
            Swal.fire({
                title: 'Menyimpan Maintenance...',
                text: 'Memperbarui status pemeliharaan sistem.',
                allowOutsideClick: false,
                background: '#1E293B',
                color: '#E2E8F0',
                didOpen: () => Swal.showLoading(),
            });

            const res = await fetch('/api/admin/maintenance', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            Swal.close();

            if (res.ok) {
                Swal.fire({
                    title: 'Berhasil Disimpan!',
                    text: result.message || 'Pengaturan maintenance berhasil diperbarui.',
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
                loadMaintenanceData();
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: result.message || 'Gagal menyimpan pengaturan maintenance.',
                    icon: 'error',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Error', text: 'Gagal menghubungkan ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadMaintenanceData();
    });
</script>
@endsection
