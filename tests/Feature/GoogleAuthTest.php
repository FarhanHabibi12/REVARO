<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['nama' => 'Pelanggan', 'deskripsi' => 'Pengguna jasa']);
        Role::create(['nama' => 'Teknisi', 'deskripsi' => 'Penyedia servis']);
    }

    public function test_redirect_to_google_oauth(): void
    {
        $response = $this->get('/auth/google');
        // Harus redirect ke google auth URL
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_google_callback_with_error_returns_to_home(): void
    {
        $response = $this->get('/auth/google/callback?error=access_denied');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_new_user_redirects_to_complete_profile(): void
    {
        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-unique-999');
        $abstractUser->shouldReceive('getEmail')->andReturn('newgoogle@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('New Google User');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatar.google.com/test.jpg');

        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('auth.google.complete'));
        $this->assertEquals('newgoogle@example.com', session('google_auth.email'));
    }

    public function test_save_complete_profile_creates_user_and_pelanggan_profile(): void
    {
        // Set Google session
        $this->withSession([
            'google_auth' => [
                'user_id' => null,
                'google_id' => 'google-unique-888',
                'email' => 'newuser@gmail.com',
                'nama' => 'Budi Santoso',
                'avatar' => 'https://avatar.google.com/budi.jpg',
            ]
        ]);

        $response = $this->post(route('auth.google.complete.save'), [
            'nama' => 'Budi Santoso Updated',
            'hp' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 10 Jakarta Pusat',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('auth.google_callback_success');

        // Pastikan user ada di DB
        $user = User::where('email', 'newuser@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google-unique-888', $user->google_id);
        $this->assertNotNull($user->email_verified_at);

        // Pastikan profil pelanggan ada di DB
        $pelanggan = Pelanggan::where('user_id', $user->id)->first();
        $this->assertNotNull($pelanggan);
        $this->assertEquals('081234567890', $pelanggan->hp);
        $this->assertEquals('Jl. Merdeka No. 10 Jakarta Pusat', $pelanggan->alamat);

        // Pastikan session dibersihkan
        $this->assertNull(session('google_auth'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_user_with_complete_profile_logs_in_directly(): void
    {
        $role = Role::where('nama', 'Pelanggan')->first();
        $user = User::create([
            'role_id' => $role->id,
            'nama' => 'Existing User',
            'email' => 'existing@gmail.com',
            'google_id' => 'google-existing-777',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Existing User',
            'email' => 'existing@gmail.com',
            'hp' => '08987654321',
            'alamat' => 'Jl. Kebon Jeruk No. 5 Jakarta Barat',
            'saldo' => 50000,
        ]);

        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-existing-777');
        $abstractUser->shouldReceive('getEmail')->andReturn('existing@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Existing User');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');
        $response->assertStatus(200);
        $response->assertViewIs('auth.google_callback_success');
        $this->assertAuthenticatedAs($user);
    }
}
