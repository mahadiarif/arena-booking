<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class DefaultScheduleSeeder extends Seeder
{
    public function run(): void
    {
        Schedule::firstOrCreate(
            ['name' => 'Standard Schedule'],
            [
                'timezone'              => 'Asia/Dhaka',
                'start_time'            => '06:00:00',
                'end_time'              => '22:00:00',
                'slot_interval_minutes' => 120,
                'allowed_days'          => [0, 1, 2, 3, 4, 5, 6],
                'allow_concurrent'      => false,
                'max_concurrent'        => 1,
                'is_active'             => true,
            ]
        );
    }
}
