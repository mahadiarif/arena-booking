<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingConfirmationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BookingCreated $event): void
    {
        app(NotificationService::class)->sendBookingConfirmation($event->booking);
    }
}
