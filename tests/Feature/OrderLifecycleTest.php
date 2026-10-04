<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\PelanggaranLog;
use App\Models\PendaftaranVerifikasi;
use App\Models\Role;
use App\Models\Sparepart;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected $rolePelanggan;
    protected $roleTeknisi;
    protected $roleToko;

    protected $userCustomer;
    protected $userTech1;
    protected $userTech2;
    protected $userShop;

    protected $customer;
    protected $tech1;
    protected $tech2;
    protected $shop;

    protected $sparepart;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->rolePelanggan = Role::create(['nama' => 'Pelanggan'])->id;
        $this->roleTeknisi = Role::create(['nama' => 'Teknisi'])->id;
        $this->roleToko = Role::create(['nama' => 'Toko Sparepart'])->id;

        // Create Customer (with initial wallet balance Rp 500.000)
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
            'hp' => '081234',
            'alamat' => 'Sudirman',
            'saldo' => 500000.00,
        ]);

        // Create Tech 1 (ACTIVE, nearby, 1.2 KM away)
        $this->userTech1 = User::create([
            'role_id' => $this->roleTeknisi,
            'nama' => 'Teknisi Joko',
            'email' => 'joko@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->tech1 = Teknisi::create([
            'user_id' => $this->userTech1->id,
            'nama' => 'Teknisi Joko',
            'hp' => '0856',
            'kategori' => 'AC',
            'status' => 'ACTIVE',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'tersedia' => true,
            'saldo' => 0.00,
        ]);

        // Create Tech 2 (ACTIVE, further away, 12 KM away)
        $this->userTech2 = User::create([
            'role_id' => $this->roleTeknisi,
            'nama' => 'Teknisi Tono',
            'email' => 'tono@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->tech2 = Teknisi::create([
            'user_id' => $this->userTech2->id,
            'nama' => 'Teknisi Tono',
            'hp' => '085699',
            'kategori' => 'AC',
            'status' => 'ACTIVE',
            'latitude' => -6.2690,
            'longitude' => 106.9456,
            'tersedia' => true,
            'saldo' => 0.00,
        ]);

        // Create Shop (ACTIVE)
        $this->userShop = User::create([
            'role_id' => $this->roleToko,
            'nama' => 'Toko Jaya',
            'email' => 'jaya@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        $this->shop = Toko::create([
            'user_id' => $this->userShop->id,
            'nama' => 'Toko Jaya',
            'izin_usaha' => 'SIUP-1',
            'alamat' => 'Glodok',
            'telepon' => '021',
            'status' => 'ACTIVE',
            'latitude' => -6.1512,
            'longitude' => 106.8145,
            'saldo' => 0.00,
        ]);

        // Create Sparepart item (stock = 5, price = Rp 50.000)
        $this->sparepart = Sparepart::create([
            'toko_id' => $this->shop->id,
            'nama' => 'Kapasitor AC 35uF',
            'harga' => 50000.00,
            'stok' => 5,
            'merk' => 'Shizuki',
        ]);
    }

    /**
     * Test full success path: SOS -> Accept -> OnTheWay -> Arrive -> Request Parts -> Approve -> Confirm -> Complete -> Pay -> Release.
     */
    public function test_complete_order_success_lifecycle()
    {
        // 1. SOS Creation (Customer lat: -6.2000, lon: 106.8166)
        $sosPayload = [
            'keluhan' => 'AC berisik',
            'alamat_servis' => 'Sudirman No. 45',
            'latitude' => -6.2000,
            'longitude' => 106.8166,
        ];
        $responseSOS = $this->actingAs($this->userCustomer, 'sanctum')
                            ->postJson('/api/orders', $sosPayload);

        $responseSOS->assertStatus(201);
        $orderId = $responseSOS->json('order.id');
        $order = Order::find($orderId);
        $this->assertEquals('SEARCHING', $order->status);

        // Verify that nearby Tech 1 received notification, but Tech 2 (too far) did not
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->userTech1->id,
            'judul' => 'Order Baru Tersedia',
        ]);
        $this->assertDatabaseMissing('notifikasi', [
            'user_id' => $this->userTech2->id,
            'judul' => 'Order Baru Tersedia',
        ]);

        // 2. Accept Order & Race Condition Handling
        // Tech 1 accepts first
        $responseAccept1 = $this->actingAs($this->userTech1, 'sanctum')
                               ->postJson("/api/orders/{$order->id}/accept");
        $responseAccept1->assertStatus(200);
        $this->assertEquals('DISPATCHED', $order->fresh()->status);
        $this->assertEquals($this->tech1->id, $order->fresh()->teknisi_id);
        $this->assertFalse((bool) $this->tech1->fresh()->tersedia);

        // Tech 2 tries to accept but gets 409 Conflict
        $responseAccept2 = $this->actingAs($this->userTech2, 'sanctum')
                               ->postJson("/api/orders/{$order->id}/accept");
        $responseAccept2->assertStatus(409)
                        ->assertJsonFragment(['message' => 'Order sudah diterima oleh teknisi lain.']);

        // 3. Travel to location
        $this->actingAs($this->userTech1, 'sanctum')
             ->postJson("/api/orders/{$order->id}/ontheway")
             ->assertStatus(200);
        $this->assertEquals('ON_THE_WAY', $order->fresh()->status);

        // 4. Arrive & Diagnose
        $this->actingAs($this->userTech1, 'sanctum')
             ->postJson("/api/orders/{$order->id}/arrive")
             ->assertStatus(200);
        $this->assertEquals('IN_PROGRESS', $order->fresh()->status);

        // 5. Request Sparepart
        $partsPayload = [
            'spareparts' => [
                [
                    'sparepart_id' => $this->sparepart->id,
                    'qty' => 2,
                ]
            ]
        ];
        $this->actingAs($this->userTech1, 'sanctum')
             ->postJson("/api/orders/{$order->id}/spareparts", $partsPayload)
             ->assertStatus(200);
        
        $this->assertDatabaseHas('order_sparepart', [
            'order_id' => $order->id,
            'sparepart_id' => $this->sparepart->id,
            'qty' => 2,
            'status' => 'PENDING_CUSTOMER',
        ]);

        // 6. Customer Approves Spareparts
        $this->actingAs($this->userCustomer, 'sanctum')
             ->postJson("/api/orders/{$order->id}/spareparts/approve", ['approve' => true])
             ->assertStatus(200);

        // 7. Shop Confirms Spareparts -> stock is deducted
        $this->actingAs($this->userShop, 'sanctum')
             ->postJson("/api/toko/orders/{$order->id}/spareparts/confirm", ['status' => 'CONFIRMED'])
             ->assertStatus(200);

        $this->assertEquals(3, $this->sparepart->fresh()->stok); // 5 - 2 = 3
        $this->assertEquals('CONFIRMED', $order->spareparts()->first()->pivot->status);

        // 8. Complete Service -> Order status AWAITING_PAYMENT & Transaksi created
        $this->actingAs($this->userTech1, 'sanctum')
             ->postJson("/api/orders/{$order->id}/complete")
             ->assertStatus(200);

        $this->assertEquals('AWAITING_PAYMENT', $order->fresh()->status);
        $this->assertDatabaseHas('transaksi', [
            'order_id' => $order->id,
            'biaya_jasa' => 100000.00,
            'biaya_part' => 100000.00, // 50.000 * 2
            'total' => 200000.00,
            'status_pembayaran' => 'UNPAID',
            'status_escrow' => 'NONE',
        ]);

        // 9. Pay Invoice -> customer balance deducted, status PAID, escrow HELD, tech available
        $this->actingAs($this->userCustomer, 'sanctum')
             ->postJson("/api/orders/{$order->id}/pay")
             ->assertStatus(200);

        $this->assertEquals('COMPLETED', $order->fresh()->status);
        $this->assertEquals(300000.00, $this->customer->fresh()->saldo); // 500.000 - 200.000
        $this->assertTrue((bool) $this->tech1->fresh()->tersedia);
        $this->assertDatabaseHas('transaksi', [
            'order_id' => $order->id,
            'status_pembayaran' => 'PAID',
            'status_escrow' => 'RELEASED',
        ]);

        // 10. Confirm Escrow Release -> wallets updated
        $this->actingAs($this->userCustomer, 'sanctum')
             ->postJson("/api/orders/{$order->id}/confirm-release")
             ->assertStatus(200);

        $this->assertDatabaseHas('transaksi', [
            'order_id' => $order->id,
            'status_escrow' => 'RELEASED',
        ]);
        $this->assertEquals(100000.00, $this->tech1->fresh()->saldo); // Rp 100.000 service fee
        $this->assertEquals(100000.00, $this->shop->fresh()->saldo); // Rp 100.000 parts fee
    }

    /**
     * Test Auto Release Escrow job after 24 hours.
     */
    public function test_auto_release_escrow_command()
    {
        $order = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->tech1->id,
            'kode_order' => 'REV-RELEASE-01',
            'status' => 'COMPLETED',
            'keluhan' => 'AC berisik',
            'alamat_servis' => 'Sudirman No. 45',
        ]);

        $transaksi = Transaksi::create([
            'order_id' => $order->id,
            'biaya_jasa' => 120000.00,
            'biaya_part' => 0.00,
            'total' => 120000.00,
            'metode_pembayaran' => 'WALLET',
            'status_pembayaran' => 'PAID',
            'status_escrow' => 'HELD',
            'paid_at' => Carbon::now()->subHours(25), // > 24 hours ago
        ]);

        // Execute auto release
        $this->artisan('revaro:auto-release-escrow')
             ->expectsOutput('Scanning for escrow transactions to release...')
             ->expectsOutput('Completed. Auto-released 1 transactions.')
             ->assertExitCode(0);

        $this->assertEquals('RELEASED', $transaksi->fresh()->status_escrow);
        $this->assertEquals(120000.00, $this->tech1->fresh()->saldo);
    }

    /**
     * Test Cancellation policies.
     */
    public function test_cancellation_compensation_and_penalties()
    {
        // 1. Customer cancellation while technician is ON_THE_WAY
        $order1 = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->tech1->id,
            'kode_order' => 'REV-CANCEL-1',
            'status' => 'ON_THE_WAY',
            'keluhan' => 'AC berisik',
            'alamat_servis' => 'Sudirman No. 45',
        ]);

        $response1 = $this->actingAs($this->userCustomer, 'sanctum')
                          ->postJson("/api/orders/{$order1->id}/cancel");
        $response1->assertStatus(200);

        $this->assertEquals('CANCELLED', $order1->fresh()->status);
        $this->assertEquals(480000.00, $this->customer->fresh()->saldo); // Deducted Rp 20.000 compensation
        $this->assertEquals(20000.00, $this->tech1->fresh()->saldo); // Tech receives Rp 20.000

        // 2. Unilateral cancellation by Technician -> Penalti WARNING log
        $order2 = Order::create([
            'pelanggan_id' => $this->customer->id,
            'teknisi_id' => $this->tech1->id,
            'kode_order' => 'REV-CANCEL-2',
            'status' => 'DISPATCHED',
            'keluhan' => 'AC berisik',
            'alamat_servis' => 'Sudirman No. 45',
        ]);

        $response2 = $this->actingAs($this->userTech1, 'sanctum')
                          ->postJson("/api/orders/{$order2->id}/cancel");
        $response2->assertStatus(200);

        $this->assertEquals('CANCELLED', $order2->fresh()->status);
        $this->assertDatabaseHas('pelanggaran_log', [
            'teknisi_id' => $this->tech1->id,
            'jenis_pelanggaran' => 'Pembatalan sepihak setelah pesanan diterima',
            'tingkat_penalti' => 'WARNING',
        ]);
    }
}
