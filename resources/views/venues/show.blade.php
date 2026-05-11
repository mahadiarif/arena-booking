@extends('layouts.app')

@section('title', $venue->name)

@section('content')
<div class="bg-slate-50 min-h-screen pb-20">
    {{-- Image Gallery & Header --}}
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Gallery --}}
                <div class="w-full lg:w-2/3" x-data="{activeImg: '{{ $venue->primaryImage ? asset('storage/' . $venue->primaryImage->path) : 'https://images.unsplash.com/photo-1459865264687-595d652de67e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}'}">
                    <div class="aspect-video rounded-3xl overflow-hidden bg-slate-100 mb-4 shadow-inner border border-slate-100">
                        <img :src="activeImg" class="w-full h-full object-cover transition duration-500">
                    </div>
                    @if($venue->images->count() > 1)
                    <div class="grid grid-cols-5 gap-3">
                        @foreach($venue->images as $img)
                        <button @click="activeImg = '{{ asset('storage/' . $img->path) }}'" 
                                class="aspect-square rounded-xl overflow-hidden border-2 transition"
                                :class="activeImg === '{{ asset('storage/' . $img->path) }}' ? 'border-blue-600' : 'border-transparent hover:border-blue-200'">
                            <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Venue Info Summary --}}
                <div class="w-full lg:w-1/3 flex flex-col justify-center">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-blue-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Verified Venue</span>
                            <div class="flex items-center gap-1 text-amber-500 font-bold">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ number_format($venue->average_rating, 1) }}</span>
                                <span class="text-xs text-slate-400 font-normal">({{ $venue->review_count }} reviews)</span>
                            </div>
                        </div>
                        <h1 class="text-3xl font-extrabold text-slate-900 mb-2">{{ $venue->name }}</h1>
                        <div class="flex items-center gap-1.5 text-slate-500 mb-6">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-medium">{{ $venue->location }}</span>
                        </div>
                    </div>

                    <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 mb-8">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-600 uppercase tracking-wider">Hourly Rate</span>
                            <span class="text-3xl font-black text-blue-600">৳{{ number_format($venue->base_price, 0) }}</span>
                        </div>
                    </div>

                    <a href="#booking-section" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 transition btn-3d">Check Availability</a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            {{-- Left Side: Details & Reviews --}}
            <div class="w-full lg:w-2/3 space-y-12">
                {{-- Description --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Venue Description
                    </h3>
                    <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $venue->description }}</p>
                </div>

                {{-- Booking Section (Anchor) --}}
                <div id="booking-section" class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <svg class="w-32 h-32 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19 19H5V8h14m-4-7v2H9V1H7v2H5c-1.11 0-2 .89-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2h-2V1"/></svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                        Select Date & Time
                    </h3>

                    <div x-data="{date: '{{ date('Y-m-d') }}', slots: [], loading: false, 
                        fetchSlots() {
                            this.loading = true;
                            fetch('{{ route('calendar.index') }}?venue_id={{ $venue->id }}&date=' + this.date)
                                .then(res => res.json())
                                .then(data => {
                                    this.slots = data.slots;
                                    this.loading = false;
                                });
                        }
                    }" x-init="fetchSlots()">
                        
                        <div class="flex flex-col md:flex-row gap-6 mb-8">
                            <div class="w-full md:w-64" @date-selected="date = $event.detail; fetchSlots()">
                                <x-calendar-picker 
                                    name="date" 
                                    :value="date('Y-m-d')" 
                                    label="Choose Date" 
                                    :minDate="date('Y-m-d')"
                                />
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-bold text-slate-400 uppercase mb-2 block">Available Slots</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <template x-if="loading">
                                        <div class="col-span-full py-10 text-center">
                                            <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto"></div>
                                        </div>
                                    </template>
                                    <template x-for="slot in slots" :key="slot.id">
                                        <button :disabled="slot.status !== 'available'" 
                                                @click="window.location.href = '/venues/slots/' + slot.id + '/checkout'"
                                                class="px-4 py-3 rounded-2xl text-xs font-bold transition border-2 text-center"
                                                :class="slot.status === 'available' 
                                                    ? 'bg-blue-50 border-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-sm' 
                                                    : 'bg-slate-50 border-slate-50 text-slate-300 cursor-not-allowed'">
                                            <span x-text="slot.start_time_formatted"></span>
                                        </button>
                                    </template>
                                </div>
                                <template x-if="!loading && slots.length === 0">
                                    <p class="py-10 text-center text-slate-400 text-sm">No slots available for this date.</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 text-amber-700 text-xs flex gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Select a slot to proceed with booking. You can pay via your credit balance or at the venue after confirmation.</p>
                    </div>
                </div>

                {{-- Reviews --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        What Players Say
                    </h3>
                    
                    <div class="space-y-8">
                        @forelse($venue->reviews->where('is_published', true) as $review)
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center font-bold text-slate-400 shrink-0 uppercase">
                                {{ substr($review->customer->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-bold text-slate-800">{{ $review->customer->name }}</h4>
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-0.5 text-amber-400 mb-2">
                                    @for($i=1; $i<=5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'fill-slate-100' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-sm text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <p class="text-slate-400 text-sm">No reviews yet for this venue.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Side: Information & Location --}}
            <div class="w-full lg:w-1/3 space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6">Venue Rules</h3>
                    <ul class="space-y-4">
                        @foreach([
                            ['Standard shoes only (no metal studs)', 'M9 12l2 2 4-4'],
                            ['Cancellation 24h prior for full refund', 'M9 12l2 2 4-4'],
                            ['Arrive 10 minutes before your slot', 'M9 12l2 2 4-4'],
                            ['Respect the arena time limits', 'M9 12l2 2 4-4']
                        ] as [$rule, $icon])
                        <li class="flex items-start gap-3 text-sm text-slate-600">
                            <div class="w-5 h-5 bg-green-50 text-green-600 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                            </div>
                            {{ $rule }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-slate-900 rounded-3xl p-8 text-white">
                    <h3 class="text-lg font-bold mb-6">Need Help?</h3>
                    <p class="text-slate-400 text-sm mb-8 leading-relaxed">Have questions about this venue or need help with a large booking?</p>
                    <a href="tel:+880123456789" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 p-4 rounded-2xl transition mb-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Call us</p>
                            <p class="font-bold">+880 123 456 789</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
