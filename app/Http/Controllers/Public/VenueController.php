<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(Request $request): View
    {
        $venues = Venue::active()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->with(['primaryImage'])
            ->orderBy('sort_order')
            ->paginate(12);

        return view('venues.index', compact('venues'));
    }

    public function show(Venue $venue): View
    {
        $venue->load(['images', 'reviews.customer']);
        
        return view('venues.show', compact('venue'));
    }
}
