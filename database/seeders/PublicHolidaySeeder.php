<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PublicHoliday;
use Carbon\Carbon;

class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            // 2024 Malaysian Public Holidays
            [
                'name' => 'New Year\'s Day',
                'date' => '2024-01-01',
                'description' => 'First day of the calendar year',
                'is_active' => true,
            ],
            [
                'name' => 'Chinese New Year',
                'date' => '2024-02-10',
                'description' => 'First day of Chinese New Year',
                'is_active' => true,
            ],
            [
                'name' => 'Chinese New Year (2nd Day)',
                'date' => '2024-02-11',
                'description' => 'Second day of Chinese New Year',
                'is_active' => true,
            ],
            [
                'name' => 'Good Friday',
                'date' => '2024-03-29',
                'description' => 'Christian holiday commemorating the crucifixion of Jesus',
                'is_active' => true,
            ],
            [
                'name' => 'Labour Day',
                'date' => '2024-05-01',
                'description' => 'International Workers\' Day',
                'is_active' => true,
            ],
            [
                'name' => 'Wesak Day',
                'date' => '2024-05-22',
                'description' => 'Buddhist holiday celebrating the birth of Buddha',
                'is_active' => true,
            ],
            [
                'name' => 'Hari Raya Aidilfitri',
                'date' => '2024-04-10',
                'description' => 'First day of Eid al-Fitr',
                'is_active' => true,
            ],
            [
                'name' => 'Hari Raya Aidilfitri (2nd Day)',
                'date' => '2024-04-11',
                'description' => 'Second day of Eid al-Fitr',
                'is_active' => true,
            ],
            [
                'name' => 'Malaysia Day',
                'date' => '2024-09-16',
                'description' => 'Commemorates the formation of Malaysia',
                'is_active' => true,
            ],
            [
                'name' => 'Hari Raya Aidiladha',
                'date' => '2024-06-17',
                'description' => 'Festival of Sacrifice',
                'is_active' => true,
            ],
            [
                'name' => 'Merdeka Day',
                'date' => '2024-08-31',
                'description' => 'Malaysia Independence Day',
                'is_active' => true,
            ],
            [
                'name' => 'Deepavali',
                'date' => '2024-10-31',
                'description' => 'Hindu festival of lights',
                'is_active' => true,
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2024-12-25',
                'description' => 'Christian holiday celebrating the birth of Jesus',
                'is_active' => true,
            ],

            // 2025 Malaysian Public Holidays
            [
                'name' => 'New Year\'s Day',
                'date' => '2025-01-01',
                'description' => 'First day of the calendar year',
                'is_active' => true,
            ],
            [
                'name' => 'Chinese New Year',
                'date' => '2025-01-29',
                'description' => 'First day of Chinese New Year',
                'is_active' => true,
            ],
            [
                'name' => 'Chinese New Year (2nd Day)',
                'date' => '2025-01-30',
                'description' => 'Second day of Chinese New Year',
                'is_active' => true,
            ],
            [
                'name' => 'Good Friday',
                'date' => '2025-04-18',
                'description' => 'Christian holiday commemorating the crucifixion of Jesus',
                'is_active' => true,
            ],
            [
                'name' => 'Labour Day',
                'date' => '2025-05-01',
                'description' => 'International Workers\' Day',
                'is_active' => true,
            ],
            [
                'name' => 'Wesak Day',
                'date' => '2025-05-12',
                'description' => 'Buddhist holiday celebrating the birth of Buddha',
                'is_active' => true,
            ],
            [
                'name' => 'Hari Raya Aidilfitri',
                'date' => '2025-03-31',
                'description' => 'First day of Eid al-Fitr (tentative)',
                'is_active' => true,
            ],
            [
                'name' => 'Hari Raya Aidilfitri (2nd Day)',
                'date' => '2025-04-01',
                'description' => 'Second day of Eid al-Fitr (tentative)',
                'is_active' => true,
            ],
            [
                'name' => 'Hari Raya Aidiladha',
                'date' => '2025-06-07',
                'description' => 'Festival of Sacrifice (tentative)',
                'is_active' => true,
            ],
            [
                'name' => 'Merdeka Day',
                'date' => '2025-08-31',
                'description' => 'Malaysia Independence Day',
                'is_active' => true,
            ],
            [
                'name' => 'Malaysia Day',
                'date' => '2025-09-16',
                'description' => 'Commemorates the formation of Malaysia',
                'is_active' => true,
            ],
            [
                'name' => 'Deepavali',
                'date' => '2025-10-20',
                'description' => 'Hindu festival of lights (tentative)',
                'is_active' => true,
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2025-12-25',
                'description' => 'Christian holiday celebrating the birth of Jesus',
                'is_active' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            PublicHoliday::create([
                'name' => $holiday['name'],
                'date' => Carbon::parse($holiday['date']),
                'description' => $holiday['description'],
                'is_active' => $holiday['is_active'],
            ]);
        }
    }
}
