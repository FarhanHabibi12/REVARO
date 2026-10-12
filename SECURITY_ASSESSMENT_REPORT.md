# LAPORAN PENGUJIAN KEAMANAN SISTEM (SECURITY ASSESSMENT REPORT)
**Target Aplikasi**: REVARO — Platform Marketplace & On-Demand Service  
**Lingkungan Audit**: Localhost / Development (PHP 8.2 / Laravel 10 / MySQL)  
**Metodologi**: OWASP Testing Guide (WSTG v4.2) & OWASP Top 10  
**Tanggal Pengujian**: 12 Oktober 2026  
**Auditor**: Security QA & Assessment Engineer  

---

## 1. RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)

Pengujian keamanan dan audit kerentanan komprehensif telah dilakukan terhadap arsitektur web dan basis kode sistem **REVARO**. Pengujian mencakup pemetaan route (Web & API), mekanisme autentikasi dan manajemen sesi (Laravel Sanctum & Session Guard), sistem kontrol hak akses berbasis peran (RBAC), validasi input dan sanitasi tampilan frontend Blade, alur transaksi dompet digital dan payment gateway (Midtrans), serta integritas logika bisnis pemesanan (*order lifecycle*).

Secara umum, platform REVARO memiliki lapisan pertahanan yang baik, seperti *WAF AntiMaliciousScanMiddleware* untuk mendeteksi scanner otomatis/SQLi dasar, enkripsi kata sandi SMTP, dan rate-limiting pada endpoint autentikasi. Namun, audit menemukan sejumlah **kerentanan berisiko kritis dan tinggi** yang dapat dieksploitasi oleh pengguna biasa untuk mengambil alih akun administrator (*Account Takeover*), memanipulasi saldo dompet (*Financial Fraud*), dan menyebabkan hilangnya saldo pelanggan secara tidak sengaja.

---

## 2. PEMBERITAHUAN KUNCI API & KREDENSIAL SENSITIF (API KEYS DISCLOSURE)

> [!WARNING]
> Sesuai instruksi khusus dari pemilik sistem, seluruh API Key, Secret Token, dan Kredensial sensitif yang ditemukan di dalam file konfigurasi lokal (`.env`) dicatat di bawah ini agar segera diamankan / di-rotasi sebelum rilis production:

| Komponen / Provider | Tipe Kunci / Variabel | Nilai yang Ditemukan | Rekomendasi Tindakan |
| :--- | :--- | :--- | :--- |
| **Google OAuth 2.0** | `GOOGLE_CLIENT_ID` | `[REDACTED_GOOGLE_CLIENT_ID]` | Pastikan domain redirect dibatasi ketat di Google Cloud Console. |
| **Google OAuth 2.0** | `GOOGLE_CLIENT_SECRET` | `[REDACTED_GOOGLE_SECRET]` | **Sangat Rahasia**: Jangan pernah di-commit ke Git repo publik. |
| **Midtrans Payment** | `MIDTRANS_MERCHANT_ID` | `M069582458` | Sandbox ID. Ganti ke kredensial Production saat live. |
| **Midtrans Payment** | `MIDTRANS_CLIENT_KEY` | `Mid-client-gRhn6e69vMwEpYi-` | Client key publik untuk Snap.js. |
| **Midtrans Payment** | `MIDTRANS_SERVER_KEY` | `[REDACTED_MIDTRANS_SERVER_KEY]` | **Sangat Rahasia**: Digunakan untuk verifikasi notifikasi webhook dan status order. |
| **Laravel App Key** | `APP_KEY` | `[REDACTED_APP_KEY]` | Jangan diubah sembarangan karena mengenkripsi session & password SMTP. |
| **Super Admin Default** | `DatabaseSeeder.php` | Email: `srz.senn@gmail.com`<br>Pass: `********` | **Kritis**: Segera ganti kata sandi bawaan seeder! |

---

## 3. TOP 5 TEMUAN KERENTANAN PALING KRITIS

