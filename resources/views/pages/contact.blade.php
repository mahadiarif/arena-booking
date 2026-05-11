@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
@php
    $address = \App\Models\Setting::get('app.address', 'Dhaka, Bangladesh');
    $phone = \App\Models\Setting::get('app.phone', '') ?: '+880 1XXX-XXXXXX';
    $email = \App\Models\Setting::get('app.email', '') ?: 'support@metroarenabook.com';
@endphp

<section class="bg-slate-950">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-300">Contact</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight text-white md:text-5xl">Need help with a booking or venue setup?</h1>
            <p class="mt-4 text-base leading-7 text-slate-300">Reach the ArenaBook team for booking support, venue onboarding, or operational questions.</p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-16">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
        <div class="space-y-4">
            @foreach([
                ['Address', $address, 'M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                ['Email', $email, 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['Phone', $phone, 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
            ] as [$label, $value, $icon])
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-3xl bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $value }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-sm">
                <h2 class="text-lg font-black">Business Hours</h2>
                <div class="mt-4 space-y-2 text-sm text-slate-300">
                    <div class="flex justify-between gap-4"><span>Mon - Fri</span><span class="font-bold text-white">9:00 AM - 9:00 PM</span></div>
                    <div class="flex justify-between gap-4"><span>Sat - Sun</span><span class="font-bold text-white">8:00 AM - 11:00 PM</span></div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-8">
            <div class="mb-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Message</p>
                <h2 class="mt-1 text-2xl font-black text-slate-900">Send an inquiry</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">This form prepares your message in email so it reaches the support inbox with your details included.</p>
            </div>

            <form action="mailto:{{ $email }}" method="POST" enctype="text/plain" class="space-y-5">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-widest text-slate-500">Full Name</label>
                        <input name="name" type="text" required class="h-11 w-full rounded-3xl border border-slate-200 bg-white px-4 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Your name">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-widest text-slate-500">Email Address</label>
                        <input name="email" type="email" required class="h-11 w-full rounded-3xl border border-slate-200 bg-white px-4 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="you@example.com">
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-widest text-slate-500">Subject</label>
                    <input name="subject" type="text" required class="h-11 w-full rounded-3xl border border-slate-200 bg-white px-4 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Booking inquiry">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-widest text-slate-500">Message</label>
                    <textarea name="message" rows="5" required class="w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="How can we help?"></textarea>
                </div>
                <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-3xl bg-blue-600 px-6 text-xs font-black uppercase tracking-widest text-white transition hover:bg-blue-700">Send Message</button>
            </form>
        </div>
    </div>
</section>
@endsection
