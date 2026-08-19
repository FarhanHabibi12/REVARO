(function () {
    const dictionary = {
        'en': {
            // === LAYOUT & USER INFO ===
            'Login sebagai:': 'Logged in as:',
            'SUPER ADMIN — PEMILIK': 'SUPER ADMIN — OWNER',
            'KENDALI STRATEGIS & DATA MASTER': 'STRATEGIC CONTROL & MASTER DATA',
            'MONITORING & KINERJA PLATFORM': 'MONITORING & PLATFORM PERFORMANCE',

            // === DASHBOARD KEUANGAN & ANALITIK ===
            'Dashboard Analitik Keuangan': 'Financial Analytics Dashboard',
            'Ringkasan GMV, pendapatan komisi bersih, dan transaksi aktif platform': 'Summary of GMV, net commission revenue, and active platform transactions',
            'Gross Merchandise Value': 'Gross Merchandise Value',
            'Pendapatan Komisi Bersih': 'Net Commission Revenue',
            'PENDAPATAN KOMISI BERSIH': 'NET COMMISSION REVENUE',
            'Transaksi Aktif Berjalan': 'Active Transactions in Progress',
            'TRANSAKSI AKTIF BERJALAN': 'ACTIVE TRANSACTIONS IN PROGRESS',
            'TRANSAKSI ACTIVE BERJALAN': 'ACTIVE TRANSACTIONS IN PROGRESS',
            'Berdasarkan tarif komisi aktif': 'Based on active commission rates',
            'Semua wilayah operasional aktif': 'All operational regions active',
            'dari bulan lalu': 'from last month',
            'Pesanan': 'Orders',
            '1 Pesanan': '1 Order',
            'Grafik Tren GMV & Komisi Bulanan': 'Monthly GMV & Commission Trend Chart',
            'Ringkasan Verifikasi Onboarding': 'Onboarding Verification Summary',
            'Ringkasan Dispute & Eskalasi': 'Dispute & Escalation Summary',
            'Approved': 'Approved',
            'Pending': 'Pending',
            'Rejected': 'Rejected',
            'Open': 'Open',
            'Proses': 'In Progress',
            'Resolved': 'Resolved',

            // === NAVIGATION & SIDEBAR ===
            'Financial Dashboard': 'Financial Dashboard',
            'Staff Accounts': 'Staff Accounts',
            'Manajemen Akun Staf': 'Staff Account Management',
            'Service Categories': 'Service Categories',
            'Kategori Servis': 'Service Categories',
            'Commission Policy': 'Commission Policy',
            'Kebijakan Komisi': 'Commission Policy',
            'Operational Regions': 'Operational Regions',
            'Wilayah Operasional': 'Operational Regions',
            'New Partner Verification': 'New Partner Verification',
            'Verifikasi Mitra Baru': 'New Partner Verification',
            'DB Backup & Restore': 'DB Backup & Restore',
            'Backup & Restore DB': 'DB Backup & Restore',
            'System Audit Logs': 'System Audit Logs',
            'Audit Log System': 'System Audit Logs',
            'Maintenance Mode': 'Maintenance Mode',
            'Mode Pemeliharaan': 'Maintenance Mode',
            'System Settings': 'System Settings',
            'Pengaturan Sistem': 'System Settings',
            'Pengaturan Platform': 'Platform Settings',
            'Scorecard Verifikator': 'Verificator Scorecard',
            'Scorecard Resolusi': 'Resolution Scorecard',
            'Tren & Analitik': 'Trends & Analytics',
            'Peringatan SLA & Anomali': 'SLA Alerts & Anomalies',
            'Flag Laporan Masuk': 'Incoming Report Flags',
            'Eskalasi Kasus Dispute': 'Dispute Case Escalation',
            'Keluar (Logout)': 'Logout',
            'Keluar': 'Logout',
            'Antrian Pending': 'Pending Queue',
            'Antrian Resubmit': 'Resubmit Queue',
            'Re-Verifikasi Berkala': 'Periodic Re-Verification',
            'Riwayat Keputusan Saya': 'My Decision History',
            'Antrian Sengketa Saya': 'My Dispute Queue',
            'Pengaturan Profil': 'Profile Settings',
            'Dasbor Toko': 'Shop Dashboard',
            'Dasbor Teknisi': 'Technician Dashboard',
            'Dasbor Pelanggan': 'Customer Dashboard',

            // === AUDIT LOG & SECURITY ===
            'Keamanan System & Audit Log Intrusi': 'System Security & Intrusion Audit Log',
            'Monitoring pengunjung realtime, perangkap siber (Intrusion Trap), serta mitigasi otomatis SQL Injection & Brute Force.': 'Real-time visitor monitoring, cyber trap (Intrusion Trap), and automated SQL Injection & Brute Force mitigation.',
            'Lacak IP pengakses website dan lakukan pemblokiran IP mencurigakan secara instan.': 'Track website visitor IPs and perform instant blocking on suspicious IPs.',
            'TOTAL PERCOBAAN': 'TOTAL ATTEMPTS',
            'Serangan Terdeteksi': 'Detected Attacks',
            'IP UNIK TERCATAT': 'LOGGED UNIQUE IPS',
            'Alamat IP Berbeda': 'Distinct IP Addresses',
            'IP DIBLOKIR AKTIF': 'ACTIVE BLOCKED IPS',
            'IP Terblokir Saat Ini': 'Currently Blocked IPs',
            'Monitoring Pengunjung Aktif (Realtime Online)': 'Active Visitor Monitoring (Realtime Online)',
            'Daftar IP Terblokir Saat Ini': 'Currently Blocked IP List',
            'Log Percobaan Serangan (30 Terakhir)': 'Attack Attempt Logs (Last 30)',
            'ALAMAT IP': 'IP ADDRESS',
            'PENGGUNA & PERAN': 'USER & ROLE',
            'ENDPOINT': 'ENDPOINT',
            'PERANGKAT / BROWSER': 'DEVICE / BROWSER',
            'WAKTU AKSES': 'ACCESS TIME',
            'STATUS IP': 'IP STATUS',
            'TINDAKAN BAN': 'BAN ACTION',
            'BAN IP Ini': 'BAN This IP',
            'Buka Blokir': 'Unblock',
            'Tidak ada IP address yang sedang diblokir saat ini.': 'No IP address is currently blocked.',
            'Normal': 'Normal',
            'Trap: Aktif': 'Trap: Active',
            'Trap: Nonaktif': 'Trap: Disabled',
            'Matikan Trap': 'Disable Trap',
            'Aktifkan Trap': 'Enable Trap',
            'Refresh Realtime': 'Realtime Refresh',
            'Refresh Log Data': 'Refresh Log Data',
            'Live Auto': 'Live Auto',
            'AKSI': 'ACTION',
            'MASA BERLAKU': 'EXPIRATION',
            'JENIS BLOKIR': 'BLOCK TYPE',
            'ALASAN DIBLOKIR': 'BLOCK REASON',
            'KATEGORI ANCAMAN': 'THREAT CATEGORY',
            'TINDAKAN MITIGASI': 'MITIGATION ACTION',

            // === SETTINGS PAGE ===
            'Pengaturan Platform & Akun Super Admin': 'Platform Settings & Super Admin Account',
            'Konfigurasi Parametrik Platform': 'Platform Parametric Configuration',
            'Profil & Sandi Super Admin': 'Super Admin Profile & Password',
            'Profil & Sandi Admin Verifikator': 'Verificator Admin Profile & Password',
            'Profil & Sandi Admin Resolusi': 'Resolution Admin Profile & Password',
            'Profil & Sandi Toko Sparepart': 'Sparepart Shop Profile & Password',
            'Profil & Sandi Mitra Teknisi': 'Technician Partner Profile & Password',
            'Profil & Sandi Akun Pelanggan': 'Customer Account Profile & Password',
            'Spesifikasi Lingkungan Platform': 'Platform Environment Specifications',
            'Pintasan Cepat Modul SuperAdmin': 'SuperAdmin Quick Shortcuts',
            'Pintasan Cepat Modul Verifikator': 'Verificator Quick Shortcuts',
            'Pintasan Cepat Modul Resolusi': 'Resolution Quick Shortcuts',
            'Pintasan Cepat Modul Toko': 'Shop Quick Shortcuts',
            'Pintasan Cepat Modul Teknisi': 'Technician Quick Shortcuts',
            'Pintasan Cepat Modul Pelanggan': 'Customer Quick Shortcuts',
            'Umum & Identitas': 'General & Identity',
            'Logo & Aset Gambar': 'Logo & Image Assets',
            'Komisi & Keuangan': 'Commission & Finance',
            'SLA & Ambang Batas': 'SLA & Thresholds',
            'Fitur & Maintenance': 'Features & Maintenance',
            'Keamanan & Sesi': 'Security & Sessions',
            'Identitas Platform & Layanan': 'Platform & Service Identity',
            'Nama Resmi Platform': 'Official Platform Name',
            'Tagline Platform': 'Platform Tagline',
            'Email Customer Support': 'Customer Support Email',
            'Call Center / WhatsApp CS': 'Call Center / WhatsApp CS',
            'Jam Operasional Service Dispatch': 'Service Dispatch Operating Hours',
            'Mata Uang Transaksi Default': 'Default Transaction Currency',
            'Bahasa Default Platform (Global i18n)': 'Default Platform Language (Global i18n)',
            'Simpan Seluruh Pengaturan': 'Save All Settings',
            'Simpan Perubahan Profil & Password': 'Save Profile & Password Changes',
            'Simpan Parameter Sistem': 'Save System Parameters',

            // === MAINTENANCE MODE PAGE ===
            'Pengaturan Mode Pemeliharaan Sistem': 'System Maintenance Mode Settings',
            'Kontrol akses pemeliharaan server secara terpusat.': 'Centralized server maintenance access control.',
            'Sakelar Maintenance Global': 'Global Maintenance Switch',
            'PESAN MAINTENANCE *': 'MAINTENANCE MESSAGE *',
            'ESTIMASI SELESAI (ETA)': 'ESTIMATED COMPLETION (ETA)',
            'DAFTAR IP BYPASS (DIPISAHKAN KOMA)': 'BYPASS IP LIST (COMMA SEPARATED)',
            'Simpan Pengaturan Maintenance': 'Save Maintenance Settings',
            'Riwayat Aktivitas Pemeliharaan': 'Maintenance Activity History',
            'Belum ada riwayat aktivitas pemeliharaan.': 'No maintenance activity history yet.',

            // === GENERAL STATUSES & ROLES ===
            'Aktif': 'Active',
            'Nonaktif': 'Inactive',
            'Disetujui (Active)': 'Approved (Active)',
            'Aktif Terverifikasi': 'Active Verified',
            'NAMA PLATFORM': 'PLATFORM NAME',
            'VERSI FRAMEWORK': 'FRAMEWORK VERSION',
            'VERSI ENGINE PHP': 'PHP ENGINE VERSION',
            'ENVIRONMENT': 'ENVIRONMENT',
            'STATUS DEBUG': 'DEBUG STATUS',
            'ZONA WAKTU': 'TIMEZONE',
            'PERAN AKSES': 'ACCESS ROLE',
            'STATUS VERIFIKASI': 'VERIFICATION STATUS',
            'STATUS AKUN': 'ACCOUNT STATUS',
            'REGISTERED CUSTOMER': 'REGISTERED CUSTOMER',
            'SUPER ADMIN PLATFORM': 'SUPER ADMIN PLATFORM',
            'ADMIN VERIFIKATOR': 'VERIFICATOR ADMIN',
            'ADMIN RESOLUSI': 'RESOLUTION ADMIN',
            'MITRA TOKO SPAREPART': 'SPAREPART SHOP PARTNER',
            'MITRA TEKNISI': 'TECHNICIAN PARTNER',
            'PELANGGAN': 'CUSTOMER',
            'GUEST VISITOR': 'GUEST VISITOR'
        }
    };

    window.getAppLanguage = function () {
        return localStorage.getItem('app_language') || window.PLATFORM_LANGUAGE || 'id';
    };

    window.setAppLanguage = function (lang) {
        localStorage.setItem('app_language', lang);
        document.cookie = "app_language=" + lang + ";path=/;max-age=31536000";
        
        const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
        if (token) {
            fetch('/api/admin/settings', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ settings: { app_language: lang } })
            }).then(() => {
                window.location.reload();
            }).catch(() => {
                window.location.reload();
            });
        } else {
            window.location.reload();
        }
    };

    window.translatePage = function () {
        const lang = window.getAppLanguage();
        if (lang !== 'en') return;

        const dict = dictionary['en'];
        const keys = Object.keys(dict).sort((a, b) => b.length - a.length);

        function translateText(str) {
            if (!str) return str;
            let res = str;
            for (let i = 0; i < keys.length; i++) {
                const k = keys[i];
                if (res.includes(k)) {
                    res = res.replaceAll(k, dict[k]);
                }
            }
            return res;
        }

        function walk(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                const text = node.nodeValue;
                if (text && text.trim().length > 0) {
                    node.nodeValue = translateText(text);
                }
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                if (['SCRIPT', 'STYLE', 'TEXTAREA', 'INPUT', 'SELECT'].includes(node.tagName)) return;

                if (node.placeholder) node.placeholder = translateText(node.placeholder);
                if (node.title) node.title = translateText(node.title);

                for (let child of node.childNodes) {
                    walk(child);
                }
            }
        }

        walk(document.body);
    };

    document.addEventListener('DOMContentLoaded', () => {
        const backendLang = window.PLATFORM_LANGUAGE || 'id';
        if (!localStorage.getItem('app_language')) {
            localStorage.setItem('app_language', backendLang);
        }

        window.translatePage();

        const observer = new MutationObserver(() => {
            if (window.getAppLanguage() === 'en') {
                window.translatePage();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
})();
