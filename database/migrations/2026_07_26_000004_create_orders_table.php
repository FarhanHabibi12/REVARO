<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->cascadeOnDelete();
            $table->foreignId('teknisi_id')->nullable()->constrained('teknisis')->nullOnDelete();
            $table->string('keluhan');
            $table->enum('status', [
                'searching',
                'dispatched',
                'on_the_way',
                'diagnosing',
                'in_progress',
                'completed',
                'disputed',
                'cancelled',
            ])->default('searching');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
