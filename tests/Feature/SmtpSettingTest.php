<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'nama' => 'Super Admin Pemilik',
            'deskripsi' => 'Platform Owner'
        ]);

        $this->superAdmin = User::create([
            'role_id' => $role->id,
            'nama' => 'Super Admin',
            'email' => 'superadmin@revaro.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }

    public function test_can_view_smtp_settings_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.smtp.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('superadmin.smtp_settings');
    }

    public function test_can_update_smtp_settings_and_encrypt_password(): void
    {
        $payload = [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_username' => 'testuser@gmail.com',
            'mail_password' => 'secretAppPassword123',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'support@revaro.com',
            'mail_from_name' => 'REVARO Support',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.smtp.update'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan tersimpan di DB
        $smtp = SmtpSetting::first();
        $this->assertNotNull($smtp);
        $this->assertEquals('smtp.gmail.com', $smtp->mail_host);
        $this->assertEquals(587, $smtp->mail_port);
        $this->assertEquals('testuser@gmail.com', $smtp->mail_username);
        $this->assertEquals('support@revaro.com', $smtp->mail_from_address);
        $this->assertEquals('REVARO Support', $smtp->mail_from_name);
        $this->assertTrue($smtp->is_active);

        // Pastikan password didekripsi dengan benar melalui accessor
        $this->assertEquals('secretAppPassword123', $smtp->mail_password);

        // Pastikan raw password di database terenkripsi (bukan plain-text)
        $rawPassword = $smtp->getAttributes()['mail_password'];
        $this->assertNotEquals('secretAppPassword123', $rawPassword);

        // Pastikan konfigurasi runtime Laravel ter-override
        $this->assertEquals('smtp.gmail.com', Config::get('mail.mailers.smtp.host'));
        $this->assertEquals(587, Config::get('mail.mailers.smtp.port'));
        $this->assertEquals('testuser@gmail.com', Config::get('mail.mailers.smtp.username'));
        $this->assertEquals('support@revaro.com', Config::get('mail.from.address'));
    }

    public function test_can_test_smtp_connection(): void
    {
        Mail::fake();

        SmtpSetting::create([
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_username' => 'tester@gmail.com',
            'mail_password' => 'app-password-abc',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'tester@gmail.com',
            'mail_from_name' => 'Tester',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.smtp.test'), [
            'test_email' => 'destination@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
