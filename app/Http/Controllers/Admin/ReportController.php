<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function index(): View
    {
        $summary = $this->reportService->getDailySummary(now());

        return view('admin.reports.index', compact('summary'));
    }

    public function utilization(Request $request): View
    {
        $from   = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to     = $request->filled('to')   ? Carbon::parse($request->to)   : now()->endOfMonth();
        $data   = $this->reportService->getUtilizationReport($from, $to, $request->venue_id);
        $venues = Venue::active()->get();

        return view('admin.reports.utilization', compact('data', 'venues', 'from', 'to'));
    }

    public function revenue(Request $request): View
    {
        $from   = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to     = $request->filled('to')   ? Carbon::parse($request->to)   : now()->endOfMonth();
        $data   = $this->reportService->getRevenueReport($from, $to, $request->venue_id);
        $venues = Venue::active()->get();

        return view('admin.reports.revenue', compact('data', 'venues', 'from', 'to'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $request->validate([
            'type' => ['required', 'in:utilization,revenue'],
            'from' => ['required', 'date'],
            'to'   => ['required', 'date'],
            'venue_id' => ['nullable', 'integer', 'exists:venues,id'],
        ]);

        $from     = Carbon::parse($request->from);
        $to       = Carbon::parse($request->to);
        $filename = 'arenabook-' . $request->type . '-' . now()->format('Ymd');

        return $this->reportService->exportToExcel(
            $request->type,
            $from,
            $to,
            $filename,
            $request->integer('venue_id') ?: null,
        );
    }
}
