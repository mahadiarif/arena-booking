<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Events\BookingCancelled;
use App\Events\BookingCheckedIn;
use App\Events\BookingCompleted;
use App\Events\BookingConfirmed;
use App\Events\BookingCreated;
use App\Exceptions\InvalidBookingTransitionException;
use App\Exceptions\SlotNotAvailableException;
use App\Models\BlackoutPeriod;
use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Payment;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected SlotGeneratorService $slotGenerator
    ) {}

    // ── Public API ─────────────────────────────────────────────────────────────

    public function checkAvailability(int $venueId, int $slotId): bool
    {
        $slot = Slot::with('venue', 'schedule')->find($slotId);

        if (! $slot) {
            return false;
        }

        // Wrong venue
        if ((int) $slot->venue_id !== $venueId) {
            return false;
        }

        // Slot status must allow booking
        if (! $slot->status->isBookable()) {
            return false;
        }

        // Capacity check
        if ($slot->current_bookings >= $slot->max_bookings) {
            return false;
        }

        // Blackout check
        if ($this->isSlotBlackedOut($slot)) {
            return false;
        }

        return true;
    }

    public function createBooking(array $data, User $createdBy): Booking
    {
        return DB::transaction(function () use ($data, $createdBy) {

            // 1. Availability guard
            if (! $this->checkAvailability($data['venue_id'], $data['slot_id'])) {
                throw new SlotNotAvailableException();
            }

            $slot     = Slot::with('venue')->lockForUpdate()->find($data['slot_id']);
            $venue    = $slot->venue;
            $customer = \App\Models\Customer::find($data['customer_id']);

            // 2. Determine initial status
            $status = $venue->requires_approval
                ? BookingStatus::Pending
                : BookingStatus::Confirmed;

            // 3. Create booking
            $booking = Booking::create([
                'booking_ref'   => $this->generateBookingRef(),
                'customer_id'   => $data['customer_id'],
                'venue_id'      => $data['venue_id'],
                'slot_id'       => $data['slot_id'],
                'booked_by'     => $createdBy->id,
                'status'        => $status,
                'total_amount'  => $data['total_amount'],
                'paid_amount'   => 0,
                'notes'         => $data['notes'] ?? null,
            ]);

            // 4. Initial payment
            $initialPayment = (float) ($data['initial_payment'] ?? 0);
            if ($initialPayment > 0) {
                Payment::create([
                    'booking_id'   => $booking->id,
                    'amount'       => $initialPayment,
                    'method'       => $data['payment_method'] ?? PaymentMethod::Cash->value,
                    'reference_no' => $data['payment_reference'] ?? null,
                    'received_by'  => $createdBy->id,
                    'paid_at'      => now(),
                ]);

                $booking->update(['paid_amount' => $initialPayment]);
            }

            // 5. Update slot occupancy
            $slot->increment('current_bookings');
            $slot->refresh();
            $slot->update([
                'status' => $slot->current_bookings >= $slot->max_bookings
                    ? 'booked'
                    : 'partial',
            ]);

            // 6. Increment customer booking count
            $customer->increment('total_bookings');

            // 7. Participants
            if (! empty($data['participants']) && is_array($data['participants'])) {
                foreach ($data['participants'] as $participant) {
                    BookingParticipant::create([
                        'booking_id' => $booking->id,
                        'name'       => $participant['name'],
                        'phone'      => $participant['phone'] ?? null,
                        'note'       => $participant['note'] ?? null,
                    ]);
                }
            }

            // 8. Fire event
            event(new BookingCreated($booking));

            return $booking->fresh(['customer', 'venue', 'slot', 'payments']);
        });
    }

    public function confirmBooking(Booking $booking, User $by): Booking
    {
        if (! $booking->status->canTransitionTo(BookingStatus::Confirmed)) {
            throw new InvalidBookingTransitionException($booking->status, BookingStatus::Confirmed);
        }

        $booking->update([
            'status'      => BookingStatus::Confirmed,
            'approved_by' => $by->id,
            'approved_at' => now(),
        ]);

        event(new BookingConfirmed($booking->fresh()));

        return $booking->fresh();
    }

    public function cancelBooking(Booking $booking, string $reason, User $by): Booking
    {
        return DB::transaction(function () use ($booking, $reason, $by) {

            if (! $booking->status->canTransitionTo(BookingStatus::Cancelled)) {
                throw new InvalidBookingTransitionException($booking->status, BookingStatus::Cancelled);
            }

            $booking->update([
                'status'        => BookingStatus::Cancelled,
                'cancelled_by'  => $by->id,
                'cancelled_at'  => now(),
                'cancel_reason' => $reason,
            ]);

            // Adjust slot occupancy
            $slot = $booking->slot()->lockForUpdate()->first();
            $slot->decrement('current_bookings');
            $slot->refresh();
            $slot->update(['status' => $this->computeSlotStatus($slot)]);

            // Adjust customer count
            $customer = $booking->customer()->lockForUpdate()->first();
            if ($customer->total_bookings > 0) {
                $customer->decrement('total_bookings');
            }

            event(new BookingCancelled($booking->fresh()));

            return $booking->fresh();
        });
    }

    public function checkIn(Booking $booking, User $by): Booking
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            throw new InvalidBookingTransitionException($booking->status, BookingStatus::CheckedIn);
        }

        $booking->update([
            'status'      => BookingStatus::CheckedIn,
            'check_in_at' => now(),
        ]);

        event(new BookingCheckedIn($booking->fresh()));

        return $booking->fresh();
    }

    public function checkOut(Booking $booking, User $by): Booking
    {
        if ($booking->status !== BookingStatus::CheckedIn) {
            throw new InvalidBookingTransitionException($booking->status, BookingStatus::Completed);
        }

        $booking->update([
            'status'       => BookingStatus::Completed,
            'check_out_at' => now(),
        ]);

        event(new BookingCompleted($booking->fresh()));

        return $booking->fresh();
    }

    public function markNoShow(Booking $booking): Booking
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            throw new InvalidBookingTransitionException($booking->status, BookingStatus::NoShow);
        }

        $booking->update(['status' => BookingStatus::NoShow]);

        $slot = $booking->slot()->lockForUpdate()->first();
        $slot->decrement('current_bookings');
        $slot->refresh();
        $slot->update(['status' => $this->computeSlotStatus($slot)]);

        return $booking->fresh();
    }

    // ── Private Helpers ────────────────────────────────────────────────────────

    private function generateBookingRef(): string
    {
        return DB::transaction(function () {
            $today = now()->format('Ymd');
            $prefix = "BK-{$today}-";

            $todayCount = Booking::withTrashed()
                ->where('booking_ref', 'LIKE', "{$prefix}%")
                ->lockForUpdate()
                ->count();

            $seq = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            return "{$prefix}{$seq}";
        });
    }

    private function isSlotBlackedOut(Slot $slot): bool
    {
        return BlackoutPeriod::where(function ($q) use ($slot) {
                $q->where('venue_id', $slot->venue_id)
                  ->orWhereNull('venue_id');
            })
            ->where('start_datetime', '<=', $slot->date->endOfDay())
            ->where('end_datetime', '>=', $slot->date->startOfDay())
            ->exists();
    }

    private function computeSlotStatus(Slot $slot): string
    {
        if ($slot->current_bookings <= 0) {
            return 'available';
        }

        if ($slot->current_bookings < $slot->max_bookings) {
            return 'partial';
        }

        return 'booked';
    }
}
