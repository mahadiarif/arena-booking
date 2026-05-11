<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SlotStatus;
use App\Http\Controllers\Controller;
use App\Models\Slot;
use App\Models\Venue;
use App\Services\SlotGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotController extends Controller
{
    public function __construct(
        protected SlotGeneratorService $slotGenerator,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $venues        = Venue::active()->get();
        $selectedVenue = $request->filled('venue_id')
            ? Venue::find($request->venue_id)
            : $venues->first();
        $date  = $request->filled('date') ? Carbon::parse($request->date) : now();
        $slots = $selectedVenue
            ? $this->slotGenerator->getDayView($selectedVenue, $date)
            : collect();

        // JSON response for AJAX (booking create form slot picker)
        if ($request->format === 'json' || $request->wantsJson()) {
            return response()->json(
                $slots->map(fn ($slot) => [
                    'id'               => $slot->id,
                    'label'            => \Carbon\Carbon::createFromTimeString($slot->start_time)->format('g:i A')
                                         . ' – '
                                         . \Carbon\Carbon::createFromTimeString($slot->end_time)->format('g:i A'),
                    'start_time'       => $slot->start_time,
                    'end_time'         => $slot->end_time,
                    'status'           => $slot->status->value,
                    'duration_minutes' => $slot->duration_minutes,
                    'is_bookable'      => $slot->status->isBookable()
                                         && $slot->current_bookings < $slot->max_bookings,
                ])->values()
            );
        }

        return view('admin.slots.index', compact('venues', 'selectedVenue', 'date', 'slots'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'days'     => ['required', 'integer', 'min:1', 'max:90'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'force'    => ['nullable', 'boolean'],
        ]);

        $force = $request->boolean('force');
        $days  = (int) $request->days;

        if ($request->filled('venue_id')) {
            $venue  = Venue::findOrFail($request->venue_id);
            $result = $this->slotGenerator->generateForVenue($venue, now(), now()->addDays($days), $force);
        } else {
            $result = $this->slotGenerator->generateForDateRange(now(), now()->addDays($days), $force);
        }

        return back()->with('success', "{$result['created']} slots generated successfully.");
    }

    public function blockSlot(Slot $slot): JsonResponse
    {
        if (! auth()->user()->hasRole(['admin', 'super_admin'])) {
            abort(403);
        }

        if ($slot->current_bookings > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot block a slot with active bookings.'], 422);
        }

        $slot->update(['status' => SlotStatus::Blocked]);

        return response()->json(['success' => true]);
    }

    public function unblockSlot(Slot $slot): JsonResponse
    {
        if (! auth()->user()->hasRole(['admin', 'super_admin'])) {
            abort(403);
        }

        $slot->update([
            'status' => $slot->current_bookings > 0
                ? SlotStatus::Partial
                : SlotStatus::Available,
        ]);

        return response()->json(['success' => true]);
    }
}
