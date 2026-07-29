<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['pelanggan_id', 'teknisi_id', 'keluhan', 'status'];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Teknisi::class);
    }

    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(Sparepart::class, 'order_sparepart')
            ->withPivot('qty', 'status_persetujuan')
            ->withTimestamps();
    }

    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }
}
