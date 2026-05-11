<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VenueReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = VenueReview::with(['venue', 'customer'])
            ->when($request->filled('venue_id'), fn($q) => $q->where('venue_id', $request->venue_id))
            ->when($request->filled('rating'), fn($q) => $q->where('rating', $request->rating))
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function togglePublish(VenueReview $review): RedirectResponse
    {
        $review->update(['is_published' => ! $review->is_published]);

        return back()->with('success', 'Review visibility updated.');
    }

    public function destroy(VenueReview $review): RedirectResponse
    {
        Gate::authorize('delete', $review);
        
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
