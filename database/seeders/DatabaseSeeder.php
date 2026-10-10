<?php

namespace Database\Seeders;

use App\Models\Role;
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
     * Clean production seeder: Hanya membuat Roles, 1 Akun Super Admin Pemilik, dan Master Data Awal.
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

        // Fetch Super Admin Pemilik Role ID
        $roleSuperPemilik = Role::where('nama', 'Super Admin (Pemilik Sistem)')->first()->id;

        // 2. Seed Hanya 1 Akun Super Admin Pemilik
        $pemilik = User::updateOrCreate(
            ['email' => 'srz.senn@gmail.com'],
            [
                'nama' => 'Salman',
                'password' => Hash::make('12345678'),
                'role_id' => $roleSuperPemilik,
                'foto' => 'admins/pemilik.png',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Reset admin meta untuk super admin seeder
        \App\Models\SystemSetting::whereIn('key', [
            'admin_username_' . $pemilik->id,
            'admin_must_change_pwd_' . $pemilik->id,
        ])->delete();
        \App\Models\SystemSetting::where('key', 'like', 'admin_user_lookup_%')->delete();

        // 3. Seed Master Data: Jasa Servis (Kategori Layanan Dasar)
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

        // 4. Seed Kebijakan Komisi Dasar (10% Jasa, 5% Sparepart)
        KebijakanKomisi::firstOrCreate(
            ['id' => 1],
            [
                'persen_komisi_jasa' => 10.00,
                'persen_fee_sparepart' => 5.00,
            ]
        );

        // 5. Seed Wilayah Operasional Awal
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
    }
}
