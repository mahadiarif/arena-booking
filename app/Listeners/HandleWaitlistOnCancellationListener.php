<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Services\WaitlistService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleWaitlistOnCancellationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'default';

    public function handle(BookingCancelled $event): void
    {
        app(WaitlistService::class)->notifyNext($event->booking->slot);
    }
}
