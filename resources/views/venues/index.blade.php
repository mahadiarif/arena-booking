@extends('layouts.app')

@section('title', 'Browse All Turfs')

@section('content')
<div class="bg-slate-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search & Filters --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-12">
            <form action="{{ route('venues.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or location..." 
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition font-medium">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 transition btn-3d">Apply Filter</button>
            </form>
        </div>

        {{-- Venue Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($venues as $venue)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden card-hover group">
                <div class="relative h-56">
                    <img src="{{ $venue->primaryImage ? asset('storage/' . $venue->primaryImage->path) : 'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                         alt="{{ $venue->name }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-blue-600/90 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $venue->type ?? 'Multi-purpose' }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $venue->name }}</h3>
                    <div class="flex items-center gap-1 text-slate-400 text-xs mb-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $venue->location }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Starts From</span>
                            <span class="text-lg font-bold text-blue-600">৳{{ number_format($venue->base_price, 0) }}<span class="text-[10px] text-slate-400 font-normal">/hr</span></span>
                        </div>
                        <a href="{{ route('venues.show', $venue) }}" class="bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold transition">View Details</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-700 mb-2">No turfs found</h3>
                <p class="text-slate-500">Try adjusting your search criteria.</p>
            </div>
            @endforelse
        </div>

        @if($venues->hasPages())
        <div class="mt-12">
            {{ $venues->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
