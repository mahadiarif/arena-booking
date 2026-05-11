<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Utilization extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $slug = 'reports/utilization';

    protected static string $view = 'filament.pages.utilization';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\UtilizationChart::class,
        ];
    }
}
