<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::query()->find($key)?->value ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function registrationsOpen(): bool
    {
        return static::get('registrations_open', '0') === '1';
    }

    /**
     * @return list<string>
     */
    public static function catalogSources(): array
    {
        $sources = json_decode(static::get('catalog_sources', '[]'), true);

        return is_array($sources) ? array_values(array_filter($sources, 'is_string')) : [];
    }

    public static function setCatalogSources(array $sources): void
    {
        static::set('catalog_sources', json_encode(array_values($sources)));
    }
}
