<?php

namespace App\Providers;

use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Events\BookingCreated;
use App\Events\PaymentReceived;
use App\Listeners\HandleWaitlistOnCancellationListener;
use App\Listeners\SendBookingConfirmedListener;
use App\Listeners\SendBookingConfirmationListener;
use App\Listeners\SendCancellationNoticeListener;
use App\Listeners\SendPaymentReceiptListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            SendBookingConfirmationListener::class,
        ],

        BookingConfirmed::class => [
            SendBookingConfirmedListener::class,
        ],

        BookingCancelled::class => [
            SendCancellationNoticeListener::class,
            HandleWaitlistOnCancellationListener::class,
        ],

        PaymentReceived::class => [
            SendPaymentReceiptListener::class,
        ],
    ];

    public function boot(): void {}

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
