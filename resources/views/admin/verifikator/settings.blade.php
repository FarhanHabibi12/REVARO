@extends('layouts.admin')

@section('title', 'Pengaturan Profil Admin Verifikator — REVARO')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>DK</span> / <span>ADMIN VERIFIKATOR</span> / <span class="text-brandOrange">PENGATURAN</span>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>VERIFIER CONTROLLER SYNCED</span>
            </span>
        </div>
    </div>

    <!-- 2 Column Section: Profil & Sandi Admin Verifikator + Spesifikasi Lingkungan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Box (7 cols): Profil & Sandi Admin Verifikator -->
        <div class="lg:col-span-7 card p-6 md:p-8 space-y-6 border-l-4 border-l-orange-500">
            <div>
                <h3 class="text-lg font-black text-white">Profil & Sandi Admin Verifikator</h3>
                <p class="text-xs text-slate-400 mt-1">Perbarui informasi identitas dan keamanan akun Admin Verifikator mitra.</p>
            </div>

            <form id="verifikatorForm" onsubmit="event.preventDefault(); saveVerifikatorProfile();" class="space-y-5">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NAMA LENGKAP ADMIN VERIFIKATOR *</label>
                    <input type="text" id="verifikatorNama" required class="input-field font-bold" placeholder="Jero">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ALAMAT SUREL LOGIN *</label>
                    <input type="email" id="verifikatorEmail" required class="input-field font-mono" placeholder="verifikator@revaro.com">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NOMOR TELEPON</label>
                        <input type="text" id="verifikatorPhone" class="input-field font-mono" placeholder="081234567890">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">UNIT KANTOR VERIFIKASI</label>
                        <input type="text" id="verifikatorOffice" class="input-field" placeholder="Divisi Verifikasi Pendaftaran REVARO">
                    </div>
                </div>

                <!-- Password change sub-section -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block">UBAH KATA SANDI (OPSIONAL)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <input type="password" id="verifikatorNewPassword" minlength="6" class="input-field pr-10" placeholder="Kata sandi baru">
                            <button type="button" onclick="toggleGenericPassword('verifikatorNewPassword', 'verifEye1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                <span id="verifEye1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="password" id="verifikatorConfirmPassword" minlength="6" class="input-field pr-10" placeholder="Ulangi kata sandi">
                            <button type="button" onclick="toggleGenericPassword('verifikatorConfirmPassword', 'verifEye2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                <span id="verifEye2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs py-3.5 px-6 rounded-xl border border-slate-700 transition shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan Profil &amp; Password</span>
                </button>
            </form>
        </div>

        <!-- Right Box (5 cols): Spesifikasi Lingkungan Platform & Status Role -->
        <div class="lg:col-span-5 card p-6 md:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-black text-white">Spesifikasi Lingkungan Platform</h3>
                <p class="text-xs text-slate-400 mt-1">Status dan modul lingkungan verifikasi mitra.</p>
            </div>

            <!-- Profile Header Card -->
            <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-xl border border-emerald-500/30 shrink-0" id="badgeVerifikatorInitial">
                    J
                </div>
                <div class="space-y-0.5">
                    <h4 class="text-sm font-extrabold text-white" id="displayVerifikatorName">Jero</h4>
                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 uppercase tracking-wider inline-block">
                        VERIFICATION CONTROLLER
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
                    <span class="text-emerald-400 font-bold uppercase">Admin Verifikator</span>
                </div>

                <div class="flex items-center justify-between py-2">
                    <span class="text-slate-400 uppercase">ZONA WAKTU</span>
                    <span class="text-white font-bold">{{ config('app.timezone', 'Asia/Jakarta') }} (WIB)</span>
                </div>
            </div>

            <!-- Active Controller Footer Box -->
            <div class="p-4 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                <p class="text-[10px] font-extrabold text-emerald-400 uppercase tracking-widest flex items-center gap-1.5">
                    <span>VERIFIER CONTROLLER ACTIVE</span>
                </p>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Seluruh antrian verifikasi berkas pendaftaran mitra, foto KTP, dan re-verifikasi berkala berjalan dalam status aktif 100%.
                </p>
            </div>
        </div>
    </div>

    <!-- Section 2: Pintasan Cepat Modul Verifikator -->
    <div class="space-y-4">
        <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span>Pintasan Cepat Modul Verifikator</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Shortcut 1: Antrian Pending -->
            <a href="/admin/verifikator/dashboard" class="card p-5 hover:border-orange-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-400 flex items-center justify-center border border-orange-500/20 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-orange-400 transition">Antrian Pending</h4>
                    <p class="text-xs text-slate-400">Verifikasi mitra baru</p>
                </div>
            </a>

            <!-- Shortcut 2: Antrian Resubmit -->
            <a href="/admin/verifikator/resubmitted" class="card p-5 hover:border-blue-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-blue-400 transition">Antrian Resubmit</h4>
                    <p class="text-xs text-slate-400">Mitra perbaikan berkas</p>
                </div>
            </a>

            <!-- Shortcut 3: Re-Verifikasi Berkala -->
            <a href="/admin/verifikator/reverifications" class="card p-5 hover:border-emerald-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-emerald-400 transition">Re-Verifikasi Berkala</h4>
                    <p class="text-xs text-slate-400">Pemeriksaan tahunan</p>
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

    function loadVerifikatorData() {
        document.getElementById('verifikatorNama').value = user.nama || 'Jero';
        document.getElementById('verifikatorEmail').value = user.email || 'verifikator@revaro.com';
        document.getElementById('displayVerifikatorName').textContent = user.nama || 'Jero';
        document.getElementById('badgeVerifikatorInitial').textContent = (user.nama || 'J').charAt(0).toUpperCase();
        document.getElementById('verifikatorPhone').value = user.telepon || '081234567890';
        document.getElementById('verifikatorOffice').value = 'Divisi Verifikasi Pendaftaran REVARO';
    }

    async function saveVerifikatorProfile() {
        const nama = document.getElementById('verifikatorNama').value.trim();
        const email = document.getElementById('verifikatorEmail').value.trim();
        const newPassword = document.getElementById('verifikatorNewPassword').value;
        const confirmPassword = document.getElementById('verifikatorConfirmPassword').value;

        if (newPassword && newPassword !== confirmPassword) {
            Swal.fire({ title: 'Password Mismatch', text: 'Konfirmasi kata sandi baru tidak cocok.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            return;
        }

        const formData = new FormData();
        formData.append('nama', nama);
        formData.append('email', email);
        if (newPassword) {
            formData.append('new_password', newPassword);
            formData.append('password_confirmation', confirmPassword);
        }

        try {
            Swal.fire({
                title: 'Menyimpan Perubahan...',
                allowOutsideClick: false,
                background: '#1E293B',
                color: '#E2E8F0',
                didOpen: () => Swal.showLoading()
            });

            const res = await fetch('/api/user/profile', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                user.nama = data.user.nama;
                user.email = data.user.email;
                localStorage.setItem('admin_user', JSON.stringify(user));
                document.getElementById('displayVerifikatorName').textContent = data.user.nama;
                document.getElementById('badgeVerifikatorInitial').textContent = data.user.nama.charAt(0).toUpperCase();
                document.getElementById('verifikatorNewPassword').value = '';
                document.getElementById('verifikatorConfirmPassword').value = '';

                Swal.fire({ title: 'Profil Disimpan!', text: 'Informasi akun Admin Verifikator dan kata sandi berhasil diperbarui.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            } else {
                let errMsg = data.message || 'Terjadi kesalahan saat menyimpan.';
                if (data.errors) {
                    errMsg = Object.values(data.errors).flat().join('<br>');
                }
                Swal.fire({ title: 'Gagal', html: errMsg, icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadVerifikatorData();
    });
</script>
@endsection
