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
        Schema::create('active_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nama')->default('Pengunjung Umum (Guest)');
            $table->string('role')->default('GUEST VISITOR');
            $table->string('endpoint')->default('/');
            $table->text('user_agent')->nullable();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_visitors');
    }
};
