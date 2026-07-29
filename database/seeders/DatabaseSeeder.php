<?php

namespace Database\Seeders;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\Sparepart;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==== Users (staf internal) ====
        $dewi = User::create([
            'name'     => 'Kokoh golodok',
            'email'    => 'Farhan@revaro.id',
            'role'     => 'admin_verifikator',
            'password' => Hash::make('Farhan1234'),
        ]);

        $rian = User::create([
            'name'     => 'Sharizi Sauilaman',
            'email'    => 'sharizi@revaro.id',
            'role'     => 'admin_resolusi',
            'password' => Hash::make('sharizi1234'),
        ]);

        User::create([
            'name'     => 'Ibnu Nigas',
            'email'    => 'ibnu@revaro.id',
            'role'     => 'super_admin',
            'password' => Hash::make('ibnu1234'),
        ]);

        // ==== Pelanggan ====
        $pelanggans = collect([
            ['nama' => 'Siti Rahma', 'hp' => '081234560001', 'alamat' => 'Jl. Kenanga No. 12, Jakarta Selatan'],
            ['nama' => 'Andi Saputra', 'hp' => '081234560002', 'alamat' => 'Jl. Merdeka No. 5, Bandung'],
            ['nama' => 'Lestari Wulandari', 'hp' => '081234560003', 'alamat' => 'Jl. Sudirman No. 88, Surabaya'],
            ['nama' => 'Fajar Nugroho', 'hp' => '081234560004', 'alamat' => 'Jl. Gatot Subroto No. 21, Semarang'],
            ['nama' => 'Rina Marlina', 'hp' => '081234560005', 'alamat' => 'Jl. Diponegoro No. 3, Yogyakarta'],
        ])->map(fn ($d) => Pelanggan::create($d));

        // ==== Teknisi ====
        $teknisis = collect([
            ['nama' => 'Budi Santoso', 'kategori' => 'AC', 'tier' => 'gold', 'status' => 'active'],
            ['nama' => 'Joko Wibowo', 'kategori' => 'Kulkas', 'tier' => 'silver', 'status' => 'active'],
            ['nama' => 'Hendra Kurniawan', 'kategori' => 'Mesin cuci', 'tier' => 'bronze', 'status' => 'active'],
            ['nama' => 'Agus Salim', 'kategori' => 'Elektronik umum', 'tier' => 'bronze', 'status' => 'pending_review'],
            ['nama' => 'Wahyu Setiadi', 'kategori' => 'AC', 'tier' => 'silver', 'status' => 'pending_review'],
        ])->map(fn ($d) => Teknisi::create($d));

        // ==== Toko sparepart ====
        $tokoSinar = Toko::create(['nama' => 'Sinar Elektronik', 'izin_usaha' => 'NIB 8120xxxxxx', 'status' => 'active']);
        $tokoJaya = Toko::create(['nama' => 'Jaya Sparepart', 'izin_usaha' => 'SIUP 503/112/xx', 'status' => 'active']);
        $tokoBaru = Toko::create(['nama' => 'Maju Teknik', 'izin_usaha' => 'NIB 8120yyyyyy', 'status' => 'pending_review']);

        // ==== Sparepart ====
        $kapasitor = Sparepart::create(['toko_id' => $tokoSinar->id, 'nama' => 'Kapasitor AC 1 PK', 'harga' => 150000]);
        $kompresor = Sparepart::create(['toko_id' => $tokoSinar->id, 'nama' => 'Kompresor AC', 'harga' => 950000]);
        Sparepart::create(['toko_id' => $tokoJaya->id, 'nama' => 'Thermostat kulkas', 'harga' => 120000]);
        Sparepart::create(['toko_id' => $tokoJaya->id, 'nama' => 'Motor mesin cuci', 'harga' => 480000]);
        Sparepart::create(['toko_id' => $tokoJaya->id, 'nama' => 'Freon R32 (1 kg)', 'harga' => 220000]);
        Sparepart::create(['toko_id' => $tokoBaru->id, 'nama' => 'Selang fleksibel', 'harga' => 45000]);

        // ==== Orders ====
        $order1 = Order::create([
            'pelanggan_id' => $pelanggans[0]->id,
            'teknisi_id'   => $teknisis[0]->id,
            'keluhan'      => 'AC mati total dan netes air',
            'status'       => 'completed',
        ]);
        $order1->spareparts()->attach($kapasitor->id, ['qty' => 1, 'status_persetujuan' => 'disetujui']);
        Transaksi::create([
            'order_id'      => $order1->id,
            'biaya_jasa'    => 100000,
            'biaya_part'    => 150000,
            'status_escrow' => 'dicairkan',
            'dicairkan_pada'=> now()->subDays(2),
        ]);

        $order2 = Order::create([
            'pelanggan_id' => $pelanggans[1]->id,
            'teknisi_id'   => $teknisis[1]->id,
            'keluhan'      => 'Kulkas tidak dingin',
            'status'       => 'disputed',
        ]);
        Transaksi::create([
            'order_id'      => $order2->id,
            'biaya_jasa'    => 90000,
            'biaya_part'    => 0,
            'status_escrow' => 'ditahan',
        ]);
        Dispute::create([
            'order_id' => $order2->id,
            'status'   => 'diajukan',
            'batas_sla'=> now()->addHours(30),
        ]);

        $order3 = Order::create([
            'pelanggan_id' => $pelanggans[2]->id,
            'teknisi_id'   => $teknisis[0]->id,
            'keluhan'      => 'AC bunyi berisik saat menyala',
            'status'       => 'disputed',
        ]);
        $order3->spareparts()->attach($kompresor->id, ['qty' => 1, 'status_persetujuan' => 'disetujui']);
        Transaksi::create([
            'order_id'      => $order3->id,
            'biaya_jasa'    => 150000,
            'biaya_part'    => 950000,
            'status_escrow' => 'ditahan',
        ]);
        Dispute::create([
            'order_id'  => $order3->id,
            'status'    => 'diinvestigasi',
            'resolusi'  => null,
            'batas_sla' => now()->addHours(10),
        ]);

        $order4 = Order::create([
            'pelanggan_id' => $pelanggans[3]->id,
            'teknisi_id'   => $teknisis[2]->id,
            'keluhan'      => 'Mesin cuci tidak bisa spin',
            'status'       => 'completed',
        ]);
        Transaksi::create([
            'order_id'      => $order4->id,
            'biaya_jasa'    => 120000,
            'biaya_part'    => 480000,
            'status_escrow' => 'dicairkan',
            'dicairkan_pada'=> now()->subDay(),
        ]);

        Order::create([
            'pelanggan_id' => $pelanggans[4]->id,
            'teknisi_id'   => null,
            'keluhan'      => 'TV tidak menyala sama sekali',
            'status'       => 'searching',
        ]);

        $order6 = Order::create([
            'pelanggan_id' => $pelanggans[0]->id,
            'teknisi_id'   => $teknisis[1]->id,
            'keluhan'      => 'Kulkas bocor air di bagian bawah',
            'status'       => 'disputed',
        ]);
        Dispute::create([
            'order_id'  => $order6->id,
            'status'    => 'selesai',
            'resolusi'  => 'reservis_gratis',
            'batas_sla' => now()->subDays(1),
        ]);
    }
}
