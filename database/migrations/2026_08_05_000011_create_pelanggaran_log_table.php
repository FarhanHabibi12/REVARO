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
        Schema::create('pelanggaran_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teknisi_id')->nullable()->constrained('teknisi')->onDelete('cascade');
            $table->foreignId('toko_id')->nullable()->constrained('toko')->onDelete('cascade');
            $table->foreignId('dispute_id')->nullable()->constrained('dispute')->onDelete('cascade');
            $table->string('jenis_pelanggaran');
            $table->string('tingkat_penalti'); // WARNING, TIER_DOWN, SUSPENSION, PERMANENT_FREEZE
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_log');
    }
};
