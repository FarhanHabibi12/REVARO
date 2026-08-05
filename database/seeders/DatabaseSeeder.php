<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $roleSuperPemilik = \App\Models\Role::where('nama', 'Super Admin (Pemilik Sistem)')->first()->id;

        \App\Models\User::firstOrCreate(
            ['email' => 'pemilik@revaro.com'],
            [
                'nama' => 'Super Admin Pemilik',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role_id' => $roleSuperPemilik,
                'foto' => 'admins/pemilik.png',
                'is_active' => true,
            ]
        );
    }
}
