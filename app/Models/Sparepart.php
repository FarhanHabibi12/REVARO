<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = ['toko_id', 'nama', 'harga'];

    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_sparepart')
            ->withPivot('qty', 'status_persetujuan')
            ->withTimestamps();
    }
}
