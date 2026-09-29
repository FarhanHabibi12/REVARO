<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityIncidentLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ip_address',
        'event_type',
        'method',
        'url',
        'details',
        'user_id',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
