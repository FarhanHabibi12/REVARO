<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'nama',
        'email',
        'google_id',
        'password',
        'foto',
        'avatar',
        'email_verified_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'admin_username',
        'must_change_password',
    ];

    /**
     * Get admin username dynamically from system_settings without migrations.
     */
    public function getAdminUsernameAttribute(): ?string
    {
        return \App\Models\SystemSetting::get('admin_username_' . $this->id);
    }

    /**
     * Get must_change_password flag dynamically from system_settings.
     */
    public function getMustChangePasswordAttribute(): bool
    {
        return \App\Models\SystemSetting::get('admin_must_change_pwd_' . $this->id, '0') === '1';
    }

    /**
     * Set admin username and register reverse lookup.
     */
    public function setAdminUsername(string $username): void
    {
        $normalized = strtolower(trim($username));
        // Remove old lookup if existing
        $old = $this->admin_username;
        if ($old && $old !== $normalized) {
            \App\Models\SystemSetting::where('key', 'admin_user_lookup_' . $old)->delete();
        }

        \App\Models\SystemSetting::set('admin_username_' . $this->id, $normalized, 'admin_meta');
        \App\Models\SystemSetting::set('admin_user_lookup_' . $normalized, (string)$this->id, 'admin_meta');
    }

    /**
     * Set must_change_password flag.
     */
    public function setMustChangePassword(bool $state): void
    {
        \App\Models\SystemSetting::set('admin_must_change_pwd_' . $this->id, $state ? '1' : '0', 'admin_meta');
    }

    /**
     * Find admin by username or email.
     */
    public static function findAdminByIdentifier(string $identifier): ?self
    {
        $clean = trim($identifier);
        if (str_contains($clean, '@')) {
            return self::where('email', $clean)->first();
        }

        $userId = \App\Models\SystemSetting::get('admin_user_lookup_' . strtolower($clean));
        if ($userId) {
            return self::find($userId);
        }

        return self::where('email', $clean)->first();
    }

    /**
     * Get the role of this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get all roles associated with this user (Multi-Role Support).
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Get the Pelanggan profile of the user.
     */
    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class);
    }

    /**
     * Get the Teknisi profile of the user.
     */
    public function teknisi(): HasOne
    {
        return $this->hasOne(Teknisi::class);
    }

    /**
     * Get the Toko profile of the user.
     */
    public function toko(): HasOne
    {
        return $this->hasOne(Toko::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Role Helper Methods
    public function isPelanggan(): bool
    {
        return $this->pelanggan !== null;
    }

    public function isTeknisi(): bool
    {
        return $this->teknisi !== null;
    }

    public function isToko(): bool
    {
        return $this->toko !== null;
    }

    public function isAdminVerifikator(): bool
    {
        return $this->role?->nama === 'Admin Verifikator';
    }

    public function isAdminResolusi(): bool
    {
        return $this->role?->nama === 'Admin Resolusi';
    }

    public function isSuperAdminPemilik(): bool
    {
        return $this->role?->nama === 'Super Admin (Pemilik Sistem)';
    }

    public function isSuperAdminMonitoring(): bool
    {
        return $this->role?->nama === 'Super Admin (Monitoring Kinerja)';
    }

    public function isAdminSuper(): bool
    {
        return $this->isSuperAdminPemilik() || $this->isSuperAdminMonitoring();
    }

    /**
     * Get unified wallet balance for the user across all roles.
     */
    public function getUnifiedSaldo(): float
    {
        $pelSaldo = $this->pelanggan ? (float) $this->pelanggan->saldo : 0.00;
        $tekSaldo = $this->teknisi ? (float) $this->teknisi->saldo : 0.00;
        $tokSaldo = $this->toko ? (float) $this->toko->saldo : 0.00;

        // If any profile has balance, take the active/highest balance
        $maxSaldo = max($pelSaldo, $tekSaldo, $tokSaldo);
        if ($maxSaldo > 0) {
            // Keep all existing profiles synchronized to this unified balance
            if ($this->pelanggan && (float)$this->pelanggan->saldo !== $maxSaldo) {
                $this->pelanggan->saldo = $maxSaldo;
                $this->pelanggan->save();
            }
            if ($this->teknisi && (float)$this->teknisi->saldo !== $maxSaldo) {
                $this->teknisi->saldo = $maxSaldo;
                $this->teknisi->save();
            }
            if ($this->toko && (float)$this->toko->saldo !== $maxSaldo) {
                $this->toko->saldo = $maxSaldo;
                $this->toko->save();
            }
            return $maxSaldo;
        }

        return 0.00;
    }

    /**
     * Add saldo to the unified user wallet and sync across all profiles of the user.
     */
    /**
     * Add saldo to the unified user wallet and sync across all profiles of the user with pessimistic locking.
     */
    public function addUnifiedSaldo(float $amount): float
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount) {
            $pel = $this->pelanggan()->lockForUpdate()->first();
            $tek = $this->teknisi()->lockForUpdate()->first();
            $tok = $this->toko()->lockForUpdate()->first();

            $current = max(
                $pel ? (float)$pel->saldo : 0.0,
                $tek ? (float)$tek->saldo : 0.0,
                $tok ? (float)$tok->saldo : 0.0
            );

            $newSaldo = $current + $amount;

            if ($pel) {
                $pel->saldo = $newSaldo;
                $pel->save();
            }
            if ($tek) {
                $tek->saldo = $newSaldo;
                $tek->save();
            }
            if ($tok) {
                $tok->saldo = $newSaldo;
                $tok->save();
            }

            if (!$pel && !$tek && !$tok) {
                Pelanggan::create([
                    'user_id' => $this->id,
                    'nama' => $this->nama,
                    'email' => $this->email,
                    'hp' => $this->no_telp ?? '081234567890',
                    'alamat' => 'Alamat Pengguna',
                    'saldo' => $newSaldo,
                ]);
            }

            return $newSaldo;
        });
    }

    /**
     * Deduct saldo from the unified user wallet and sync across all profiles with pessimistic locking.
     */
    public function deductUnifiedSaldo(float $amount): float
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount) {
            $pel = $this->pelanggan()->lockForUpdate()->first();
            $tek = $this->teknisi()->lockForUpdate()->first();
            $tok = $this->toko()->lockForUpdate()->first();

            $current = max(
                $pel ? (float)$pel->saldo : 0.0,
                $tek ? (float)$tek->saldo : 0.0,
                $tok ? (float)$tok->saldo : 0.0
            );

            if ($current < $amount) {
                throw new \InvalidArgumentException("Saldo dompet tidak mencukupi untuk melakukan transaksi.");
            }

            $newSaldo = $current - $amount;

            if ($pel) {
                $pel->saldo = $newSaldo;
                $pel->save();
            }
            if ($tek) {
                $tek->saldo = $newSaldo;
                $tek->save();
            }
            if ($tok) {
                $tok->saldo = $newSaldo;
                $tok->save();
            }

            return $newSaldo;
        });
    }
}
