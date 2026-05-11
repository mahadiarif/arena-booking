<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;

class MarkNoShows extends Command
{
    protected $signature   = 'bookings:mark-noshows {--grace=15 : Grace period in minutes after slot ends before marking no-show}';
    protected $description = 'Mark confirmed bookings as no-show if customer did not check in after the slot ended';

    public function handle(): int
    {
        $grace  = (int) $this->option('grace');
        $cutoff = now()->subMinutes($grace);

        $this->info("Scanning for no-shows (grace: {$grace} min, cutoff: {$cutoff->format('Y-m-d H:i')})...");

        $bookings = Booking::where('status', BookingStatus::Confirmed)
            ->whereNull('check_in_at')
            ->whereHas('slot', fn ($q) => $q->whereRaw(
                "CONCAT(date, ' ', end_time) < ?",
                [$cutoff->toDateTimeString()]
            ))
            ->with('slot')
            ->get();

        $count = $bookings->count();

        if ($count === 0) {
            $this->info('No bookings to mark as no-show.');
            return self::SUCCESS;
        }

        $service = app(BookingService::class);

        foreach ($bookings as $booking) {
            try {
                $service->markNoShow($booking);
            } catch (\Throwable $e) {
                $this->warn("Could not mark {$booking->booking_ref} as no-show: {$e->getMessage()}");
            }
        }

        $this->info("✓ {$count} booking(s) marked as no-show.");

        return self::SUCCESS;
    }
}
