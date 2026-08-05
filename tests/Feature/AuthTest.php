<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Teknisi;
use App\Models\Toko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles
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
            Role::create(['nama' => $role]);
        }
    }

    /**
     * Test user registration as Pelanggan.
     */
    public function test_customer_registration()
    {
        $payload = [
            'role_slug' => 'pelanggan',
            'nama' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => 'password123',
            'hp' => '081234567890',
            'alamat' => 'Jl. Sudirman No. 45',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'profile', 'access_token']);

        $this->assertDatabaseHas('users', ['email' => 'budi@gmail.com']);
        $this->assertDatabaseHas('pelanggan', ['hp' => '081234567890']);
    }

    /**
     * Test user registration as Teknisi starts as PENDING_REVIEW.
     */
    public function test_technician_registration_pending_review()
    {
        $payload = [
            'role_slug' => 'teknisi',
            'nama' => 'Joko Suprianto',
            'email' => 'joko@gmail.com',
            'password' => 'password123',
            'hp' => '085611223344',
            'kategori' => 'AC,Kulkas',
            'latitude' => -6.2000,
            'longitude' => 106.8166,
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('teknisi', [
            'hp' => '085611223344',
            'status' => 'PENDING_REVIEW',
        ]);
    }

    /**
     * Test login block on deactivated users.
     */
    public function test_login_blocked_for_deactivated_users()
    {
        $role = Role::where('nama', 'Pelanggan')->first();
        $user = User::create([
            'role_id' => $role->id,
            'nama' => 'Banned User',
            'email' => 'banned@gmail.com',
            'password' => Hash::make('password123'),
            'is_active' => false, // Deactivated
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'banned@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Akun Anda dinonaktifkan.']);
    }

    /**
     * Test login block for FROZEN technician.
     */
    public function test_login_blocked_for_frozen_technician()
    {
        $role = Role::where('nama', 'Teknisi')->first();
        $user = User::create([
            'role_id' => $role->id,
            'nama' => 'Frozen Tech',
            'email' => 'frozen@gmail.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        Teknisi::create([
            'user_id' => $user->id,
            'nama' => 'Frozen Tech',
            'hp' => '085611223344',
            'kategori' => 'AC',
            'status' => 'FROZEN', // Terminology unified to FROZEN
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'frozen@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Akun Teknisi Anda dibekukan (FROZEN).']);
    }

    /**
     * Test login allowed for PENDING_REVIEW profile.
     */
    public function test_login_allowed_for_pending_review_profile()
    {
        $role = Role::where('nama', 'Teknisi')->first();
        $user = User::create([
            'role_id' => $role->id,
            'nama' => 'Pending Tech',
            'email' => 'pending@gmail.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        Teknisi::create([
            'user_id' => $user->id,
            'nama' => 'Pending Tech',
            'hp' => '085611223344',
            'kategori' => 'AC',
            'status' => 'PENDING_REVIEW', // PENDING_REVIEW is allowed
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'pending@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token']);
    }

    /**
     * Test authentication rate limiting (throttle).
     */
    public function test_login_rate_limiting()
    {
        // Try multiple logins with incorrect credentials to trigger throttle
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'nonexistent@gmail.com',
                'password' => 'wrongpass',
            ])->assertStatus(401);
        }

        // The 6th attempt should be throttled
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@gmail.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }
}
