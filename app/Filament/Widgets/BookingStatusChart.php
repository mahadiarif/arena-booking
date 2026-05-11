<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Booking Status Distribution';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = [
            'pending'   => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#fbbf24', // warning/pending
                        '#3b82f6', // primary/confirmed
                        '#10b981', // success/completed
                        '#ef4444', // danger/cancelled
                    ],
                ],
            ],
            'labels' => ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
