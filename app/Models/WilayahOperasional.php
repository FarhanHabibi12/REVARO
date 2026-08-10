<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahOperasional extends Model
{
    use HasFactory;

    protected $table = 'wilayah_operasional';

    protected $fillable = [
        'nama_wilayah',
        'kode_pos_prefix',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
