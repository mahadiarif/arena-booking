<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect(range(11, 0))->mapWithKeys(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $monthName = $date->format('M Y');
            $revenue = Payment::whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
            
            return [$monthName => $revenue];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (৳)',
                    'data' => $data->values()->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => '#3b82f6',
                    'pointBackgroundColor' => '#3b82f6',
                    'pointBorderColor' => '#fff',
                    'pointRadius' => 5,
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'tension' => 0.4,
                ],
            ],
        ];
    }
}
