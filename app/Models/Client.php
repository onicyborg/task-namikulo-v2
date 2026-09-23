<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Propaganistas\LaravelPhone\PhoneNumber;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $table = 'client';
    protected $fillable = [
        'id',
        'customer',
        'handphone',
        'handphone_country',
        'jk',
        'asal',
        'user_id',
    ];

    public static function normalizeHandphone(?string $value, ?string $country = 'ID'): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '' || in_array($raw, ['0', '-'], true)) {
            return null;
        }

        try {
            $phone = new PhoneNumber($raw, strtoupper($country ?: 'ID'));
            if (!$phone->isValid()) {
                return null;
            }

            return $phone->formatE164();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public static function countryForHandphone(?string $value, ?string $defaultCountry = 'ID'): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '' || in_array($raw, ['0', '-'], true)) return null;

        try {
            $phone = new PhoneNumber($raw, strtoupper($defaultCountry ?: 'ID'));
            return $phone->isValid() ? $phone->getCountry() : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function setHandphoneAttribute($value): void
    {
        $country = $this->attributes['handphone_country'] ?? 'ID';
        $this->attributes['handphone'] = self::normalizeHandphone($value, $country);
    }
}
