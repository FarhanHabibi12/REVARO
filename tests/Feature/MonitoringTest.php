<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected $rolePelanggan;
    protected $roleTeknisi;
    protected $roleToko;
    protected $roleMonitoring;
    protected $roleOwner;

    protected $userCustomer;
    protected $userTechActive;
    protected $userTechFrozen;
    protected $userShopActive;
    protected $userShopRejected;
    protected $userMonitoring;
    protected $userOwner;

    protected $customer;
    protected $techActive;
    protected $techFrozen;
    protected $shopActive;
    protected $shopRejected;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->rolePelanggan = Role::create(['nama' => 'Pelanggan'])->id;
        $this->roleTeknisi = Role::create(['nama' => 'Teknisi'])->id;
        $this->roleToko = Role::create(['nama' => 'Toko Sparepart'])->id;
        $this->roleMonitoring = Role::create(['nama' => 'Super Admin (Monitoring Kinerja)'])->id;
        $this->roleOwner = Role::create(['nama' => 'Super Admin (Pemilik Sistem)'])->id;

        // Users & Profiles
        $this->userCustomer = User::create([
            'role_id' => $this->rolePelanggan,
            'nama' => 'Pelanggan Budi',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->customer = Pelanggan::create([
            'user_id' => $this->userCustomer->id,
            'nama' => 'Pelanggan Budi',
            'email' => 'budi@gmail.com',
            'hp' => '0812',
            'alamat' => 'Sudirman',
        ]);

        $this->userTechActive = User::create([
            'role_id' => $this->roleTeknisi,
            'nama' => 'Teknisi Aktif',
            'email' => 'techactive@revaro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->techActive = Teknisi::create([
            'user_id' => $this->userTechActive->id,
            'nama' => 'Teknisi Aktif',
            'hp' => '0856',
            'kategori' => 'AC',
            'status' => 'ACTIVE',
        ]);

        $this->userTechFrozen = User::create([
            'role_id' => $this->roleTeknisi,
            'nama' => 'Teknisi Fraud',
            'email' => 'techfrozen@revaro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->techFrozen = Teknisi::create([
            'user_id' => $this->userTechFrozen->id,
            'nama' => 'Teknisi Fraud',
            'hp' => '08569',
            'kategori' => 'AC',
            'status' => 'FROZEN',
        ]);

        $this->userShopActive = User::create([
            'role_id' => $this->roleToko,
            'nama' => 'Toko Aktif',
            'email' => 'shopactive@revaro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->shopActive = Toko::create([
            'user_id' => $this->userShopActive->id,
            'nama' => 'Toko Aktif',
            'izin_usaha' => 'SIUP-1',
            'alamat' => 'Sudirman',
            'telepon' => '021',
            'status' => 'ACTIVE',
        ]);

        $this->userShopRejected = User::create([
            'role_id' => $this->roleToko,
            'nama' => 'Toko Palsu',
            'email' => 'shoprejected@revaro.com',
            'password' => Hash::make('password123'),
        ]);
        $this->shopRejected = Toko::create([
            'user_id' => $this->userShopRejected->id,
            'nama' => 'Toko Palsu',
            'izin_usaha' => 'SIUP-FAKE',
            'alamat' => 'Sudirman',
            'telepon' => '021',
            'status' => 'REJECTED',
        ]);

        // Admins
        $this->userMonitoring = User::create([
            'role_id' => $this->roleMonitoring,
            'nama' => 'Monitoring Admin',
            'email' => 'monitoring@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        $this->userOwner = User::create([
            'role_id' => $this->roleOwner,
            'nama' => 'Owner Admin',
            'email' => 'owner@revaro.com',
            'password' => Hash::make('password123'),
        ]);

        // --- Mock Disputes for Metrics ---
        // 1. Resolved Dispute (took 4 hours)
        $order1 = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->techActive->id,
            'kode_order' => 'REV-MOCK-1',
            'status' => 'RESOLVED',
            'keluhan' => 'AC',
            'alamat_servis' => 'Sudirman',
        ]);
        $dispute1 = Dispute::create([
            'order_id' => $order1->id,
            'status' => 'RESOLVED',
            'alasan' => 'AC bocor',
            'keputusan' => 'REFUND_PELANGGAN',
            'resolved_at' => Carbon::now()->subHours(2),
        ]);
        $dispute1->created_at = Carbon::now()->subHours(6);
        $dispute1->save();

        // 2. Open Dispute (Active)
        $order2 = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->techActive->id,
            'kode_order' => 'REV-MOCK-2',
            'status' => 'DISPUTED',
            'keluhan' => 'AC',
            'alamat_servis' => 'Sudirman',
        ]);
        Dispute::create([
            'order_id' => $order2->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'AC bocor',
            'sla_deadline' => Carbon::now()->addHours(24),
        ]);

        // 3. SLA Breached Dispute (Deadline was 1 day ago)
        $order3 = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->techActive->id,
            'kode_order' => 'REV-MOCK-3',
            'status' => 'DISPUTED',
            'keluhan' => 'AC',
            'alamat_servis' => 'Sudirman',
        ]);
        Dispute::create([
            'order_id' => $order3->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'AC bocor',
            'sla_deadline' => Carbon::now()->subDays(1),
        ]);
    }

    /**
     * Test metrics access restrictions.
     */
    public function test_metrics_access_controls()
    {
        // 1. Customer blocked
        $this->actingAs($this->userCustomer, 'sanctum')
             ->getJson('/api/monitoring/metrics')
             ->assertStatus(403);

        // 2. Monitoring Admin allowed
        $this->actingAs($this->userMonitoring, 'sanctum')
             ->getJson('/api/monitoring/metrics')
             ->assertStatus(200);

        // 3. Owner Admin allowed
        $this->actingAs($this->userOwner, 'sanctum')
             ->getJson('/api/monitoring/metrics')
             ->assertStatus(200);
    }

    /**
     * Test aggregate metrics calculations correctness.
     */
    public function test_metrics_calculations()
    {
        $response = $this->actingAs($this->userMonitoring, 'sanctum')
                         ->getJson('/api/monitoring/metrics');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'total_active_technicians' => 1,
                     'total_active_shops' => 1,
                     'open_disputes' => 2,
                     'resolved_disputes' => 1,
                     'average_resolution_time_hours' => 4.0, // resolved diff = 4 hours
                     'onboarding_rejection_rate_percent' => 25.0, // 1 rejected out of 4 total profiles (25%)
                     'operational_freeze_rate_percent' => 25.0, // 1 frozen out of 4 total profiles (25%)
                     'sla_breach_count' => 1, // 1 exceeded deadline
                 ]);
    }

    /**
     * Test performance warning flagging alerts.
     */
    public function test_flag_reporting_system()
    {
        // Block other roles from flagging
        $this->actingAs($this->userCustomer, 'sanctum')
             ->postJson('/api/monitoring/flags', [
                 'judul' => 'Kinerja Menurun',
                 'pesan' => 'Rata-rata penyelesaian di atas 24 jam.',
                 'urgency' => 'MEDIUM',
             ])->assertStatus(403);

        // Success flagging by Monitoring Admin
        $response = $this->actingAs($this->userMonitoring, 'sanctum')
                         ->postJson('/api/monitoring/flags', [
                             'judul' => 'SLA Breach Rate High',
                             'pesan' => 'Lebih dari 30% sengketa melewati batas waktu.',
                             'urgency' => 'HIGH',
                         ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Flag berhasil dikirim ke Super Admin Pemilik.']);

        // Assert Owner received flag notification
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->userOwner->id,
            'judul' => 'FLAG ALERT [HIGH]: SLA Breach Rate High',
        ]);
    }
}
