<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Slot;
use App\Models\Venue;
use App\Services\CalendarService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService,
        protected ReportService   $reportService,
    ) {}

    public function index(Request $request): View
    {
        // ── KPI Cards ─────────────────────────────────────────────────────────
        $todaysBookingsCount = Booking::whereDate('created_at', today())->count();

        $monthlyRevenue = Payment::whereYear('paid_at', now()->year)
                                 ->whereMonth('paid_at', now()->month)
                                 ->sum('amount');

        $lastMonthRevenue = Payment::whereYear('paid_at', now()->subMonth()->year)
                                   ->whereMonth('paid_at', now()->subMonth()->month)
                                   ->sum('amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Total outstanding due across active bookings
        $totalDue = Booking::whereNotIn('status', ['cancelled', 'no_show', 'completed'])
                           ->sum(DB::raw('GREATEST(total_amount - paid_amount, 0)'));

        $activeCustomersCount = Customer::count();

        // New customers this month
        $newCustomersThisMonth = Customer::whereYear('created_at', now()->year)
                                         ->whereMonth('created_at', now()->month)
                                         ->count();

        // ── Slot Grid (venue-filtered) ─────────────────────────────────────────
        $venueId = $request->filled('venue_id')
            ? (int) $request->venue_id
            : Venue::active()->orderBy('sort_order')->value('id');

        // Today's slot stats (filtered by venue if selected, otherwise total)
        $slotsQuery = Slot::whereDate('date', today())->where('venue_id', $venueId);

        $todaySlotsTotal     = (clone $slotsQuery)->count();
        $todaySlotsAvailable = (clone $slotsQuery)->where('status', 'available')->count();
        $todaySlotsBooked    = (clone $slotsQuery)->where('status', 'booked')->count();

        $dailyView = $this->calendarService->getDailyView(now(), $venueId);

        // ── Venue Utilization (this month) ────────────────────────────────────
        $venueUtilization = $this->reportService->getUtilizationReport(
            now()->startOfMonth(),
            now()->endOfMonth()
        );

        // ── Recent Bookings (last 10) ──────────────────────────────────────────
        $recentBookings = Booking::with(['customer', 'venue', 'slot'])
                                 ->latest()
                                 ->take(10)
                                 ->get();

        // ── Revenue Chart Data (last 7 days) ──────────────────────────────────
        $revenueChartData = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label'  => $date->format('D'),
                'amount' => Payment::whereDate('paid_at', $date)->sum('amount'),
            ];
        });

        return view('admin.dashboard', compact(
            'todaysBookingsCount',
            'monthlyRevenue',
            'lastMonthRevenue',
            'revenueGrowth',
            'totalDue',
            'activeCustomersCount',
            'newCustomersThisMonth',
            'todaySlotsTotal',
            'todaySlotsAvailable',
            'todaySlotsBooked',
            'venueUtilization',
            'dailyView',
            'recentBookings',
            'venueId',
            'revenueChartData',
        ));
    }
}
