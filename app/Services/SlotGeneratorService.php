<?php

namespace App\Services;

use App\Models\BlackoutPeriod;
use App\Models\Schedule;
use App\Models\Slot;
use App\Models\Venue;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SlotGeneratorService
{
    // ── Public API ─────────────────────────────────────────────────────────────

    public function generateForDateRange(Carbon $from, Carbon $to, bool $force = false): array
    {
        $venues = Venue::with('schedule')
            ->where('is_active', true)
            ->whereHas('schedule', fn ($q) => $q->where('is_active', true))
            ->get();

        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($venues as $venue) {
            $result = $this->generateForVenue($venue, $from, $to, $force);
            $totalCreated += $result['created'];
            $totalSkipped += $result['skipped'];
        }

        return [
            'created' => $totalCreated,
            'skipped' => $totalSkipped,
            'venues'  => $venues->count(),
        ];
    }

    public function generateForVenue(Venue $venue, Carbon $from, Carbon $to, bool $force = false): array
    {
        $schedule = $venue->schedule;

        if (! $schedule || ! $schedule->is_active) {
            return ['created' => 0, 'skipped' => 0];
        }

        $created = 0;
        $skipped = 0;

        foreach (CarbonPeriod::create($from->toDateString(), $to->toDateString()) as $date) {

            if (! $this->isDateWithinScheduleAvailability($date, $schedule)) {
                $skipped++;
                continue;
            }

            if (! $this->isDayAllowed($date, $schedule)) {
                $skipped++;
                continue;
            }

            if ($this->isBlackedOut($date, $venue)) {
                $skipped++;
                continue;
            }

            $created += $this->buildSlotsForDay($date, $venue, $schedule, $force);
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    public function refreshSlots(Venue $venue, Carbon $from = null): array
    {
        $from = $from ?? now()->startOfDay();

        $deleted = Slot::where('venue_id', $venue->id)
            ->where('date', '>=', $from->toDateString())
            ->whereIn('status', ['available', 'blocked'])
            ->where('current_bookings', 0)
            ->delete();

        $result = $this->generateForVenue($venue, $from, $from->copy()->addDays(30));

        return [
            'deleted' => $deleted,
            'created' => $result['created'],
        ];
    }

    public function getAvailableSlots(Venue $venue, Carbon $date): Collection
    {
        return Slot::forVenue($venue->id)
            ->forDate($date)
            ->available()
            ->orderBy('start_time')
            ->get();
    }

    public function getDayView(Venue $venue, Carbon $date): Collection
    {
        return Slot::with(['bookings.customer', 'venue'])
            ->forVenue($venue->id)
            ->forDate($date)
            ->orderBy('start_time')
            ->get();
    }

    // ── Private: Core Builders ─────────────────────────────────────────────────

    private function buildSlotsForDay(Carbon $date, Venue $venue, Schedule $schedule, bool $force): int
    {
        $times = $this->calculateSlotTimes($schedule);

        if ($times->isEmpty()) {
            return 0;
        }

        $now  = now()->toDateTimeString();
        $rows = $times->map(fn ($t) => [
            'venue_id'         => $venue->id,
            'schedule_id'      => $schedule->id,
            'date'             => $date->toDateString(),
            'start_time'       => $t['start'],
            'end_time'         => $t['end'],
            'label'            => $t['label'],
            'status'           => 'available',
            'max_bookings'     => $schedule->allow_concurrent ? $schedule->max_concurrent : 1,
            'current_bookings' => 0,
            'created_at'       => $now,
            'updated_at'       => $now,
        ])->values()->all();

        if ($force) {
            Slot::where('venue_id', $venue->id)
                ->where('date', $date->toDateString())
                ->where('current_bookings', 0)
                ->delete();

            Slot::insert($rows);
            return count($rows);
        }

        // Upsert — skip rows that already have bookings (matched by unique key)
        Slot::upsert(
            $rows,
            ['venue_id', 'date', 'start_time'],  // unique keys
            ['updated_at']                          // only touch timestamp on conflict
        );

        return count($rows);
    }

    private function calculateSlotTimes(Schedule $schedule): Collection
    {
        $interval = (int) $schedule->slot_interval_minutes;
        $current  = Carbon::today()->setTimeFromTimeString($schedule->start_time);
        $end      = Carbon::today()->setTimeFromTimeString($schedule->end_time);

        $slots = collect();

        while ($current->copy()->addMinutes($interval)->lte($end)) {
            $next = $current->copy()->addMinutes($interval);

            $slots->push([
                'start' => $current->format('H:i:s'),
                'end'   => $next->format('H:i:s'),
                'label' => $this->generateLabel($current->format('H:i:s'), $next->format('H:i:s')),
            ]);

            $current->addMinutes($interval);
        }

        return $slots;
    }

    private function generateLabel(string $start, string $end): string
    {
        $startHour = (int) substr($start, 0, 2);

        $period = match (true) {
            $startHour >= 5  && $startHour < 12 => 'Morning',
            $startHour >= 12 && $startHour < 17 => 'Afternoon',
            $startHour >= 17 && $startHour < 21 => 'Evening',
            default                              => 'Night',
        };

        $fmt = fn (string $time) => Carbon::createFromTimeString($time)->format('g:i A');

        return "{$period} ({$fmt($start)} – {$fmt($end)})";
    }

    // ── Private: Guard Checks ──────────────────────────────────────────────────

    private function isDateWithinScheduleAvailability(Carbon $date, Schedule $schedule): bool
    {
        if ($schedule->availability_start && $date->lt($schedule->availability_start)) {
            return false;
        }

        if ($schedule->availability_end && $date->gt($schedule->availability_end)) {
            return false;
        }

        return true;
    }

    private function isDayAllowed(Carbon $date, Schedule $schedule): bool
    {
        $allowedDays = $schedule->allowed_days; // cast to array e.g. [0,1,2,3,4,5,6]

        return in_array($date->dayOfWeek, (array) $allowedDays, true);
    }

    private function isBlackedOut(Carbon $date, Venue $venue): bool
    {
        return BlackoutPeriod::where(function ($q) use ($venue) {
                $q->where('venue_id', $venue->id)
                  ->orWhereNull('venue_id');
            })
            ->where('start_datetime', '<=', $date->endOfDay())
            ->where('end_datetime', '>=', $date->startOfDay())
            ->exists();
    }
}
