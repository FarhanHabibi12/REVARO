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
        'password',
        'foto',
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
}
