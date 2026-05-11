<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\RecurrenceRule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RecurrenceService
{
    public function __construct(
        protected BookingService $bookingService,
    ) {}

    /**
     * Create multiple recurring bookings based on a RecurrenceRule.
     * Returns an array of created Booking instances.
     *
     * @return Booking[]
     */
    public function createRecurringBookings(array $data, RecurrenceRule $rule, User $createdBy): array
    {
        $dates   = $this->resolveDates($rule, Carbon::parse($data['booking_date'] ?? now()));
        $created = [];
        $parentId = null;

        foreach ($dates as $date) {
            $slotData = array_merge($data, ['booking_date' => $date->toDateString()]);

            // Find a matching slot on this date for same venue
            $slot = \App\Models\Slot::where('venue_id', $data['venue_id'])
                ->where('date', $date->toDateString())
                ->where('start_time', $data['slot_start_time'] ?? '')
                ->first();

            if (! $slot) {
                continue; // Skip dates where slot doesn't exist
            }

            $slotData['slot_id'] = $slot->id;

            try {
                $booking = $this->bookingService->createBooking($slotData, $createdBy);

                if ($parentId === null) {
                    $parentId = $booking->id;
                } else {
                    $booking->update(['parent_booking_id' => $parentId]);
                }

                $created[] = $booking;
            } catch (\Throwable $e) {
                // Skip unavailable dates silently
                continue;
            }
        }

        return $created;
    }

    /**
     * Resolve all booking dates based on recurrence rule.
     *
     * @return Carbon[]
     */
    private function resolveDates(RecurrenceRule $rule, Carbon $start): array
    {
        $dates    = [$start->copy()];
        $current  = $start->copy();
        $interval = (int) ($rule->interval ?? 1);
        $maxCount = 52; // safety cap

        $endDate  = $rule->end_date   ? Carbon::parse($rule->end_date) : null;
        $endCount = $rule->end_after_count ?? $maxCount;

        $iterations = 0;

        while ($iterations < $maxCount) {
            $iterations++;

            match ($rule->type) {
                'daily'   => $current->addDays($interval),
                'weekly'  => $current->addWeeks($interval),
                'monthly' => $current->addMonths($interval),
                default   => $current->addWeeks(1),
            };

            if ($endDate && $current->gt($endDate)) break;
            if (count($dates) >= $endCount) break;

            // For weekly: filter by allowed days
            if ($rule->type === 'weekly' && ! empty($rule->days_of_week)) {
                if (! in_array($current->dayOfWeek, (array) $rule->days_of_week)) {
                    continue;
                }
            }

            $dates[] = $current->copy();
        }

        return $dates;
    }
}
