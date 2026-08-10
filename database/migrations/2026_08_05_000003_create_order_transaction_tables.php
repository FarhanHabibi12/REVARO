<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->foreignId('teknisi_id')->nullable()->constrained('teknisi')->onDelete('set null');
            $table->string('kode_order')->unique();
            $table->string('status')->default('SEARCHING')->index();
            $table->text('keluhan');
            $table->text('alamat_servis');
            $table->timestamp('tanggal_servis')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->index('pelanggan_id');
            $table->index('teknisi_id');
            $table->timestamps();
        });

        // 2. Order Sparepart pivot
        Schema::create('order_sparepart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('sparepart_id')->constrained('sparepart')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('status')->default('PENDING_SHOP');
            $table->timestamps();
        });

        // 3. Transaksi (Escrow payments)
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->decimal('biaya_jasa', 15, 2)->default(0.00);
            $table->decimal('biaya_part', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->string('metode_pembayaran');
            $table->string('status_pembayaran')->default('UNPAID');
            $table->string('status_escrow')->default('HELD');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('order_sparepart');
        Schema::dropIfExists('orders');
    }
};
