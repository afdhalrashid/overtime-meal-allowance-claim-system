<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($setting->value) ? (float) $setting->value : $default,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value, string $type = 'string', string $description = null): void
    {
        $formattedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $formattedValue,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Format amount with currency symbol.
     */
    public static function formatCurrency($amount, $decimals = 2): string
    {
        $symbol = static::get('currency_symbol', 'RM');
        $position = static::get('currency_position', 'before');
        $formattedAmount = number_format($amount, $decimals);

        return $position === 'before'
            ? $symbol . ' ' . $formattedAmount
            : $formattedAmount . ' ' . $symbol;
    }
}
