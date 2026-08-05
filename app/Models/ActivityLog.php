<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    // Disable updated_at since we only need created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'aktivitas',
        'ip_address',
    ];

    /**
     * Get the user linked to this log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
