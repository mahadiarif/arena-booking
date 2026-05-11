<?php

return [
    'booking_ref_prefix'    => env('BOOKING_REF_PREFIX', 'BK'),
    'slot_generation_days'  => env('SLOT_GENERATION_DAYS', 30),
    'waitlist_hold_minutes' => env('WAITLIST_HOLD_MINUTES', 30),
    'noshow_grace_minutes'  => env('NOSHOW_GRACE_MINUTES', 15),
    'invoice_logo_path'     => public_path('images/logo.png'),
    'currency_symbol'       => '৳',
    'timezone'              => 'Asia/Dhaka',
    'monitor_token'         => env('MONITOR_TOKEN', 'arenabook-monitor-2025'),
];
