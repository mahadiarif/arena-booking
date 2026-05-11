<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Slot;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $venues    = Venue::where('is_active', true)->get();

        if ($customers->isEmpty() || $venues->isEmpty()) {
            $this->command->warn('No customers or venues found — seed those first.');
            return;
        }

        $created = 0;
        $target  = 80;

        // Spread over past 45 days + next 15 days = 60 days
        for ($i = -45; $i <= 15; $i++) {
            if ($created >= $target) break;

            $date     = now()->addDays($i)->toDateString();
            $venue    = $venues->random();
            $customer = $customers->random();

            // Find or skip — get a slot for this date+venue
            $slot = Slot::where('venue_id', $venue->id)
                        ->where('date', $date)
                        ->where('current_bookings', 0)
                        ->inRandomOrder()
                        ->first();

            if (! $slot) {
                continue;
            }

            // Determine status based on date
            $daysAgo = now()->diffInDays($date, false); // negative = past

            $status = $this->resolveStatus($daysAgo, $venue->requires_approval);

            $totalAmount = $venue->hourly_rate * ($slot->duration_minutes / 60);

            $paidAmount = match (true) {
                in_array($status, [BookingStatus::Completed, BookingStatus::CheckedIn]) => $totalAmount,
                $status === BookingStatus::Confirmed => rand(0, 1) ? $totalAmount : round($totalAmount * 0.5, 2),
                default => 0,
            };

            $ref = 'BK-' . now()->addDays($i)->format('Ymd') . '-' . str_pad(++$created, 4, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($slot, $customer, $venue, $status, $totalAmount, $paidAmount, $ref, $daysAgo) {
                $checkInAt  = null;
                $checkOutAt = null;
                $approvedAt = null;
                $approvedBy = null;

                if ($status === BookingStatus::CheckedIn || $status === BookingStatus::Completed) {
                    $checkInAt = now()->addDays($daysAgo)->setTimeFromTimeString($slot->start_time);
                }
                if ($status === BookingStatus::Completed) {
                    $checkOutAt = now()->addDays($daysAgo)->setTimeFromTimeString($slot->end_time);
                }
                if (in_array($status, [BookingStatus::Confirmed, BookingStatus::CheckedIn, BookingStatus::Completed])) {
                    $approvedAt = now()->addDays($daysAgo)->subHour();
                    $approvedBy = 1;
                }

                $booking = Booking::create([
                    'booking_ref'      => $ref,
                    'customer_id'      => $customer->id,
                    'venue_id'         => $venue->id,
                    'slot_id'          => $slot->id,
                    'booked_by'        => 1,
                    'status'           => $status,
                    'total_amount'     => $totalAmount,
                    'paid_amount'      => $paidAmount,
                    'approved_by'      => $approvedBy,
                    'approved_at'      => $approvedAt,
                    'check_in_at'      => $checkInAt,
                    'check_out_at'     => $checkOutAt,
                ]);

                if ($paidAmount > 0) {
                    Payment::create([
                        'booking_id'  => $booking->id,
                        'amount'      => $paidAmount,
                        'method'      => PaymentMethod::Cash->value,
                        'received_by' => 1,
                        'paid_at'     => now()->addDays($daysAgo),
                    ]);
                }

                // Update slot counter
                $slot->increment('current_bookings');
                if ($slot->current_bookings >= $slot->max_bookings) {
                    $slot->update(['status' => 'booked']);
                } else {
                    $slot->update(['status' => 'partial']);
                }

                // Increment customer counter
                $customer->increment('total_bookings');
            });
        }

        $this->command->info("✓ {$created} bookings seeded.");
    }

    private function resolveStatus(int $daysOffset, bool $requiresApproval): BookingStatus
    {
        // Past (more than 2 days ago)
        if ($daysOffset < -2) {
            $roll = rand(1, 100);
            if ($roll <= 40) return BookingStatus::Completed;
            if ($roll <= 60) return BookingStatus::NoShow;
            if ($roll <= 75) return BookingStatus::Cancelled;
            return BookingStatus::Completed;
        }

        // Yesterday / today
        if ($daysOffset >= -2 && $daysOffset <= 0) {
            return BookingStatus::CheckedIn;
        }

        // Future
        if ($requiresApproval && rand(0, 1)) {
            return BookingStatus::Pending;
        }

        return BookingStatus::Confirmed;
    }
}
