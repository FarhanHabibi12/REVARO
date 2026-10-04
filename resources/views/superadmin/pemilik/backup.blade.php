@extends('layouts.superadmin')

@section('title', 'Pemeliharaan Database & Performa — REVARO')
@section('page-title', 'Pemeliharaan Database & Performa Sistem')
@section('page-subtitle', 'Unduh cadangan data basis data, pulihkan data dari file SQL, optimalkan cache Laravel, atau reset riwayat transaksi.')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Card 1: Backup Database SQL -->
    <div class="card p-6 border-l-4 border-l-blue-500 flex flex-col justify-between space-y-6">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl border border-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white">Backup Database SQL</h3>
                    <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">CADANGAN DATA INSTANS</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
                Unduh seluruh struktur dan isi data platform dalam format berkas berkode <code class="bg-slate-800 text-slate-200 px-1.5 py-0.5 rounded font-mono text-[11px]">.sql</code>. Simpan berkas ini di tempat aman.
            </p>
        </div>
        <div class="pt-2">
            <button onclick="downloadBackup()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs py-3 px-6 rounded-xl transition shadow flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh Cadangan SQL (.sql)</span>
            </button>
        </div>
    </div>

    <!-- Card 2: Pemulihan Database (Restore) -->
    <div class="card p-6 border-l-4 border-l-amber-500 flex flex-col justify-between space-y-6">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl border border-amber-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white">Pemulihan Database (Restore)</h3>
                    <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest">IMPOR BERKAS SQL</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
                Kembalikan data platform dari berkas <code class="bg-slate-800 text-slate-200 px-1.5 py-0.5 rounded font-mono text-[11px]">.sql</code>. Seluruh data yang ada saat ini akan <strong class="text-slate-200">ditimpa ulang</strong>.
            </p>
        </div>
        <div class="space-y-3">
            <div class="relative flex items-center bg-slate-950 border border-slate-800 rounded-xl p-2.5 hover:border-slate-700 transition">
                <input type="file" id="backupFile" accept=".sql" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                <span id="fileNameDisplay" class="text-xs text-slate-500">Belum ada file dipilih...</span>
            </div>
            <button onclick="restoreDatabase()" class="w-full bg-amber-700 hover:bg-amber-600 text-white font-extrabold text-xs py-3 px-6 rounded-xl transition shadow flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Jalankan Pemulihan Restore Data</span>
            </button>
        </div>
    </div>

    <!-- Card 3: Bersihkan Cache Framework -->
    <div class="card p-6 border-l-4 border-l-emerald-500 flex flex-col justify-between space-y-6">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl border border-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white">Bersihkan Cache Framework</h3>
                    <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">OPTIMASI PERFORMA PLATFORM</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
                Bersihkan kompilasi cache route, config, event, dan tampilan Blade Laravel secara instan tanpa mengganggu data pengguna.
            </p>
        </div>
        <div class="pt-2">
            <button onclick="clearCacheFramework()" class="w-full bg-emerald-900/80 hover:bg-emerald-800 text-emerald-300 hover:text-white font-extrabold text-xs py-3 px-6 rounded-xl transition border border-emerald-700/40 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Bersihkan Seluruh Cache Framework</span>
            </button>
        </div>
    </div>

    <!-- Card 4: Reset Riwayat Transaksi -->
    <div class="card p-6 border-l-4 border-l-red-500 flex flex-col justify-between space-y-6">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-xl border border-red-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white">Reset Riwayat Transaksi</h3>
                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest">PERGANTIAN PERIODE / TAHUN</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
                Hapus seluruh riwayat pengaduan, permohonan surat, dan ulasan survei / riwayat transaksi. <strong class="text-red-400">Tindakan ini tidak dapat dibatalkan!</strong>
            </p>
        </div>
        <div class="pt-2">
            <button onclick="resetTransactions()" class="w-full bg-red-800 hover:bg-red-700 text-white font-extrabold text-xs py-3 px-6 rounded-xl transition shadow flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Reset Seluruh Data Transaksi</span>
            </button>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('admin_token');

    // File input name decorator
    document.getElementById('backupFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const display = document.getElementById('fileNameDisplay');
        if (file) {
            display.textContent = file.name;
            display.classList.remove('text-slate-500');
            display.classList.add('text-slate-200', 'font-mono');
        } else {
            display.textContent = 'Belum ada file dipilih...';
            display.classList.remove('text-slate-200', 'font-mono');
            display.classList.add('text-slate-500');
        }
    });

    async function downloadBackup() {
        Swal.fire({
            title: 'Mempersiapkan Backup...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            background: '#1E293B',
            color: '#E2E8F0',
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch('/api/admin/database/backup', {
                headers: { 'Authorization': 'Bearer ' + token }
            });

            if (!response.ok) throw new Error('Gagal mengunduh backup');

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `backup_revaro_${new Date().toISOString().slice(0,10)}_${Math.floor(Date.now()/1000)}.sql`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            Swal.close();

            Swal.fire({ title: 'Unduh Berhasil!', text: 'File cadangan SQL telah disimpan.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        } catch(e) {
            Swal.fire({
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memproses ekspor backup.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    }

    async function restoreDatabase() {
        const fileInput = document.getElementById('backupFile');
        const file = fileInput.files[0];

        if (!file) {
            Swal.fire({
                title: 'Pilih File',
                text: 'Silakan pilih berkas cadangan SQL (.sql) terlebih dahulu.',
                icon: 'warning',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        const confirm = await Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: 'Tindakan ini akan menghapus semua data aktif di database saat ini dan menggantinya dengan isi file SQL cadangan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Timpa Sekarang',
            cancelButtonText: 'Batal',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#475569'
        });

        if (!confirm.isConfirmed) return;

        Swal.fire({
            title: 'Menimpa Database...',
            text: 'Mohon tidak menutup halaman ini.',
            allowOutsideClick: false,
            background: '#1E293B',
            color: '#E2E8F0',
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('backup_file', file);

        try {
            const r = await fetch('/api/admin/database/restore', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });

            const d = await r.json();
            if (r.ok) {
                await Swal.fire({
                    title: 'Berhasil Pemulihan!',
                    text: d.message + ' Silakan login kembali.',
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                window.location.href = '/admin/login';
            } else {
                Swal.fire({
                    title: 'Gagal Restorasi',
                    text: d.message || 'Gagal memproses restorasi database.',
                    icon: 'error',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch(e) {
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kegagalan koneksi ke server.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    }

    async function clearCacheFramework() {
        Swal.fire({
            title: 'Membersihkan Cache...',
            text: 'Memproses pembersihan kompilasi cache Laravel.',
            allowOutsideClick: false,
            background: '#1E293B',
            color: '#E2E8F0',
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const res = await fetch('/api/admin/database/clear-cache', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const d = await res.json();
            Swal.close();

            if (res.ok) {
                Swal.fire({ title: 'Cache Dibersihkan!', text: d.message, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            } else {
                Swal.fire({ title: 'Gagal', text: d.message, icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            }
        } catch (e) {
            Swal.fire({ title: 'Error', text: 'Gagal menghubungkan ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    async function resetTransactions() {
        const confirm = await Swal.fire({
            title: 'Reset Seluruh Transaksi?',
            text: 'Tindakan ini akan MENGHAPUS SEMUA DATA transaksi, order, dispute, dan ulasan! Tindakan ini TIDAK DAPAT DIBATALKAN!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset Sekarang',
            cancelButtonText: 'Batal',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#475569'
        });

        if (!confirm.isConfirmed) return;

        Swal.fire({
            title: 'Mereset Data Transaksi...',
            text: 'Mohon tunggu.',
            allowOutsideClick: false,
            background: '#1E293B',
            color: '#E2E8F0',
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const res = await fetch('/api/admin/database/reset-transactions', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const d = await res.json();
            Swal.close();

            if (res.ok) {
                Swal.fire({ title: 'Reset Berhasil!', text: d.message, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            } else {
                Swal.fire({ title: 'Gagal', text: d.message, icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            }
        } catch (e) {
            Swal.fire({ title: 'Error', text: 'Gagal menghubungkan ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }
</script>
@endsection
