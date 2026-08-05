<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Toko extends Model
{
    use HasFactory;

    protected $table = 'toko';

    protected $fillable = [
        'user_id',
        'nama',
        'izin_usaha',
        'alamat',
        'telepon',
        'status',
        'latitude',
        'longitude',
        'saldo',
    ];

    protected $casts = [
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
     * Get the spareparts owned by this shop.
     */
    public function spareparts(): HasMany
    {
        return $this->hasMany(Sparepart::class);
    }

    /**
     * Get the verifications associated with this shop.
     */
    public function pendaftaranVerifikasi(): HasMany
    {
        return $this->hasMany(PendaftaranVerifikasi::class);
    }

    /**
     * Get the violation logs of this shop.
     */
    public function pelanggaranLogs(): HasMany
    {
        return $this->hasMany(PelanggaranLog::class);
    }
}
