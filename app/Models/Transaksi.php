<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'order_id',
        'biaya_jasa',
        'biaya_part',
        'total',
        'metode_pembayaran',
        'status_pembayaran',
        'status_escrow',
        'paid_at',
    ];

    protected $casts = [
        'biaya_jasa' => 'decimal:2',
        'biaya_part' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order that owns this transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
