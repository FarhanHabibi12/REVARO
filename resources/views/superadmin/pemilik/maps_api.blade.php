@extends('layouts.superadmin')

@section('title', 'Kunci API Maps & Geocoding (GOAPI) — Super Admin REVARO')
@section('page-title', 'Kelola Kunci API Maps (GOAPI)')
@section('page-subtitle', 'Pantau siklus masa berlaku 10 minggu dan perbarui kode API Maps/Geocoding platform REVARO.')

@section('content')
<div class="space-y-8">

    <!-- Top Action & Breadcrumb Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>SISTEM & SERVER</span> / <span class="text-brandOrange">KUNCI API MAPS (GOAPI)</span>
        </div>
        <div class="flex items-center gap-2">
            <span id="headerKeyStatusBadge" class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="headerKeyStatusText">ENGINE GOAPI AKTIF</span>
            </span>
        </div>
    </div>

    <!-- 10-Week Renewal Cycle Tracker Card -->
    <div class="card p-6 md:p-8 space-y-6 border-l-4 border-l-orange-500 bg-gradient-to-br from-slate-900 via-slate-900/90 to-slate-950">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-400 bg-orange-500/15 px-2.5 py-0.5 rounded-full border border-orange-500/30">
                        ⏳ SIKLUS MASA BERLAKU 10 MINGGU (70 HARI)
                    </span>
                    <span id="cycleStatusBadge" class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        SINKRON
                    </span>
                </div>
                <h3 class="text-xl font-black text-white">Status Siklus Pembaruan Kunci GOAPI</h3>
                <p class="text-xs text-slate-400">
                    Sesuai kebijakan penyedia API GOAPI, kunci API wajib diperbarui setiap 10 minggu sekali agar pencarian alamat & geokoding tetap lancar.
                </p>
            </div>
            <div class="text-left md:text-right bg-slate-950/70 p-4 rounded-2xl border border-slate-800 shrink-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sisa Masa Aktif Kunci</p>
                <p id="daysRemainingDisplay" class="text-2xl font-black text-amber-400 font-mono mt-0.5">-- Hari</p>
                <p id="cycleFormattedDate" class="text-[10px] text-slate-400 mt-0.5">Terakhir diperbarui: --</p>
            </div>
        </div>

        <!-- Progress Bar Siklus -->
        <div class="space-y-2">
            <div class="flex justify-between items-center text-xs font-bold text-slate-300">
                <span>Progres Siklus Penggunaan Kunci Saat Ini:</span>
                <span id="progressPercentDisplay" class="font-mono text-orange-400">0% Terpakai</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-3.5 overflow-hidden p-0.5 border border-slate-700 shadow-inner">
                <div id="cycleProgressBar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 via-amber-500 to-orange-500 transition-all duration-700 shadow-sm" style="width: 0%;"></div>
            </div>
            <div class="flex justify-between text-[11px] text-slate-400 font-mono pt-1">
                <span>Hari ke-0 (Kunci Baru)</span>
                <span class="text-center font-bold text-slate-300" id="cycleMilestoneLabel">Status Normal</span>
                <span>Hari ke-70 (Batas 10 Minggu)</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Edit Key Form & API Diagnostics -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Form (7 cols): Perbarui Kode API -->
        <div class="lg:col-span-7 card p-6 md:p-8 space-y-6">
            <div class="border-b border-slate-800 pb-4">
                <h3 class="text-lg font-black text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Kode Otentikasi API GOAPI</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Masukkan kode API token GOAPI yang baru didapatkan. Saat disimpan, siklus 10 minggu akan otomatis di-reset ke 70 hari penuh.
                </p>
            </div>

            <form id="goapiConfigForm" onsubmit="event.preventDefault(); saveMapsApiKey();" class="space-y-5">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-2">
                        KODE API KEY GOAPI *
                    </label>
                    <div class="relative">
                        <input type="password" id="goapiKeyInput" required class="input-field font-mono text-sm tracking-wider pr-20" placeholder="Masukkan token goapi-xxxxxxxx...">
                        <button type="button" onclick="toggleGoApiKeyVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition" title="Lihat / Sembunyikan Kunci">
                            <span id="eyeIcon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1.5">
                        <span>Kunci ini digunakan oleh modul pencarian otomatis alamat, geokoding, dan radar teknisi di portal Pelanggan.</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800/80">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">
                            Radius Standar Teknisi (KM)
                        </label>
                        <input type="number" id="settingRadarRadius" min="1" max="50" class="input-field font-mono font-bold" placeholder="7">
                        <p class="text-[10px] text-slate-500 mt-1">Jangkauan reguler pencarian pesanan SOS.</p>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block mb-1.5">
                            Tarif Jangkauan Luas Prioritas (Rp)
                        </label>
                        <input type="number" id="settingPremiumFee" min="0" step="1000" class="input-field font-mono font-bold" placeholder="25000">
                        <p class="text-[10px] text-slate-500 mt-1">Biaya ekstra radius 20 KM pelanggan.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="testCurrentGoApiKey()" id="btnTestApi" class="w-full sm:w-auto btn-outline text-xs py-3.5 px-5 font-bold flex items-center justify-center gap-2">
                        <span id="testSpinner" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Tes Koneksi API</span>
                    </button>
                    <button type="submit" id="btnSaveApi" class="w-full sm:flex-1 btn-primary text-xs py-3.5 px-6 font-black flex items-center justify-center gap-2 shadow-lg shadow-orange-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Simpan &amp; Reset Siklus 10 Minggu</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Box (5 cols): Diagnostic & Quick Help -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Diagnostics Output Card -->
            <div class="card p-6 md:p-7 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h4 class="text-sm font-extrabold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                        <span>Hasil Diagnostik API GOAPI</span>
                    </h4>
                    <span id="diagStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-400">
                        Belum Dites
                    </span>
                </div>

                <div id="diagContainer" class="p-4 rounded-xl bg-slate-950 border border-slate-800 font-mono text-xs space-y-2 text-slate-300">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Endpoint:</span>
                        <span class="text-emerald-400 font-bold">Nominatim / GOAPI Geo</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Latensi Respon:</span>
                        <span id="diagLatency" class="text-slate-400">-- ms</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Format Lokasi:</span>
                        <span class="text-blue-400">Bahasa Indonesia (ID)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Status Autentikasi:</span>
                        <span id="diagAuthStatus" class="text-slate-400">--</span>
                    </div>
                </div>

                <div id="diagMessage" class="text-[11px] text-slate-400 bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                    Klik tombol <strong>"Tes Koneksi API"</strong> untuk memverifikasi validitas kunci API GOAPI saat ini ke server geocoding.
                </div>
            </div>

            <!-- 10-Week Documentation & Tips Card -->
            <div class="card p-6 space-y-3 bg-slate-900/50 border border-slate-800">
                <h4 class="text-xs font-black uppercase text-orange-400 tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Petunjuk Pembaruan 10 Minggu</span>
                </h4>
                <ul class="text-xs text-slate-400 space-y-2.5 list-disc pl-4">
                    <li>
                        <strong>Siklus Otomatis:</strong> Saat tombol <span class="text-white font-bold">"Simpan & Reset Siklus 10 Minggu"</span> ditekan, sistem otomatis mencatat tanggal hari ini sebagai titik awal 70 hari baru.
                    </li>
                    <li>
                        <strong>Pemberitahuan Kadaluarsa:</strong> Jika sisa waktu &le; 14 hari, dasbor Super Admin akan menampilkan banner peringatan oranye, dan jika &le; 3 hari akan berstatus merah darurat.
                    </li>
                    <li>
                        <strong>Pencarian Alamat:</strong> GOAPI menggerakkan pencarian instan jalan/kota di Indonesia dengan akurasi tinggi pada formulir pemesanan teknisi.
                    </li>
                </ul>
            </div>
        </div>

    </div>

