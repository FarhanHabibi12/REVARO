<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelanggaranLog extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_log';

    // Disable updated_at since we only need created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'teknisi_id',
        'toko_id',
        'dispute_id',
        'jenis_pelanggaran',
        'tingkat_penalti',
    ];

    /**
     * Get the technician linked to this log.
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Teknisi::class);
    }

    /**
     * Get the shop linked to this log.
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    /**
     * Get the dispute that triggered this log.
     */
    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }
}
