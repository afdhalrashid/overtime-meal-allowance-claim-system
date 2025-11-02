<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PublicHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if a given date is a public holiday.
     */
    public static function isPublicHoliday(Carbon $date): bool
    {
        return static::where('date', $date->toDateString())
                    ->where('is_active', true)
                    ->exists();
    }

    /**
     * Get public holidays for a given year.
     */
    public static function getHolidaysForYear(int $year)
    {
        return static::whereYear('date', $year)
                    ->where('is_active', true)
                    ->orderBy('date')
                    ->get();
    }
}
