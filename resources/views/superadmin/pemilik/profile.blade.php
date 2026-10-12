@extends('layouts.superadmin')

@section('title', 'Profil Akun Saya — REVARO')
@section('title', 'Profil Akun Saya — REVARO')
@section('page-title', 'Profil & Pengaturan Akun Pribadi')
@section('page-subtitle', 'Kelola informasi identitas pribadi, foto avatar, kata sandi, dan preferensi akun administrator Anda.')

@section('content')
<div class="space-y-6 max-w-5xl">
    
    <!-- Profile Summary Banner -->
    <div class="card p-6 flex flex-col sm:flex-row items-center gap-6 bg-gradient-to-r from-slate-900 via-slate-900/90 to-orange-950/30 border-orange-500/20">
        <div class="relative group">
            <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-brandOrange to-amber-500 p-1 shadow-xl">
                <div class="w-full h-full rounded-full bg-slate-950 flex items-center justify-center overflow-hidden relative">
                    <img id="bannerAvatarPreview" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                    <span id="bannerAvatarFallback" class="text-white">
                        <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                </div>
            </div>
            <button onclick="document.getElementById('profileAvatarInput').click()" class="absolute bottom-0 right-0 bg-brandOrange hover:bg-orange-600 text-white p-2 rounded-full shadow-lg transition" title="Ubah Foto Profil">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
        </div>

        <div class="space-y-1.5 text-center sm:text-left flex-1">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <h2 class="text-xl font-black text-white" id="profileHeaderName">Memuat...</h2>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 uppercase tracking-widest">AKUN AKTIF</span>
            </div>
            <p class="text-xs text-slate-400 font-mono" id="profileHeaderEmail">—</p>
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-orange-500/15 text-orange-400 border border-orange-500/30" id="profileHeaderRole">
                    SUPER ADMIN — PEMILIK
                </span>
                <span class="text-[11px] text-slate-400 font-mono">Sesi Login: Aktif (Sanctum Encrypted)</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-800" id="profileTabs">
        <button onclick="switchProfileTab('tab-profile-info')" id="btn-tab-profile-info" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 bg-orange-500/15 text-orange-400 border-b-2 border-orange-500">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Informasi Profil
        </button>
        <button onclick="switchProfileTab('tab-profile-security')" id="btn-tab-profile-security" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Ganti Kata Sandi
        </button>
        <button onclick="switchProfileTab('tab-profile-preferences')" id="btn-tab-profile-preferences" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            Preferensi Tampilan
        </button>
    </div>

    <!-- Tab 1: Informasi Profil Pribadi -->
    <div id="tab-profile-info" class="profile-tab-content space-y-6">
        <form id="profileInfoForm" onsubmit="event.preventDefault(); saveProfileInfo();" class="card p-6 space-y-6">
            <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Data Identitas Administrator</span>
            </h3>

            <!-- Photo upload field -->
            <div class="flex items-center gap-5 p-4 bg-slate-900/60 rounded-2xl border border-slate-800">
                <div class="w-16 h-16 rounded-full bg-slate-950 flex items-center justify-center overflow-hidden border border-slate-800">
                    <img id="formAvatarPreview" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                    <span id="formAvatarFallback">
                        <svg class="w-8 h-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                </div>
                <div class="space-y-1.5 flex-1">
                    <label class="text-xs font-bold text-white uppercase tracking-wider block">Foto Profil Saya</label>
                    <p class="text-[11px] text-slate-400">Pilih foto profil (.png, .jpg, .webp). Ukuran rekomendasi 400x400 px max 5MB.</p>
                    <input type="file" id="profileAvatarInput" accept="image/*" class="hidden" onchange="handleAvatarFileSelect(this)">
                    <button type="button" onclick="document.getElementById('profileAvatarInput').click()" class="btn-outline text-xs px-3 py-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Pilih Berkas Foto</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Nama Lengkap Administrator *</label>
                    <input type="text" id="inputProfileNama" required class="input-field" placeholder="Nama Anda...">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Alamat Email *</label>
                    <input type="email" id="inputProfileEmail" required class="input-field font-mono" placeholder="email@revaro.com">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Peran / Role Pengguna</label>
                    <input type="text" id="inputProfileRoleDisplay" readonly class="input-field font-mono opacity-60 cursor-not-allowed bg-slate-900" value="Super Admin (Pemilik Sistem)">
                    <p class="text-[10px] text-slate-500 mt-1">Peran akun ditentukan oleh manajemen sistem.</p>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ID Pengguna (User ID)</label>
                    <input type="text" id="inputProfileUserId" readonly class="input-field font-mono opacity-60 cursor-not-allowed bg-slate-900" value="#—">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 text-right">
                <button type="submit" class="btn-primary font-extrabold text-xs px-6 py-2.5 flex items-center gap-2 ml-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 2: Ganti Kata Sandi -->
    <div id="tab-profile-security" class="profile-tab-content space-y-6 hidden">
        <form id="passwordForm" onsubmit="event.preventDefault(); saveNewPassword();" class="card p-6 space-y-6">
            <div>
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Ganti Kata Sandi Pribadi</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Perbarui kata sandi login Anda secara berkala untuk menjaga keamanan akun.</p>
            </div>

            <div class="space-y-4 max-w-md">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Kata Sandi Saat Ini *</label>
                    <div class="relative">
                        <input type="password" id="inputCurrentPassword" required class="input-field pr-10" placeholder="••••••••">
                        <button type="button" onclick="toggleGenericPassword('inputCurrentPassword', 'profEyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="profEyeIcon1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Kata Sandi Baru *</label>
                    <div class="relative">
                        <input type="password" id="inputNewPassword" required minlength="6" class="input-field pr-10" placeholder="Minimal 6 karakter">
                        <button type="button" onclick="toggleGenericPassword('inputNewPassword', 'profEyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="profEyeIcon2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Konfirmasi Kata Sandi Baru *</label>
                    <div class="relative">
                        <input type="password" id="inputConfirmPassword" required minlength="6" class="input-field pr-10" placeholder="Ulangi kata sandi baru">
                        <button type="button" onclick="toggleGenericPassword('inputConfirmPassword', 'profEyeIcon3')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                            <span id="profEyeIcon3">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- OTP verification box -->
                <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-orange-500/30 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-black text-white flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Verifikasi OTP Email Resmi
                            </span>
                            <p class="text-[11px] text-slate-400">Kode 6 digit dikirim ke email terdaftar untuk mengonfirmasi penggantian sandi.</p>
                        </div>
                        <button type="button" id="btnRequestProfPwdOtp" onclick="requestProfilePwdOtp()" class="px-3.5 py-2 rounded-xl text-xs font-black bg-orange-500/20 text-orange-400 border border-orange-500/40 hover:bg-orange-500 hover:text-white transition shrink-0">
                            Kirim Kode OTP
                        </button>
                    </div>
                    <div>
                        <input type="text" id="inputProfilePasswordOtp" maxlength="6" required class="input-field font-mono text-center tracking-widest text-sm" placeholder="KODE 6-DIGIT OTP">
                    </div>
                </div>
            </div>

            <div class="p-3.5 bg-slate-900/60 rounded-xl border border-slate-800 text-[11px] text-amber-300/80 leading-relaxed">
                🔒 <strong>Pemberitahuan Keamanan:</strong> Setelah kata sandi berhasil diperbarui dengan verifikasi OTP, seluruh sesi aktif Anda di semua perangkat akan langsung diakhiri (logout total).
            </div>

            <div class="pt-2 border-t border-slate-800 text-right">
                <button type="submit" class="btn-primary font-extrabold text-xs px-6 py-2.5 flex items-center gap-2 ml-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 3: Preferensi Tampilan -->
    <div id="tab-profile-preferences" class="profile-tab-content space-y-6 hidden">
        <div class="card p-6 space-y-6">
            <div>
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    <span>Preferensi Tampilan &amp; Antarmuka Sesi</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Pengaturan kustomisasi pengalaman penggunaan portal khusus perangkat ini.</p>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white">Mode Tampilan Aplikasi (Theme Mode)</h4>
                        <p class="text-[11px] text-slate-400">Ganti antara Mode Gelap (Dark Mode) dan Mode Terang (Light Mode).</p>
                    </div>
                    <button onclick="toggleTheme()" class="btn-outline text-xs px-4 py-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <span>Saklar Mode Tampilan</span>
                    </button>
                </div>

                <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white">Notifikasi Suara Alert</h4>
                        <p class="text-[11px] text-slate-400">Mainkan efek suara audio saat ada laporan flag atau dispute baru masuk.</p>
                    </div>
                    <select id="prefAudioAlert" class="input-field w-32 font-bold" onchange="savePreferences()">
                        <option value="true">Aktif</option>
                        <option value="false">Mute</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('admin_token');
    let selectedAvatarFile = null;

    function switchProfileTab(tabId) {
        document.querySelectorAll('.profile-tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');

        document.querySelectorAll('#profileTabs button').forEach(btn => {
            btn.className = 'px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white';
        });

        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.className = 'px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 bg-orange-500/15 text-orange-400 border-b-2 border-orange-500';
        }
    }

    function handleAvatarFileSelect(input) {
        if (input.files && input.files[0]) {
            selectedAvatarFile = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('bannerAvatarPreview').src = e.target.result;
                document.getElementById('bannerAvatarPreview').classList.remove('hidden');
                document.getElementById('bannerAvatarFallback').classList.add('hidden');

                document.getElementById('formAvatarPreview').src = e.target.result;
                document.getElementById('formAvatarPreview').classList.remove('hidden');
                document.getElementById('formAvatarFallback').classList.add('hidden');
            };
            reader.readAsDataURL(selectedAvatarFile);
        }
    }

    async function loadUserProfile() {
        try {
            const res = await fetch('/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const user = data.user || {};

            document.getElementById('profileHeaderName').textContent = user.nama || 'Administrator';
            document.getElementById('profileHeaderEmail').textContent = user.email || '—';
            document.getElementById('profileHeaderRole').textContent = (user.role ? user.role.nama : 'SUPER ADMIN').toUpperCase();

            document.getElementById('inputProfileNama').value = user.nama || '';
            document.getElementById('inputProfileEmail').value = user.email || '';
            document.getElementById('inputProfileUserId').value = '#' + (user.id || '—');

            if (user.foto) {
                document.getElementById('bannerAvatarPreview').src = user.foto;
                document.getElementById('bannerAvatarPreview').classList.remove('hidden');
                document.getElementById('bannerAvatarFallback').classList.add('hidden');

                document.getElementById('formAvatarPreview').src = user.foto;
                document.getElementById('formAvatarPreview').classList.remove('hidden');
                document.getElementById('formAvatarFallback').classList.add('hidden');
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function saveProfileInfo() {
        const nama = document.getElementById('inputProfileNama').value.trim();
        const email = document.getElementById('inputProfileEmail').value.trim();

        if (!nama || !email) {
            Swal.fire({ title: 'Input Wajib', text: 'Nama dan email tidak boleh kosong.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            return;
        }

        const formData = new FormData();
        formData.append('nama', nama);
        formData.append('email', email);
        if (selectedAvatarFile) {
            formData.append('foto', selectedAvatarFile);
        }

        try {
            Swal.fire({
                title: 'Menyimpan Profil...',
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
            const data = await res.json();
            Swal.close();

            if (res.ok) {
                Swal.fire({ title: 'Profil Diperbarui!', text: data.message, icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
                
                // Update local storage user object
                const localUser = JSON.parse(localStorage.getItem('admin_user') || '{}');
                localUser.nama = data.user.nama;
                localUser.email = data.user.email;
                if (data.user.foto) localUser.foto = data.user.foto;
                localStorage.setItem('admin_user', JSON.stringify(localUser));

                document.getElementById('sidebarUserName').textContent = data.user.nama;
                loadUserProfile();
            } else {
                Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Error', text: 'Gagal terhubung ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    async function requestProfilePwdOtp() {
        const btn = document.getElementById('btnRequestProfPwdOtp');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        try {
            const res = await fetch('/api/admin/profile/password/request-otp', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire({
                    title: 'OTP Terkirim!',
                    text: data.message,
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });

                let seconds = data.cooldown_seconds || 60;
                btn.textContent = `Tunggu (${seconds}s)`;
                const timer = setInterval(() => {
                    seconds--;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        btn.disabled = false;
                        btn.textContent = 'Kirim Ulang OTP';
                    } else {
                        btn.textContent = `Tunggu (${seconds}s)`;
                    }
                }, 1000);
            } else {
                btn.disabled = false;
                btn.textContent = 'Kirim Kode OTP';
                Swal.fire({
                    title: 'Gagal Mengirim OTP',
                    text: data.message || 'Terjadi kesalahan.',
                    icon: 'error',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = 'Kirim Kode OTP';
            Swal.fire({
                title: 'Error',
                text: 'Gagal terhubung ke server.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    }

    async function saveNewPassword() {
        const currentPassword = document.getElementById('inputCurrentPassword').value;
        const newPassword = document.getElementById('inputNewPassword').value;
        const confirmPassword = document.getElementById('inputConfirmPassword').value;
        const otpCode = document.getElementById('inputProfilePasswordOtp').value.trim();

        if (newPassword !== confirmPassword) {
            Swal.fire({ title: 'Password Mismatch', text: 'Konfirmasi password baru tidak cocok.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            return;
        }

        if (!otpCode || otpCode.length !== 6) {
            Swal.fire({ title: 'Kode OTP 6-Digit Wajib', text: 'Silakan klik tombol "Kirim Kode OTP" dan masukkan 6 angka kode OTP dari email resmi Anda.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            return;
        }

        try {
            Swal.fire({
                title: 'Mengubah Kata Sandi...',
                allowOutsideClick: false,
                background: '#1E293B',
                color: '#E2E8F0',
                didOpen: () => Swal.showLoading()
            });

            const res = await fetch('/api/admin/profile/password', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword,
                    otp_code: otpCode
                })
            });

            const data = await res.json();
            Swal.close();

            if (res.ok) {
                await Swal.fire({
                    title: 'Kata Sandi Diperbarui!',
                    text: data.message || 'Kata sandi berhasil diperbarui. Seluruh sesi aktif Anda telah diakhiri demi keamanan. Silakan login kembali.',
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });

                localStorage.removeItem('admin_token');
                localStorage.removeItem('admin_user');
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                sessionStorage.clear();
                document.cookie = "auth_token=; path=/; max-age=0;";
                window.location.href = '/login-admin';
            } else {
                Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Error', text: 'Gagal terhubung ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    function savePreferences() {
        const val = document.getElementById('prefAudioAlert').value;
        localStorage.setItem('admin_audio_alert', val);
        Swal.fire({ title: 'Preferensi Disimpan!', icon: 'success', background: '#1E293B', color: '#E2E8F0', timer: 1200, showConfirmButton: false });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadUserProfile();
        const savedAudio = localStorage.getItem('admin_audio_alert');
        if (savedAudio) document.getElementById('prefAudioAlert').value = savedAudio;
    });
</script>
@endsection
