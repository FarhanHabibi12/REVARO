<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Dispute;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\PelanggaranLog;
use App\Models\PendaftaranVerifikasi;
use App\Models\Rating;
use App\Models\Role;
use App\Models\Sparepart;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch role IDs
        $rolePelanggan = Role::where('nama', 'Pelanggan')->first()->id;
        $roleTeknisi = Role::where('nama', 'Teknisi')->first()->id;
        $roleToko = Role::where('nama', 'Toko Sparepart')->first()->id;
        $roleVerifikator = Role::where('nama', 'Admin Verifikator')->first()->id;
        $roleResolusi = Role::where('nama', 'Admin Resolusi')->first()->id;
        $roleSuperPemilik = Role::where('nama', 'Super Admin (Pemilik Sistem)')->first()->id;
        $roleSuperMonitoring = Role::where('nama', 'Super Admin (Monitoring Kinerja)')->first()->id;

        // --- Admins ---
        $verifikator = User::firstOrCreate(
            ['email' => 'verifikator@revaro.com'],
            [
                'nama' => 'Verifikator Admin 1',
                'password' => Hash::make('password123'),
                'role_id' => $roleVerifikator,
                'foto' => 'admins/verifikator1.png',
                'is_active' => true,
            ]
        );

        $resolusi = User::firstOrCreate(
            ['email' => 'resolusi@revaro.com'],
            [
                'nama' => 'Resolusi Admin 1',
                'password' => Hash::make('password123'),
                'role_id' => $roleResolusi,
                'foto' => 'admins/resolusi1.png',
                'is_active' => true,
            ]
        );

        $pemilik = User::firstOrCreate(
            ['email' => 'pemilik@revaro.com'],
            [
                'nama' => 'Super Admin Pemilik',
                'password' => Hash::make('password123'),
                'role_id' => $roleSuperPemilik,
                'foto' => 'admins/pemilik.png',
                'is_active' => true,
            ]
        );

        $monitoring = User::firstOrCreate(
            ['email' => 'monitoring@revaro.com'],
            [
                'nama' => 'Super Admin Monitoring',
                'password' => Hash::make('password123'),
                'role_id' => $roleSuperMonitoring,
                'foto' => 'admins/monitoring.png',
                'is_active' => true,
            ]
        );

        // --- Pelanggan ---
        $userBudi = User::firstOrCreate(
            ['email' => 'budi@gmail.com'],
            [
                'nama' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role_id' => $rolePelanggan,
                'is_active' => true,
            ]
        );
        $pelangganBudi = Pelanggan::firstOrCreate(
            ['user_id' => $userBudi->id],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'hp' => '081234567890',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'saldo' => 500000.00,
            ]
        );

        $userAni = User::firstOrCreate(
            ['email' => 'ani@gmail.com'],
            [
                'nama' => 'Ani Wijaya',
                'password' => Hash::make('password123'),
                'role_id' => $rolePelanggan,
                'is_active' => true,
            ]
        );
        $pelangganAni = Pelanggan::firstOrCreate(
            ['user_id' => $userAni->id],
            [
                'nama' => 'Ani Wijaya',
                'email' => 'ani@gmail.com',
                'hp' => '081298765432',
                'alamat' => 'Jl. Thamrin No. 12, Jakarta Pusat',
                'saldo' => 150000.00,
            ]
        );

        // --- Teknisi ---
        $userJoko = User::firstOrCreate(
            ['email' => 'joko@gmail.com'],
            [
                'nama' => 'Joko Suprianto',
                'password' => Hash::make('password123'),
                'role_id' => $roleTeknisi,
                'is_active' => true,
            ]
        );
        $teknisiJoko = Teknisi::firstOrCreate(
            ['user_id' => $userJoko->id],
            [
                'nama' => 'Joko Suprianto',
                'kategori' => 'AC,Kulkas,Mesin Cuci',
                'tier' => 'Silver',
                'hp' => '085611223344',
                'rating' => 4.80,
                'tersedia' => true,
                'status' => 'ACTIVE',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
            ]
        );

        $userTono = User::firstOrCreate(
            ['email' => 'tono@gmail.com'],
            [
                'nama' => 'Tono Suherman',
                'password' => Hash::make('password123'),
                'role_id' => $roleTeknisi,
                'is_active' => true,
            ]
        );
        $teknisiTono = Teknisi::firstOrCreate(
            ['user_id' => $userTono->id],
            [
                'nama' => 'Tono Suherman',
                'kategori' => 'TV,Audio,Kipas Angin',
                'tier' => 'Bronze',
                'hp' => '085699887766',
                'rating' => 4.20,
                'tersedia' => true,
                'status' => 'ACTIVE',
                'latitude' => -6.2201,
                'longitude' => 106.8271,
            ]
        );

        // --- Toko Sparepart ---
        $userJaya = User::firstOrCreate(
            ['email' => 'jaya@sparepart.com'],
            [
                'nama' => 'Jaya Elektronik Toko',
                'password' => Hash::make('password123'),
                'role_id' => $roleToko,
                'is_active' => true,
            ]
        );
        $tokoJaya = Toko::firstOrCreate(
            ['user_id' => $userJaya->id],
            [
                'nama' => 'Jaya Sparepart Elektronik',
                'izin_usaha' => 'SIUP-12345-JAYA',
                'alamat' => 'Glodok Plaza Lt. 2 No. 8, Jakarta Barat',
                'telepon' => '021-625899',
                'status' => 'ACTIVE',
                'latitude' => -6.1512,
                'longitude' => 106.8145,
            ]
        );

        $userBerkah = User::firstOrCreate(
            ['email' => 'berkah@sparepart.com'],
            [
                'nama' => 'Berkah Jaya Toko',
                'password' => Hash::make('password123'),
                'role_id' => $roleToko,
                'is_active' => true,
            ]
        );
        $tokoBerkah = Toko::firstOrCreate(
            ['user_id' => $userBerkah->id],
            [
                'nama' => 'Toko Elektronik Berkah',
                'izin_usaha' => 'SIUP-67890-BERKAH',
                'alamat' => 'Kramat Jati No. 50, Jakarta Timur',
                'telepon' => '021-809772',
                'status' => 'ACTIVE',
                'latitude' => -6.2690,
                'longitude' => 106.8682,
            ]
        );

        // --- Sparepart ---
        $partACCompressor = Sparepart::firstOrCreate(
            ['toko_id' => $tokoJaya->id, 'nama' => 'Kompresor AC Panasonic 1 PK'],
            [
                'harga' => 350000.00,
                'stok' => 15,
                'merk' => 'Panasonic',
            ]
        );

        $partACCapacitor = Sparepart::firstOrCreate(
            ['toko_id' => $tokoJaya->id, 'nama' => 'Kapasitor AC 35uF'],
            [
                'harga' => 45000.00,
                'stok' => 50,
                'merk' => 'Shizuki',
            ]
        );

        $partTVBoard = Sparepart::firstOrCreate(
            ['toko_id' => $tokoBerkah->id, 'nama' => 'Mainboard TV LED Samsung 32"'],
            [
                'harga' => 450000.00,
                'stok' => 5,
                'merk' => 'Samsung',
            ]
        );

        // --- Pendaftaran Verifikasi Logs ---
        PendaftaranVerifikasi::firstOrCreate(
            ['teknisi_id' => $teknisiJoko->id],
            [
                'admin_id' => $verifikator->id,
                'status' => 'APPROVED',
                'catatan' => 'Sertifikat dan dokumen KTP lengkap dan terverifikasi.',
                'verified_at' => now()->subDays(5),
            ]
        );

        PendaftaranVerifikasi::firstOrCreate(
            ['toko_id' => $tokoJaya->id],
            [
                'admin_id' => $verifikator->id,
                'status' => 'APPROVED',
                'catatan' => 'Izin legalitas usaha valid.',
                'verified_at' => now()->subDays(4),
            ]
        );

        // --- Order 1: Completed ---
        $order1 = Order::firstOrCreate(
            ['kode_order' => 'REV-20260805-0001'],
            [
                'pelanggan_id' => $pelangganBudi->id,
                'teknisi_id' => $teknisiJoko->id,
                'status' => 'COMPLETED',
                'keluhan' => 'AC tidak dingin dan mengeluarkan bunyi bising.',
                'alamat_servis' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'tanggal_servis' => now()->subDays(2),
                'latitude' => -6.2088,
                'longitude' => 106.8456,
            ]
        );

        // Attach spare parts to Order 1
        if ($order1->spareparts()->count() === 0) {
            $order1->spareparts()->attach($partACCapacitor->id, [
                'qty' => 1,
                'harga' => $partACCapacitor->harga,
                'subtotal' => $partACCapacitor->harga,
            ]);
        }

        // Transaction 1
        $transaksi1 = Transaksi::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'biaya_jasa' => 100000.00,
                'biaya_part' => 45000.00,
                'total' => 145000.00,
                'metode_pembayaran' => 'WALLET',
                'status_pembayaran' => 'PAID',
                'status_escrow' => 'RELEASED',
                'paid_at' => now()->subDays(2),
            ]
        );

        // Rating Order 1 (Budi rates Joko)
        Rating::firstOrCreate(
            ['order_id' => $order1->id, 'dari_user_id' => $userBudi->id],
            [
                'ke_user_id' => $userJoko->id,
                'skor' => 5,
                'komentar' => 'Sangat cepat dan ramah! AC dingin kembali.',
            ]
        );

        // --- Order 2: Disputed ---
        $order2 = Order::firstOrCreate(
            ['kode_order' => 'REV-20260805-0002'],
            [
                'pelanggan_id' => $pelangganAni->id,
                'teknisi_id' => $teknisiTono->id,
                'status' => 'DISPUTED',
                'keluhan' => 'TV Samsung 32" layar bergaris.',
                'alamat_servis' => 'Jl. Thamrin No. 12, Jakarta Pusat',
                'tanggal_servis' => now()->subDays(1),
                'latitude' => -6.2201,
                'longitude' => 106.8271,
            ]
        );

        // Attach spare parts to Order 2
        if ($order2->spareparts()->count() === 0) {
            $order2->spareparts()->attach($partTVBoard->id, [
                'qty' => 1,
                'harga' => $partTVBoard->harga,
                'subtotal' => $partTVBoard->harga,
            ]);
        }

        // Transaction 2
        $transaksi2 = Transaksi::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'biaya_jasa' => 150000.00,
                'biaya_part' => 450000.00,
                'total' => 600000.00,
                'metode_pembayaran' => 'WALLET',
                'status_pembayaran' => 'PAID',
                'status_escrow' => 'HELD',
                'paid_at' => now()->subDays(1),
            ]
        );

        // Dispute entry for Order 2
        $dispute2 = Dispute::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'admin_id' => $resolusi->id,
                'status' => 'INVESTIGATING',
                'alasan' => 'Pelanggan mengeluh bahwa TV kembali bergaris setelah 1 jam ditinggal teknisi, tapi teknisi tidak merespons chat.',
                'resolusi' => null,
                'resolved_at' => null,
            ]
        );

        // Pelanggaran log warning for Tono
        PelanggaranLog::firstOrCreate(
            ['dispute_id' => $dispute2->id],
            [
                'teknisi_id' => $teknisiTono->id,
                'toko_id' => null,
                'jenis_pelanggaran' => 'Mengabaikan komunikasi pelanggan setelah servis selesai',
                'tingkat_penalti' => 'WARNING',
            ]
        );

        // --- Some Notifications ---
        Notifikasi::firstOrCreate(
            ['user_id' => $userBudi->id, 'judul' => 'Servis Selesai'],
            [
                'pesan' => 'Pesanan servis AC Anda telah selesai dikerjakan oleh Joko Suprianto.',
                'dibaca' => true,
            ]
        );

        Notifikasi::firstOrCreate(
            ['user_id' => $userTono->id, 'judul' => 'Sengketa Baru Diajukan'],
            [
                'pesan' => 'Pelanggan Ani Wijaya mengajukan sengketa untuk order REV-20260805-0002.',
                'dibaca' => false,
            ]
        );

        // --- Activity Logs ---
        ActivityLog::create([
            'user_id' => $verifikator->id,
            'aktivitas' => 'Melakukan persetujuan pendaftaran teknisi Joko Suprianto',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $resolusi->id,
            'aktivitas' => 'Memulai investigasi sengketa order REV-20260805-0002',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
