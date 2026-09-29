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
        // 1. Pivot Table User Roles (Multi-Role Architecture)
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'role_id']);
            });
        }

        // 2. Additional Order Columns for Order-for-Others & Geoloc Formatted Address
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_for_other')) {
                $table->boolean('is_for_other')->default(false)->after('keluhan');
            }
            if (!Schema::hasColumn('orders', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('is_for_other');
            }
            if (!Schema::hasColumn('orders', 'recipient_phone')) {
                $table->string('recipient_phone')->nullable()->after('recipient_name');
            }
            if (!Schema::hasColumn('orders', 'recipient_address')) {
                $table->text('recipient_address')->nullable()->after('recipient_phone');
            }
            if (!Schema::hasColumn('orders', 'additional_notes')) {
                $table->text('additional_notes')->nullable()->after('recipient_address');
            }
            if (!Schema::hasColumn('orders', 'formatted_address')) {
                $table->text('formatted_address')->nullable()->after('additional_notes');
            }
        });

        // 3. Security Incident Logs Table
        if (!Schema::hasTable('security_incident_logs')) {
            Schema::create('security_incident_logs', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->index();
                $table->string('event_type')->index(); // FAILED_LOGIN, UNAUTHORIZED_ACCESS, SESSION_KICK, HONEYPOT
                $table->string('method', 10)->default('GET');
                $table->text('url')->nullable();
                $table->text('details')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 4. Blocked IPs Table
        if (!Schema::hasTable('blocked_ips')) {
            Schema::create('blocked_ips', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->unique();
                $table->text('reason')->nullable();
                $table->string('blocked_by')->nullable();
                $table->timestamps();
            });
        }

        // 5. Add attempts to email_otps table for brute-force protection
        if (Schema::hasTable('email_otps')) {
            Schema::table('email_otps', function (Blueprint $table) {
                if (!Schema::hasColumn('email_otps', 'attempts')) {
                    $table->integer('attempts')->default(0)->after('otp_code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('email_otps') && Schema::hasColumn('email_otps', 'attempts')) {
            Schema::table('email_otps', function (Blueprint $table) {
                $table->dropColumn('attempts');
            });
        }

        Schema::dropIfExists('blocked_ips');
        Schema::dropIfExists('security_incident_logs');

        Schema::table('orders', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('orders', 'is_for_other') ? 'is_for_other' : null,
                Schema::hasColumn('orders', 'recipient_name') ? 'recipient_name' : null,
                Schema::hasColumn('orders', 'recipient_phone') ? 'recipient_phone' : null,
                Schema::hasColumn('orders', 'recipient_address') ? 'recipient_address' : null,
                Schema::hasColumn('orders', 'additional_notes') ? 'additional_notes' : null,
                Schema::hasColumn('orders', 'formatted_address') ? 'formatted_address' : null,
            ]);
            if (!empty($columns)) {
                $table->dropColumn(array_values($columns));
            }
        });

        Schema::dropIfExists('user_roles');
    }
};
