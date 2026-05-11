<?php

namespace App\Http\Controllers\Public;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Slot;
use App\Services\BookingService;
use App\Services\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected CreditService  $creditService,
    ) {}

    public function checkout(Slot $slot): View|RedirectResponse
    {
        if ($slot->status !== 'available') {
            return redirect()->route('venues.show', $slot->venue_id)
                             ->with('error', 'This slot is no longer available.');
        }

        $customer = auth()->user()->customer;
        
        if (!$customer) {
            return redirect()->route('admin.dashboard')->with('error', 'Only customers can book venues.');
        }

        return view('bookings.checkout', compact('slot', 'customer'));
    }

    public function store(Request $request, Slot $slot): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,manual',
            'notes'          => 'nullable|string|max:500',
        ]);

        $customer = auth()->user()->customer;

        if ($slot->status !== 'available') {
            return back()->with('error', 'Slot already booked by someone else.');
        }

        try {
            return DB::transaction(function () use ($request, $slot, $customer) {
                // 1. Create the booking
                $booking = Booking::create([
                    'booking_ref' => 'BK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                    'customer_id' => $customer->id,
                    'venue_id'    => $slot->venue_id,
                    'slot_id'     => $slot->id,
                    'booked_by'   => auth()->id(),
                    'status'      => BookingStatus::PENDING,
                    'total_amount'=> $slot->venue->base_price,
                    'paid_amount' => 0,
                    'due_amount'  => $slot->venue->base_price,
                    'check_in_at' => $slot->start_at,
                    'check_out_at'=> $slot->end_at,
                    'notes'       => $request->notes,
                ]);

                // 2. Mark slot as booked
                $slot->update(['status' => 'booked']);

                // 3. Handle Wallet Payment
                if ($request->payment_method === 'wallet') {
                    if ($customer->credit_balance < $booking->total_amount) {
                        throw new \Exception('Insufficient wallet balance.');
                    }

                    $this->creditService->applyToBooking($customer, $booking->total_amount, $booking->id);
                    
                    $booking->update([
                        'paid_amount' => $booking->total_amount,
                        'due_amount'  => 0,
                        'status'      => BookingStatus::CONFIRMED
                    ]);
                }

                return redirect()->route('venues.booking-success', $booking)
                                 ->with('success', 'Booking placed successfully!');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success(Booking $booking): View
    {
        return view('bookings.success', compact('booking'));
    }
}
