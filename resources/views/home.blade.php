@extends('layouts.app')

@section('title', 'Book Your Favorite Turf')

@section('content')
{{-- Hero Section --}}
<div class="relative overflow-hidden bg-slate-900 pt-16 pb-32">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1551958219-acbc608c6377?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center opacity-30"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 tracking-tight">
            Find and Book Your <br><span class="text-blue-500">Perfect Arena</span>
        </h1>
        <p class="max-w-2xl mx-auto text-lg text-slate-300 mb-10">
            Discover top-rated sports turfs, stadiums, and courts in your area. Real-time availability and instant confirmation.
        </p>
        
        <div class="max-w-3xl mx-auto bg-white p-2 rounded-2xl shadow-2xl flex flex-col md:flex-row gap-2">
            <div class="flex-1 flex items-center px-4 py-2 border-b md:border-b-0 md:border-r border-slate-100">
                <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Search for turfs..." class="w-full text-sm outline-none font-medium">
            </div>
            <div class="flex-1 flex items-center px-4 py-2">
                <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                <input type="date" class="w-full text-sm outline-none font-medium text-slate-600">
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold transition btn-3d">Search Now</button>
        </div>
    </div>
</div>

{{-- Featured Venues --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-10">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-white md:text-slate-900">Featured Venues</h2>
        <a href="{{ route('venues.index') }}" class="text-sm font-bold text-blue-500 hover:text-blue-400 md:text-blue-600 md:hover:text-blue-700 flex items-center gap-1 transition">
            View All 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($featuredVenues as $venue)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden card-hover group">
            <div class="relative h-56">
                <img src="{{ $venue->primaryImage ? asset('storage/' . $venue->primaryImage->path) : 'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                     alt="{{ $venue->name }}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full flex items-center gap-1 shadow-sm">
                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span class="text-xs font-bold">{{ number_format($venue->average_rating, 1) }}</span>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-lg font-bold text-slate-800">{{ $venue->name }}</h3>
                    <span class="text-blue-600 font-bold">৳{{ number_format($venue->base_price, 0) }}<span class="text-[10px] text-slate-400 font-normal">/hr</span></span>
                </div>
                <p class="text-sm text-slate-500 mb-6 line-clamp-2">{{ $venue->description }}</p>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs">{{ $venue->location }}</span>
                    </div>
                </div>

                <a href="{{ route('venues.show', $venue) }}" class="block w-full text-center bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-2xl text-sm font-bold transition btn-3d">Book Now</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Features --}}
<div class="bg-white py-24 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-4">Why Book With Us?</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Experience the most convenient way to secure your favorite sports ground.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            @foreach([
                ['Instant Confirmation', 'No more waiting for calls. Get instant confirmation of your booking.', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Real-time Slots', 'See exactly which hours are free and pick what suits you best.', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Secure Payments', 'Pay via Wallet, bKash or Cash at the venue with full security.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V11.5']
            ] as [$title, $desc, $icon])
            <div class="group">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                </div>
                <h4 class="text-xl font-bold mb-3 text-slate-800">{{ $title }}</h4>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