Berikut adalah urutan 5 celah dengan tingkat dampak dan risiko tertinggi terhadap operasional platform REVARO:

1. **[CRITICAL] IDOR / Flaw Verifikasi Pembayaran Saldo Dompet (`/api/wallet/check-status`)**
   - Pengguna mana pun dapat mengklaim `order_id` top-up milik orang lain yang sudah berstatus sukses di Midtrans untuk dikreditkan ke saldonya sendiri.
2. **[CRITICAL] Stored Cross-Site Scripting (XSS) pada Chat & Sengketa Menuju Pengambilalihan Akun Admin**
   - Input pesan chat dan alasan sengketa dirender langsung ke dalam `.innerHTML` tanpa sanitasi HTML, memungkinkan script jahat mencuri sesi Admin Resolusi dan Super Admin.
3. **[HIGH] Cookie `auth_token` Diterbitkan Tanpa Flag `HttpOnly`**
   - Cookie token otentikasi Sanctum disetel dengan `$httpOnly = false`, sehingga JavaScript di browser dapat membaca token secara langsung saat terjadi XSS.
4. **[HIGH] Reset Saldo Pelanggan Menjadi Nol pada Endpoint Registrasi (`/api/auth/register`)**
   - Logika `Pelanggan::updateOrCreate` menimpa nilai `saldo` menjadi `0.00` jika pelanggan yang sudah memiliki saldo memanggil kembali endpoint registrasi.
5. **[HIGH] Kurangnya Otorisasi Hak Akses pada Setup Administrator Awal (`/api/admin/setup-first-time`)**
   - Endpoint internal admin dapat dipanggil oleh sembarang user login (role pelanggan/teknisi) untuk mendaftarkan dan membajak pemetaan *username* admin.

---

## 4. RINCIAN TEMUAN KEAMANAN (DETAILED VULNERABILITY FINDINGS)

---

### TEMUAN #1: IDOR & Flaw Verifikasi Kepemilikan Transaksi Top-Up Saldo
- **Kategori OWASP**: A01:2021 – Broken Access Control & A04:2021 – Insecure Design
- **Tingkat Risiko**: **CRITICAL (CVSS: 9.1)**
- **Lokasi Kode**: `app/Http/Controllers/Api/WalletController.php` (Metode `checkStatus` & `settleTopupTransaction`)
- **Endpoint**: `POST /api/wallet/check-status`

#### Deskripsi & Analisis Kerentanan
Pada metode `checkStatus()`, sistem menerima parameter `order_id` langsung dari request user (`$request->input('order_id')`). Sistem kemudian mengecek status pembayaran ke Midtrans API. Jika Midtrans mengembalikan status `settlement`, sistem memanggil:
```php
$settleResult = $this->settleTopupTransaction($user, $orderId, $grossAmount, $request);
```
Di dalam `settleTopupTransaction()`, pengecekan riwayat sukses hanya dibatasi pada ID user yang sedang login:
```php
$existingRecords = WalletTransaction::where('user_id', $user->id)
    ->where('tipe', 'IN')
    ->where('deskripsi', 'like', "%{$orderId}%")
```
Sistem **TIDAK** memverifikasi apakah `order_id` tersebut dibuat oleh `$user->id` atau user lain.
Selain itu, pembuatan kode `order_id` di `topup()` menggunakan format:
`TOPUP-{userId}-{timestamp}-{rand(100,999)}` yang dapat ditebak polanya.

#### Dampak Nyata
- **Klaim Saldo Orang Lain**: Pengguna A yang berhasil membayar top-up Rp 1.000.000 dapat diserobot oleh Pengguna B (penyerang) jika Pengguna B memanggil `/api/wallet/check-status` dengan `order_id` milik Pengguna A sebelum Pengguna A menyelesaikannya.
- Saldo Rp 1.000.000 akan bertambah ke akun Pengguna B, sementara Pengguna A kehilangan hak klaim atas uang tersebut.

