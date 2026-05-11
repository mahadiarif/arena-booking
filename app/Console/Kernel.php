<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Generate 7 days of slots daily at midnight
        $schedule->command('slots:generate --days=7')
                 ->dailyAt('00:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Send 24-hour reminders at 9 AM
        $schedule->command('bookings:send-reminders')
                 ->dailyAt('09:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Auto-mark no-shows every 30 minutes with 15-min grace
        $schedule->command('bookings:mark-noshows --grace=15')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Expire waitlist holds every 15 minutes
        $schedule->command('waitlist:expire-entries')
                 ->everyFifteenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
