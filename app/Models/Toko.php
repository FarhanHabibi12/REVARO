<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Toko extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'izin_usaha', 'status'];

    public function spareparts(): HasMany
    {
        return $this->hasMany(Sparepart::class);
    }
}
