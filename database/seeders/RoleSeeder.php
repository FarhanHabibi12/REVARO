<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function up(): void
    {
        // For safe execution in case of reruns
    }

    public function run(): void
    {
        $roles = [
            'Pelanggan',
            'Teknisi',
            'Toko Sparepart',
            'Admin Verifikator',
            'Admin Resolusi',
            'Super Admin (Pemilik Sistem)',
            'Super Admin (Monitoring Kinerja)',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama' => $role]);
        }
    }
}
