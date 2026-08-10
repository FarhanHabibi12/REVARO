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
use App\Models\JasaServis;
use App\Models\KebijakanKomisi;
use App\Models\WilayahOperasional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            'Pelanggan',
            'Teknisi',
            'Toko Sparepart',
            'Admin Verifikator',
            'Admin Resolusi',
            'Super Admin (Pemilik Sistem)',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['nama' => $roleName]);
        }

        // Fetch role IDs
        $rolePelanggan = Role::where('nama', 'Pelanggan')->first()->id;
        $roleTeknisi = Role::where('nama', 'Teknisi')->first()->id;
        $roleToko = Role::where('nama', 'Toko Sparepart')->first()->id;
        $roleVerifikator = Role::where('nama', 'Admin Verifikator')->first()->id;
        $roleResolusi = Role::where('nama', 'Admin Resolusi')->first()->id;
        $roleSuperPemilik = Role::where('nama', 'Super Admin (Pemilik Sistem)' )->first()->id;

        // 2. Seed Admin Accounts
        $pemilik = User::firstOrCreate(
            ['email' => 'pemilik@revaro.com'],
            [
                'nama' => 'Super Admin Pemilik',
                'password' => Hash::make('12345678'),
                'role_id' => $roleSuperPemilik,
                'foto' => 'admins/pemilik.png',
                'is_active' => true,
            ]
        );

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

        // 3. Seed JasaServis (Service Categories)
        JasaServis::firstOrCreate(
            ['nama_jasa' => 'Servis AC Split'],
            [
                'kategori' => 'AC',
                'harga_baseline' => 150000.00,
                'garansi_hari' => 30,
                'deskripsi' => 'Pembersihan dan servis AC standar.',
                'is_active' => true,
            ]
        );
        JasaServis::firstOrCreate(
            ['nama_jasa' => 'Perbaikan Kulkas 2 Pintu'],
            [
                'kategori' => 'Kulkas',
                'harga_baseline' => 250000.00,
                'garansi_hari' => 45,
                'deskripsi' => 'Investigasi dan perbaikan Kulkas tidak dingin.',
                'is_active' => true,
            ]
        );
        JasaServis::firstOrCreate(
            ['nama_jasa' => 'Servis Mesin Cuci Front Loading'],
            [
                'kategori' => 'Mesin Cuci',
                'harga_baseline' => 200000.00,
                'garansi_hari' => 30,
                'deskripsi' => 'Perbaikan putaran drum atau selang pembuangan.',
                'is_active' => true,
            ]
        );

        // 4. Seed KebijakanKomisi (Commission Policy)
        KebijakanKomisi::firstOrCreate(
            ['id' => 1],
            [
                'persen_komisi_jasa' => 10.00,
                'persen_fee_sparepart' => 5.00,
            ]
        );

        // 5. Seed WilayahOperasional (Operational Regions)
        WilayahOperasional::firstOrCreate(
            ['nama_wilayah' => 'Jakarta Pusat'],
            [
                'kode_pos_prefix' => '10',
                'is_active' => true,
            ]
        );
        WilayahOperasional::firstOrCreate(
            ['nama_wilayah' => 'Jakarta Barat'],
            [
                'kode_pos_prefix' => '11',
                'is_active' => true,
            ]
        );
        WilayahOperasional::firstOrCreate(
            ['nama_wilayah' => 'Jakarta Selatan'],
            [
                'kode_pos_prefix' => '12',
                'is_active' => true,
            ]
        );

        // 6. Seed Customers (Pelanggan)
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

        // 7. Seed Technicians (Teknisi)
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

        // 8. Seed Toko Sparepart
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

        // 9. Seed Spareparts
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

        // 10. Seed Onboarding Logs
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

        // 11. Seed Orders & Transactions
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

        if ($order1->spareparts()->count() === 0) {
            $order1->spareparts()->attach($partACCapacitor->id, [
                'qty' => 1,
                'harga' => $partACCapacitor->harga,
                'subtotal' => $partACCapacitor->harga,
            ]);
        }

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

        Rating::firstOrCreate(
            ['order_id' => $order1->id, 'dari_user_id' => $userBudi->id],
            [
                'ke_user_id' => $userJoko->id,
                'skor' => 5,
                'komentar' => 'Sangat cepat dan ramah! AC dingin kembali.',
            ]
        );

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

        if ($order2->spareparts()->count() === 0) {
            $order2->spareparts()->attach($partTVBoard->id, [
                'qty' => 1,
                'harga' => $partTVBoard->harga,
                'subtotal' => $partTVBoard->harga,
            ]);
        }

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

        PelanggaranLog::firstOrCreate(
            ['dispute_id' => $dispute2->id],
            [
                'teknisi_id' => $teknisiTono->id,
                'toko_id' => null,
                'jenis_pelanggaran' => 'Mengabaikan komunikasi pelanggan setelah servis selesai',
                'tingkat_penalti' => 'WARNING',
            ]
        );

        // 12. Seed Notifications
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

        // 13. Seed Activity Logs
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
