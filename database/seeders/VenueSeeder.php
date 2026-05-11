<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Venue;
use App\Services\SlotGeneratorService;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $schedule = Schedule::where('name', 'Standard Schedule')->first();

        if (! $schedule) {
            $this->command->warn('Standard Schedule not found — run DefaultScheduleSeeder first.');
            return;
        }

        $venues = [
            [
                'name'                 => 'Main Stadium',
                'slug'                 => 'main-stadium',
                'type'                 => 'stadium',
                'capacity'             => 500,
                'color'                => '#185FA5',
                'hourly_rate'          => 5000,
                'min_duration_minutes' => 120,
                'max_duration_minutes' => 480,
                'requires_approval'    => false,
                'is_active'            => true,
                'sort_order'           => 1,
            ],
            [
                'name'                 => 'Indoor Turf A',
                'slug'                 => 'indoor-turf-a',
                'type'                 => 'turf_indoor',
                'capacity'             => 20,
                'color'                => '#3B6D11',
                'hourly_rate'          => 2500,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 240,
                'requires_approval'    => false,
                'is_active'            => true,
                'sort_order'           => 2,
            ],
            [
                'name'                 => 'Outdoor Turf B',
                'slug'                 => 'outdoor-turf-b',
                'type'                 => 'turf_outdoor',
                'capacity'             => 22,
                'color'                => '#534AB7',
                'hourly_rate'          => 2000,
                'min_duration_minutes' => 60,
                'max_duration_minutes' => 240,
                'requires_approval'    => false,
                'is_active'            => true,
                'sort_order'           => 3,
            ],
            [
                'name'                 => 'VIP Box',
                'slug'                 => 'vip-box',
                'type'                 => 'vip_box',
                'capacity'             => 50,
                'color'                => '#854F0B',
                'hourly_rate'          => 8000,
                'min_duration_minutes' => 120,
                'max_duration_minutes' => 720,
                'requires_approval'    => true,
                'is_active'            => true,
                'sort_order'           => 4,
            ],
        ];

        foreach ($venues as $data) {
            Venue::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['schedule_id' => $schedule->id])
            );
        }

        $this->command->info('Generating slots for 60 days...');
        app(SlotGeneratorService::class)->generateForDateRange(now(), now()->addDays(60));
        $this->command->info('Slots generated.');
    }
}
