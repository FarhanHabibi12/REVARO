<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teknisi extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kategori', 'tier', 'status'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
