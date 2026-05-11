<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCancellationNoticeListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(BookingCancelled $event): void
    {
        app(NotificationService::class)->sendCancellationNotice($event->booking);
    }
}
