@extends('layouts.superadmin')

@section('title', 'Konfigurasi SMTP Email Dinamis — REVARO Super Admin')
@section('page-title', 'Konfigurasi SMTP Email Dinamis')
@section('page-subtitle', 'Kelola setelan server email keluar (SMTP) untuk pengiriman OTP verifikasi & notifikasi tanpa edit file .env.')

@section('content')
<div class="space-y-8">

    <!-- Top Action & Breadcrumb Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>SUPERADMIN</span> / <span class="text-brandOrange">PENGATURAN SMTP</span>
        </div>
        <div class="flex items-center gap-2">
            @if($smtp && $smtp->is_active && !empty($smtp->mail_host))
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>SMTP STATUS: AKTIF (DATABASE)</span>
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>SMTP STATUS: DEFAULT (.ENV)</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <div>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs space-y-1">
            <p class="font-bold">Terjadi kesalahan validasi data:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Form Pengaturan SMTP (8 Cols) -->
        <div class="lg:col-span-8 card p-6 md:p-8 space-y-6 border-l-4 border-l-orange-500">
            <div>
                <h3 class="text-lg font-black text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Setelan Server Email Keluar (SMTP)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Setelan ini disimpan langsung di database dan otomatis menimpa konfigurasi email Laravel secara instan.
                </p>
            </div>

            <form action="{{ route('superadmin.smtp.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="_admin_token" class="admin-auth-token-input">

                <!-- Status Aktif Toggle -->
                <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white">Gunakan Konfigurasi Dinamis dari Database</h4>
                        <p class="text-[11px] text-slate-400">Jika aktif, Laravel akan menggunakan setelan di bawah ini untuk mengirimkan OTP & notifikasi.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $smtp->is_active ?? true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>

                <!-- Driver & Host -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">MAIL DRIVER *</label>
                        <input type="text" name="mail_mailer" value="{{ old('mail_mailer', $smtp->mail_mailer ?? 'smtp') }}" required
                               class="input-field font-mono text-xs" placeholder="smtp">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">SMTP HOST *</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $smtp->mail_host ?? 'smtp.gmail.com') }}" required
                               class="input-field font-mono text-xs" placeholder="smtp.gmail.com atau mail.domainanda.com">
                    </div>
                </div>

                <!-- Port & Encryption -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">SMTP PORT *</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $smtp->mail_port ?? 587) }}" required
                               class="input-field font-mono text-xs" placeholder="587 / 465 / 25">
                        <span class="text-[10px] text-slate-500 mt-1 block">Umumnya 587 untuk TLS, 465 untuk SSL</span>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ENKRIPSI (SECURITY)</label>
                        <select name="mail_encryption" class="input-field text-xs">
                            <option value="tls" {{ old('mail_encryption', $smtp->mail_encryption ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Direkomendasikan - Port 587)</option>
                            <option value="ssl" {{ old('mail_encryption', $smtp->mail_encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="null" {{ old('mail_encryption', $smtp->mail_encryption ?? '') === null ? 'selected' : '' }}>Tanpa Enkripsi (None)</option>
                        </select>
                    </div>
                </div>

                <!-- Username & Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">SMTP USERNAME / EMAIL *</label>
                        <input type="text" name="mail_username" value="{{ old('mail_username', $smtp->mail_username ?? '') }}" required
                               class="input-field font-mono text-xs" placeholder="contoh: akunanda@gmail.com">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">
                            SMTP PASSWORD / APP PASSWORD
                        </label>
                        <div class="relative">
                            <input type="password" id="smtpPasswordInput" name="mail_password"
                                   class="input-field font-mono text-xs pr-10"
                                   placeholder="{{ !empty($smtp->mail_password) ? '•••••••••••••••• (Tersimpan aman, biarkan kosong jika tidak diubah)' : 'Masukkan password / app password' }}">
                            <button type="button" onclick="togglePasswordVisibility('smtpPasswordInput', 'smtpEyeIcon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-200">
                                <span id="smtpEyeIcon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block">Password dienkripsi otomatis dengan aman di database.</span>
                    </div>
                </div>

                <!-- Sender Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">EMAIL PENGIRIM (FROM ADDRESS) *</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $smtp->mail_from_address ?? '') }}" required
                               class="input-field font-mono text-xs" placeholder="noreply@revaro.com atau akunanda@gmail.com">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NAMA PENGIRIM (FROM NAME) *</label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $smtp->mail_from_name ?? 'REVARO Support') }}" required
                               class="input-field text-xs" placeholder="REVARO Platform">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                    <button type="submit" class="btn-primary text-xs px-6 py-3 font-extrabold shadow-lg shadow-orange-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Simpan &amp; Terapkan Konfigurasi</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Quick Guide & Live Test Tool (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Live Test Email Box -->
            <div class="card p-6 space-y-4 border-t-4 border-t-emerald-500">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <h4 class="text-sm font-extrabold text-white">Uji Coba Pengiriman Email</h4>
                </div>
                <p class="text-xs text-slate-400">
                    Kirim email uji coba instan untuk memastikan server SMTP Anda berhasil terhubung dan tidak terblokir.
                </p>

                <form action="{{ route('superadmin.smtp.test') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="_admin_token" class="admin-auth-token-input">
                    <div>
                        <label class="text-[10px] font-bold text-slate-300 uppercase tracking-wider block mb-1">KIRIM KE EMAIL TUJUAN</label>
                        <input type="email" name="test_email" required
                               class="input-field font-mono text-xs"
                               placeholder="emailanda@gmail.com">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition shadow flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Kirim Email Uji Coba</span>
                    </button>
                </form>
            </div>

            <!-- Gmail Setup Quick Guide -->
            <div class="card p-6 space-y-3 text-xs border border-slate-800 bg-slate-900/60">
                <h4 class="font-black text-amber-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span>Panduan Menggunakan Gmail Pribadi</span>
                </h4>
                <ol class="list-decimal list-inside space-y-2 text-slate-300 leading-relaxed">
                    <li>Buka akun Google Anda di <a href="https://myaccount.google.com/security" target="_blank" class="text-orange-400 hover:underline font-bold">myaccount.google.com/security</a>.</li>
                    <li>Pastikan **Verifikasi 2 Langkah (2-Step Verification)** sudah aktif.</li>
                    <li>Cari menu **Sandi Aplikasi (App Passwords)**.</li>
                    <li>Buat sandi baru (beri nama "REVARO"), salin 16-karakter sandi yang muncul (misal: <code class="bg-slate-800 px-1 py-0.5 rounded text-amber-300">xxxx xxxx xxxx xxxx</code>).</li>
                    <li>Isi **Host**: <code class="text-emerald-400">smtp.gmail.com</code>, **Port**: <code class="text-emerald-400">587</code>, **Enkripsi**: <code class="text-emerald-400">TLS</code>, dan masukkan **App Password** tersebut ke kolom password di samping.</li>
                </ol>
            </div>

            <!-- Company/Hosting Email Guide -->
            <div class="card p-6 space-y-2 text-xs border border-slate-800 bg-slate-900/60">
                <h4 class="font-black text-blue-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    <span>Migrasi ke Email Hosting Perusahaan</span>
                </h4>
                <p class="text-slate-400 leading-relaxed">
                    Saat proyek sudah di-hosting, cukup buka halaman ini dan ganti **Host** dengan <code class="text-blue-300">mail.namadomain.com</code>, serta username/password email bisnis Anda tanpa perlu menyentuh file server sama sekali.
                </p>
            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
        if (token) {
            document.querySelectorAll('.admin-auth-token-input').forEach(input => {
                input.value = token;
            });
            document.cookie = "admin_token=" + encodeURIComponent(token) + "; path=/; SameSite=Lax";
        }
    });

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
        }
    }
</script>
@endsection