#### Rekomendasi Remediasi
Validasi bahwa transaksi pending dengan `order_id` tersebut memang milik user yang sedang meminta verifikasi, atau ekstrak user_id langsung dari database `WalletTransaction` pending:
```php
$pendingTx = WalletTransaction::where('deskripsi', 'like', "%[ORDER_ID:{$orderId}]%")
    ->where('tipe', 'PENDING')
    ->first();

if (!$pendingTx || (int)$pendingTx->user_id !== (int)$user->id) {
    return response()->json(['message' => 'Transaksi tidak valid atau bukan milik Anda.'], 403);
}
```

---

### TEMUAN #2: Stored Cross-Site Scripting (XSS) pada Chat & Sengketa (DOM Sink `innerHTML`)
- **Kategori OWASP**: A03:2021 – Injection (Cross-Site Scripting)
- **Tingkat Risiko**: **CRITICAL (CVSS: 8.8)**
- **Lokasi Kode**:
  - `resources/views/admin/resolusi/dashboard.blade.php` (Baris 311)
  - `resources/views/superadmin/pemilik/disputes.blade.php` (Baris 148)
  - `resources/views/teknisi/dashboard.blade.php` (Baris 1430)
- **Endpoint Terkait**: `POST /api/orders/{order}/chats`, `POST /api/disputes`, `POST /api/orders`

#### Deskripsi & Analisis Kerentanan
Input teks dari pengguna seperti pesan obrolan (`pesan`), alasan sengketa (`alasan`), dan deskripsi keluhan pelanggan (`keluhan`) disimpan ke database tanpa sanitasi tag HTML, kemudian di render pada sisi antarmuka menggunakan template literal JavaScript ke dalam properti `.innerHTML`:

*Pada Dashboard Admin Resolusi:*
```javascript
container.innerHTML = messages.map(m => `
    <p class="text-slate-400 text-[11px] leading-relaxed">${m.pesan}</p>
`).join('');
```

*Pada Dashboard Super Admin Pemilik (Disputes):*
```javascript
tbody.innerHTML = data.map(d => `
    <td class="text-slate-400 text-xs max-w-xs truncate">${d.alasan || '—'}</td>
`).join('');
```

#### Dampak Nyata
- Penyerang (sebagai Pelanggan) mengirim pesan chat pada order atau membuat sengketa berisi payload:
  `<img src=x onerror="fetch('/api/admin/accounts',{headers:{Authorization:'Bearer '+localStorage.getItem('auth_token')}})...">`
- Saat Admin Resolusi atau Super Admin membuka halaman sengketa/chat tersebut, script otomatis berjalan di konteks browser admin.
- Skrip dapat membuat akun superadmin baru, memicu reset database, atau mencuri seluruh token sesi.

#### Rekomendasi Remediasi
Gunakan fungsi helper sanitasi/escape HTML sebelum menyisipkan string ke `.innerHTML`, atau gunakan properti aman seperti `.textContent` / `.innerText`:
```javascript
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
// Penggunaan:
`<p>${escapeHtml(m.pesan)}</p>`
```

---

### TEMUAN #3: Cookie Token Autentikasi Diterbitkan Tanpa Flag `HttpOnly`
- **Kategori OWASP**: A07:2021 – Identification and Authentication Failures / CWE-1004
- **Tingkat Risiko**: **HIGH (CVSS: 7.4)**
- **Lokasi Kode**:
  - `app/Http/Middleware/WebRoleMiddleware.php` (Baris 164)
  - `app/Http/Controllers/Api/AuthController.php` (Baris 878)

#### Deskripsi & Analisis Kerentanan
Saat pengguna login atau berpindah portal, controller dan middleware menyertakan cookie `auth_token`:
```php
return $response->withCookie(cookie('auth_token', $tokenString, 120, '/', null, false, false));
```
Parameter ke-7 fungsi `cookie()` di Laravel adalah parameter `$httpOnly`. Nilai yang diberikan adalah `false`.

