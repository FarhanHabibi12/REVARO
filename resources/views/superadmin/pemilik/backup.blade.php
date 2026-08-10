@extends('layouts.superadmin')

@section('title', 'Backup & Restore DB — REVARO')
@section('page-title', '💾 Backup & Restore Database')
@section('page-subtitle', 'Ekspor cadangan data lengkap platform atau timpa database dari file SQL cadangan.')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Card 1: Download/Export Backup -->
    <div class="card p-8 space-y-6 flex flex-col justify-between">
        <div class="space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 text-brandOrange flex items-center justify-center text-2xl border border-orange-500/20">
                📥
            </div>
            <h3 class="text-base font-extrabold text-white">Buat & Unduh Backup Database</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Mengekstrak seluruh tabel database (skema tabel beserta baris data transaksi, akun, verifikasi, kebijakan komisi, dll) ke dalam bentuk file cadangan SQL terkompresi. Aman disimpan secara lokal.
            </p>
        </div>
        <div class="pt-4">
            <button onclick="downloadBackup()" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3 px-6 rounded-xl transition shadow flex items-center justify-center gap-2">
                <span>📥</span> Unduh File Backup SQL
            </button>
        </div>
    </div>

    <!-- Card 2: Restore/Overwrite Database -->
    <div class="card p-8 space-y-6 flex flex-col justify-between">
        <div class="space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-400 flex items-center justify-center text-2xl border border-red-500/20">
                📤
            </div>
            <h3 class="text-base font-extrabold text-white">Pulihkan / Timpa Database</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Unggah file backup SQL Anda untuk menimpa seluruh skema dan data database saat ini. Tindakan ini bersifat merusak data aktif saat ini! Pastikan berkas cadangan valid.
            </p>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih File SQL (.sql)</label>
                <div class="relative flex items-center bg-slate-950 border border-slate-800 rounded-xl p-3 hover:border-slate-700 transition">
                    <input type="file" id="backupFile" accept=".sql" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    <span id="fileNameDisplay" class="text-xs text-slate-500">Belum ada file dipilih...</span>
                </div>
            </div>
            <button onclick="restoreDatabase()" class="w-full bg-red-950 hover:bg-red-900 text-red-400 hover:text-white font-bold text-xs py-3 px-6 rounded-xl transition shadow border border-red-900/30 flex items-center justify-center gap-2">
                <span>⚠️</span> Mulai Timpa Database
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // File input name decorator
    document.getElementById('backupFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const display = document.getElementById('fileNameDisplay');
        if (file) {
            display.textContent = file.name;
            display.classList.remove('text-slate-500');
            display.classList.add('text-slate-300', 'font-mono');
        } else {
            display.textContent = 'Belum ada file dipilih...';
            display.classList.remove('text-slate-300', 'font-mono');
            display.classList.add('text-slate-500');
        }
    });

    async function downloadBackup() {
        const token = localStorage.getItem('admin_token');
        Swal.fire({
            title: 'Mempersiapkan Backup...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch('/api/admin/database/backup', {
                headers: { 'Authorization': 'Bearer ' + token }
            });

            if (!response.ok) throw new Error('Failed to download backup');

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
        const token = localStorage.getItem('admin_token');
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
                    title: 'Berhasil!',
                    text: d.message + ' Silakan login kembali untuk memperbarui sesi data Anda.',
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
                // Clear session and force re-login as database might be overwritten
                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                window.location.href = '/admin/login';
            } else {
                Swal.fire({
                    title: 'Gagal',
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
</script>
@endsection
