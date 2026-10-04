@extends('layouts.superadmin')

@section('title', 'Pengaturan Platform & Akun Super Admin — REVARO')
@section('page-title', 'Pengaturan Platform & Akun Super Admin')
@section('page-subtitle', 'Kelola profil Super Admin, ubah kata sandi, dan pantau informasi spesifikasi lingkungan platform.')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action & Breadcrumb Banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-2">
            <span>DK</span> / <span>SUPERADMIN</span> / <span class="text-brandOrange">PENGATURAN</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>SYSTEM PARAMETERS SYNCE0</span>
            </span>
        </div>
    </div>

    <!-- 2 Column Section: Profil & Sandi Super Admin + Spesifikasi Lingkungan Platform -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Box (7 cols): Profil & Sandi Super Admin -->
        <div class="lg:col-span-7 card p-6 md:p-8 space-y-6 border-l-4 border-l-orange-500">
            <div>
                <h3 class="text-lg font-black text-white">Profil & Sandi Super Admin</h3>
                <p class="text-xs text-slate-400 mt-1">Perbarui informasi identitas dan keamanan akun platform owner.</p>
            </div>

            <form id="adminPersonalForm" onsubmit="event.preventDefault(); savePersonalProfile();" class="space-y-5">
                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NAMA LENGKAP SUPER ADMIN *</label>
                    <input type="text" id="settingProfileName" required class="input-field font-bold" placeholder="Super Admin Platform">
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ALAMAT SUREL LOGIN *</label>
                    <input type="email" id="settingProfileEmail" required class="input-field font-mono" placeholder="superadmin@revaro.com">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">NOMOR TELEPON</label>
                        <input type="text" id="settingProfilePhone" class="input-field font-mono" placeholder="081200000000">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">ALAMAT KANTOR PUSAT</label>
                        <input type="text" id="settingProfileAddress" class="input-field" placeholder="Kantor Pusat REVARO Platform">
                    </div>
                </div>

                <!-- Password change sub-section -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block">UBAH KATA SANDI (OPSIONAL)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <input type="password" id="settingNewPassword" minlength="6" class="input-field pr-10" placeholder="Kata sandi baru">
                            <button type="button" onclick="toggleGenericPassword('settingNewPassword', 'setEye1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                <span id="setEye1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="password" id="settingConfirmPassword" minlength="6" class="input-field pr-10" placeholder="Ulangi kata sandi">
                            <button type="button" onclick="toggleGenericPassword('settingConfirmPassword', 'setEye2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white">
                                <span id="setEye2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs py-3.5 px-6 rounded-xl border border-slate-700 transition shadow-lg flex items-center justify-center gap-2">
                    <span>✓</span> Simpan Perubahan Profil & Password
                </button>
            </form>
        </div>

        <!-- Right Box (5 cols): Spesifikasi Lingkungan Platform -->
        <div class="lg:col-span-5 card p-6 md:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-black text-white">Spesifikasi Lingkungan Platform</h3>
                <p class="text-xs text-slate-400 mt-1">Status dan versi komponen teknis server yang aktif saat ini.</p>
            </div>

            <!-- Super Admin Profile Card Header -->
            <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-black text-xl border border-amber-500/30 shrink-0">
                    S
                </div>
                <div class="space-y-0.5">
                    <h4 class="text-sm font-extrabold text-white" id="displayProfileName">Super Admin Platform</h4>
                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-amber-500/15 text-amber-400 border border-amber-500/30 uppercase tracking-wider inline-block">
                        SUPREME PLATFORM CONTROLLER
                    </span>
                </div>
            </div>

            <!-- Key-Value System Specs -->
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
                    <span class="text-slate-400 uppercase">ENVIRONMENT</span>
                    <span class="text-emerald-400 font-bold uppercase">{{ config('app.env', 'local') }}</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-slate-800/80">
                    <span class="text-slate-400 uppercase">STATUS DEBUG</span>
                    <span class="text-emerald-400 font-bold">{{ config('app.debug') ? 'Aktif' : 'Nonaktif' }}</span>
                </div>

                <div class="flex items-center justify-between py-2">
                    <span class="text-slate-400 uppercase">ZONA WAKTU</span>
                    <span class="text-white font-bold">{{ config('app.timezone', 'Asia/Jakarta') }} (WIB)</span>
                </div>
            </div>

            <!-- Supreme Active Footer Box -->
            <div class="p-4 bg-slate-900 rounded-xl border border-slate-800 space-y-1">
                <p class="text-[10px] font-extrabold text-amber-400 uppercase tracking-widest flex items-center gap-1.5">
                    <span>SUPREME PLATFORM CONTROLLER ACTIVE</span>
                </p>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Seluruh instruksi kontrol platform, backup database, dan audit keamanan berjalan dalam status aktif 100%.
                </p>
            </div>
        </div>
    </div>

    <!-- Section 2: Pintasan Cepat Modul SuperAdmin (Quick Shortcuts) -->
    <div class="space-y-4">
        <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span>Pintasan Cepat Modul SuperAdmin</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Shortcut 1: Dasbor Platform -->
            <a href="/superadmin/pemilik/dashboard" class="card p-5 hover:border-orange-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-400 flex items-center justify-center border border-orange-500/20 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-orange-400 transition">Dasbor Platform</h4>
                    <p class="text-xs text-slate-400">Overview ekosistem</p>
                </div>
            </a>

            <!-- Shortcut 2: Daftar Admin Staf -->
            <a href="/superadmin/pemilik/accounts" class="card p-5 hover:border-emerald-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-emerald-400 transition">Daftar Admin Staf</h4>
                    <p class="text-xs text-slate-400">Kelola akun instansi</p>
                </div>
            </a>

            <!-- Shortcut 3: Mode Maintenance -->
            <a href="/superadmin/pemilik/maintenance" class="card p-5 hover:border-blue-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-blue-400 transition">Mode Maintenance</h4>
                    <p class="text-xs text-slate-400">Kontrol status akses</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Section 3: Konfigurasi Parametrik Platform (Tabs) -->
    <div class="space-y-6 pt-4 border-t border-slate-800">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold text-white">Konfigurasi Parametrik Platform</h3>
                <p class="text-xs text-slate-400">Kelola parameter operasional, komisi, SLA, dan feature flags sistem.</p>
            </div>
            <button onclick="saveAllSettings()" class="btn-primary text-xs px-5 py-2.5 font-extrabold shadow-lg shadow-orange-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span>Simpan Parameter Sistem</span>
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-800" id="settingsTabs">
            <button onclick="switchTab('tab-general')" id="btn-tab-general" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 bg-orange-500/15 text-orange-400 border-b-2 border-orange-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Umum &amp; Identitas</span>
            </button>
            <button onclick="switchTab('tab-branding')" id="btn-tab-branding" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Logo &amp; Aset Gambar</span>
            </button>
            <button onclick="switchTab('tab-financial')" id="btn-tab-financial" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Komisi &amp; Keuangan</span>
            </button>
            <button onclick="switchTab('tab-sla')" id="btn-tab-sla" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>SLA &amp; Ambang Batas</span>
            </button>
            <button onclick="switchTab('tab-features')" id="btn-tab-features" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Fitur &amp; Maintenance</span>
            </button>
            <button onclick="switchTab('tab-security')" id="btn-tab-security" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Keamanan &amp; Sesi</span>
            </button>
            <button onclick="switchTab('tab-maps')" id="btn-tab-maps" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Integrasi Maps (GOAPI)</span>
            </button>
        </div>

        <!-- Form Container -->
        <form id="settingsForm" onsubmit="event.preventDefault(); saveAllSettings();">

            <!-- Tab 1: Umum & Identitas Platform -->
            <div id="tab-general" class="tab-content space-y-6">
                <div class="card p-6">
                    <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 0h-2m2 0h2"/></svg>
                        <span>Identitas Platform &amp; Layanan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mb-6">Informasi identitas publik platform REVARO dan kontak bantuan.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Nama Resmi Platform</label>
                            <input type="text" id="setting_app_name" class="input-field" placeholder="REVARO — Replikasi & Variasi Otomotif">
                            <p class="text-[10px] text-slate-500 mt-1">Ditampilkan pada judul aplikasi, email, dan invoice.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Tagline Platform</label>
                            <input type="text" id="setting_app_tagline" class="input-field" placeholder="Solusi Perbaikan & Komponen Otomotif Terpercaya">
                            <p class="text-[10px] text-slate-500 mt-1">Slogan utama pada landing page.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Email Customer Support</label>
                            <input type="email" id="setting_cs_email" class="input-field font-mono" placeholder="support@revaro.id">
                            <p class="text-[10px] text-slate-500 mt-1">Alamat email tujuan pesan kendala pengguna.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Call Center / WhatsApp CS</label>
                            <input type="text" id="setting_cs_phone" class="input-field font-mono" placeholder="0812-3456-7890">
                            <p class="text-[10px] text-slate-500 mt-1">Nomor kontak resmi hotline pelanggan.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Jam Operasional Service Dispatch</label>
                            <input type="text" id="setting_operating_hours" class="input-field" placeholder="08:00 - 21:00 WIB">
                            <p class="text-[10px] text-slate-500 mt-1">Waktu penerimaan order servis reguler.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Mata Uang Transaksi Default</label>
                            <select id="setting_default_currency" class="input-field">
                                <option value="IDR">Rupiah Indonesia (IDR - Rp)</option>
                                <option value="USD">US Dollar (USD - $)</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">Format penulisan harga pada seluruh sistem.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block mb-1.5">Bahasa Default Platform (Global i18n)</label>
                            <select id="setting_app_language" class="input-field font-bold" onchange="window.setAppLanguage(this.value)">
                                <option value="id">🇮🇩 Bahasa Indonesia (id)</option>
                                <option value="en">🇬🇧 English (en)</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">Mengubah bahasa tampilan secara global di seluruh portal platform.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Logo & Visual Branding -->
            <div id="tab-branding" class="tab-content space-y-6 hidden">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Upload Logo &amp; Aset Gambar Branding</span>
                        </h3>
                        <span class="text-[10px] font-bold bg-orange-500/20 text-orange-400 px-2.5 py-0.5 rounded-full border border-orange-500/30">SUPPORT PNG, JPG, SVG, WEBP</span>
                    </div>
                    <p class="text-xs text-slate-400 mb-6">Unggah berkas foto/gambar resmi untuk logo header, favicon, hero banner, dan stempel verifikasi.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Item 1: Logo Utama Platform -->
                        <div class="p-5 bg-slate-900/60 rounded-2xl border border-slate-800 space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-white uppercase tracking-wider">1. Logo Utama Platform</label>
                                    <span class="text-[10px] text-slate-400 font-mono">PNG / SVG Transparent</span>
                                </div>
                                <div class="w-full h-32 rounded-xl bg-slate-950 border-2 border-dashed border-slate-800 flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview_app_logo" src="" alt="Preview Logo" class="max-h-24 max-w-full object-contain hidden">
                                    <div id="empty_app_logo" class="text-center p-3">
                                        <svg class="w-8 h-8 text-slate-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs font-bold text-slate-400">Belum ada logo diunggah</p>
                                        <p class="text-[10px] text-slate-500">Logo standar REVARO aktif</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 512x512 px. Rasio persegi atau lanskap transparan.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_logo" accept="image/*" class="hidden" onchange="previewImage('app_logo')">
                                <button type="button" onclick="document.getElementById('file_app_logo').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    <span>Pilih Gambar</span>
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_logo')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Upload</span>
                                </button>
                            </div>
                        </div>

                        <!-- Item 2: Favicon Browser & Icon -->
                        <div class="p-5 bg-slate-900/60 rounded-2xl border border-slate-800 space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-white uppercase tracking-wider">2. Favicon Browser &amp; Icon App</label>
                                    <span class="text-[10px] text-slate-400 font-mono">ICO / PNG Square</span>
                                </div>
                                <div class="w-full h-32 rounded-xl bg-slate-950 border-2 border-dashed border-slate-800 flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview_app_favicon" src="" alt="Preview Favicon" class="w-12 h-12 object-contain hidden">
                                    <div id="empty_app_favicon" class="text-center p-3">
                                        <svg class="w-8 h-8 text-slate-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        <p class="text-xs font-bold text-slate-400">Belum ada favicon diunggah</p>
                                        <p class="text-[10px] text-slate-500">Favicon standar browser aktif</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 64x64 px atau 128x128 px rasio 1:1.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_favicon" accept="image/*" class="hidden" onchange="previewImage('app_favicon')">
                                <button type="button" onclick="document.getElementById('file_app_favicon').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    <span>Pilih Gambar</span>
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_favicon')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Upload</span>
                                </button>
                            </div>
                        </div>

                        <!-- Item 3: Banner Hero Landing Page -->
                        <div class="p-5 bg-slate-900/60 rounded-2xl border border-slate-800 space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-white uppercase tracking-wider">3. Hero Banner Background</label>
                                    <span class="text-[10px] text-slate-400 font-mono">JPG / WEBP HD</span>
                                </div>
                                <div class="w-full h-32 rounded-xl bg-slate-950 border-2 border-dashed border-slate-800 flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview_app_banner" src="" alt="Preview Banner" class="w-full h-full object-cover hidden">
                                    <div id="empty_app_banner" class="text-center p-3">
                                        <svg class="w-8 h-8 text-slate-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 0h-2m2 0h2"/></svg>
                                        <p class="text-xs font-bold text-slate-400">Belum ada banner diunggah</p>
                                        <p class="text-[10px] text-slate-500">Banner gradien standar aktif</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 1920x1080 px lanskap HD.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_banner" accept="image/*" class="hidden" onchange="previewImage('app_banner')">
                                <button type="button" onclick="document.getElementById('file_app_banner').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    <span>Pilih Gambar</span>
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_banner')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Upload</span>
                                </button>
                            </div>
                        </div>

                        <!-- Item 4: Stempel & Tanda Tangan Digital -->
                        <div class="p-5 bg-slate-900/60 rounded-2xl border border-slate-800 space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-white uppercase tracking-wider">4. Stempel Verifikasi Digital</label>
                                    <span class="text-[10px] text-slate-400 font-mono">PNG Transparan</span>
                                </div>
                                <div class="w-full h-32 rounded-xl bg-slate-950 border-2 border-dashed border-slate-800 flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview_app_stamp" src="" alt="Preview Stamp" class="max-h-24 max-w-full object-contain hidden">
                                    <div id="empty_app_stamp" class="text-center p-3">
                                        <svg class="w-8 h-8 text-slate-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-xs font-bold text-slate-400">Belum ada stempel diunggah</p>
                                        <p class="text-[10px] text-slate-500">Cap resmi sistem</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 400x400 px PNG transparan.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_stamp" accept="image/*" class="hidden" onchange="previewImage('app_stamp')">
                                <button type="button" onclick="document.getElementById('file_app_stamp').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    <span>Pilih Gambar</span>
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_stamp')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Upload</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Komisi & Keuangan Platform -->
            <div id="tab-financial" class="tab-content space-y-6 hidden">
                <div class="card p-6">
                    <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kebijakan Komisi &amp; Penarikan Dana</span>
                    </h3>
                    <p class="text-xs text-slate-400 mb-6">Atur bagi hasil komisi platform dari jasa servis dan sparepart mitra.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 space-y-2">
                            <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block">Komisi Jasa Servis (%)</label>
                            <div class="flex items-center gap-3">
                                <input type="number" step="0.1" min="0" max="100" id="setting_commission_jasa_percent" class="input-field font-mono text-lg font-bold w-36">
                                <span class="text-sm font-bold text-slate-300">% dari total jasa</span>
                            </div>
                            <p class="text-[10px] text-slate-400">Potongan komisi otomatis saat transaksi servis diselesaikan oleh teknisi.</p>
                        </div>

                        <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 space-y-2">
                            <label class="text-[11px] font-bold text-blue-400 uppercase tracking-wider block">Fee Toko Sparepart (%)</label>
                            <div class="flex items-center gap-3">
                                <input type="number" step="0.1" min="0" max="100" id="setting_fee_sparepart_percent" class="input-field font-mono text-lg font-bold w-36">
                                <span class="text-sm font-bold text-slate-300">% dari harga sparepart</span>
                            </div>
                            <p class="text-[10px] text-slate-400">Fee potongan dari setiap penjualan sparepart oleh mitra toko.</p>
                        </div>

                        <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 space-y-2">
                            <label class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block">Batas Minimum Payout Wallet (Rp)</label>
                            <div class="flex items-center gap-3">
                                <span class="text-slate-400 font-bold font-mono">Rp</span>
                                <input type="number" min="10000" step="5000" id="setting_min_payout_amount" class="input-field font-mono text-base font-bold">
                            </div>
                            <p class="text-[10px] text-slate-400">Syarat minimal saldo wallet mitra untuk dapat mengajukan penarikan dana.</p>
                        </div>

                        <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 space-y-2">
                            <label class="text-[11px] font-bold text-purple-400 uppercase tracking-wider block">Auto-Release Dispute (Hari)</label>
                            <div class="flex items-center gap-3">
                                <input type="number" min="1" max="30" id="setting_auto_release_days" class="input-field font-mono text-base font-bold w-32">
                                <span class="text-sm font-bold text-slate-300">Hari Kalender</span>
                            </div>
                            <p class="text-[10px] text-slate-400">Pencairan otomatis dana transaksi ke mitra jika pelanggan tidak melakukan komplain.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: SLA & Ambang Batas Sistem -->
            <div id="tab-sla" class="tab-content space-y-6 hidden">
                <div class="card p-6">
                    <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Service Level Agreement (SLA) &amp; Ambang Batas</span>
                    </h3>
                    <p class="text-xs text-slate-400 mb-6">Tentukan standar performa layanan dan batas toleransi pengawasan operational.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Max Respond Verifikasi Berkas (Jam)</label>
                            <input type="number" min="1" max="72" id="setting_max_verification_hours" class="input-field font-mono font-bold" placeholder="24">
                            <p class="text-[10px] text-slate-500 mt-1">Target waktu maksimal Admin Verifikator dalam memproses pendaftaran mitra.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Target Kedatangan Teknisi (Menit)</label>
                            <input type="number" min="15" max="180" id="setting_target_technician_arrival_minutes" class="input-field font-mono font-bold" placeholder="45">
                            <p class="text-[10px] text-slate-500 mt-1">Estimasi standar waktu teknisi tiba di lokasi pesanan SOS.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Max Pembatalan Pesanan / Hari</label>
                            <input type="number" min="1" max="20" id="setting_max_cancel_per_day" class="input-field font-mono font-bold" placeholder="5">
                            <p class="text-[10px] text-slate-500 mt-1">Batas toleransi pembatalan order oleh mitra sebelum otomatis di-flag SLA Alert.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Fitur & Maintenance Mode -->
            <div id="tab-features" class="tab-content space-y-6 hidden">
                <div class="card p-6">
                    <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        <span>Feature Flags &amp; Modus Pemeliharaan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mb-6">Aktifkan atau nonaktifkan modul platform secara fleksibel.</p>

                    <div class="space-y-6">
                        <div class="p-4 bg-slate-900/60 rounded-xl border border-red-500/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-extrabold text-white">Maintenance Mode (Mode Pemeliharaan)</span>
                                    <span id="badgeMaintenanceStatus" class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">NONAKTIF</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Jika diaktifkan, portal pengguna umum (Pelanggan/Mitra) akan menampilkan pengumuman pemeliharaan.</p>
                            </div>
                            <div>
                                <select id="setting_maintenance_mode" class="input-field font-bold w-36" onchange="updateMaintenanceBadge()">
                                    <option value="false">Off (Normal)</option>
                                    <option value="true">On (Aktifkan)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Pesan Pengumuman Maintenance</label>
                            <textarea id="setting_maintenance_message" rows="2" class="input-field" placeholder="Pesan pemeliharaan sistem..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-800">
                            <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800">
                                <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-2">Pendaftaran Mitra Baru</label>
                                <select id="setting_partner_registration_open" class="input-field font-bold">
                                    <option value="true">Open (Dibuka)</option>
                                    <option value="false">Closed (Ditutup)</option>
                                </select>
                                <p class="text-[10px] text-slate-500 mt-2">Membuka atau menutup formulir registrasi teknisi & toko baru.</p>
                            </div>

                            <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800">
                                <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-2">Instant Chat Pelanggan-Teknisi</label>
                                <select id="setting_customer_technician_chat" class="input-field font-bold">
                                    <option value="true">Aktif (Enabled)</option>
                                    <option value="false">Nonaktif (Disabled)</option>
                                </select>
                                <p class="text-[10px] text-slate-500 mt-2">Fitur kirim pesan langsung saat order aktif.</p>
                            </div>

                            <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800">
                                <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-2">Broadcast Notifikasi WA/Email</label>
                                <select id="setting_broadcast_notifications" class="input-field font-bold">
                                    <option value="true">Aktif (Enabled)</option>
                                    <option value="false">Nonaktif (Disabled)</option>
                                </select>
                                <p class="text-[10px] text-slate-500 mt-2">Pengiriman pesan otomatis via email dan gateway WA.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Keamanan & Sesi Admin -->
            <div id="tab-security" class="tab-content space-y-6 hidden">
                <div class="card p-6">
                    <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Kebijakan Keamanan &amp; Masa Berlaku Sesi</span>
                    </h3>
                    <p class="text-xs text-slate-400 mb-6">Kelola keamanan autentikasi akun staf dan penyimpanan log audit.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Durasi Sesi Login Admin (Menit)</label>
                            <input type="number" min="15" max="1440" id="setting_session_lifetime_minutes" class="input-field font-mono font-bold" placeholder="120">
                            <p class="text-[10px] text-slate-500 mt-1">Sesi login otomatis expired jika tidak ada aktivitas dalam durasi ini.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Batas Failed Login Attempts</label>
                            <input type="number" min="3" max="10" id="setting_max_login_attempts" class="input-field font-mono font-bold" placeholder="5">
                            <p class="text-[10px] text-slate-500 mt-1">Jumlah batas kesalahan kata sandi sebelum IP/akun terproteksi sementara.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Wajibkan MFA / 2FA Super Admin</label>
                            <select id="setting_force_mfa_admin" class="input-field font-bold">
                                <option value="false">Opsional (Disabled)</option>
                                <option value="true">Wajib (Enabled)</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">Mewajibkan verifikasi OTP dua faktor saat login sebagai Super Admin.</p>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">Masa Simpan Audit Log (Hari)</label>
                            <input type="number" min="30" max="365" id="setting_audit_retention_days" class="input-field font-mono font-bold" placeholder="90">
                            <p class="text-[10px] text-slate-500 mt-1">Jangka waktu penyimpanan data audit log aktivitas sebelum diarsipkan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 7: Integrasi Maps API & AI Geokoding (GOAPI - Siklus 10 Minggu) -->
            <div id="tab-maps" class="tab-content space-y-6 hidden">
                <div class="card p-6 md:p-8 space-y-6 border-l-4 border-l-blue-500">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Integrasi Engine Maps &amp; Geocoding (GOAPI)</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                Konfigurasi kunci otentikasi GOAPI, batas jangkauan radar teknisi, dan tarif fitur jangkauan luas prioritas.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="badgeMapsKeyStatus" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                KEY AKTIF
                            </span>
                        </div>
                    </div>

                    <!-- 10-Week Renewal Cycle Tracker Card -->
                    <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-orange-400 bg-orange-500/15 px-2.5 py-0.5 rounded-full border border-orange-500/30">
                                    SIKLUS PEMBARUAN 10 MINGGU
                                </span>
                                <h4 class="text-sm font-extrabold text-white mt-1.5">Masa Berlaku API Key GOAPI</h4>
                            </div>
                            <div class="text-right">
                                <span id="mapsCycleCountdown" class="text-sm font-black text-amber-400 font-mono">Memuat siklus...</span>
                                <p class="text-[10px] text-slate-400">Total masa siklus: 10 Minggu (70 Hari)</p>
                            </div>
                        </div>

                        <!-- Progress Bar Siklus -->
                        <div class="space-y-1.5">
                            <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-700">
                                <div id="mapsCycleProgressBar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-amber-500 transition-all duration-500" style="width: 50%;"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 font-mono">
                                <span id="mapsLastUpdatedDate">Diperbarui: —</span>
                                <span id="mapsProgressPercent">50% terpakai</span>
                            </div>
                        </div>

                        <!-- Notice banner -->
                        <div id="mapsCycleWarningAlert" class="p-3.5 rounded-xl border border-amber-500/30 bg-amber-500/10 text-xs text-amber-200 leading-relaxed flex items-start gap-2.5">
                            <i class="fas fa-info-circle text-amber-400 mt-0.5 text-sm shrink-0"></i>
                            <div>
                                <strong>Pemberitahuan Rutin:</strong> Sesuai kebijakan GOAPI, API Key perlu diperbarui setiap <strong>10 minggu sekali</strong>. Masukkan key baru di formulir bawah ini dan klik <em>"Simpan &amp; Reset Siklus"</em> untuk langsung memperbarui peta seluruh sistem (Pelanggan, Teknisi &amp; Dispatch SOS).
                            </div>
                        </div>
                    </div>

                    <!-- Maps API Parameters Form -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- Input GOAPI Key -->
                        <div class="md:col-span-2">
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">
                                Kunci Otentikasi GOAPI (API Key) *
                            </label>
                            <div class="relative">
                                <input type="text" id="setting_maps_goapi_key" class="input-field font-mono text-sm tracking-wide pr-24" placeholder="ff44d139-e2e0-51a5-9f35-312a2392">
                                <button type="button" onclick="testMapsConnection()" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-bold transition flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-satellite-dish"></i> <span>Tes Key</span>
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Kunci autentikasi resmi GOAPI reverse geocoding &amp; location suggest engine Indonesia.</p>
                        </div>

                        <!-- Biaya Tambahan Jangkauan Luas Prioritas (Fitur Premium 20 KM) -->
                        <div>
                            <label class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block mb-1.5">
                                Biaya Tambahan Fitur Jangkauan Premium 20 KM (Rp) *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" min="0" step="1000" id="setting_maps_premium_fee" class="input-field font-mono font-bold pl-10" placeholder="25000">
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Biaya tambahan yang dikenakan ke pelanggan saat memilih opsi Jangkauan Luas Prioritas (20 KM).</p>
                        </div>

                        <!-- Radius Reguler Standar -->
                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">
                                Radius Standar Reguler (KM) *
                            </label>
                            <div class="relative">
                                <input type="number" min="1" max="50" id="setting_maps_regular_radius_km" class="input-field font-mono font-bold pr-12" placeholder="7">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">KM</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Radius standar default pencarian teknisi di lingkungan sekitar (Default: 7 KM).</p>
                        </div>

                        <!-- Radius Maksimal Prioritas Premium -->
                        <div>
                            <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block mb-1.5">
                                Radius Maksimal Jangkauan Prioritas (KM) *
                            </label>
                            <div class="relative">
                                <input type="number" min="5" max="100" id="setting_maps_premium_radius_km" class="input-field font-mono font-bold pr-12" placeholder="20">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">KM</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Jangkauan radar maksimal untuk pesanan dengan fitur prioritas premium (Default: 20 KM).</p>
                        </div>

                        <!-- Checkbox Reset Siklus 10 Minggu -->
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <input type="checkbox" id="setting_maps_reset_cycle" class="accent-orange-500 w-4 h-4 rounded cursor-pointer">
                            <label for="setting_maps_reset_cycle" class="text-xs font-bold text-slate-300 cursor-pointer">
                                Reset siklus 10 minggu ke hari ini (Gunakan opsi ini saat baru memperbarui GOAPI Key)
                            </label>
                        </div>
                    </div>

                    <!-- Live Test Result Box (Hidden until tested) -->
                    <div id="mapsTestResultCard" class="hidden p-4 rounded-xl border border-blue-500/30 bg-slate-900/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Hasil Diagnosa Koneksi Maps &amp; Geocoding:</span>
                            </span>
                            <span id="mapsTestLatency" class="text-xs font-mono font-bold text-emerald-400">0 ms</span>
                        </div>
                        <p id="mapsTestMessage" class="text-xs text-slate-300">Menghubungkan ke engine GOAPI...</p>
                        <div class="text-[11px] text-slate-400 font-mono bg-slate-950 p-2.5 rounded border border-slate-800">
                            Sample Geocoding: <span id="mapsTestSample" class="text-slate-200 font-semibold">—</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" onclick="testMapsConnection()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition flex items-center justify-center gap-2">
                            <i class="fas fa-vial"></i> <span>Uji Validitas GOAPI</span>
                        </button>
                        <button type="button" onclick="saveMapsSettings()" class="w-full sm:w-auto btn-primary text-xs px-6 py-2.5 font-extrabold shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> <span>Simpan Konfigurasi Maps</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('admin_token');
    const user = JSON.parse(localStorage.getItem('admin_user') || '{}');

    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(tabId).classList.remove('hidden');

        document.querySelectorAll('#settingsTabs button').forEach(btn => {
            btn.className = 'px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white';
        });

        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.className = 'px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 bg-orange-500/15 text-orange-400 border-b-2 border-orange-500';
        }
    }

    function updateMaintenanceBadge() {
        const val = document.getElementById('setting_maintenance_mode').value;
        const badge = document.getElementById('badgeMaintenanceStatus');
        if (val === 'true') {
            badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30';
            badge.textContent = 'AKTIF (MAINTENANCE)';
        } else {
            badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
            badge.textContent = 'NORMAL (OFF)';
        }
    }

    function previewImage(key) {
        const fileInput = document.getElementById('file_' + key);
        const file = fileInput.files[0];
        const preview = document.getElementById('preview_' + key);
        const empty = document.getElementById('empty_' + key);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                empty.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    async function uploadImageAsset(key) {
        const fileInput = document.getElementById('file_' + key);
        const file = fileInput.files[0];

        if (!file) {
            Swal.fire({
                title: 'Pilih File Gambar',
                text: 'Silakan pilih berkas gambar terlebih dahulu sebelum mengunggah.',
                icon: 'warning',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            return;
        }

        const formData = new FormData();
        formData.append('key', key);
        formData.append('file', file);

        try {
            Swal.fire({
                title: 'Mengunggah Gambar...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                background: '#1E293B',
                color: '#E2E8F0',
                didOpen: () => Swal.showLoading()
            });

            const res = await fetch('/api/admin/settings/upload-asset', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });

            const data = await res.json();
            Swal.close();

            if (res.ok) {
                Swal.fire({
                    title: 'Upload Berhasil!',
                    text: data.message,
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
                
                const preview = document.getElementById('preview_' + key);
                const empty = document.getElementById('empty_' + key);
                preview.src = data.url;
                preview.classList.remove('hidden');
                empty.classList.add('hidden');
            } else {
                Swal.fire({
                    title: 'Gagal Upload',
                    text: data.message || 'Terjadi kesalahan saat mengunggah gambar.',
                    icon: 'error',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch (e) {
            console.error(e);
            Swal.fire({ title: 'Error', text: 'Gagal terhubung ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    async function loadAdminPersonalProfile() {
        document.getElementById('settingProfileName').value = user.nama || 'Super Admin Platform';
        document.getElementById('settingProfileEmail').value = user.email || 'superadmin@revaro.com';
        document.getElementById('displayProfileName').textContent = user.nama || 'Super Admin Platform';
        document.getElementById('settingProfilePhone').value = user.telepon || '081200000000';
        document.getElementById('settingProfileAddress').value = 'Kantor Pusat REVARO Platform';
    }

    async function savePersonalProfile() {
        const nama = document.getElementById('settingProfileName').value.trim();
        const email = document.getElementById('settingProfileEmail').value.trim();
        const newPassword = document.getElementById('settingNewPassword').value;
        const confirmPassword = document.getElementById('settingConfirmPassword').value;

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
                document.getElementById('displayProfileName').textContent = data.user.nama;
                document.getElementById('settingNewPassword').value = '';
                document.getElementById('settingConfirmPassword').value = '';

                Swal.fire({ title: 'Profil Disimpan!', text: 'Informasi akun & kata sandi Super Admin berhasil diperbarui.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            } else {
                let errMsg = data.message || 'Terjadi kesalahan server.';
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

    async function loadSettings() {
        try {
            const res = await fetch('/api/admin/settings', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Gagal memuat setting');

            const data = await res.json();
            const settings = data.settings || {};

            Object.keys(settings).forEach(key => {
                const input = document.getElementById('setting_' + key);
                if (input) {
                    input.value = settings[key];
                }

                if (['app_logo', 'app_favicon', 'app_banner', 'app_stamp'].includes(key)) {
                    const imgUrl = settings[key];
                    const preview = document.getElementById('preview_' + key);
                    const empty = document.getElementById('empty_' + key);
                    if (imgUrl && preview && empty) {
                        preview.src = imgUrl;
                        preview.classList.remove('hidden');
                        empty.classList.add('hidden');
                    }
                }
            });

            updateMaintenanceBadge();
        } catch (e) {
            console.error(e);
        }
    }

    async function saveAllSettings() {
        const payload = {};
        const keys = [
            'app_name', 'app_tagline', 'cs_email', 'cs_phone', 'operating_hours', 'default_currency', 'app_language',
            'commission_jasa_percent', 'fee_sparepart_percent', 'min_payout_amount', 'auto_release_days',
            'max_verification_hours', 'target_technician_arrival_minutes', 'max_cancel_per_day',
            'maintenance_mode', 'maintenance_message', 'partner_registration_open', 'customer_technician_chat', 'broadcast_notifications',
            'session_lifetime_minutes', 'max_login_attempts', 'force_mfa_admin', 'audit_retention_days'
        ];

        keys.forEach(k => {
            const input = document.getElementById('setting_' + k);
            if (input) {
                payload[k] = input.value;
            }
        });

        try {
            Swal.fire({
                title: 'Menyimpan Parameter...',
                text: 'Memperbarui parameter sistem platform.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                background: '#1E293B',
                color: '#E2E8F0',
            });

            const res = await fetch('/api/admin/settings', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ settings: payload })
            });

            const result = await res.json();
            Swal.close();

            if (res.ok) {
                Swal.fire({
                    title: 'Pengaturan Disimpan!',
                    text: result.message || 'Seluruh perubahan parameter sistem telah berhasil diberlakukan.',
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch (e) {
            console.error(e);
        }
    }

    // --- Maps & GOAPI Lifecycle Management ---
    async function loadMapsSettings() {
        try {
            const res = await fetch('/api/admin/settings/maps', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const data = await res.json();
            
            document.getElementById('setting_maps_goapi_key').value = data.maps_goapi_key || '';
            document.getElementById('setting_maps_premium_fee').value = data.maps_premium_fee || 25000;
            document.getElementById('setting_maps_regular_radius_km').value = data.maps_regular_radius_km || 7;
            document.getElementById('setting_maps_premium_radius_km').value = data.maps_premium_radius_km || 20;

            const cycle = data.cycle_info || {};
            const countdownEl = document.getElementById('mapsCycleCountdown');
            const progressEl = document.getElementById('mapsCycleProgressBar');
            const lastUpdatedEl = document.getElementById('mapsLastUpdatedDate');
            const percentEl = document.getElementById('mapsProgressPercent');
            const badgeEl = document.getElementById('badgeMapsKeyStatus');
            const warningEl = document.getElementById('mapsCycleWarningAlert');

            if (lastUpdatedEl) {
                lastUpdatedEl.textContent = `Pembaruan: ${data.maps_goapi_updated_at_formatted || '—'}`;
            }

            if (countdownEl) {
                if (cycle.is_expired) {
                    countdownEl.innerHTML = `<span class="text-red-400 font-extrabold animate-pulse">KADALUARSA (${cycle.days_elapsed} hari lalu)</span>`;
                } else {
                    countdownEl.innerHTML = `<span>${cycle.weeks_remaining} Minggu ${cycle.days_remaining % 7} Hari Tersisa</span>`;
                }
            }

            if (progressEl) {
                progressEl.style.width = `${cycle.progress_percent || 0}%`;
                if (cycle.is_expired) {
                    progressEl.className = 'h-full rounded-full bg-red-500';
                } else if (cycle.is_expiring_soon) {
                    progressEl.className = 'h-full rounded-full bg-gradient-to-r from-amber-500 to-red-500';
                } else {
                    progressEl.className = 'h-full rounded-full bg-gradient-to-r from-emerald-500 to-amber-500';
                }
            }

            if (percentEl) {
                percentEl.textContent = `${cycle.progress_percent}% masa aktif terpakai`;
            }

            if (badgeEl) {
                if (cycle.is_expired) {
                    badgeEl.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/15 text-red-400 border border-red-500/30';
                    badgeEl.textContent = 'PERLU DIPERBARUI SEGERA';
                } else if (cycle.is_expiring_soon) {
                    badgeEl.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/15 text-amber-400 border border-amber-500/30';
                    badgeEl.textContent = 'SEGERA KADALUARSA (10 MINGGU)';
                } else {
                    badgeEl.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30';
                    badgeEl.textContent = 'KEY AKTIF';
                }
            }

            if (warningEl && cycle.is_expiring_soon) {
                warningEl.className = 'p-3.5 rounded-xl border border-red-500/40 bg-red-500/15 text-xs text-red-200 leading-relaxed flex items-start gap-2.5';
            }
        } catch(e) {
            console.error('Error loading maps settings:', e);
        }
    }

    async function saveMapsSettings() {
        const goapiKey = document.getElementById('setting_maps_goapi_key').value.trim();
        const premiumFee = document.getElementById('setting_maps_premium_fee').value;
        const regularRadius = document.getElementById('setting_maps_regular_radius_km').value;
        const premiumRadius = document.getElementById('setting_maps_premium_radius_km').value;
        const resetCycle = document.getElementById('setting_maps_reset_cycle').checked;

        if (!goapiKey) {
            Swal.fire({ title: 'GOAPI Key Wajib', text: 'Kunci otentikasi GOAPI tidak boleh kosong.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            return;
        }

        try {
            Swal.fire({
                title: 'Menyimpan Pengaturan Maps...',
                text: 'Memperbarui GOAPI Key dan parameter jangkauan radius.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                background: '#1E293B',
                color: '#E2E8F0',
            });

            const res = await fetch('/api/admin/settings/maps', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    maps_goapi_key: goapiKey,
                    maps_premium_fee: premiumFee,
                    maps_regular_radius_km: regularRadius,
                    maps_premium_radius_km: premiumRadius,
                    reset_cycle: resetCycle
                })
            });

            const data = await res.json();
            Swal.close();

            if (res.ok) {
                document.getElementById('setting_maps_reset_cycle').checked = false;
                await loadMapsSettings();
                Swal.fire({
                    title: 'Integrasi Maps Disimpan!',
                    text: data.message || 'GOAPI Key dan tarif jangkauan prioritas berhasil diperbarui ke seluruh sistem.',
                    icon: 'success',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            } else {
                Swal.fire({
                    title: 'Gagal Menyimpan',
                    text: data.message || 'Periksa kembali kelengkapan formulir.',
                    icon: 'error',
                    background: '#1E293B',
                    color: '#E2E8F0',
                    confirmButtonColor: '#F97316'
                });
            }
        } catch(e) {
            console.error(e);
            Swal.fire({ title: 'Error', text: 'Gagal terhubung ke server.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    }

    async function testMapsConnection() {
        const goapiKey = document.getElementById('setting_maps_goapi_key').value.trim();
        const testCard = document.getElementById('mapsTestResultCard');
        const latencyEl = document.getElementById('mapsTestLatency');
        const messageEl = document.getElementById('mapsTestMessage');
        const sampleEl = document.getElementById('mapsTestSample');

        testCard.classList.remove('hidden');
        messageEl.textContent = 'Mengirim request uji validasi ke engine geocoding...';
        latencyEl.textContent = '...';

        try {
            const res = await fetch('/api/admin/settings/maps/test', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ key: goapiKey })
            });

            const data = await res.json();
            if (res.ok) {
                latencyEl.textContent = `${data.latency_ms} ms`;
                messageEl.innerHTML = `<span class="text-emerald-400 font-bold">✓ ${data.message}</span> (Key: <code>${data.key_preview}</code>)`;
                sampleEl.textContent = data.sample_result || 'Valid';
            } else {
                latencyEl.textContent = 'Error';
                messageEl.innerHTML = `<span class="text-red-400 font-bold">✕ ${data.message || 'Gagal tersambung'}</span>`;
            }
        } catch(e) {
            console.error(e);
            latencyEl.textContent = 'Timeout';
            messageEl.innerHTML = `<span class="text-red-400 font-bold">✕ Terjadi kegagalan koneksi jaringan.</span>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAdminPersonalProfile();
        loadSettings();
        loadMapsSettings();
    });
</script>
@endsection
