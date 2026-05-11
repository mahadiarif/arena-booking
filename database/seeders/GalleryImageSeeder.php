<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            ['path' => 'images/gallery/play_moment_1.png', 'title' => 'Match Night', 'description' => 'Players competing under bright arena lights.'],
            ['path' => 'images/gallery/play_moment_2.png', 'title' => 'Team Warmup', 'description' => 'A focused session before the first whistle.'],
            ['path' => 'images/gallery/play_moment_3.png', 'title' => 'Cricket Session', 'description' => 'Fast-paced practice on a prepared indoor surface.'],
            ['path' => 'images/gallery/play_moment_4.png', 'title' => 'Weekend Football', 'description' => 'Friends gathering for a high-energy match.'],
            ['path' => 'images/gallery/play_moment_5.png', 'title' => 'Arena Focus', 'description' => 'A clean venue ready for the next booking.'],
            ['path' => 'images/gallery/play_moment_6.png', 'title' => 'Winning Moment', 'description' => 'The celebration after a close finish.'],
        ];

        foreach ($images as $index => $image) {
            GalleryImage::updateOrCreate(
                ['path' => $image['path']],
                [
                    'title' => $image['title'],
                    'description' => $image['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
