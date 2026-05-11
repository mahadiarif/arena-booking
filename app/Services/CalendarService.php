<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Slot;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Get all slots for a specific day, optionally filtered by venue.
     */
    public function getDailyView(Carbon $date, ?int $venueId = null): Collection
    {
        $query = Slot::with(['venue', 'bookings.customer'])
            ->where('date', $date->toDateString());

        if ($venueId) {
            $query->where('venue_id', $venueId);
        }

        return $query->orderBy('start_time')->get();
    }

    /**
     * Get structured data for the monitor display.
     * Returns current + upcoming bookings per venue.
     */
    public function getMonitorData(): array
    {
        $now    = now();
        $today  = $now->toDateString();
        $venues = Venue::active()->get();

        return [
            'venues' => $venues->map(function (Venue $venue) use ($today, $now) {
                $slots = Slot::with(['bookings.customer'])
                    ->where('venue_id', $venue->id)
                    ->where('date', $today)
                    ->orderBy('start_time')
                    ->get();

                $current  = null;
                $upcoming = [];

                foreach ($slots as $slot) {
                    $startDt = Carbon::parse("{$today} {$slot->start_time}");
                    $endDt   = Carbon::parse("{$today} {$slot->end_time}");

                    $activeBooking = $slot->bookings
                        ->whereNotIn('status', ['cancelled', 'no_show'])
                        ->first();

                    if (! $activeBooking) {
                        continue;
                    }

                    $timeRange = $startDt->format('g:i A') . ' – ' . $endDt->format('g:i A');

                    if ($now->between($startDt, $endDt)) {
                        $current = [
                            'customer_name' => $activeBooking->customer?->name ?? 'Unknown',
                            'booking_ref'   => $activeBooking->booking_ref,
                            'time_range'    => $timeRange,
                        ];
                    } elseif ($startDt->gt($now) && count($upcoming) < 3) {
                        $upcoming[] = [
                            'customer_name' => $activeBooking->customer?->name ?? 'Unknown',
                            'booking_ref'   => $activeBooking->booking_ref,
                            'time_range'    => $timeRange,
                        ];
                    }
                }

                return [
                    'id'       => $venue->id,
                    'name'     => $venue->name,
                    'color'    => $venue->color,
                    'type'     => $venue->getTypeLabel(),
                    'current'  => $current,
                    'upcoming' => $upcoming,
                ];
            })->values()->all(),
            'generated_at' => $now->toIso8601String(),
        ];
    }
}
