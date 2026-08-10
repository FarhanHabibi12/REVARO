<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaServis extends Model
{
    use HasFactory;

    protected $table = 'jasa_servis';

    protected $fillable = [
        'nama_jasa',
        'kategori',
        'harga_baseline',
        'garansi_hari',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'harga_baseline' => 'decimal:2',
        'garansi_hari' => 'integer',
        'is_active' => 'boolean',
    ];
}
