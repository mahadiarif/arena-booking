<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentReceiptListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(PaymentReceived $event): void
    {
        app(NotificationService::class)->sendPaymentReceipt($event->payment);
    }
}
