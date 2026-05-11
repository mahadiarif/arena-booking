<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredVenues = Venue::active()
            ->with(['primaryImage'])
            ->withCount('reviews')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return view('home', compact('featuredVenues'));
    }
}