</div>

<script>
    let isKeyVisible = false;

    document.addEventListener('DOMContentLoaded', () => {
        loadMapsApiSettings();
    });

    function toggleGoApiKeyVisibility() {
        const input = document.getElementById('goapiKeyInput');
        const eye = document.getElementById('eyeIcon');
        isKeyVisible = !isKeyVisible;
        if (isKeyVisible) {
            input.type = 'text';
            eye.innerHTML = `<svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>`;
        } else {
            input.type = 'password';
            eye.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
        }
    }

    async function loadMapsApiSettings() {
        try {
            const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
            const res = await fetch('/api/admin/settings/maps', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                }
            });

            if (!res.ok) throw new Error('Gagal memuat setelan Maps');
            const data = await res.json();

            // Populate form
            document.getElementById('goapiKeyInput').value = data.maps_goapi_key || '';
            document.getElementById('settingRadarRadius').value = data.maps_regular_radius_km || data.maps_technician_radar_radius || 7;
            document.getElementById('settingPremiumFee').value = data.maps_premium_fee || data.maps_priority_wide_radius_fee || 25000;

            // 10-Week Lifecycle Data
            const cycleInfo = data.cycle_info || {};
            const daysRemaining = cycleInfo.days_remaining !== undefined ? cycleInfo.days_remaining : (data.days_remaining !== undefined ? data.days_remaining : 70);
            const daysUsed = Math.max(0, 70 - daysRemaining);
            const percentUsed = Math.min(100, Math.max(0, Math.round((daysUsed / 70) * 100)));

            document.getElementById('daysRemainingDisplay').innerText = `${daysRemaining} Hari Lagi`;
            document.getElementById('cycleFormattedDate').innerText = `Terakhir diperbarui: ${data.maps_goapi_updated_at_formatted || 'Baru Saja'}`;
            document.getElementById('progressPercentDisplay').innerText = `${percentUsed}% Terpakai (${daysUsed}/70 Hari)`;
            
            const progressBar = document.getElementById('cycleProgressBar');
            progressBar.style.width = `${percentUsed}%`;

            const cycleBadge = document.getElementById('cycleStatusBadge');
            const milestone = document.getElementById('cycleMilestoneLabel');
            const headerBadge = document.getElementById('headerKeyStatusBadge');
            const headerText = document.getElementById('headerKeyStatusText');

            if (daysRemaining <= 0) {
                cycleBadge.className = 'text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 border border-red-500/30';
                cycleBadge.innerText = 'KADALUARSA (WAJIB PERBARUI)';
                milestone.innerText = 'Wajib Diperbarui Sekarang!';
                milestone.className = 'text-center font-bold text-red-400';
                progressBar.className = 'h-full rounded-full bg-red-500 transition-all duration-700';

                headerBadge.className = 'px-3 py-1 rounded-full text-xs font-extrabold bg-red-500/15 text-red-400 border border-red-500/30 flex items-center gap-2';
                headerText.innerText = 'KUNCI KADALUARSA';
            } else if (daysRemaining <= 14) {
                cycleBadge.className = 'text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30';
                cycleBadge.innerText = 'SEGERA KADALUARSA';
                milestone.innerText = 'Perlu Disiapkan Kunci Baru';
                milestone.className = 'text-center font-bold text-amber-400';
                progressBar.className = 'h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-500 transition-all duration-700';

                headerBadge.className = 'px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center gap-2';
                headerText.innerText = 'SEGERA DIPERBARUI';
            } else {
                cycleBadge.className = 'text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                cycleBadge.innerText = 'MASA AKTIF AMAN';
                milestone.innerText = 'Status Normal';
                milestone.className = 'text-center font-bold text-emerald-400';
                progressBar.className = 'h-full rounded-full bg-gradient-to-r from-emerald-500 to-amber-500 transition-all duration-700';

                headerBadge.className = 'px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2';
                headerText.innerText = 'ENGINE GOAPI AKTIF';
            }

        } catch (err) {
            console.error('Error loading maps API settings:', err);
        }
    }

    async function saveMapsApiKey() {
        const keyVal = document.getElementById('goapiKeyInput').value.trim();
        const radarVal = parseInt(document.getElementById('settingRadarRadius').value) || 7;
        const feeVal = parseFloat(document.getElementById('settingPremiumFee').value) || 25000;

        if (!keyVal) {
            Swal.fire({
                title: 'Kode API Kosong',
                text: 'Silakan masukkan kode API GOAPI terlebih dahulu.',
                icon: 'warning',
                background: '#0F172A',
                color: '#F8FAFC',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        const confirm = await Swal.fire({
            title: 'Perbarui Kunci & Reset Siklus?',
            html: 'Kunci API baru akan disimpan dan <strong>siklus masa berlaku 10 minggu akan di-reset ke 70 hari</strong>.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan & Reset',
            cancelButtonText: 'Batal',
            background: '#0F172A',
            color: '#F8FAFC',
            confirmButtonColor: '#F97316',
            cancelButtonColor: '#334155'
        });

        if (!confirm.isConfirmed) return;

        const btn = document.getElementById('btnSaveApi');
        btn.disabled = true;
        btn.innerHTML = `<span><i class="fas fa-spinner fa-spin"></i></span> Menyimpan...`;

        try {
            const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
            const res = await fetch('/api/admin/settings/maps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                },
                body: JSON.stringify({
                    maps_goapi_key: keyVal,
                    maps_regular_radius_km: radarVal,
                    maps_premium_fee: feeVal,
                    maps_premium_radius_km: 20,
                    reset_cycle: true
                })
            });

            const result = await res.json();
            if (!res.ok) throw new Error(result.message || 'Gagal menyimpan kunci API');

            Swal.fire({
                title: 'Berhasil Diperbarui!',
                text: 'Kode API GOAPI berhasil diperbarui dan siklus 10 minggu telah di-reset ke 70 hari penuh.',
                icon: 'success',
                background: '#0F172A',
                color: '#F8FAFC',
                confirmButtonColor: '#F97316'
            });

            await loadMapsApiSettings();
        } catch (err) {
            console.error(err);
            Swal.fire({
                title: 'Gagal Menyimpan',
                text: err.message || 'Terjadi kesalahan saat memperbarui kode API.',
                icon: 'error',
                background: '#0F172A',
                color: '#F8FAFC',
                confirmButtonColor: '#F97316'
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg> Simpan &amp; Reset Siklus 10 Minggu`;
        }
    }

    async function testCurrentGoApiKey() {
        const keyVal = document.getElementById('goapiKeyInput').value.trim();
        const btn = document.getElementById('btnTestApi');
        const spinner = document.getElementById('testSpinner');
        const badge = document.getElementById('diagStatusBadge');
        const latency = document.getElementById('diagLatency');
        const authStatus = document.getElementById('diagAuthStatus');
        const diagMsg = document.getElementById('diagMessage');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        badge.innerText = 'Menguji...';
        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400';

        const startTime = performance.now();

        try {
            const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
            const res = await fetch('/api/admin/settings/maps/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                },
                body: JSON.stringify({
                    key: keyVal,
                    test_key: keyVal
                })
            });

            const elapsed = Math.round(performance.now() - startTime);
            latency.innerText = `${elapsed} ms`;

            const data = await res.json();

            if (res.ok && (data.success || data.status === 'ONLINE')) {
                badge.innerText = 'TERHUBUNG (200 OK)';
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                authStatus.innerText = 'Valid & Terautentikasi';
                authStatus.className = 'text-emerald-400 font-bold';
                diagMsg.innerHTML = `<strong>Koneksi Berhasil:</strong> Engine GOAPI merespon dengan lancar (${data.latency_ms || elapsed} ms) dan siap melayani pencarian lokasi & radar teknisi.`;
            } else {
                badge.innerText = 'GAGAL / ERROR';
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-red-500/20 text-red-400 border border-red-500/30';
                authStatus.innerText = 'Gagal / Tidak Terautentikasi';
                authStatus.className = 'text-red-400 font-bold';
                diagMsg.innerHTML = `<strong>Peringatan:</strong> ${data.message || 'Kunci API tidak merespon valid. Pastikan token GOAPI benar dan aktif.'}`;
            }
        } catch (err) {
            console.error(err);
            badge.innerText = 'ERROR KONEKSI';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-red-500/20 text-red-400';
            authStatus.innerText = 'Koneksi Terputus';
            authStatus.className = 'text-red-400 font-bold';
            diagMsg.innerHTML = `<strong>Kesalahan Jaringan:</strong> ${err.message}`;
        } finally {
            btn.disabled = false;
            spinner.classList.add('hidden');
        }
    }
</script>
@endsection
