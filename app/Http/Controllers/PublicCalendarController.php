<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCalendarController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService,
    ) {}

    public function index(Request $request)
    {
        $date    = $request->filled('date') ? Carbon::parse($request->date) : now();
        $venueId = $request->input('venue_id');

        if ($request->wantsJson() || $venueId) {
            $slots = $this->calendarService->getDailyView($date, $venueId);
            
            return response()->json([
                'slots' => $slots->map(function($slot) {
                    return [
                        'id' => $slot->id,
                        'start_time_formatted' => $slot->start_time_formatted,
                        'end_time_formatted' => $slot->end_time_formatted,
                        'status' => $slot->status,
                    ];
                })
            ]);
        }

        $venues = Venue::where('is_active', true)->orderBy('sort_order')->get();
        $calendarData = $this->calendarService->getDailyView($date);

        return view('public-calendar.index', compact('venues', 'calendarData', 'date'));
    }
}
