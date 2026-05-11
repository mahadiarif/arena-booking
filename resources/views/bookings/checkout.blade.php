@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('venues.show', $slot->venue_id) }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Venue
            </a>
            <h1 class="text-3xl font-black text-slate-900 mt-4 tracking-tight">Complete Your <span class="text-blue-600">Booking</span></h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
            {{-- Booking Summary --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
                <div class="bg-slate-900 px-8 py-6 text-white flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Selected Venue</p>
                        <h3 class="text-xl font-bold">{{ $slot->venue->name }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-2 gap-8 mb-8">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Date</p>
                            <p class="font-bold text-slate-800">{{ $slot->date->format('l, d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Time Slot</p>
                            <p class="font-bold text-slate-800">{{ $slot->start_time_formatted }} - {{ $slot->end_time_formatted }}</p>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-500">Booking Fees</span>
                        <span class="text-2xl font-black text-slate-900">৳{{ number_format($slot->venue->base_price, 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Checkout Form --}}
            <form action="{{ route('venues.book', $slot) }}" method="POST" x-data="{payment: 'manual'}" class="space-y-8">
                @csrf
                
                {{-- Payment Method --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Choose Payment Method
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Wallet Option --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="wallet" x-model="payment" class="sr-only">
                            <div class="p-6 rounded-2xl border-2 transition-all duration-200"
                                 :class="payment === 'wallet' ? 'border-blue-600 bg-blue-50/50 shadow-lg shadow-blue-100' : 'border-slate-100 bg-white hover:border-blue-200'">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div x-show="payment === 'wallet'" class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    </div>
                                </div>
                                <h4 class="font-bold text-slate-800">Arena Wallet</h4>
                                <p class="text-xs text-slate-400 mt-1">Available: <span class="text-blue-600 font-bold">৳{{ number_format($customer->credit_balance, 0) }}</span></p>
                                
                                @if($customer->credit_balance < $slot->venue->base_price)
                                <p class="text-[9px] text-red-500 font-bold mt-2 uppercase tracking-tighter">Insufficient Balance</p>
                                @endif
                            </div>
                        </label>

                        {{-- Manual Option --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="manual" x-model="payment" class="sr-only">
                            <div class="p-6 rounded-2xl border-2 transition-all duration-200"
                                 :class="payment === 'manual' ? 'border-slate-800 bg-slate-50 shadow-lg shadow-slate-200' : 'border-slate-100 bg-white hover:border-slate-200'">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div x-show="payment === 'manual'" class="w-5 h-5 bg-slate-800 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                    </div>
                                </div>
                                <h4 class="font-bold text-slate-800">Pay at Venue</h4>
                                <p class="text-xs text-slate-400 mt-1">Cash on Arrival</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Additional Notes (Optional)
                    </h3>
                    <textarea name="notes" rows="3" placeholder="Any special requests or team name?" 
                              class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition resize-none font-medium"></textarea>
                </div>

                <div class="bg-blue-600 rounded-3xl p-1 shadow-xl shadow-blue-200">
                    <button type="submit" 
                            :disabled="payment === 'wallet' && {{ $customer->credit_balance }} < {{ $slot->venue->base_price }}"
                            class="w-full bg-white text-blue-600 hover:bg-blue-50 py-5 rounded-2xl font-black text-xl tracking-tight transition flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirm Booking
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
