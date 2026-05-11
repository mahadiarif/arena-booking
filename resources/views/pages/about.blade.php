@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="bg-white">
    {{-- Hero Section --}}
    <div class="relative py-24 bg-[#0f172a] overflow-hidden min-h-[400px] flex items-center">
        <div class="absolute inset-0 opacity-40">
            <img src="{{ asset('images/about-hero.png') }}" alt="Arena Banner" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] to-transparent"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6">About <span class="text-blue-500">Metro ArenaBook</span></h1>
            <p class="text-xl text-slate-200 max-w-3xl mx-auto leading-relaxed font-medium">
                We are on a mission to revolutionize how sports enthusiasts find and book their favorite arenas. Metro ArenaBook connects you to the best turfs in Bangladesh with just a few clicks.
            </p>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-20 items-center">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 mb-8 border-l-4 border-blue-600 pl-4">Our Story</h2>
                <div class="space-y-6 text-slate-600 leading-relaxed text-lg">
                    <p>
                        Founded in 2024, Metro ArenaBook was born out of a simple frustration: the difficulty of finding and booking sports venues. We realized that many players spend more time making phone calls than actually playing the game.
                    </p>
                    <p>
                        We built Metro ArenaBook to be the bridge between venue owners and players. Our platform provides real-time availability, secure payments, and a seamless booking experience for everyone.
                    </p>
                </div>
                <div class="mt-10 grid grid-cols-2 gap-8">
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div class="text-3xl font-bold text-blue-600 mb-2">50+</div>
                        <div class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Premium Venues</div>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div class="text-3xl font-bold text-blue-600 mb-2">10k+</div>
                        <div class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Happy Players</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-blue-50 rounded-[2rem] -rotate-2"></div>
                <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80" alt="Arena Experience" class="relative rounded-[2rem] shadow-2xl object-cover h-[400px] w-full">
            </div>
        </div>
    </div>

    {{-- Values Section --}}
    <div class="bg-slate-50 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Our Core Values</h2>
                <div class="w-20 h-1.5 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Efficiency</h3>
                    <p class="text-slate-600 leading-relaxed">Book your turf in less than 60 seconds. We value your time as much as you do.</p>
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Reliability</h3>
                    <p class="text-slate-600 leading-relaxed">No double bookings, no surprises. Our real-time system ensures your slot is yours.</p>
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-slate-900">Community</h3>
                    <p class="text-slate-600 leading-relaxed">Building a stronger sports culture by bringing players and venue owners together.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
