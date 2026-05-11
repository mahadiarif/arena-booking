<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    // ── Typed Value Accessor ──────────────────────────────────────────────────

    /**
     * Get the setting value cast to its declared type.
     */
    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => in_array($this->value, ['true', '1', true], true),
            'integer' => (int) $this->value,
            'float'   => (float) $this->value,
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }

    // ── Static Helpers ────────────────────────────────────────────────────────

    /**
     * Get a setting value by key with optional default.
     * Results are cached for performance.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return static::all()->keyBy('key');
        });

        $setting = $settings->get($key);

        if (! $setting) {
            return $default;
        }

        return $setting->typed_value ?? $default;
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget('app_settings');
    }
}
