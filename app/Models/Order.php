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

    protected $table = 'orders';

    protected $fillable = [
        'pelanggan_id',
        'teknisi_id',
        'kode_order',
        'status',
        'keluhan',
        'kategori',
        'alamat_servis',
        'tanggal_servis',
        'latitude',
        'longitude',
        'is_premium',
        'biaya_prioritas',
        'is_for_other',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'additional_notes',
        'formatted_address',
        'pemesan_nama',
        'pemesan_hp',
        'pemesan_alamat',
    ];

    protected $appends = [
        'kategori',
    ];

    protected $casts = [
        'tanggal_servis' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get order service category with fallback to SystemSetting if column is not yet populated.
     */
    public function getKategoriAttribute($val): ?string
    {
        if (!empty($val)) {
            return $val;
        }
        return \App\Models\SystemSetting::get('order_kategori_' . $this->id);
    }

    /**
     * Get the customer (pelanggan) who made the order.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the technician (teknisi) assigned to this order.
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Teknisi::class);
    }

    /**
     * Get the spare parts requested for this order.
     */
    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(Sparepart::class, 'order_sparepart')
            ->withPivot('qty', 'harga', 'subtotal', 'status')
            ->withTimestamps();
    }

    /**
     * Get the transaction details associated with this order.
     */
    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class);
    }

    /**
     * Get the dispute associated with this order.
     */
    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    /**
     * Get the rating given for this order.
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }
}
