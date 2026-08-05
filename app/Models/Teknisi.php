<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teknisi extends Model
{
    use HasFactory;

    protected $table = 'teknisi';

    protected $fillable = [
        'user_id',
        'nama',
        'kategori',
        'tier',
        'hp',
        'rating',
        'tersedia',
        'status',
        'latitude',
        'longitude',
        'saldo',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'tersedia' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'saldo' => 'decimal:2',
    ];

    /**
     * Get the user that owns this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders associated with this teknisi.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the verifications associated with this teknisi.
     */
    public function pendaftaranVerifikasi(): HasMany
    {
        return $this->hasMany(PendaftaranVerifikasi::class);
    }

    /**
     * Get the violation logs of this teknisi.
     */
    public function pelanggaranLogs(): HasMany
    {
        return $this->hasMany(PelanggaranLog::class);
    }
}
