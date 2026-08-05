<?php

namespace Tests\Feature;

use App\Models\PendaftaranVerifikasi;
use App\Models\Role;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected $rolePelanggan;
    protected $roleTeknisi;
    protected $roleToko;
    protected $roleVerifikator;

    protected $userVerifikator;
    protected $userTeknisi;
    protected $userToko;

    protected $teknisi;
    protected $toko;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->rolePelanggan = Role::create(['nama' => 'Pelanggan'])->id;
        $this->roleTeknisi = Role::create(['nama' => 'Teknisi'])->id;
        $this->roleToko = Role::create(['nama' => 'Toko Sparepart'])->id;
        $this->roleVerifikator = Role::create(['nama' => 'Admin Verifikator'])->id;

        // Create Admin Verifikator
        $this->userVerifikator = User::create([
            'role_id' => $this->roleVerifikator,
            'nama' => 'Verifikator Admin',
            'email' => 'verifier@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        // Create Technician (Pending Review)
        $this->userTeknisi = User::create([
            'role_id' => $this->roleTeknisi,
            'nama' => 'Teknisi Joko',
            'email' => 'joko@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->teknisi = Teknisi::create([
            'user_id' => $this->userTeknisi->id,
            'nama' => 'Teknisi Joko',
            'hp' => '0856',
            'kategori' => 'AC',
            'status' => 'PENDING_REVIEW',
        ]);

        // Create Shop (Pending Review)
        $this->userToko = User::create([
            'role_id' => $this->roleToko,
            'nama' => 'Toko Jaya',
            'email' => 'jaya@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->toko = Toko::create([
            'user_id' => $this->userToko->id,
            'nama' => 'Toko Jaya',
            'izin_usaha' => 'SIUP-1',
            'alamat' => 'Glodok',
            'telepon' => '021',
            'status' => 'PENDING_REVIEW',
        ]);
    }

    /**
     * Test admin verifikator can access the pending review list.
     */
    public function test_admin_can_view_pending_queue()
    {
        $response = $this->actingAs($this->userVerifikator, 'sanctum')
                         ->getJson('/api/verifications/pending');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'technicians')
                 ->assertJsonCount(1, 'shops');
    }

    /**
     * Test verifikator decision logic for APPROVED, NEEDS_REVISION, and REJECTED.
     */
    public function test_verifier_decisions()
    {
        // 1. Approve Shop -> turns ACTIVE
        $response1 = $this->actingAs($this->userVerifikator, 'sanctum')
                          ->postJson('/api/verifications/decision', [
                              'target_type' => 'toko',
                              'target_id' => $this->toko->id,
                              'status' => 'APPROVED',
                              'catatan' => 'Izin legalitas usaha valid.',
                          ]);
        $response1->assertStatus(200);
        $this->assertEquals('ACTIVE', $this->toko->fresh()->status);
        $this->assertDatabaseHas('pendaftaran_verifikasi', [
            'toko_id' => $this->toko->id,
            'status' => 'APPROVED',
        ]);

        // 2. Reject Tech -> turns REJECTED and user is_active = false
        $response2 = $this->actingAs($this->userVerifikator, 'sanctum')
                          ->postJson('/api/verifications/decision', [
                              'target_type' => 'teknisi',
                              'target_id' => $this->teknisi->id,
                              'status' => 'REJECTED',
                              'catatan' => 'Dokumen palsu.',
                          ]);
        $response2->assertStatus(200);
        $this->assertEquals('REJECTED', $this->teknisi->fresh()->status);
        $this->assertFalse((bool) $this->userTeknisi->fresh()->is_active);

        // Try login as rejected tech -> must fail
        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => 'joko@gmail.com',
            'password' => 'password123',
        ]);
        $responseLogin->assertStatus(403);
    }

    /**
     * Test verification fails if mandatory catatan is missing.
     */
    public function test_verification_requires_mandatory_catatan()
    {
        $response = $this->actingAs($this->userVerifikator, 'sanctum')
                         ->postJson('/api/verifications/decision', [
                             'target_type' => 'toko',
                             'target_id' => $this->toko->id,
                             'status' => 'APPROVED',
                             'catatan' => '', // Empty
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['catatan']);
    }

    /**
     * Test profile resubmit logic for NEEDS_REVISION status.
     */
    public function test_profile_resubmission()
    {
        // Pre-set technician status to NEEDS_REVISION
        $this->teknisi->update(['status' => 'NEEDS_REVISION']);

        $payload = [
            'hp' => '089999999',
            'kategori' => 'AC,TV,Kulkas',
        ];

        // Resubmit -> status returns to PENDING_REVIEW
        $response = $this->actingAs($this->userTeknisi, 'sanctum')
                         ->postJson('/api/profile/resubmit', $payload);

        $response->assertStatus(200);
        $this->assertEquals('PENDING_REVIEW', $this->teknisi->fresh()->status);
        $this->assertEquals('089999999', $this->teknisi->fresh()->hp);
    }

    /**
     * Test CheckReverification command scans the last approved verification verified_at.
     */
    public function test_periodic_reverification_scheduling()
    {
        // 1. Setup technician as ACTIVE with last approved verification dated 400 days ago
        $this->teknisi->update(['status' => 'ACTIVE']);
        PendaftaranVerifikasi::create([
            'teknisi_id' => $this->teknisi->id,
            'admin_id' => $this->userVerifikator->id,
            'status' => 'APPROVED',
            'catatan' => 'Initial OK',
            'verified_at' => Carbon::now()->subDays(400),
        ]);

        // 2. Setup shop as ACTIVE with last approved verification dated 200 days ago (>180 days threshold)
        $this->toko->update(['status' => 'ACTIVE']);
        PendaftaranVerifikasi::create([
            'toko_id' => $this->toko->id,
            'admin_id' => $this->userVerifikator->id,
            'status' => 'APPROVED',
            'catatan' => 'Initial OK',
            'verified_at' => Carbon::now()->subDays(200),
        ]);

        // Run re-verification check command
        $this->artisan('revaro:check-reverification')
             ->expectsOutput('Starting periodic re-verification check...')
             ->expectsOutput('Completed. Expired Technicians: 1, Expired Shops: 1')
             ->assertExitCode(0);

        // Assert profile status returned to PENDING_REVIEW
        $this->assertEquals('PENDING_REVIEW', $this->teknisi->fresh()->status);
        $this->assertEquals('PENDING_REVIEW', $this->toko->fresh()->status);

        // Assert notifications were created
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->userTeknisi->id,
            'judul' => 'Verifikasi Ulang Berkala',
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->userToko->id,
            'judul' => 'Verifikasi Ulang Berkala',
        ]);
    }
}
