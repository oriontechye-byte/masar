<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';
    protected $fillable = ['key', 'value'];
    public $timestamps = true;

    public static function get(string $key, $default = null)
    {
        $row = static::query()->where('key', $key)->first();
        return $row?->value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }
}
