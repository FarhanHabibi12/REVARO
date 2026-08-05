<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'sparepart';

    protected $fillable = [
        'toko_id',
        'nama',
        'harga',
        'stok',
        'merk',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Get the shop that owns this spare part.
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    /**
     * Get the orders that requested this spare part.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_sparepart')
            ->withPivot('qty', 'harga', 'subtotal')
            ->withTimestamps();
    }
}
