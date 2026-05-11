<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Slot;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Get venue utilization for a date range.
     * Returns per-venue: total_slots, booked_slots, occupancy%, revenue, color.
     */
    public function getUtilizationReport(Carbon $from, Carbon $to, ?int $venueId = null): Collection
    {
        $venues = Venue::active()
            ->when($venueId, fn ($q) => $q->where('id', $venueId))
            ->get();

        return $venues->map(function (Venue $venue) use ($from, $to) {
            $totalSlots = Slot::where('venue_id', $venue->id)
                ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->count();

            $bookedSlots = Slot::where('venue_id', $venue->id)
                ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->whereIn('status', ['booked', 'partial'])
                ->count();

            $revenue = Payment::whereHas('booking', fn ($q) => $q
                ->where('venue_id', $venue->id)
                ->whereHas('slot', fn ($q2) => $q2->whereBetween('date', [$from->toDateString(), $to->toDateString()]))
            )->sum('amount');

            $occupancy = $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100) : 0;

            return [
                'venue_id'    => $venue->id,
                'venue'       => $venue->name,
                'color'       => $venue->color,
                'total_slots' => $totalSlots,
                'booked_slots'=> $bookedSlots,
                'occupancy'   => $occupancy,
                'revenue'     => $revenue,
            ];
        });
    }

    /**
     * Get revenue breakdown by date+venue.
     */
    public function getRevenueReport(Carbon $from, Carbon $to, ?int $venueId = null): Collection
    {
        $query = Booking::with(['venue', 'slot', 'payments'])
            ->whereHas('slot', fn ($q) => $q->whereBetween('date', [$from->toDateString(), $to->toDateString()]))
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($venueId, fn ($q) => $q->where('venue_id', $venueId));

        return $query->get()->groupBy(fn ($b) => $b->slot?->date?->toDateString() . '_' . $b->venue_id)
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'date'         => $first->slot?->date?->toDateString(),
                    'venue'        => $first->venue?->name,
                    'color'        => $first->venue?->color,
                    'bookings'     => $group->count(),
                    'total_amount' => $group->sum('total_amount'),
                    'collected'    => $group->sum('paid_amount'),
                    'outstanding'  => $group->sum(fn ($b) => max(0, $b->total_amount - $b->paid_amount)),
                ];
            })
            ->sortBy('date')
            ->values();
    }

    /**
     * Get daily summary (for reports index / dashboard).
     */
    public function getDailySummary(Carbon $date): array
    {
        $dateStr  = $date->toDateString();
        $bookings = Booking::whereHas('slot', fn ($q) => $q->where('date', $dateStr))
                           ->with('payments')
                           ->get();

        // Count by each status
        $byStatus = [];
        foreach (\App\Enums\BookingStatus::cases() as $status) {
            $byStatus[$status->value] = $bookings
                ->filter(fn ($b) => $b->status === $status)
                ->count();
        }

        $revenuToday = $bookings->sum('paid_amount');
        $dueToday    = $bookings->sum(fn ($b) => max(0, (float)$b->total_amount - (float)$b->paid_amount));

        return [
            'date'           => $dateStr,
            'total_bookings' => $bookings->count(),
            'by_status'      => $byStatus,
            'revenue_today'  => $revenuToday,
            'due_today'      => $dueToday,
            // Legacy keys (dashboard compatibility)
            'revenue'        => $revenuToday,
            'pending'        => $byStatus['pending'] ?? 0,
            'confirmed'      => $byStatus['confirmed'] ?? 0,
            'checked_in'     => $byStatus['checked_in'] ?? 0,
        ];
    }

    /**
     * Export report data to Excel (requires maatwebsite/excel).
     */
    public function exportToExcel(string $type, Carbon $from, Carbon $to, string $filename, ?int $venueId = null): StreamedResponse
    {
        // Requires: composer require maatwebsite/excel
        // Placeholder — implement with specific Export classes once package is installed
        $data = $type === 'utilization'
            ? $this->getUtilizationReport($from, $to, $venueId)
            : $this->getRevenueReport($from, $to, $venueId);

        $headers = array_keys($data->first() ?? []);

        return response()->streamDownload(function () use ($headers, $data) {
            $output = fopen('php://output', 'w');

            if ($headers !== []) {
                fputcsv($output, $headers);
            }

            foreach ($data as $row) {
                fputcsv($output, array_values($row));
            }

            fclose($output);
        }, $filename . '.csv', ['Content-Type' => 'text/csv']);
    }
}
