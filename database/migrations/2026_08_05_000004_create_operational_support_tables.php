<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pendaftaran Verifikasi (Onboarding verification logs)
        Schema::create('pendaftaran_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teknisi_id')->nullable()->constrained('teknisi')->onDelete('cascade');
            $table->foreignId('toko_id')->nullable()->constrained('toko')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->index();
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 2. Dispute (Escrow conflict resolution)
        Schema::create('dispute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('OPEN')->index();
            $table->string('keputusan')->nullable();
            $table->timestamp('sla_deadline')->nullable();
            $table->text('alasan');
            $table->text('resolusi')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // 3. Pelanggaran Log (Violation/penalty logs)
        Schema::create('pelanggaran_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teknisi_id')->nullable()->constrained('teknisi')->onDelete('cascade');
            $table->foreignId('toko_id')->nullable()->constrained('toko')->onDelete('cascade');
            $table->foreignId('dispute_id')->nullable()->constrained('dispute')->onDelete('cascade');
            $table->string('jenis_pelanggaran');
            $table->string('tingkat_penalti');
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. Notifikasi (In-app notifications)
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('pesan');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });

        // 5. Activity Logs (Audit trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('aktivitas');
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. Rating
        Schema::create('rating', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ke_user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('skor');
            $table->text('komentar')->nullable();
            $table->timestamps();
        });

        // 7. Chats
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('cascade');
            $table->text('pesan');
            $table->timestamps();
        });

        // 8. Jasa Servis (Service categories & warranties)
        Schema::create('jasa_servis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jasa');
            $table->string('kategori');
            $table->decimal('harga_baseline', 15, 2)->default(0.00);
            $table->integer('garansi_hari')->default(30);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Kebijakan Komisi (Commission policies)
        Schema::create('kebijakan_komisi', function (Blueprint $table) {
            $table->id();
            $table->decimal('persen_komisi_jasa', 5, 2)->default(10.00);
            $table->decimal('persen_fee_sparepart', 5, 2)->default(5.00);
            $table->timestamps();
        });

        // 10. Wilayah Operasional (Operational regions)
        Schema::create('wilayah_operasional', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah');
            $table->string('kode_pos_prefix')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_operasional');
        Schema::dropIfExists('kebijakan_komisi');
        Schema::dropIfExists('jasa_servis');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('rating');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('pelanggaran_log');
        Schema::dropIfExists('dispute');
        Schema::dropIfExists('pendaftaran_verifikasi');
    }
};
