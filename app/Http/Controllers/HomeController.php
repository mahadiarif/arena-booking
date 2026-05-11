<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredVenues = Venue::active()
            ->with(['primaryImage'])
            ->withCount([
                'reviews as published_reviews_count' => fn ($query) => $query->where('is_published', true),
            ])
            ->withAvg([
                'reviews as published_reviews_avg_rating' => fn ($query) => $query->where('is_published', true),
            ], 'rating')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $galleryImages = GalleryImage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('home', compact('featuredVenues', 'galleryImages'));
    }
}
