<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature   = 'bookings:send-reminders';
    protected $description = 'Send 24-hour reminder notifications for tomorrow\'s confirmed bookings';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::where('status', BookingStatus::Confirmed)
            ->whereHas('slot', fn ($q) => $q->where('date', $tomorrow))
            ->with(['customer', 'venue', 'slot'])
            ->get();

        $this->info("Found {$bookings->count()} confirmed booking(s) for tomorrow ({$tomorrow}).");

        if ($bookings->isEmpty()) {
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();

        $service = app(NotificationService::class);

        foreach ($bookings as $booking) {
            try {
                $service->sendReminder($booking);
            } catch (\Throwable $e) {
                $this->warn("Failed reminder for {$booking->booking_ref}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Reminders dispatched for {$bookings->count()} booking(s).");

        return self::SUCCESS;
    }
}
