<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispute extends Model
{
    use HasFactory;

    protected $table = 'dispute';

    protected $fillable = [
        'order_id',
        'admin_id',
        'status',
        'keputusan',
        'sla_deadline',
        'alasan',
        'resolusi',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'sla_deadline' => 'datetime',
    ];

    /**
     * Get the order that this dispute belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the administrator who handled this dispute.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the violation logs generated from this dispute.
     */
    public function pelanggaranLogs(): HasMany
    {
        return $this->hasMany(PelanggaranLog::class);
    }
}
