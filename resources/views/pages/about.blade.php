@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<section class="relative overflow-hidden bg-slate-950">
    <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1800&q=80')"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/85 to-slate-900/40"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-300">About Metro ArenaBook</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-white md:text-6xl">A simpler way to get teams onto the field</h1>
            <p class="mt-5 text-lg leading-8 text-slate-200">Metro ArenaBook connects players with active sports venues, real-time slots, and clear pricing so more time goes into playing and less into coordination.</p>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Our Story</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Built for players, organizers, and venue teams</h2>
            <div class="mt-6 space-y-5 text-base leading-8 text-slate-600">
                <p>Booking a turf should not depend on repeated calls, unclear availability, or manual follow-ups. This platform keeps venue discovery, slot selection, and booking flow in one place.</p>
                <p>For venue owners, ArenaBook provides the operational tools needed to manage schedules, payments, customers, waitlists, and reports without losing visibility.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-3xl font-black text-blue-600">Live</p>
                <p class="mt-2 text-xs font-black uppercase tracking-widest text-slate-500">Slot visibility</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-3xl font-black text-blue-600">BDT</p>
                <p class="mt-2 text-xs font-black uppercase tracking-widest text-slate-500">Clear pricing</p>
            </div>
            <div class="col-span-2 overflow-hidden rounded-3xl">
                <img src="https://images.unsplash.com/photo-1521412644187-c49fa049e84d?auto=format&fit=crop&w=1200&q=80" alt="Players on a sports field" class="h-72 w-full object-cover">
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">What We Care About</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Operational values that matter on match day</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach([
                ['Efficiency', 'Book and manage slots quickly, with fewer manual steps.', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Reliability', 'Reduce double booking risk with centralized availability.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04'],
                ['Community', 'Help teams, players, and venues stay connected around sport.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857'],
            ] as [$title, $desc, $icon])
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-3xl bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
