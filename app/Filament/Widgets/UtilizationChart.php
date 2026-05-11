<?php

namespace App\Filament\Widgets;

use App\Models\Slot;
use App\Models\Venue;
use Filament\Widgets\ChartWidget;

class UtilizationChart extends ChartWidget
{
    protected static ?string $heading = 'Venue Utilization Rate (%)';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $venues = Venue::active()->get();
        $labels = $venues->pluck('name')->toArray();
        $rates = [];

        foreach ($venues as $venue) {
            $totalSlots = Slot::where('venue_id', $venue->id)
                ->where('date', '>=', now()->subDays(30))
                ->count();
            
            $bookedSlots = Slot::where('venue_id', $venue->id)
                ->where('date', '>=', now()->subDays(30))
                ->where('current_bookings', '>', 0)
                ->count();

            $rate = $totalSlots > 0 ? ($bookedSlots / $totalSlots) * 100 : 0;
            $rates[] = round($rate, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Utilization Rate (%)',
                    'data' => $rates,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => '#10b981',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'max' => 100,
                    'ticks' => [
                        'callback' => "function(value) { return value + '%'; }",
                    ],
                ],
            ],
        ];
    }
}
