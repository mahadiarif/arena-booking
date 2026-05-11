@extends('layouts.admin')
@section('title','Slot Management')
@section('breadcrumb','Slot Management')
@section('content')

{{-- Controls Bar --}}
<div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-5 mb-6">
  <div class="flex flex-wrap items-center gap-3">
    <form method="GET" action="{{ route('admin.slots.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
      {{-- Venue Dropdown --}}
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <select name="venue_id" onchange="this.form.submit()" class="pl-9 pr-8 py-2.5 text-sm border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 font-semibold appearance-none cursor-pointer">
          <option value="">All Venues</option>
          @foreach($venues as $v)
          <option value="{{ $v->id }}" {{ $selectedVenue?->id == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Date Picker --}}
      <div class="w-48">
          <x-calendar-picker 
            name="date" 
            :value="$date->toDateString()" 
            label=""
          />
      </div>
    </form>

    {{-- Date Navigation --}}
    <div class="flex items-center gap-1 bg-slate-50 border border-slate-100 rounded-2xl p-1">
      <a href="{{ route('admin.slots.index', ['date'=>$date->copy()->subDay()->toDateString(), 'venue_id'=>request('venue_id')]) }}"
         class="p-2 text-slate-500 hover:text-blue-600 hover:bg-white rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </a>
      <span class="text-sm font-black text-slate-800 px-3 whitespace-nowrap">{{ $date->format('d M Y') }}</span>
      <a href="{{ route('admin.slots.index', ['date'=>$date->copy()->addDay()->toDateString(), 'venue_id'=>request('venue_id')]) }}"
         class="p-2 text-slate-500 hover:text-blue-600 hover:bg-white rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    {{-- Today Button --}}
    <a href="{{ route('admin.slots.index', ['date'=>now()->toDateString(), 'venue_id'=>request('venue_id')]) }}"
       class="text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2.5 rounded-2xl transition border border-blue-100">
      Today
    </a>
  </div>
</div>

{{-- Stats Row --}}
@if(!$slots->isEmpty())
@php
  $available = $slots->where('status.value','available')->count();
  $booked    = $slots->where('status.value','booked')->count();
  $blocked   = $slots->where('status.value','blocked')->count();
  $total     = $slots->count();
@endphp
<div class="grid grid-cols-4 gap-4 mb-6">
  @foreach([
    ['Total Slots', $total,     'slate',  'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['Available',   $available, 'green',  'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['Booked',      $booked,    'blue',   'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['Blocked',     $blocked,   'red',    'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
  ] as [$label,$count,$color,$icon])
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0
      {{ $color==='green'?'bg-green-50 text-green-600' : ($color==='blue'?'bg-blue-50 text-blue-600' : ($color==='red'?'bg-red-50 text-red-500':'bg-slate-50 text-slate-500')) }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
    </div>
    <div>
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $label }}</p>
      <p class="text-2xl font-black text-slate-800">{{ $count }}</p>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- Generate Slots Panel --}}
<div x-data="{gen:false}" class="mb-6">
  <button @click="gen=!gen"
          class="flex items-center gap-2 text-sm font-bold bg-slate-800 hover:bg-slate-700 text-white px-5 py-3 rounded-2xl transition shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
    Generate Slots
  </button>
  <div x-show="gen" x-cloak x-transition class="mt-3 bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.slots.generate') }}" class="flex flex-wrap items-end gap-4">
      @csrf
      @if(request('venue_id'))<input type="hidden" name="venue_id" value="{{ request('venue_id') }}">@endif
      <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Days Ahead</label>
        <input type="number" name="days" value="7" min="1" max="90"
               class="w-28 text-sm border border-slate-200 rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
      </div>
      <label class="flex items-center gap-2 text-sm font-semibold text-slate-600 cursor-pointer mb-0.5">
        <input type="checkbox" name="force" value="1" class="w-4 h-4 rounded accent-blue-600"> Force Overwrite
      </label>
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2.5 rounded-2xl transition shadow-md shadow-blue-100">
        Generate Now
      </button>
    </form>
  </div>
</div>

{{-- Slot Grid --}}
@if($slots->isEmpty())
<div class="text-center py-24 bg-white rounded-3xl border border-slate-100 shadow-sm">
  <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  </div>
  <h3 class="text-xl font-bold text-slate-700 mb-2">No Slots Found</h3>
  <p class="text-slate-400 text-sm">No slots exist for this date. Use "Generate Slots" above to create them.</p>
</div>
@else

{{-- Group slots by venue --}}
@php $byVenue = $slots->groupBy(fn($s) => $s->venue->name); @endphp