#### Dampak Nyata
Cookie `auth_token` dapat diakses langsung oleh skrip JavaScript sisi klien (`document.cookie`). Ini memperparah dampak dari temuan Stored XSS di atas, karena penyerang dapat mengekstrak token otentikasi Sanctum secara instan.

#### Rekomendasi Remediasi
Ubah parameter `$httpOnly` menjadi `true`:
```php
return $response->withCookie(cookie('auth_token', $tokenString, 120, '/', null, false, true));
```

---

### TEMUAN #4: Penghapusan/Reset Saldo Pelanggan pada Endpoint Pendaftaran (`/api/auth/register`)
- **Kategori OWASP**: A04:2021 – Insecure Design / Business Logic Flaw
- **Tingkat Risiko**: **HIGH (CVSS: 7.5)**
- **Lokasi Kode**: `app/Http/Controllers/Api/AuthController.php` (Baris 115–124)
- **Endpoint**: `POST /api/auth/register`

#### Deskripsi & Analisis Kerentanan
Pada metode `register()`, ketika profil `pelanggan` diperbarui atau dibuat:
```php
$profile = Pelanggan::updateOrCreate(
    ['user_id' => $user->id],
    [
        'nama' => $request->nama,
        'email' => $request->email,
        'hp' => $request->hp,
        'alamat' => $request->alamat,
        'saldo' => 0.00, // <-- KERENTANAN LOGIKA
    ]
);
```
Jika pengguna yang telah memiliki akun dan memiliki saldo dompet (misalnya Rp 500.000) memanggil endpoint ini lagi (misalnya saat registrasi ulang form atau migrasi profil), `updateOrCreate` akan menimpa saldo yang tersimpan menjadi `0.00`.

#### Dampak Nyata
Kehilangan dana riil pengguna dalam sistem (*data corruption / balance loss*).

#### Rekomendasi Remediasi
Jangan sertakan kolom `saldo` di dalam array pembaruan `updateOrCreate`, atau gunakan `firstOrCreate` yang hanya menginisialisasi saldo saat baris pertama kali dibuat:
```php
$profile = Pelanggan::firstOrCreate(['user_id' => $user->id], ['saldo' => 0.00]);
$profile->update([
    'nama' => $request->nama,
    'email' => $request->email,
    'hp' => $request->hp,
    'alamat' => $request->alamat,
]);
```

---

### TEMUAN #5: Kurangnya Otorisasi Peran pada Setup Administrator Awal (`/api/admin/setup-first-time`)
- **Kategori OWASP**: A01:2021 – Broken Access Control
- **Tingkat Risiko**: **HIGH (CVSS: 7.1)**
- **Lokasi Kode**: `routes/api.php` (Baris 65) & `app/Http/Controllers/Api/AdminAccountController.php` (Baris 391)
- **Endpoint**: `POST /api/admin/setup-first-time`

#### Deskripsi & Analisis Kerentanan
Route `/api/admin/setup-first-time` berada di dalam grup `auth:sanctum` umum tanpa middleware pembatas peran (`role:super-admin-pemilik` atau verifikasi staf admin):
```php
Route::post('/admin/setup-first-time', [AdminAccountController::class, 'setupFirstTime']);
```
Di dalam controllernya, sistem tidak memeriksa apakah user yang memanggil adalah staf admin, melainkan langsung menetapkan *lookup username* admin:
```php
$chosenUsername = strtolower(trim($request->username));
$user->setAdminUsername($chosenUsername);
```

#### Dampak Nyata
Pengguna biasa (role Pelanggan atau Teknisi) dapat mendaftarkan username seperti `"admin"` atau `"resolusi"`. Saat sistem mencari admin berdasarkan identifier di kemudian hari (`User::findAdminByIdentifier()`), lookup tersebut akan mengarahkan ke ID pengguna biasa tersebut.

