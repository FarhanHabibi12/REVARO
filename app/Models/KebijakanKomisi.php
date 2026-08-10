<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KebijakanKomisi extends Model
{
    use HasFactory;

    protected $table = 'kebijakan_komisi';

    protected $fillable = [
        'persen_komisi_jasa',
        'persen_fee_sparepart',
    ];

    protected $casts = [
        'persen_komisi_jasa' => 'decimal:2',
        'persen_fee_sparepart' => 'decimal:2',
    ];
}
