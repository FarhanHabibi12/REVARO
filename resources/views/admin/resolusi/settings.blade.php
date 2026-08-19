@extends('layouts.admin')

@section('title', 'Pengaturan Profil Admin Resolusi — REVARO')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>DK</span> / <span>ADMIN RESOLUSI</span> / <span class="text-brandOrange">PENGATURAN</span>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                <span>RESOLUTION CONTROLLER SYNCED</span>
            </span>
        </div>
    </div>

    <!-- 2 Column Section: Profil & Sandi Admin Resolusi + Spesifikasi Lingkungan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Box (7 cols): Profil & Sandi Admin Resolusi -->
        <div class="lg:col-span-7 card p-6 md:p-8 space-y-6 border-l-4 border-l-blue-500">
            <div>
                <h3 class="text-lg font-black text-white">Profil & Sandi Admin Resolusi</h3>
                <p class="text-xs text-slate-400 mt-1">Perbarui informasi identitas dan keamanan akun Admin Resolusi Sengketa.</p>
            </div>

            <form id="resolusiForm" onsubmit="event.preventDefault(); saveResolusiProfile();" class="space-y-5">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NAMA LENGKAP ADMIN RESOLUSI *</label>
                    <input type="text" id="resolusiNama" required class="input-field font-bold" placeholder="Nunu Nigas">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ALAMAT SUREL LOGIN *</label>
                    <input type="email" id="resolusiEmail" required class="input-field font-mono" placeholder="resolusi@revaro.com">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NOMOR TELEPON</label>
                        <input type="text" id="resolusiPhone" class="input-field font-mono" placeholder="081234567891">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">DEPARTEMEN HUKUM & DISPUTE</label>
                        <input type="text" id="resolusiOffice" class="input-field" placeholder="Divisi Mediasi & Resolution Center REVARO">
                    </div>
                </div>

                <!-- Password change sub-section -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <label class="text-[11px] font-bold text-blue-400 uppercase tracking-wider block">UBAH KATA SANDI (OPSIONAL)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="password" id="resolusiNewPassword" minlength="6" class="input-field" placeholder="Kata sandi baru">
                        <input type="password" id="resolusiConfirmPassword" minlength="6" class="input-field" placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs py-3.5 px-6 rounded-xl border border-slate-700 transition shadow-lg flex items-center justify-center gap-2">
                    <span>✓</span> Simpan Perubahan Profil & Password
                </button>
            </form>
        </div>

        <!-- Right Box (5 cols): Spesifikasi Lingkungan Platform & Status Role -->
        <div class="lg:col-span-5 card p-6 md:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-black text-white">Spesifikasi Lingkungan Platform</h3>
                <p class="text-xs text-slate-400 mt-1">Status dan modul lingkungan mediasi sengketa.</p>
            </div>

            <!-- Profile Header Card -->
            <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xl border border-blue-500/30 shrink-0" id="badgeResolusiInitial">
                    N
                </div>
                <div class="space-y-0.5">
                    <h4 class="text-sm font-extrabold text-white" id="displayResolusiName">Nunu Nigas</h4>
                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-blue-500/15 text-blue-400 border border-blue-500/30 uppercase tracking-wider inline-block">
                        DISPUTE RESOLUTION CONTROLLER
                    </span>
                </div>
            </div>

            <!-- Key-Value Specs -->
            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">NAMA PLATFORM</span>
                    <span class="text-white font-bold">REVARO Platform</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">VERSI FRAMEWORK</span>
                    <span class="text-white font-bold">Laravel {{ app()->version() }}</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">VERSI ENGINE PHP</span>
                    <span class="text-white font-bold">{{ PHP_VERSION }}</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">PERAN AKSES</span>
                    <span class="text-blue-400 font-bold uppercase">Admin Resolusi Sengketa</span>
                </div>

                <div class="flex items-center justify-between py-2">
                    <span class="text-slate-400 uppercase">ZONA WAKTU</span>
                    <span class="text-white font-bold">{{ config('app.timezone', 'Asia/Jakarta') }} (WIB)</span>
                </div>
            </div>

            <!-- Active Controller Footer Box -->
            <div class="p-4 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                <p class="text-[10px] font-extrabold text-blue-400 uppercase tracking-widest flex items-center gap-1.5">
                    <span>RESOLUTION CONTROLLER ACTIVE</span>
                </p>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Seluruh eskalasi sengketa transaksi, klaim pengembalian dana (refund), dan mediasi mitra-pelanggan berjalan dalam status aktif 100%.
                </p>
            </div>
        </div>
    </div>

    <!-- Section 2: Pintasan Cepat Modul Resolusi -->
    <div class="space-y-4">
        <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span>Pintasan Cepat Modul Resolusi</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Shortcut 1: Antrian Sengketa Saya -->
            <a href="/admin/resolusi/dashboard" class="card p-5 hover:border-blue-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl border border-blue-500/20 group-hover:scale-110 transition">
                    ⚖️
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-blue-400 transition">Antrian Sengketa Saya</h4>
                    <p class="text-xs text-slate-400">Eskalasi dispute aktif</p>
                </div>
            </a>

            <!-- Shortcut 2: Riwayat Keputusan Saya -->
            <a href="/admin/resolusi/history" class="card p-5 hover:border-emerald-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl border border-emerald-500/20 group-hover:scale-110 transition">
                    📜
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-emerald-400 transition">Riwayat Keputusan Saya</h4>
                    <p class="text-xs text-slate-400">Arsip mediasi selesai</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('admin_token');
    const user = JSON.parse(localStorage.getItem('admin_user') || '{}');

    function loadResolusiData() {
        document.getElementById('resolusiNama').value = user.nama || 'Nunu Nigas';
        document.getElementById('resolusiEmail').value = user.email || 'resolusi@revaro.com';
        document.getElementById('displayResolusiName').textContent = user.nama || 'Nunu Nigas';
        document.getElementById('badgeResolusiInitial').textContent = (user.nama || 'N').charAt(0).toUpperCase();
        document.getElementById('resolusiPhone').value = user.telepon || '081234567891';
        document.getElementById('resolusiOffice').value = 'Divisi Mediasi & Resolution Center REVARO';
    }

    async function saveResolusiProfile() {
        const nama = document.getElementById('resolusiNama').value.trim();
        const email = document.getElementById('resolusiEmail').value.trim();
        const newPassword = document.getElementById('resolusiNewPassword').value;
        const confirmPassword = document.getElementById('resolusiConfirmPassword').value;

        if (newPassword && newPassword !== confirmPassword) {
            Swal.fire({ title: 'Password Mismatch', text: 'Konfirmasi kata sandi baru tidak cocok.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            return;
        }

        const formData = new FormData();
        formData.append('nama', nama);
        formData.append('email', email);

        try {
            Swal.fire({
                title: 'Menyimpan Perubahan...',
                allowOutsideClick: false,
                background: '#1E293B',
                color: '#E2E8F0',
                didOpen: () => Swal.showLoading()
            });

            const res = await fetch('/api/admin/profile', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });

            if (res.ok) {
                const data = await res.json();
                user.nama = data.user.nama;
                user.email = data.user.email;
                localStorage.setItem('admin_user', JSON.stringify(user));
                document.getElementById('displayResolusiName').textContent = data.user.nama;

                if (newPassword) {
                    await fetch('/api/admin/profile/password', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ current_password: '12345678', new_password: newPassword, new_password_confirmation: confirmPassword })
                    });
                }

                Swal.fire({ title: 'Profil Disimpan!', text: 'Informasi akun Admin Resolusi berhasil diperbarui.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadResolusiData();
    });
</script>
@endsection