#### Rekomendasi Remediasi
Tambahkan pemeriksaan hak akses atau middleware pembatas peran:
```php
$adminRoles = ['Super Admin (Pemilik Sistem)', 'Admin Verifikator', 'Admin Resolusi', 'Super Admin (Monitoring Kinerja)'];
if (!$user->role || !in_array($user->role->nama, $adminRoles)) {
    return response()->json(['message' => 'Hanya akun administrator yang diizinkan mengatur username staf.'], 403);
}
```

---

### TEMUAN #6: Kredensial Bawaan Lemah pada Seeder Super Admin (`DatabaseSeeder.php`)
- **Kategori OWASP**: A07:2021 – Identification and Authentication Failures
- **Tingkat Risiko**: **MEDIUM (CVSS: 6.5)**
- **Lokasi Kode**: `database/seeders/DatabaseSeeder.php` (Baris 40–49)

#### Deskripsi & Analisis
Akun Super Admin Pemilik (`srz.senn@gmail.com`) di-generate dengan password statis `'12345678'`. Jika database di-seed ulang pada staging atau server yang terhubung jaringan luar, akun ini sangat rentan ditebak secara instan.

#### Rekomendasi Remediasi
Gunakan generator password acak atau ambil dari environment variable:
```php
$adminPass = env('SEEDER_ADMIN_PASSWORD', \Illuminate\Support\Str::random(16));
```

---

### TEMUAN #7: Celah Ekstensi File Klien & SVG Stored XSS pada Upload Aset
- **Kategori OWASP**: A03:2021 – Injection & CWE-434 (Unrestricted File Upload)
- **Tingkat Risiko**: **MEDIUM (CVSS: 5.4)**
- **Lokasi Kode**: `app/Http/Controllers/Api/SystemSettingController.php` (Baris 241 & 256)

#### Deskripsi & Analisis
1. Validasi mengizinkan ekstensi `svg` pada upload logo/banner:
   `'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:5120'`
   Berkas SVG berbasis format XML dan dapat mengandung tag `<script>alert('XSS')</script>`. Saat dibuka langsung via URL publik `/uploads/settings/...`, browser mengeksekusi skrip tersebut.
2. Penamaan berkas menggunakan `$file->getClientOriginalExtension()`, yang mempercayai nama ekstensi yang dikirimkan oleh browser/penyerang daripada tipe MIME asli berkas.

#### Rekomendasi Remediasi
- Hindari menyajikan file SVG secara langsung atau gunakan parser sanitasi SVG (*svg-sanitizer*).
- Gunakan `$file->extension()` bawaan Symfony/Laravel yang memverifikasi ekstensi berdasarkan MIME-type sebenarnya.

---

### TEMUAN #8: Celah Logika Kompensasi Pembatalan Pesanan (`OrderLifecycleController::cancelOrder`)
- **Kategori OWASP**: A04:2021 – Insecure Design
- **Tingkat Risiko**: **MEDIUM (CVSS: 5.3)**
- **Lokasi Kode**: `app/Http/Controllers/Api/OrderLifecycleController.php` (Baris 895–897)

#### Deskripsi & Analisis
Saat pelanggan membatalkan pesanan di fase `ON_THE_WAY` atau `IN_PROGRESS`, sistem mengenakan kompensasi Rp 20.000 kepada teknisi:
```php
if ($pelanggan->saldo >= $compensation) {
    $pelanggan->decrement('saldo', $compensation);
    $order->teknisi->increment('saldo', $compensation);
}
```
Jika saldo pelanggan kurang dari Rp 20.000 (misalnya Rp 0), kondisi di atas bernilai false. Pembatalan tetap berhasil, pelanggan tidak dikenakan penalti apa pun, dan teknisi tidak mendapatkan kompensasi sepeser pun atas perjalanan yang sudah ditempuh.

#### Rekomendasi Remediasi
Terapkan sistem saldo minus (hutang kompensasi) pada pelanggan atau tahan deposit di awal sebelum status order berubah ke `ON_THE_WAY`.

---

