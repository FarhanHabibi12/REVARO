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
        // 1. Create OTPs table for email verification
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp_code', 6);
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // 2. Add phone number to users (if not exists) and remove email_verified_at from users if we strictly use OTP before creation, but since user is created we can just use email_verified_at.
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telp')->nullable()->after('email');
        });

        // 3. Add pemesan custom info to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pemesan_nama')->nullable()->after('keluhan');
            $table->string('pemesan_hp')->nullable()->after('pemesan_nama');
            $table->string('pemesan_alamat')->nullable()->after('pemesan_hp');
            $table->decimal('lat', 10, 8)->nullable()->after('alamat_servis');
            $table->decimal('lng', 11, 8)->nullable()->after('lat');
        });

        // 4. Create withdrawals table for detailed bank withdrawals
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('PENDING'); // PENDING, COMPLETED, REJECTED
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pemesan_nama', 'pemesan_hp', 'pemesan_alamat', 'lat', 'lng']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('no_telp');
        });

        Schema::dropIfExists('email_otps');
    }
};
