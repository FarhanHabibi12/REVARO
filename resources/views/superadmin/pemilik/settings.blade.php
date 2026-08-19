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
                        <input type="password" id="settingNewPassword" minlength="6" class="input-field" placeholder="Kata sandi baru">
                        <input type="password" id="settingConfirmPassword" minlength="6" class="input-field" placeholder="Ulangi kata sandi">
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
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-400 flex items-center justify-center text-xl border border-orange-500/20 group-hover:scale-110 transition">
                    🎛️
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-orange-400 transition">Dasbor Platform</h4>
                    <p class="text-xs text-slate-400">Overview ekosistem</p>
                </div>
            </a>

            <!-- Shortcut 2: Daftar Admin Staf -->
            <a href="/superadmin/pemilik/accounts" class="card p-5 hover:border-emerald-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl border border-emerald-500/20 group-hover:scale-110 transition">
                    👤
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-white group-hover:text-emerald-400 transition">Daftar Admin Staf</h4>
                    <p class="text-xs text-slate-400">Kelola akun instansi</p>
                </div>
            </a>

            <!-- Shortcut 3: Mode Maintenance -->
            <a href="/superadmin/pemilik/maintenance" class="card p-5 hover:border-blue-500/50 transition group flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl border border-blue-500/20 group-hover:scale-110 transition">
                    🔑
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
            <button onclick="saveAllSettings()" class="btn-primary text-xs px-5 py-2.5 font-extrabold shadow-lg shadow-orange-500/20">
                💾 Simpan Parameter Sistem
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-800" id="settingsTabs">
            <button onclick="switchTab('tab-general')" id="btn-tab-general" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 bg-orange-500/15 text-orange-400 border-b-2 border-orange-500">
                <span>⚙️</span> Umum & Identitas
            </button>
            <button onclick="switchTab('tab-branding')" id="btn-tab-branding" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <span>🖼️</span> Logo & Aset Gambar
            </button>
            <button onclick="switchTab('tab-financial')" id="btn-tab-financial" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <span>💳</span> Komisi & Keuangan
            </button>
            <button onclick="switchTab('tab-sla')" id="btn-tab-sla" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <span>⏱️</span> SLA & Ambang Batas
            </button>
            <button onclick="switchTab('tab-features')" id="btn-tab-features" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <span>🎛️</span> Fitur & Maintenance
            </button>
            <button onclick="switchTab('tab-security')" id="btn-tab-security" class="px-4 py-2.5 rounded-t-xl font-bold text-xs transition flex items-center gap-2 text-slate-400 hover:text-white">
                <span>🛡️</span> Keamanan & Sesi
            </button>
        </div>

        <!-- Form Container -->
        <form id="settingsForm" onsubmit="event.preventDefault(); saveAllSettings();">

            <!-- Tab 1: Umum & Identitas Platform -->
            <div id="tab-general" class="tab-content space-y-6">
                <div class="card p-6">
                    <h3 class="text-sm font-extrabold text-white mb-1 flex items-center gap-2">
                        <span>🏢</span> Identitas Platform & Layanan
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
                            <span>🖼️</span> Upload Logo & Aset Gambar Branding
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
                                        <div class="text-2xl mb-1">🔧</div>
                                        <p class="text-xs font-bold text-slate-400">Belum ada logo diunggah</p>
                                        <p class="text-[10px] text-slate-500">Logo standar REVARO aktif</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 512x512 px. Rasio persegi atau lanskap transparan.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_logo" accept="image/*" class="hidden" onchange="previewImage('app_logo')">
                                <button type="button" onclick="document.getElementById('file_app_logo').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <span>📁</span> Pilih Gambar
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_logo')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <span>📤</span> Upload
                                </button>
                            </div>
                        </div>

                        <!-- Item 2: Favicon Browser & Icon -->
                        <div class="p-5 bg-slate-900/60 rounded-2xl border border-slate-800 space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-white uppercase tracking-wider">2. Favicon Browser & Icon App</label>
                                    <span class="text-[10px] text-slate-400 font-mono">ICO / PNG Square</span>
                                </div>
                                <div class="w-full h-32 rounded-xl bg-slate-950 border-2 border-dashed border-slate-800 flex items-center justify-center relative overflow-hidden group">
                                    <img id="preview_app_favicon" src="" alt="Preview Favicon" class="w-12 h-12 object-contain hidden">
                                    <div id="empty_app_favicon" class="text-center p-3">
                                        <div class="text-2xl mb-1">🔖</div>
                                        <p class="text-xs font-bold text-slate-400">Belum ada favicon diunggah</p>
                                        <p class="text-[10px] text-slate-500">Favicon standar browser aktif</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 64x64 px atau 128x128 px rasio 1:1.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_favicon" accept="image/*" class="hidden" onchange="previewImage('app_favicon')">
                                <button type="button" onclick="document.getElementById('file_app_favicon').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <span>📁</span> Pilih Gambar
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_favicon')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <span>📤</span> Upload
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
                                        <div class="text-2xl mb-1">🏙️</div>
                                        <p class="text-xs font-bold text-slate-400">Belum ada banner diunggah</p>
                                        <p class="text-[10px] text-slate-500">Banner gradien standar aktif</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 1920x1080 px lanskap HD.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_banner" accept="image/*" class="hidden" onchange="previewImage('app_banner')">
                                <button type="button" onclick="document.getElementById('file_app_banner').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <span>📁</span> Pilih Gambar
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_banner')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <span>📤</span> Upload
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
                                        <div class="text-2xl mb-1">📜</div>
                                        <p class="text-xs font-bold text-slate-400">Belum ada stempel diunggah</p>
                                        <p class="text-[10px] text-slate-500">Cap resmi sistem</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rekomendasi resolusi: 400x400 px PNG transparan.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="file" id="file_app_stamp" accept="image/*" class="hidden" onchange="previewImage('app_stamp')">
                                <button type="button" onclick="document.getElementById('file_app_stamp').click()" class="btn-outline text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 font-bold">
                                    <span>📁</span> Pilih Gambar
                                </button>
                                <button type="button" onclick="uploadImageAsset('app_stamp')" class="btn-primary text-xs py-2 px-4 font-extrabold flex items-center gap-1">
                                    <span>📤</span> Upload
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
                        <span>💸</span> Kebijakan Komisi & Penarikan Dana
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
                        <span>⏱️</span> Service Level Agreement (SLA) & Ambang Batas
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
                        <span>🎛️</span> Feature Flags & Modus Pemeliharaan
                    </h3>
                    <p class="text-xs text-slate-400 mb-6">Aktifkan atau nonaktifkan modul platform secara fleksibel.</p>

                    <div class="space-y-6">
                        <div class="p-4 bg-slate-900/60 rounded-xl border border-red-500/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-extrabold text-white">🚧 Maintenance Mode (Mode Pemeliharaan)</span>
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
                        <span>🛡️</span> Kebijakan Keamanan & Masa Berlaku Sesi
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
                document.getElementById('displayProfileName').textContent = data.user.nama;

                if (newPassword) {
                    await fetch('/api/admin/profile/password', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ current_password: '12345678', new_password: newPassword, new_password_confirmation: confirmPassword })
                    });
                }

                Swal.fire({ title: 'Profil Disimpan!', text: 'Informasi akun & kata sandi Super Admin berhasil diperbarui.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
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

    document.addEventListener('DOMContentLoaded', () => {
        loadAdminPersonalProfile();
        loadSettings();
    });
</script>
@endsection
