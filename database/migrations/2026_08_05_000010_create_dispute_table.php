<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('OPEN')->index(); // OPEN, INVESTIGATING, RESOLVED, ESCALATED
            $table->string('keputusan')->nullable(); // REFUND_PELANGGAN, RELEASE_TEKNISI, KUNJUNGAN_ULANG, FREEZE_AKUN
            $table->timestamp('sla_deadline')->nullable();
            $table->text('alasan');
            $table->text('resolusi')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispute');
    }
};
