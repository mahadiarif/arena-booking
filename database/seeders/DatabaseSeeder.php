<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            DefaultScheduleSeeder::class,
            VenueSeeder::class,
            GalleryImageSeeder::class,
            CustomerSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
