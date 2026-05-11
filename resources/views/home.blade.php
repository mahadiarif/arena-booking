@extends('layouts.app')

@section('title', 'Book Sports Venues in Minutes')

@section('content')
@php
    $heroImage = \App\Models\Setting::get('hero_banner_image', 'https://images.unsplash.com/photo-1551958219-acbc608c6377?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
    $heroTitle = trim(strip_tags((string) \App\Models\Setting::get('hero_title', 'Find and book your perfect arena')));
    $heroSubtitle = \App\Models\Setting::get('hero_subtitle', 'Discover top-rated sports turfs, stadiums, and courts with real-time availability and simple booking.');
    $fallbackVenueImage = 'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80';
@endphp

<section class="relative overflow-hidden bg-slate-950">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroImage }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-slate-900/30"></div>
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-slate-50 to-transparent"></div>

    <div class="relative mx-auto grid min-h-[calc(100vh-4rem)] max-w-7xl items-center gap-10 px-4 pb-28 pt-20 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
        <div class="max-w-3xl">
            <p class="mb-4 inline-flex rounded-2xl border border-white/15 bg-white/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.24em] text-blue-200 backdrop-blur">Metro ArenaBook</p>
            <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $heroTitle }}</h1>
            <p class="mt-5 max-w-2xl text-base leading-8 text-slate-200 sm:text-lg">{{ $heroSubtitle }}</p>

            <div class="mt-8 grid grid-cols-3 gap-3 max-w-xl">
                <div class="rounded-3xl border border-white/10 bg-white/10 p-3 backdrop-blur">
                    <p class="text-2xl font-black text-white">{{ $featuredVenues->count() }}+</p>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-300">Venues</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-3 backdrop-blur">
                    <p class="text-2xl font-black text-white">Live</p>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-300">Slots</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-3 backdrop-blur">
                    <p class="text-2xl font-black text-white">BDT</p>
                    <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-300">Pricing</p>
                </div>
            </div>
        </div>

        <div class="relative z-20" x-data="{ selectedSport: '{{ request('type') }}' }">
            <form action="{{ route('venues.index') }}" method="GET" class="rounded-3xl border border-white/15 bg-white p-4 shadow-2xl shadow-slate-950/30">
                <input type="hidden" name="type" :value="selectedSport">

                <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
                    @foreach([
                        ['', 'Any', 'M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z'],
                        ['stadium', 'Football', 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z'],
                        ['turf_indoor', 'Cricket', 'M17 14l-1.5-1.5L12 16l2 2 3-4M4 14l8.5-8.5 1 1L5 15l-1-1z'],
                        ['turf_outdoor', 'Outdoor', 'M4 7h16M4 12h16M4 17h16'],
                    ] as [$value, $label, $icon])
                        <button type="button" @click="selectedSport = '{{ $value }}'" :class="selectedSport === '{{ $value }}' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="flex h-20 flex-col items-center justify-center rounded-3xl border text-xs font-black uppercase tracking-widest transition">
                            <svg class="mb-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                    <x-calendar-picker name="date" :value="request('date', date('Y-m-d'))" label="Playing Date" />
                    <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-3xl bg-blue-600 px-6 text-sm font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="relative z-10 -mt-16 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Popular Places</p>
            <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Featured Venues</h2>
        </div>
        <a href="{{ route('venues.index') }}" class="text-xs font-black uppercase tracking-widest text-blue-600 transition hover:text-blue-700">View all</a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($featuredVenues as $venue)
            @php
                $rating = $venue->published_reviews_avg_rating ? number_format($venue->published_reviews_avg_rating, 1) : null;
            @endphp
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    <img src="{{ $venue->primaryImage ? asset('storage/' . $venue->primaryImage->path) : $fallbackVenueImage }}"
                         alt="{{ $venue->name }}"
                         class="h-full w-full object-cover transition duration-500 hover:scale-105">
                    <div class="absolute left-3 top-3 rounded-2xl bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur">
                        {{ $venue->getTypeLabel() }}
                    </div>
                    @if($rating)
                        <div class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-2xl bg-white/90 px-2.5 py-1 text-xs font-black text-slate-800 shadow-sm backdrop-blur">
                            <svg class="h-3.5 w-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $rating }}
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate text-lg font-black text-slate-900">{{ $venue->name }}</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ number_format($venue->capacity) }} capacity</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-black text-blue-600">BDT {{ number_format($venue->hourly_rate, 0) }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">per hour</p>
                        </div>
                    </div>

                    <p class="mt-3 line-clamp-2 min-h-[2.5rem] text-sm leading-5 text-slate-500">{{ $venue->description ?: 'Ready for fast bookings with real-time slot availability.' }}</p>

                    <a href="{{ route('venues.show', $venue) }}" class="mt-4 inline-flex h-10 w-full items-center justify-center rounded-3xl bg-slate-900 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-600">Book now</a>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-white p-12 text-center sm:col-span-2 lg:col-span-3">
                <p class="text-sm font-semibold text-slate-400">No featured venues are available yet.</p>
            </div>
        @endforelse
    </div>
</section>

<section class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Gallery</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Moments on Turf</h2>
            </div>
            <p class="max-w-xl text-sm leading-6 text-slate-500">A quick look at the energy, teams, and match-day atmosphere around the venues.</p>
        </div>

        <div x-data="{ activeImg: null }">
            <div class="grid auto-rows-[160px] grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($galleryImages as $image)
                @php
                    $imageSrc = str_starts_with($image->path, 'images/') ? asset($image->path) : asset('storage/' . $image->path);
                    $tileClass = match ($loop->index % 8) {
                        0 => 'sm:col-span-2 sm:row-span-2',
                        3 => 'lg:col-span-2',
                        5 => 'sm:row-span-2',
                        6 => 'sm:col-span-2',
                        default => '',
                    };
                @endphp
                <button type="button" class="group relative min-h-[220px] overflow-hidden rounded-3xl bg-slate-100 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-300/50 {{ $tileClass }}" @click="activeImg = {src: '{{ $imageSrc }}', title: '{{ $image->title }}', desc: '{{ $image->description }}'}">
                    <img src="{{ $imageSrc }}" alt="{{ $image->title }}" onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=800&q=80'" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent opacity-80 transition group-hover:opacity-100"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5 text-left">
                        <p class="text-[10px] font-black uppercase tracking-[0.24em] text-blue-200">Arena Moment</p>
                        <h3 class="mt-1 text-lg font-black text-white">{{ $image->title }}</h3>
                        @if($image->description)
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-200">{{ $image->description }}</p>
                        @endif
                    </div>
                </button>
            @empty
                @foreach([
                    ['https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=800&q=80', 'Match Night'],
                    ['https://images.unsplash.com/photo-1540747913346-19e3adca174f?auto=format&fit=crop&w=800&q=80', 'Cricket Session'],
                    ['https://images.unsplash.com/photo-1521412644187-c49fa049e84d?auto=format&fit=crop&w=800&q=80', 'Team Play'],
                    ['https://images.unsplash.com/photo-1560272564-c83b66b1ad12?auto=format&fit=crop&w=800&q=80', 'Training Hour'],
                ] as $index => [$src, $title])
                    <button type="button" class="group relative min-h-[220px] overflow-hidden rounded-3xl bg-slate-100 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-300/50 {{ $index === 0 ? 'sm:col-span-2 sm:row-span-2' : '' }}" @click="activeImg = {src: '{{ $src }}', title: '{{ $title }}', desc: ''}">
                        <img src="{{ $src }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>
                        <span class="absolute inset-x-0 bottom-0 p-5 text-left text-lg font-black text-white">{{ $title }}</span>
                    </button>
                @endforeach
            @endforelse
            </div>

            <template x-if="activeImg">
                <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/95 p-5 backdrop-blur" @click="activeImg = null" x-cloak>
                    <button type="button" class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="activeImg = null">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="w-full max-w-6xl" @click.stop>
                        <img :src="activeImg.src" class="max-h-[76vh] w-full rounded-3xl object-contain shadow-2xl">
                        <div class="mt-5 text-center">
                            <h3 class="text-2xl font-black text-white" x-text="activeImg.title"></h3>
                            <p class="mt-1 text-sm text-blue-200" x-text="activeImg.desc"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Fast Booking</p>
            <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Why players book here</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach([
                ['Instant Confirmation', 'Book available slots without waiting for phone calls.', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Real-time Slots', 'Pick the date and hour that works for your team.', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Flexible Payments', 'Use wallet balance or pay manually at the venue.', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v-1'],
            ] as [$title, $desc, $icon])
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-3xl bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
