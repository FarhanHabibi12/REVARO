<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_verifikasi';

    protected $fillable = [
        'teknisi_id',
        'toko_id',
        'admin_id',
        'status',
        'catatan',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the technician profile being verified.
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Teknisi::class);
    }

    /**
     * Get the shop profile being verified.
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    /**
     * Get the administrator who handled this verification.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
