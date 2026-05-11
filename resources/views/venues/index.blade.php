@extends('layouts.app')

@section('title', 'Browse Turfs')

@section('content')
@php
    $fallbackVenueImage = 'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80';
@endphp

<section class="bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-300">Browse Turfs</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight text-white md:text-5xl">Find the right venue for your next match</h1>
            <p class="mt-4 text-base leading-7 text-slate-300">Filter active venues by sport type, compare capacity and pricing, then jump straight into available slots.</p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <form action="{{ route('venues.index') }}" method="GET" class="relative z-20 -mt-20 mb-10 rounded-3xl border border-slate-200 bg-white p-4 shadow-xl shadow-slate-200/60">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_220px_auto]">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by venue name" class="h-12 w-full rounded-3xl border border-slate-200 bg-white pl-12 pr-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <select name="type" class="h-12 rounded-3xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All venue types</option>
                    <option value="stadium" {{ request('type') == 'stadium' ? 'selected' : '' }}>Stadium</option>
                    <option value="turf_indoor" {{ request('type') == 'turf_indoor' ? 'selected' : '' }}>Indoor Turf</option>
                    <option value="turf_outdoor" {{ request('type') == 'turf_outdoor' ? 'selected' : '' }}>Outdoor Turf</option>
                    <option value="vip_box" {{ request('type') == 'vip_box' ? 'selected' : '' }}>VIP Box</option>
                    <option value="hall" {{ request('type') == 'hall' ? 'selected' : '' }}>Hall</option>
                </select>

                <input type="hidden" name="date" value="{{ request('date') }}">

                <button type="submit" class="inline-flex h-12 items-center justify-center rounded-3xl bg-blue-600 px-6 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-700">Apply Filter</button>
            </div>
        </form>

        <div class="mb-5 flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500">{{ $venues->total() }} venues found</p>
            @if(request()->hasAny(['search', 'type']))
                <a href="{{ route('venues.index') }}" class="text-xs font-black uppercase tracking-widest text-blue-600 hover:text-blue-700">Clear filters</a>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($venues as $venue)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="relative h-52 overflow-hidden bg-slate-100">
                        <img src="{{ $venue->primaryImage ? asset('storage/' . $venue->primaryImage->path) : $fallbackVenueImage }}"
                             alt="{{ $venue->name }}"
                             class="h-full w-full object-cover transition duration-500 hover:scale-105">
                        <span class="absolute left-3 top-3 rounded-2xl bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur">{{ $venue->getTypeLabel() }}</span>
                    </div>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="truncate text-lg font-black text-slate-900">{{ $venue->name }}</h2>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ number_format($venue->capacity) }} capacity</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-black text-blue-600">BDT {{ number_format($venue->hourly_rate, 0) }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">per hour</p>
                            </div>
                        </div>

                        <p class="mt-3 line-clamp-2 min-h-[2.5rem] text-sm leading-5 text-slate-500">{{ $venue->description ?: 'Ready for fast booking with real-time slot availability.' }}</p>

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('venues.show', $venue) }}" class="inline-flex h-10 flex-1 items-center justify-center rounded-3xl bg-slate-900 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-600">View Details</a>
                            <a href="{{ route('venues.show', $venue) }}#booking-section" class="inline-flex h-10 flex-1 items-center justify-center rounded-3xl border border-slate-200 bg-white text-xs font-black uppercase tracking-widest text-slate-700 transition hover:bg-slate-50">Slots</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border-2 border-dashed border-slate-200 bg-white p-12 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-50">
                        <svg class="h-7 w-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-800">No turfs found</h3>
                    <p class="mt-1 text-sm text-slate-500">Try a different venue name or type.</p>
                </div>
            @endforelse
        </div>

        @if($venues->hasPages())
            <div class="mt-10">
                {{ $venues->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
