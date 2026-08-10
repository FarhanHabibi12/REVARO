<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pelanggan (Customer profiles)
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('email');
            $table->string('hp');
            $table->text('alamat');
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 2. Teknisi (Technician profiles)
        Schema::create('teknisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('kategori');
            $table->string('tier')->default('Bronze');
            $table->string('hp');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->boolean('tersedia')->default(true)->index();
            $table->string('status')->default('PENDING_REVIEW')->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Toko Sparepart (Shop profiles)
        Schema::create('toko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('izin_usaha');
            $table->text('alamat');
            $table->string('telepon');
            $table->string('status')->default('PENDING_REVIEW')->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 4. Sparepart catalog
        Schema::create('sparepart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->string('nama');
            $table->decimal('harga', 15, 2);
            $table->integer('stok')->default(0);
            $table->string('merk');
            $table->timestamps();
        });

        // Geolocation composite indexes
        Schema::table('teknisi', function (Blueprint $table) {
            $table->index(['latitude', 'longitude'], 'teknisi_geolocation_index');
        });
        Schema::table('toko', function (Blueprint $table) {
            $table->index(['latitude', 'longitude'], 'toko_geolocation_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sparepart');
        Schema::dropIfExists('toko');
        Schema::dropIfExists('teknisi');
        Schema::dropIfExists('pelanggan');
    }
};
