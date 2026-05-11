<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Slot;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayBookings   = Booking::whereDate('created_at', today())->count();
        $monthlyRevenue  = Payment::whereYear('paid_at', now()->year)
                                   ->whereMonth('paid_at', now()->month)
                                   ->sum('amount');
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalCustomers  = Customer::count();
        $availableSlots  = Slot::whereDate('date', today())->where('status', 'available')->count();
        $totalDue        = Booking::whereNotIn('status', ['cancelled', 'no_show', 'completed'])
                                   ->sum(\Illuminate\Support\Facades\DB::raw('GREATEST(total_amount - paid_amount, 0)'));

        // 7-day revenue chart data
        $revenueChart = collect(range(6, 0))->map(
            fn($d) => (float) Payment::whereDate('paid_at', now()->subDays($d))->sum('amount')
        )->values()->toArray();

        return [
            Stat::make("Today's Bookings", $todayBookings)
                ->description($availableSlots . ' slots still available')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Monthly Revenue', '৳ ' . number_format($monthlyRevenue, 0))
                ->description('This month collected')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($revenueChart)
                ->color('success'),

            Stat::make('Pending Bookings', $pendingBookings)
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingBookings > 0 ? 'warning' : 'success'),

            Stat::make('Outstanding Due', '৳ ' . number_format($totalDue, 0))
                ->description('Across active bookings')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalDue > 0 ? 'danger' : 'success'),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
