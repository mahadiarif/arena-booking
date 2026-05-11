<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingConfirmedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BookingConfirmed $event): void
    {
        app(NotificationService::class)->sendBookingConfirmation($event->booking);
    }
}
