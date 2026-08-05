<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    // Map fixed slugs to display names/roles stored in database
    public static $slugMap = [
        'pelanggan' => 'Pelanggan',
        'teknisi' => 'Teknisi',
        'toko-sparepart' => 'Toko Sparepart',
        'admin-verifikator' => 'Admin Verifikator',
        'admin-resolusi' => 'Admin Resolusi',
        'super-admin-pemilik' => 'Super Admin (Pemilik Sistem)',
        'super-admin-monitoring' => 'Super Admin (Monitoring Kinerja)',
    ];

    /**
     * Get the users associated with this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
