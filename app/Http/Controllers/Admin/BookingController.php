<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\BookingAttribute;
use App\Models\RecurrenceRule;
use App\Models\Slot;
use App\Models\Venue;
use App\Services\BookingService;
use App\Services\CalendarService;
use App\Services\PaymentService;
use App\Services\RecurrenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService    $bookingService,
        protected PaymentService    $paymentService,
        protected RecurrenceService $recurrenceService,
        protected CalendarService   $calendarService,
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['customer', 'venue', 'slot'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('date_from')) {
            $query->whereHas('slot', fn ($q) => $q->where('date', '>=', $request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereHas('slot', fn ($q) => $q->where('date', '<=', $request->date_to));
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q
                ->where('booking_ref', 'LIKE', "%{$term}%")
                ->orWhereHas('customer', fn ($q2) => $q2
                    ->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('phone', 'LIKE', "%{$term}%")
                )
            );
        }

        $bookings = $query->paginate(20)->withQueryString();
        $venues   = Venue::active()->get();
        $statuses = BookingStatus::cases();

        return view('admin.bookings.index', compact('bookings', 'venues', 'statuses'));
    }

    public function create(Request $request): View
    {
        $venues           = Venue::active()->with('schedule')->get();
        $slot             = $request->filled('slot_id') ? Slot::find($request->slot_id) : null;
        $bookingAttributes = BookingAttribute::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.bookings.create', compact('venues', 'slot', 'bookingAttributes'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        if ($request->boolean('is_recurring')) {
            $rule = RecurrenceRule::create([
                'type'             => $request->recurrence_type,
                'interval'         => $request->recurrence_interval ?? 1,
                'days_of_week'     => $request->recurrence_days_of_week,
                'end_type'         => $request->recurrence_end_type,
                'end_date'         => $request->recurrence_end_date,
                'end_after_count'  => $request->recurrence_end_after_count,
            ]);

            $bookings = $this->recurrenceService->createRecurringBookings(
                $request->validated(),
                $rule,
                auth()->user()
            );

            return redirect()
                ->route('admin.bookings.show', $bookings[0])
                ->with('success', count($bookings) . ' recurring bookings created.');
        }

        $booking = $this->bookingService->createBooking($request->validated(), auth()->user());

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Booking created: ' . $booking->booking_ref);
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'customer',
            'venue',
            'slot',
            'payments.receivedBy',
            'participants',
            'attributeValues.attribute',
            'bookedBy',
            'approvedBy',
            'cancelledBy',
            'childBookings.slot',
        ]);

        $paymentSummary = $this->paymentService->getPaymentSummary($booking);
        $activityLog    = Activity::where('subject_type', Booking::class)
                                  ->where('subject_id', $booking->id)
                                  ->with('causer')
                                  ->latest()
                                  ->get();

        return view('admin.bookings.show', compact('booking', 'paymentSummary', 'activityLog'));
    }

    public function edit(Booking $booking): View
    {
        Gate::authorize('update', $booking);

        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        $booking->update($request->validated());

        return redirect()->route('admin.bookings.show', $booking)
                         ->with('success', 'Booking updated.');
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        Gate::authorize('approve', $booking);

        $this->bookingService->confirmBooking($booking, auth()->user());

        return back()->with('success', 'Booking confirmed.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('cancel', $booking);

        $request->validate(['cancel_reason' => ['required', 'string', 'max:500']]);

        $this->bookingService->cancelBooking($booking, $request->cancel_reason, auth()->user());

        return back()->with('success', 'Booking cancelled.');
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        $this->bookingService->checkIn($booking, auth()->user());

        return back()->with('success', 'Customer checked in.');
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        $this->bookingService->checkOut($booking, auth()->user());

        return back()->with('success', 'Check-out recorded.');
    }

    public function downloadInvoice(Booking $booking): Response
    {
        return $this->paymentService->generateInvoicePDF($booking);
    }
}
