<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Venue;
use App\Policies\BookingPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ReportPolicy;
use App\Policies\VenuePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class  => BookingPolicy::class,
        Venue::class    => VenuePolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Register Report policy via Gate since there is no model
        \Illuminate\Support\Facades\Gate::policy(
            \App\Services\ReportService::class,
            ReportPolicy::class
        );
    }
}
