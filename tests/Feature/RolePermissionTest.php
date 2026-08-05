<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\PendaftaranVerifikasi;
use App\Models\Role;
use App\Models\Sparepart;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected $rolePelanggan;
    protected $roleTeknisi;
    protected $roleToko;
    protected $roleVerifikator;
    protected $roleResolusi;
    protected $roleSuperPemilik;
    protected $roleSuperMonitoring;

    protected $userPelanggan;
    protected $userTeknisi;
    protected $userTokoActive;
    protected $userTokoPending;
    protected $userResolusi;
    protected $userVerifikator;
    protected $userSuperPemilik;
    protected $userSuperMonitoring;

    protected $pelanggan;
    protected $teknisi;
    protected $tokoActive;
    protected $tokoPending;

    protected $order;
    protected $dispute;
    protected $verification;
    protected $sparepart;
    protected $transaksi;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->rolePelanggan = Role::create(['nama' => 'Pelanggan'])->id;
        $this->roleTeknisi = Role::create(['nama' => 'Teknisi'])->id;
        $this->roleToko = Role::create(['nama' => 'Toko Sparepart'])->id;
        $this->roleVerifikator = Role::create(['nama' => 'Admin Verifikator'])->id;
        $this->roleResolusi = Role::create(['nama' => 'Admin Resolusi'])->id;
        $this->roleSuperPemilik = Role::create(['nama' => 'Super Admin (Pemilik Sistem)'])->id;
        $this->roleSuperMonitoring = Role::create(['nama' => 'Super Admin (Monitoring Kinerja)'])->id;

        // Create Users & Profiles
        $this->userPelanggan = User::create([
            'role_id' => $this->rolePelanggan,
            'nama' => 'Pelanggan Budi',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->userPelanggan->id,
            'nama' => 'Pelanggan Budi',
            'email' => 'budi@gmail.com',
            'hp' => '08123',
            'alamat' => 'Sudirman',
        ]);

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
            'status' => 'ACTIVE',
        ]);

        $this->userTokoActive = User::create([
            'role_id' => $this->roleToko,
            'nama' => 'Toko Active',
            'email' => 'tokoactive@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->tokoActive = Toko::create([
            'user_id' => $this->userTokoActive->id,
            'nama' => 'Toko Active',
            'izin_usaha' => 'SIUP1',
            'alamat' => 'Glodok',
            'telepon' => '021',
            'status' => 'ACTIVE',
        ]);

        $this->userTokoPending = User::create([
            'role_id' => $this->roleToko,
            'nama' => 'Toko Pending',
            'email' => 'tokopending@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->tokoPending = Toko::create([
            'user_id' => $this->userTokoPending->id,
            'nama' => 'Toko Pending',
            'izin_usaha' => 'SIUP2',
            'alamat' => 'Glodok',
            'telepon' => '021',
            'status' => 'PENDING_REVIEW',
        ]);

        $this->userVerifikator = User::create([
            'role_id' => $this->roleVerifikator,
            'nama' => 'Verifikator Admin',
            'email' => 'verifier@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        $this->userResolusi = User::create([
            'role_id' => $this->roleResolusi,
            'nama' => 'Resolusi Admin',
            'email' => 'resolver@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        $this->userSuperPemilik = User::create([
            'role_id' => $this->roleSuperPemilik,
            'nama' => 'Super Pemilik',
            'email' => 'owner@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        $this->userSuperMonitoring = User::create([
            'role_id' => $this->roleSuperMonitoring,
            'nama' => 'Super Monitoring',
            'email' => 'monitor@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        // Create Seed Data for Policies Testing
        $this->order = Order::create([
            'pelanggan_id' => $this->pelanggan->id,
            'teknisi_id' => $this->teknisi->id,
            'kode_order' => 'REV-TEST-0001',
            'status' => 'IN_PROGRESS',
            'keluhan' => 'AC panas',
            'alamat_servis' => 'Sudirman',
        ]);

        $this->dispute = Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userResolusi->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'Complaint',
        ]);

        $this->verification = PendaftaranVerifikasi::create([
            'teknisi_id' => $this->teknisi->id,
            'admin_id' => $this->userVerifikator->id,
            'status' => 'APPROVED',
            'catatan' => 'OK',
            'verified_at' => now(),
        ]);

        $this->sparepart = Sparepart::create([
            'toko_id' => $this->tokoActive->id,
            'nama' => 'Kapasitor',
            'harga' => 50000.00,
            'stok' => 10,
            'merk' => 'Shizuki',
        ]);

        $this->transaksi = Transaksi::create([
            'order_id' => $this->order->id,
            'biaya_jasa' => 100000.00,
            'biaya_part' => 50000.00,
            'total' => 150000.00,
            'metode_pembayaran' => 'WALLET',
            'status_pembayaran' => 'PAID',
            'status_escrow' => 'HELD',
        ]);
    }

    /**
     * Test Super Admin Pemilik can create internal accounts.
     */
    public function test_super_admin_pemilik_can_create_internal_accounts()
    {
        $payload = [
            'role_slug' => 'admin-verifikator',
            'nama' => 'Verifikator Baru',
            'email' => 'new_verifier@revaro.com',
            'password' => 'password123',
        ];

        $response = $this->actingAs($this->userSuperPemilik, 'sanctum')
                         ->postJson('/api/admin/accounts', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'new_verifier@revaro.com']);
    }

    /**
     * Test other roles cannot create internal accounts.
     */
    public function test_non_owner_cannot_create_internal_accounts()
    {
        $payload = [
            'role_slug' => 'admin-verifikator',
            'nama' => 'Verifikator Baru',
            'email' => 'new_verifier@revaro.com',
            'password' => 'password123',
        ];

        $response = $this->actingAs($this->userPelanggan, 'sanctum')
                         ->postJson('/api/admin/accounts', $payload);

        $response->assertStatus(403);
    }

    /**
     * Test Super Admin Monitoring read-only logic and route bypass for flags.
     */
    public function test_super_admin_monitoring_read_only_with_flag_exception()
    {
        // 1. Check blocked write operation (e.g. POST to create sparepart)
        $response1 = $this->actingAs($this->userSuperMonitoring, 'sanctum')
                          ->postJson('/api/test/spareparts', [
                              'nama' => 'Sparepart Baru',
                              'harga' => 10000,
                              'stok' => 5,
                              'merk' => 'Test',
                          ]);
        $response1->assertStatus(403);

        // 2. Check allowed write operation (POST to send flags)
        $response2 = $this->actingAs($this->userSuperMonitoring, 'sanctum')
                          ->postJson('/api/monitoring/flags', [
                              'target_admin_id' => $this->userVerifikator->id,
                              'alasan' => 'Lambat verifikasi',
                          ]);
        $response2->assertStatus(200)
                  ->assertJsonFragment(['message' => 'Flag berhasil dikirim ke Super Admin Pemilik.']);
    }

    /**
     * Test Super Admin Monitoring is blocked from viewing individual Dispute and Transaksi records.
     */
    public function test_monitoring_blocked_from_disputes_and_transactions()
    {
        // Blocked from viewing individual dispute details
        $responseDispute = $this->actingAs($this->userSuperMonitoring, 'sanctum')
                               ->getJson("/api/test/disputes/{$this->dispute->id}");
        $responseDispute->assertStatus(403);

        // Blocked from viewing individual transaction details
        $responseTransaksi = $this->actingAs($this->userSuperMonitoring, 'sanctum')
                                 ->getJson("/api/test/transactions/{$this->transaksi->id}");
        $responseTransaksi->assertStatus(403);
    }

    /**
     * Test active shop can write to catalog, but pending review shop cannot.
     */
    public function test_shop_catalog_writing_rules_based_on_status()
    {
        // Active shop can write
        $responseActive = $this->actingAs($this->userTokoActive, 'sanctum')
                               ->postJson('/api/test/spareparts', [
                                   'nama' => 'Kapasitor Premium',
                                   'harga' => 60000,
                                   'stok' => 5,
                                   'merk' => 'Panasonic',
                               ]);
        $responseActive->assertStatus(200);

        // Pending review shop is forbidden
        $responsePending = $this->actingAs($this->userTokoPending, 'sanctum')
                                ->postJson('/api/test/spareparts', [
                                    'nama' => 'Kapasitor Premium',
                                    'harga' => 60000,
                                    'stok' => 5,
                                    'merk' => 'Panasonic',
                                ]);
        $responsePending->assertStatus(403);
    }

    /**
     * Test that no user can directly write to transactions.
     */
    public function test_all_users_blocked_from_direct_transaction_writes()
    {
        // Direct transaction post blocked for Pelanggan
        $response1 = $this->actingAs($this->userPelanggan, 'sanctum')
                          ->postJson('/api/test/transactions', [
                              'status_escrow' => 'RELEASED',
                          ]);
        $response1->assertStatus(403);

        // Direct transaction post blocked for Teknisi
        $response2 = $this->actingAs($this->userTeknisi, 'sanctum')
                          ->postJson('/api/test/transactions', [
                              'status_escrow' => 'RELEASED',
                          ]);
        $response2->assertStatus(403);
    }
}
