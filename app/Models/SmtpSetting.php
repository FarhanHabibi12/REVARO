<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SmtpSetting extends Model
{
    use HasFactory;

    protected $table = 'smtp_settings';

    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_active',
    ];

    protected $casts = [
        'mail_port' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Mutator: Enkripsi password sebelum disimpan ke database untuk keamanan maksimal.
     */
    public function setMailPasswordAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['mail_password'] = Crypt::encryptString($value);
        } else {
            $this->attributes['mail_password'] = null;
        }
    }

    /**
     * Accessor: Dekripsi password saat dibaca untuk konfigurasi mailer.
     */
    public function getMailPasswordAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Fallback jika nilainya berupa plain-text lama
            return $value;
        }
    }
}
