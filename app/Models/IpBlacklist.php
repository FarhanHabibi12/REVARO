<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpBlacklist extends Model
{
    use HasFactory;

    protected $table = 'ip_blacklist';

    protected $fillable = [
        'ip_address',
        'alasan',
        'banned_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
    ];
}
