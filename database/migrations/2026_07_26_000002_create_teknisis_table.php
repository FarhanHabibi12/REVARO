<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teknisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori'); // AC, kulkas, mesin cuci, dll.
            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->enum('status', ['pending_review', 'active', 'suspended', 'frozen'])->default('pending_review');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teknisis');
    }
};
