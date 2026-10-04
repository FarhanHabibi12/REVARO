<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\PelanggaranLog;
use App\Models\Rating;
use App\Models\Role;
use App\Models\Sparepart;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Dispute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewFeaturesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $customerUser;
    protected $customer;
    protected $techUser;
    protected $teknisi;
    protected $tokoUser;
    protected $toko;
    protected $adminResolusiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePelanggan = Role::firstOrCreate(['nama' => 'Pelanggan']);
        $roleTeknisi = Role::firstOrCreate(['nama' => 'Teknisi']);
        $roleToko = Role::firstOrCreate(['nama' => 'Toko Sparepart']);
        $roleResolusi = Role::firstOrCreate(['nama' => 'Admin Resolusi']);

        $this->customerUser = User::create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Budi Pelanggan',
            'email' => 'budi_pelanggan@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        $this->customer = Pelanggan::create([
            'user_id' => $this->customerUser->id,
            'nama' => 'Budi Pelanggan',
            'email' => $this->customerUser->email,
            'hp' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 1'
        ]);

        $this->techUser = User::create([
            'role_id' => $roleTeknisi->id,
            'nama' => 'Joko Teknisi',
            'email' => 'joko_teknisi@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        $this->teknisi = Teknisi::create([
            'user_id' => $this->techUser->id,
            'nama' => 'Joko Teknisi',
            'kategori' => 'Elektronik Rumah Tangga',
            'hp' => '08987654321',
            'rating' => 5.00,
            'status' => 'ACTIVE',
            'tier' => 'Silver'
        ]);

        $this->tokoUser = User::create([
            'role_id' => $roleToko->id,
            'nama' => 'Toko Maju Jaya',
            'email' => 'toko_majujaya@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        $this->toko = Toko::create([
            'user_id' => $this->tokoUser->id,
            'nama' => 'Toko Maju Jaya Sparepart',
            'izin_usaha' => 'NIB-99182371928',
            'alamat' => 'Jl. Glodok Sentra No. 10',
            'telepon' => '081122334455',
            'saldo' => 500000.00,
            'status' => 'ACTIVE'
        ]);

        $this->adminResolusiUser = User::create([
            'role_id' => $roleResolusi->id,
            'nama' => 'Staff Resolusi',
            'email' => 'resolusi@revaro.id',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_notification_flow_and_unread_counter()
    {
        // 1. Create a notification
        Notifikasi::create([
            'user_id' => $this->customerUser->id,
            'judul' => 'Status Pesanan Diperbarui',
            'pesan' => 'Teknisi sedang menuju ke lokasi Anda.',
            'dibaca' => false,
        ]);

        // 2. Fetch notifications list
        $response = $this->actingAs($this->customerUser)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonPath('unread_count', 1)
            ->assertJsonCount(1, 'notifications');

        $notifId = $response->json('notifications.0.id');

        // 3. Mark single notification as read
        $markRes = $this->actingAs($this->customerUser)
            ->postJson("/api/notifications/{$notifId}/read");

        $markRes->assertStatus(200);
        $this->assertDatabaseHas('notifikasi', [
            'id' => $notifId,
            'dibaca' => true,
        ]);

        // 4. Mark all as read
        Notifikasi::create([
            'user_id' => $this->customerUser->id,
            'judul' => 'Promo Diskon',
            'pesan' => 'Dapatkan cashback 10%.',
            'dibaca' => false,
        ]);

        $allRes = $this->actingAs($this->customerUser)
            ->postJson('/api/notifications/read-all');

        $allRes->assertStatus(200);
        $this->assertEquals(0, Notifikasi::where('user_id', $this->customerUser->id)->where('dibaca', false)->count());
    }

    public function test_chat_creates_in_app_notification_for_recipient()
    {
        $order = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->teknisi->id,
            'kode_order' => 'REV-TEST-001',
            'status' => 'IN_PROGRESS',
            'keluhan' => 'Kulkas tidak dingin',
            'alamat_servis' => 'Jl. Mawar No. 12',
            'tanggal_servis' => now(),
        ]);

        // Customer sends chat message
        $response = $this->actingAs($this->customerUser)
            ->postJson("/api/orders/{$order->id}/chats", [
                'pesan' => 'Halo Mas Teknisi, perkiraan tiba jam berapa ya?'
            ]);

        $response->assertStatus(201);

        // Check if technician received a notification
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->techUser->id,
            'judul' => 'Pesan Baru dari ' . $this->customerUser->nama,
        ]);
    }

    public function test_rating_system_and_escalation_to_admin_resolusi_on_bad_rating()
    {
        $order = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->teknisi->id,
            'kode_order' => 'REV-RATE-001',
            'status' => 'COMPLETED',
            'keluhan' => 'AC bocor air',
            'alamat_servis' => 'Jl. Anggrek No. 5',
            'tanggal_servis' => now(),
        ]);

        Transaksi::create([
            'order_id' => $order->id,
            'biaya_jasa' => 100000,
            'biaya_part' => 0,
            'total' => 100000,
            'metode_pembayaran' => 'QRIS',
            'status_pembayaran' => 'PAID',
            'status_escrow' => 'RELEASED',
            'paid_at' => now(),
        ]);

        // Customer submits a bad rating (1 star)
        $response = $this->actingAs($this->customerUser)
            ->postJson("/api/orders/{$order->id}/rating", [
                'skor' => 1,
                'komentar' => 'Teknisi datang terlambat 3 jam dan perbaikan AC malah bertambah bocor parah!',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        // 1. Rating recorded
        $this->assertDatabaseHas('rating', [
            'order_id' => $order->id,
            'skor' => 1,
        ]);

        // 2. Technician average rating drops and tier downgraded
        $this->teknisi->refresh();
        $this->assertEquals(1.00, (float)$this->teknisi->rating);
        $this->assertEquals('Bronze', $this->teknisi->tier);

        // 3. Violation log created
        $this->assertDatabaseHas('pelanggaran_log', [
            'teknisi_id' => $this->teknisi->id,
            'tingkat_penalti' => 'BERAT',
        ]);

        // 4. Auto-escalated to Admin Resolusi (Dispute record created)
        $this->assertDatabaseHas('dispute', [
            'order_id' => $order->id,
            'admin_id' => $this->adminResolusiUser->id,
            'status' => 'INVESTIGATING',
        ]);

        // 5. Admin Resolusi notified
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->adminResolusiUser->id,
            'judul' => '⚠️ Investigasi Resolusi: Rating Sangat Buruk',
        ]);
    }

    public function test_toko_finance_summary_and_excel_export()
    {
        $sparepart = Sparepart::create([
            'toko_id' => $this->toko->id,
            'nama' => 'Kapasitor Kompresor 35uF',
            'harga' => 85000.00,
            'stok' => 20,
            'merk' => 'Panasonic'
        ]);

        $order = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->teknisi->id,
            'kode_order' => 'REV-TOKO-001',
            'status' => 'COMPLETED',
            'keluhan' => 'AC mati total',
            'alamat_servis' => 'Jl. Mawar No. 1',
            'tanggal_servis' => now(),
        ]);

        $order->spareparts()->attach($sparepart->id, [
            'qty' => 2,
            'harga' => 85000.00,
            'subtotal' => 170000.00,
            'status' => 'CONFIRMED',
        ]);

        Transaksi::create([
            'order_id' => $order->id,
            'biaya_jasa' => 100000,
            'biaya_part' => 170000,
            'total' => 270000,
            'metode_pembayaran' => 'QRIS',
            'status_pembayaran' => 'PAID',
            'status_escrow' => 'RELEASED',
            'paid_at' => now(),
        ]);

        // 1. Get finance summary
        $response = $this->actingAs($this->tokoUser)
            ->getJson('/api/toko/finance');

        $response->assertStatus(200)
            ->assertJsonPath('summary.total_omset', 170000)
            ->assertJsonPath('summary.total_transaksi', 1)
            ->assertJsonCount(1, 'ledger');

        // 2. Export Excel (.xlsx)
        $excelResponse = $this->actingAs($this->tokoUser)
            ->get('/api/toko/finance/export-excel');

        $excelResponse->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->assertStringContainsString('.xlsx', $excelResponse->headers->get('content-disposition'));
        $this->assertNotEmpty($excelResponse->streamedContent());
    }

    public function test_otp_verification_preserves_multiple_unexpired_codes_and_handles_casing()
    {
        $testEmail = 'user_test_otp@example.com';
        $user = User::create([
            'role_id' => Role::firstOrCreate(['nama' => 'Pelanggan'])->id,
            'nama' => 'Test OTP User',
            'email' => $testEmail,
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        // Simulate 1st OTP code sent
        \Illuminate\Support\Facades\DB::table('email_otps')->insert([
            'email' => $testEmail,
            'otp_code' => '787420',
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simulate 2nd OTP code sent shortly after (resend or second click)
        \Illuminate\Support\Facades\DB::table('email_otps')->insert([
            'email' => $testEmail,
            'otp_code' => '909762',
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // The user enters the FIRST OTP code with uppercase/different casing:
        $response = $this->postJson('/api/auth/verify-otp', [
            'email' => 'User_Test_Otp@example.com',
            'otp_code' => '787420',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Verifikasi email berhasil.');

        $this->assertNotNull($response->json('access_token'));

        // All OTP records for that email should now be cleaned up
        $this->assertEquals(0, \Illuminate\Support\Facades\DB::table('email_otps')->where('email', $testEmail)->count());
    }

    public function test_universal_user_profile_and_password_update()
    {
        $response = $this->actingAs($this->customerUser)
            ->postJson('/api/user/profile', [
                'nama' => 'Budi Pelanggan Update',
                'email' => $this->customerUser->email,
                'hp' => '081299988877',
                'alamat' => 'Jl. Boulevard No. 99, Jakarta',
                'new_password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->customerUser->id,
            'nama' => 'Budi Pelanggan Update',
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'user_id' => $this->customerUser->id,
            'hp' => '081299988877',
            'alamat' => 'Jl. Boulevard No. 99, Jakarta',
        ]);

        // Verify new password works
        $loginRes = $this->postJson('/api/auth/login', [
            'email' => $this->customerUser->email,
            'password' => 'newsecret123',
        ]);
        $loginRes->assertStatus(200);
    }

    public function test_duplicate_mitra_registration_blocked()
    {
        // Technician tries to register as technician again while logged in -> Blocked
        $response = $this->actingAs($this->techUser)
            ->postJson('/api/auth/register', [
                'role_slug' => 'teknisi',
                'nama' => 'Joko Duplicate',
                'email' => $this->techUser->email,
                'hp' => '08987654321',
                'kategori' => 'AC',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Anda sudah terdaftar sebagai Mitra Teknisi di sistem REVARO. Tidak perlu mendaftar ulang.');

        // Toko tries to register as Toko again -> Blocked
        \Laravel\Sanctum\Sanctum::actingAs($this->tokoUser);
        $tokoBlockRes = $this->postJson('/api/auth/register', [
                'role_slug' => 'toko-sparepart',
                'nama' => 'Toko Duplicate',
                'email' => $this->tokoUser->email,
                'telepon' => '081122334455',
                'alamat' => 'Jl. Toko Baru No. 1',
                'izin_usaha' => 'NIB-999',
            ]);
        $tokoBlockRes->assertStatus(422)
            ->assertJsonPath('message', 'Anda sudah terdaftar sebagai Mitra Toko Sparepart di sistem REVARO. Tidak perlu mendaftar ulang.');

        // But Toko user CAN register as Teknisi (Multi-Role partner)!
        $multiRoleRes = $this->postJson('/api/auth/register', [
                'role_slug' => 'teknisi',
                'nama' => 'Toko Maju Jaya Teknisi',
                'email' => $this->tokoUser->email,
                'hp' => '081122334455',
                'kategori' => 'Air Conditioner (AC)',
                'alamat' => 'Jl. Glodok Sentra No. 10',
            ]);
        $multiRoleRes->assertStatus(201);

        // Verify /api/auth/me returns has_teknisi: true AND has_toko: true
        $meRes = $this->getJson('/api/auth/me');
        $meRes->assertStatus(200)
            ->assertJsonPath('has_teknisi', true)
            ->assertJsonPath('has_toko', true);

        // Now that Toko user is also registered as Teknisi, trying to register as Teknisi again is blocked!
        $secondTechRes = $this->postJson('/api/auth/register', [
                'role_slug' => 'teknisi',
                'nama' => 'Toko Maju Jaya Teknisi 2',
                'email' => $this->tokoUser->email,
                'hp' => '081122334455',
                'kategori' => 'Air Conditioner (AC)',
                'alamat' => 'Jl. Glodok Sentra No. 10',
            ]);
        $secondTechRes->assertStatus(422);
    }

    public function test_admin_resolusi_rating_evaluation_and_technician_ban()
    {
        $order = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->teknisi->id,
            'kode_order' => 'REV-EVAL-001',
            'status' => 'COMPLETED',
            'keluhan' => 'Kulkas rusak',
            'alamat_servis' => 'Jl. Flamboyan No. 1',
            'tanggal_servis' => now(),
        ]);

        $dispute = \App\Models\Dispute::create([
            'order_id' => $order->id,
            'admin_id' => $this->adminResolusiUser->id,
            'status' => 'INVESTIGATING',
            'alasan' => 'Eskalasi Mutu Servis: Rating Buruk (1 Bintang) untuk Teknisi Joko Teknisi.',
            'sla_deadline' => now()->addHours(24),
        ]);

        // 1. Check rating-evaluations endpoint
        $evalRes = $this->actingAs($this->adminResolusiUser)
            ->getJson('/api/admin/resolusi/rating-evaluations');
        $evalRes->assertStatus(200);
        $this->assertTrue(count($evalRes->json()) >= 1);

        // 2. Direct Ban technician
        $banRes = $this->actingAs($this->adminResolusiUser)
            ->postJson("/api/admin/teknisi/{$this->teknisi->id}/ban", [
                'alasan' => 'Akumulasi ulasan buruk dan kelalaian berat.',
            ]);
        $banRes->assertStatus(200);

        // 3. Technician status is FROZEN, user is inactive
        $this->teknisi->refresh();
        $this->techUser->refresh();
        $this->assertEquals('FROZEN', $this->teknisi->status);
        $this->assertFalse((bool)$this->techUser->is_active);

        // 4. Technician CANNOT login
        $loginRes = $this->postJson('/api/auth/login', [
            'email' => $this->techUser->email,
            'password' => 'password123',
        ]);
        $loginRes->assertStatus(403);
    }
}
