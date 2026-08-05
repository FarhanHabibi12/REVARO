<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Dispute;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\PelanggaranLog;
use App\Models\PendaftaranVerifikasi;
use App\Models\Role;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DisputeTest extends TestCase
{
    use RefreshDatabase;

    protected $rolePelanggan;
    protected $roleTeknisi;
    protected $roleResolusi;
    protected $roleOwner;

    protected $userCustomer;
    protected $userTech;
    protected $userAdminA;
    protected $userAdminB;
    protected $userOwner;

    protected $customer;
    protected $tech;
    protected $order;
    protected $transaksi;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->rolePelanggan = Role::create(['nama' => 'Pelanggan'])->id;
        $this->roleTeknisi = Role::create(['nama' => 'Teknisi'])->id;
        $this->roleResolusi = Role::create(['nama' => 'Admin Resolusi'])->id;
        $this->roleOwner = Role::create(['nama' => 'Super Admin (Pemilik Sistem)'])->id;

        // Create Users
        $this->userCustomer = User::create([
            'role_id' => $this->rolePelanggan,
            'nama' => 'Budi Pelanggan',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->customer = Pelanggan::create([
            'user_id' => $this->userCustomer->id,
            'nama' => 'Budi Pelanggan',
            'email' => 'budi@gmail.com',
            'hp' => '0812',
            'alamat' => 'Sudirman',
            'saldo' => 500000.00,
        ]);

        $this->userTech = User::create([
            'role_id' => $this->roleTeknisi,
            'nama' => 'Joko Teknisi',
            'email' => 'joko@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->tech = Teknisi::create([
            'user_id' => $this->userTech->id,
            'nama' => 'Joko Teknisi',
            'hp' => '0856',
            'kategori' => 'AC Pilihan',
            'status' => 'ACTIVE',
            'tersedia' => false,
            'saldo' => 0.00,
        ]);

        // Create two Resolvers
        $this->userAdminA = User::create([
            'role_id' => $this->roleResolusi,
            'nama' => 'Admin Resolusi A',
            'email' => 'adminA@revaro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->userAdminB = User::create([
            'role_id' => $this->roleResolusi,
            'nama' => 'Admin Resolusi B',
            'email' => 'adminB@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        // Create Super Admin Owner
        $this->userOwner = User::create([
            'role_id' => $this->roleOwner,
            'nama' => 'Pemilik Sistem',
            'email' => 'owner@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        // Create Order and Transaksi
        $this->order = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->tech->id,
            'kode_order' => 'REV-DISPUTE-01',
            'status' => 'COMPLETED',
            'keluhan' => 'AC bocor air',
            'alamat_servis' => 'Sudirman No. 45',
        ]);
        $this->transaksi = Transaksi::create([
            'order_id' => $this->order->id,
            'biaya_jasa' => 100000.00,
            'biaya_part' => 0.00,
            'total' => 100000.00,
            'metode_pembayaran' => 'WALLET',
            'status_pembayaran' => 'PAID',
            'status_escrow' => 'HELD',
            'paid_at' => Carbon::now(),
        ]);
    }

    /**
     * Test load-balanced auto-assignment on dispute creation.
     */
    public function test_dispute_creation_with_load_balanced_assignment()
    {
        // Setup: Admin B already has 1 open dispute assigned
        Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userAdminB->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'Old dispute',
        ]);

        // Customer files a new dispute
        $response = $this->actingAs($this->userCustomer, 'sanctum')
                         ->postJson('/api/disputes', [
                             'order_id' => $this->order->id,
                             'alasan' => 'AC masih bocor setelah diservis.',
                         ]);

        $response->assertStatus(201);
        $disputeId = $response->json('dispute.id');
        $dispute = Dispute::find($disputeId);

        $this->assertEquals('DISPUTED', $this->order->fresh()->status);
        $this->assertEquals('INVESTIGATING', $dispute->status);
        // Should assign to Admin A (load = 0) instead of Admin B (load = 1)
        $this->assertEquals($this->userAdminA->id, $dispute->admin_id);
        // Verify SLA deadline is 48 hours
        $this->assertNotNull($dispute->sla_deadline);
        $this->assertTrue(Carbon::parse($dispute->sla_deadline)->diffInHours(Carbon::now()) >= 47);
    }

    /**
     * Test category-specific warranty validation checks.
     */
    public function test_dispute_warranty_check()
    {
        // 1. AC category has 30 days warranty. Setup paid 40 days ago.
        $this->order->update(['keluhan' => 'Kelistrikan AC rusak']);
        $this->transaksi->update(['paid_at' => Carbon::now()->subDays(40)]);

        $response1 = $this->actingAs($this->userCustomer, 'sanctum')
                           ->postJson('/api/disputes', [
                               'order_id' => $this->order->id,
                               'alasan' => 'Servis AC kurang dingin.',
                           ]);
        $response1->assertStatus(400)
                  ->assertJsonFragment(['message' => 'Masa garansi perbaikan (30 hari) telah kedaluwarsa.']);

        // 2. Non-AC categories have 7 days warranty. Setup paid 10 days ago.
        $this->order->update(['keluhan' => 'Servis Mesin Cuci mampet']);
        $this->transaksi->update(['paid_at' => Carbon::now()->subDays(10)]);

        $response2 = $this->actingAs($this->userCustomer, 'sanctum')
                           ->postJson('/api/disputes', [
                               'order_id' => $this->order->id,
                               'alasan' => 'Mesin cuci masih bocor.',
                           ]);
        $response2->assertStatus(400)
                  ->assertJsonFragment(['message' => 'Masa garansi perbaikan (7 hari) telah kedaluwarsa.']);
    }

    /**
     * Test resolutions: REFUND_PELANGGAN, RELEASE_TEKNISI, and KUNJUNGAN_ULANG.
     */
    public function test_dispute_resolutions()
    {
        // 1. REFUND_PELANGGAN
        $dispute1 = Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userAdminA->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'AC bermasalah',
        ]);
        $this->actingAs($this->userAdminA, 'sanctum')
             ->postJson("/api/disputes/{$dispute1->id}/resolve", [
                 'keputusan' => 'REFUND_PELANGGAN',
                 'resolusi' => 'Dana dikembalikan karena teknisi tidak datang.',
             ])->assertStatus(200);

        $this->assertEquals('RESOLVED', $dispute1->fresh()->status);
        $this->assertEquals('REFUNDED', $this->transaksi->fresh()->status_escrow);
        $this->assertEquals(600000.00, $this->customer->fresh()->saldo); // 500.000 + 100.000 refund

        // Reset customer wallet & escrow
        $this->customer = $this->customer->fresh();
        $this->customer->update(['saldo' => 500000.00]);
        
        $this->transaksi = $this->transaksi->fresh();
        $this->transaksi->update(['status_escrow' => 'HELD']);

        // 2. RELEASE_TEKNISI
        $dispute2 = Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userAdminA->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'AC bermasalah',
        ]);
        $this->actingAs($this->userAdminA, 'sanctum')
             ->postJson("/api/disputes/{$dispute2->id}/resolve", [
                 'keputusan' => 'RELEASE_TEKNISI',
                 'resolusi' => 'Kerja teknisi sudah sesuai standar.',
             ])->assertStatus(200);

        $this->assertEquals('RELEASED', $this->transaksi->fresh()->status_escrow);
        $this->assertEquals(100000.00, $this->tech->fresh()->saldo); // Tech gets service fee

        // 3. KUNJUNGAN_ULANG
        $this->transaksi = $this->transaksi->fresh();
        $this->transaksi->update(['status_escrow' => 'HELD']);
        $dispute3 = Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userAdminA->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'AC bermasalah',
        ]);
        $this->actingAs($this->userAdminA, 'sanctum')
             ->postJson("/api/disputes/{$dispute3->id}/resolve", [
                 'keputusan' => 'KUNJUNGAN_ULANG',
                 'resolusi' => 'Teknisi wajib datang servis ulang.',
             ])->assertStatus(200);

        $this->assertEquals('IN_PROGRESS', $this->order->fresh()->status);
        $this->assertEquals('HELD', $this->transaksi->fresh()->status_escrow);
        $this->assertFalse((bool) $this->tech->fresh()->tersedia);
    }

    /**
     * Test FREEZE_AKUN with automated penalty warnings check (penalti bertingkat).
     */
    public function test_dispute_resolution_with_account_freeze_and_warnings()
    {
        $dispute = Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userAdminA->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'Teknisi mencuri sparepart',
        ]);

        // Pre-create 2 warning violation logs for tech
        PelanggaranLog::create([
            'teknisi_id' => $this->tech->id,
            'jenis_pelanggaran' => 'Batal sepihak',
            'tingkat_penalti' => 'WARNING',
        ]);
        PelanggaranLog::create([
            'teknisi_id' => $this->tech->id,
            'jenis_pelanggaran' => 'Batal sepihak',
            'tingkat_penalti' => 'WARNING',
        ]);

        // Action: Resolver resolves dispute by freezing account (adding 3rd warning)
        $this->actingAs($this->userAdminA, 'sanctum')
             ->postJson("/api/disputes/{$dispute->id}/resolve", [
                 'keputusan' => 'FREEZE_AKUN',
                 'resolusi' => 'Kecurangan parah terbukti.',
             ])->assertStatus(200);

        // Verify warning is logged (should be 3 warnings now)
        $this->assertDatabaseHas('pelanggaran_log', [
            'teknisi_id' => $this->tech->id,
            'dispute_id' => $dispute->id,
            'tingkat_penalti' => 'WARNING',
        ]);

        // Auto-freeze verification
        $this->assertEquals('FROZEN', $this->tech->fresh()->status);
        $this->assertFalse((bool) $this->userTech->fresh()->is_active);
    }

    /**
     * Test check-dispute-sla alerts resolvers and system owners.
     */
    public function test_dispute_sla_deadline_checks()
    {
        $dispute = Dispute::create([
            'order_id' => $this->order->id,
            'admin_id' => $this->userAdminA->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'SLA test',
            'sla_deadline' => Carbon::now()->subHours(1), // Exceeded deadline
        ]);

        $this->artisan('revaro:check-dispute-sla')
             ->expectsOutput('Scanning dispute SLA deadlines...')
             ->expectsOutput('Completed. Warned: 0, Escalated: 1')
             ->assertExitCode(0);

        // Verify resolver received alert
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->userAdminA->id,
            'judul' => 'Tenggat SLA Terlewati',
        ]);

        // Verify owner received escalation
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->userOwner->id,
            'judul' => 'Eskalasi SLA Sengketa',
        ]);
    }
}