### TEMUAN #9: Mode Debug Aktif di Konfigurasi Lokal (`APP_DEBUG=true`)
- **Kategori OWASP**: A05:2021 – Security Misconfiguration
- **Tingkat Risiko**: **LOW (CVSS: 3.7)**
- **Lokasi Kode**: `.env` (Baris 4)
- **Deskripsi**: `APP_DEBUG=true` menampilkan stack trace lengkap, query SQL, dan variabel server jika terjadi uncaught exception. Pastikan diset `APP_DEBUG=false` pada lingkungan non-lokal/produksi.

---

## 5. KONFIRMASI STATUS DATA & ARTIFAK PENGUJIAN

1. **Pemulihan Data 100%**:
   - Seluruh pengujian dilakukan melalui analisis basis kode statis, pelacakan alur data (*dataflow tracking*), dan pemetaan arsitektural.
   - **TIDAK ADA data riil database yang dirusak, diubah, atau dihapus permanen**.
   - Integritas basis data dan berkas aplikasi tetap berada pada kondisi **100% utuh dan siap digunakan**.
2. **Pembersihan Artifak**:
   - Tidak ada webshell, file berbahaya, akun palsu, atau modifikasi destruktif yang ditinggalkan pada direktori proyek.

---

## 6. MATRIKS TINDAKAN PERBAIKAN PRIORITAS & STATUS REMEDIASI

| Prioritas | Target Modul / File | Tindakan Perbaikan | Status |
| :---: | :--- | :--- | :---: |
| **P1** | [WalletController.php](file:///c:/laragon/www/REVARO/app/Http/Controllers/Api/WalletController.php) | Validasi kepemilikan `order_id` top-up terhadap `user_id` pemohon di `checkStatus`. | **RESOLVED (Aman)** |
| **P1** | [admin/resolusi/dashboard.blade.php](file:///c:/laragon/www/REVARO/resources/views/admin/resolusi/dashboard.blade.php) & [superadmin/pemilik/disputes.blade.php](file:///c:/laragon/www/REVARO/resources/views/superadmin/pemilik/disputes.blade.php) | Terapkan sanitasi HTML (`esc()`) pada seluruh penyisipan variabel ke `.innerHTML`. | **RESOLVED (Aman)** |
| **P2** | [WebRoleMiddleware.php](file:///c:/laragon/www/REVARO/app/Http/Middleware/WebRoleMiddleware.php) & [AuthController.php](file:///c:/laragon/www/REVARO/app/Http/Controllers/Api/AuthController.php) | Ganti parameter flag `$httpOnly` dari `false` menjadi `true` pada cookie `auth_token`. | **RESOLVED (Aman)** |
| **P2** | [AuthController.php](file:///c:/laragon/www/REVARO/app/Http/Controllers/Api/AuthController.php) (metode `register`) | Hapus penimpaan `'saldo' => 0.00` agar saldo pelanggan tetap utuh. | **RESOLVED (Aman)** |
| **P2** | [AdminAccountController.php](file:///c:/laragon/www/REVARO/app/Http/Controllers/Api/AdminAccountController.php) | Batasi route `/api/admin/setup-first-time` dengan otorisasi peranan staf admin. | **RESOLVED (Aman)** |
| **P2** | [AdminDeviceLockMiddleware.php](file:///c:/laragon/www/REVARO/app/Http/Middleware/AdminDeviceLockMiddleware.php) | Kunci portal admin ke perangkat laptop pemilik menggunakan *Device Secret Token*. | **RESOLVED (Aktif)** |
| **P3** | [.htaccess](file:///c:/laragon/www/REVARO/.htaccess) & [public/.htaccess](file:///c:/laragon/www/REVARO/public/.htaccess) | Blokir akses publik ke file `.env`, `.git`, dan konfigurasi sensitif. | **RESOLVED (Aman)** |
| **P3** | `.env` | Rotasi Google Client Secret & Midtrans Server Key sebelum peluncuran sistem publik. | *Pending Production Launch* |
