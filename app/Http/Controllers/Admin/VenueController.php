<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVenueRequest;
use App\Models\Schedule;
use App\Models\Venue;
use App\Models\VenueImage;
use App\Services\SlotGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function __construct(
        protected SlotGeneratorService $slotGenerator,
    ) {}

    public function index(): View
    {
        $venues = Venue::with(['schedule', 'images', 'primaryImage', 'bookings'])
            ->withCount('bookings')
            ->orderBy('sort_order')
            ->get();

        return view('admin.venues.index', compact('venues'));
    }

    public function create(): View
    {
        $schedules = Schedule::active()->get();

        return view('admin.venues.create', compact('schedules'));
    }

    public function store(StoreVenueRequest $request): RedirectResponse
    {
        Gate::authorize('create', Venue::class);

        $venue = Venue::create($request->safe()->except('images'));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('venues', 'public');
                VenueImage::create([
                    'venue_id'   => $venue->id,
                    'path'       => $path,
                    'alt_text'   => $venue->name,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        $this->slotGenerator->generateForVenue($venue, now(), now()->addDays(30));

        return redirect()->route('admin.venues.index')
                         ->with('success', "Venue '{$venue->name}' created successfully.");
    }

    public function edit(Venue $venue): View
    {
        $schedules = Schedule::active()->get();

        return view('admin.venues.edit', compact('venue', 'schedules'));
    }

    public function update(StoreVenueRequest $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('update', $venue);

        $previousScheduleId = $venue->schedule_id;

        $venue->update($request->safe()->except('images'));

        // If schedule changed, refresh all future slots
        if ((int) $venue->fresh()->schedule_id !== (int) $previousScheduleId) {
            $this->slotGenerator->refreshSlots($venue);
        }

        return redirect()->route('admin.venues.edit', $venue)
                         ->with('success', 'Venue updated successfully.');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        Gate::authorize('delete', $venue);

        if (\App\Models\Booking::where('venue_id', $venue->id)->active()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete venue with active bookings.']);
        }

        $venue->delete();

        return redirect()->route('admin.venues.index')
                         ->with('success', "Venue '{$venue->name}' deleted.");
    }

    public function uploadImages(Request $request, Venue $venue): JsonResponse
    {
        $request->validate([
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $image) {
            $path  = $image->store('venues', 'public');
            $record = VenueImage::create([
                'venue_id'   => $venue->id,
                'path'       => $path,
                'alt_text'   => $venue->name,
                'is_primary' => false,
                'sort_order' => $venue->images()->count(),
            ]);
            $uploaded[] = ['id' => $record->id, 'url' => Storage::url($path)];
        }

        return response()->json(['success' => true, 'images' => $uploaded]);
    }

    public function setPrimaryImage(Venue $venue, VenueImage $image): RedirectResponse
    {
        VenueImage::where('venue_id', $venue->id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }

    public function deleteImage(Venue $venue, VenueImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }
}