@foreach($byVenue as $venueName => $venueSlots)
<div class="mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center">
      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
    </div>
    <h3 class="text-lg font-black text-slate-800">{{ $venueName }}</h3>
    <span class="text-xs font-bold text-slate-400">{{ $venueSlots->count() }} slots</span>
    <div class="flex-1 h-px bg-slate-100 ml-2"></div>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($venueSlots as $slot)
    @php
      $statusValue = $slot->status instanceof \App\Enums\SlotStatus ? $slot->status->value : $slot->status;
      $isAvailable = $statusValue === 'available';
      $isBooked    = $statusValue === 'booked' || $statusValue === 'partial';
      $isBlocked   = $statusValue === 'blocked';
    @endphp
    <div x-data="{loading:false}"
         class="relative rounded-3xl border-2 shadow-sm overflow-hidden transition-all duration-200 group
           {{ $isAvailable ? 'bg-emerald-50/40 border-emerald-100 hover:border-emerald-300 hover:shadow-lg hover:-translate-y-0.5' : '' }}
           {{ $isBooked    ? 'bg-blue-50/40 border-blue-100' : '' }}
           {{ $isBlocked   ? 'bg-slate-50 border-slate-200 opacity-60' : '' }}">

      {{-- Status Top Bar --}}
      <div class="h-1.5 w-full
        {{ $isAvailable ? 'bg-emerald-400' : '' }}
        {{ $isBooked    ? 'bg-blue-500'  : '' }}
        {{ $isBlocked   ? 'bg-slate-400' : '' }}"></div>

      <div class="p-5">
        {{-- Time --}}
        <div class="mb-3">
          <p class="text-lg font-black text-slate-800 leading-none">
            {{ \Carbon\Carbon::createFromTimeString($slot->start_time)->format('g:i') }}
            <span class="text-xs font-bold text-slate-400">{{ \Carbon\Carbon::createFromTimeString($slot->start_time)->format('A') }}</span>
          </p>
          <p class="text-[10px] font-bold text-slate-400 mt-0.5">
            Until {{ \Carbon\Carbon::createFromTimeString($slot->end_time)->format('g:i A') }}
          </p>
        </div>

        {{-- Status Badge --}}
        <div class="mb-3">
          <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full
            {{ $isAvailable ? 'bg-green-100 text-green-700' : '' }}
            {{ $isBooked    ? 'bg-blue-100 text-blue-700'   : '' }}
            {{ $isBlocked   ? 'bg-slate-100 text-slate-500' : '' }}">
            <span class="w-1.5 h-1.5 rounded-full
              {{ $isAvailable ? 'bg-green-500' : '' }}
              {{ $isBooked    ? 'bg-blue-500'  : '' }}
              {{ $isBlocked   ? 'bg-slate-400' : '' }}"></span>
            {{ ucfirst($statusValue) }}
          </span>
        </div>

        {{-- Booking Info if Booked --}}
        @if($isBooked)
          @foreach($slot->bookings->whereNotIn('status', ['cancelled','no_show']) as $bk)
          <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-2">
            <a href="{{ route('admin.bookings.show', $bk) }}"
               class="font-bold text-blue-700 hover:underline text-[11px] block">{{ $bk->booking_ref }}</a>
            <p class="text-[10px] text-slate-500 mt-0.5">{{ $bk->customer?->name }}</p>
          </div>
          @endforeach
          <p class="text-[10px] text-slate-400 font-semibold">
            {{ $slot->current_bookings }}/{{ $slot->max_bookings }} spot(s) filled
          </p>
        @endif

        {{-- Actions --}}
        <div class="mt-4 flex flex-col gap-2">
          @if($isAvailable)
          <a href="{{ route('admin.bookings.create', ['slot_id'=>$slot->id]) }}"
             class="block text-center text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-2xl transition shadow-sm shadow-blue-100">
            + Book Now
          </a>
          @endif

          @if(!$isBooked)
            @if(!$isBlocked)
            <button :disabled="loading" @click="
                loading=true;
                fetch('{{ route('admin.slots.block', $slot) }}', {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
                .then(r=>r.json()).then(d=>{ if(d.success) window.location.reload(); else alert(d.message); loading=false; })
                .catch(()=>loading=false)"
                class="text-xs font-bold bg-slate-50 hover:bg-slate-100 text-slate-600 py-2 rounded-2xl transition border border-slate-100"
                :class="loading?'opacity-50 cursor-wait':''">
              Block Slot
            </button>
            @else
            <button :disabled="loading" @click="
                loading=true;
                fetch('{{ route('admin.slots.unblock', $slot) }}', {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
                .then(r=>r.json()).then(d=>{ if(d.success) window.location.reload(); loading=false; })
                .catch(()=>loading=false)"
                class="text-xs font-bold bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-2xl transition border border-green-100">
              Unblock
            </button>
            @endif
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endforeach
@endif

@endsection