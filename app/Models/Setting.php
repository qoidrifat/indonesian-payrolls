<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("settings.$key", 600, function () use ($key, $default) {
            $row = static::query()->where('key', $key)->first();
            if (! $row) {
                return $default;
            }

            return match ($row->type) {
                'int' => (int) $row->value,
                'float' => (float) $row->value,
                'bool' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($row->value ?? 'null', true),
                default => $row->value,
            };
        });
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $stored = match ($type) {
            'json' => json_encode($value),
            'bool' => $value ? '1' : '0',
            default => (string) $value,
        };
        static::updateOrCreate(['key' => $key], ['value' => $stored, 'type' => $type, 'group' => $group]);
        Cache::forget("settings.$key");
    }
}
