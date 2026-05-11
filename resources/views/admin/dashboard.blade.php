@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

{{-- ── ROW 1: KPI CARDS ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

  {{-- Today's Bookings --}}
  <div class="bg-blue-50/30 rounded-3xl border border-blue-100 shadow-sm p-6 flex items-center gap-5 relative overflow-hidden">
    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 shrink-0">
      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Today's Bookings</p>
      <p class="text-3xl font-black text-slate-900">{{ $todaysBookingsCount }}</p>
      <p class="text-[10px] text-slate-400 mt-1">{{ $todaySlotsBooked }}/{{ $todaySlotsTotal }} slots filled today</p>
    </div>
    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full opacity-60"></div>
  </div>

  {{-- Monthly Revenue --}}
  <div class="bg-green-50/30 rounded-3xl border border-green-100 shadow-sm p-6 flex items-center gap-5 relative overflow-hidden">
    <div class="w-14 h-14 bg-green-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200 shrink-0">
      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">This Month's Revenue</p>
      <p class="text-3xl font-black text-slate-900">৳{{ number_format($monthlyRevenue, 0) }}</p>
      @if($revenueGrowth != 0)
      <p class="text-[10px] mt-1 font-bold {{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
        {{ $revenueGrowth >= 0 ? '▲' : '▼' }} {{ abs($revenueGrowth) }}% vs last month
      </p>
      @else
      <p class="text-[10px] text-slate-400 mt-1">No data for last month</p>
      @endif
    </div>
    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-green-50 rounded-full opacity-60"></div>
  </div>

  {{-- Outstanding Due --}}
  <div class="{{ $totalDue > 0 ? 'bg-red-50/30 border-red-100' : 'bg-slate-50/30 border-slate-100' }} rounded-3xl border shadow-sm p-6 flex items-center gap-5 relative overflow-hidden">
    <div class="w-14 h-14 {{ $totalDue > 0 ? 'bg-red-500' : 'bg-slate-200' }} rounded-2xl flex items-center justify-center shadow-lg {{ $totalDue > 0 ? 'shadow-red-200' : '' }} shrink-0">
      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Outstanding Due</p>
      <p class="text-3xl font-black {{ $totalDue > 0 ? 'text-red-600' : 'text-slate-900' }}">৳{{ number_format($totalDue, 0) }}</p>
      <p class="text-[10px] text-slate-400 mt-1">Across active bookings</p>
    </div>
    <div class="absolute -right-4 -bottom-4 w-24 h-24 {{ $totalDue > 0 ? 'bg-red-50' : 'bg-slate-50' }} rounded-full opacity-60"></div>
  </div>

  {{-- Customers --}}
  <div class="bg-purple-50/30 rounded-3xl border border-purple-100 shadow-sm p-6 flex items-center gap-5 relative overflow-hidden">
    <div class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200 shrink-0">
      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Customers</p>
      <p class="text-3xl font-black text-slate-900">{{ number_format($activeCustomersCount) }}</p>
      @if($newCustomersThisMonth > 0)
      <p class="text-[10px] text-purple-500 mt-1 font-bold">+{{ $newCustomersThisMonth }} new this month</p>
      @else
      <p class="text-[10px] text-slate-400 mt-1">No new signups this month</p>
      @endif
    </div>
    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-50 rounded-full opacity-60"></div>
  </div>

</div>

{{-- ── ROW 2: TODAY'S SLOTS + UTILIZATION ────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mb-6">

  {{-- Today's Slots Panel --}}
  <div class="xl:col-span-3 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50">
      <div>
        <h2 class="text-base font-black text-slate-800">Today's Slots</h2>
        <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ now()->format('l, d F Y') }}</p>
      </div>
      <div class="flex items-center gap-3">
        {{-- Slot Stats Pills --}}
        <div class="hidden sm:flex items-center gap-2">
          <span class="flex items-center gap-1 text-[10px] font-bold bg-green-50 text-green-600 px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>{{ $todaySlotsAvailable }} Free
          </span>
          <span class="flex items-center gap-1 text-[10px] font-bold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>{{ $todaySlotsBooked }} Booked
          </span>
        </div>
        <select onchange="window.location='{{ route('admin.dashboard') }}?venue_id='+this.value"
                class="text-xs border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 font-semibold cursor-pointer">
          @foreach(App\Models\Venue::active()->orderBy('sort_order')->get() as $v)
          <option value="{{ $v->id }}" {{ $venueId == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-6">
      @if($dailyView->isEmpty())
        <div class="text-center py-12">
          <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <p class="text-slate-400 text-sm font-medium">No slots generated for today.</p>
          <a href="{{ route('admin.slots.index') }}" class="inline-block mt-3 text-xs font-bold text-blue-600 hover:underline">Go to Slot Manager →</a>
        </div>
      @else
      <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-4 gap-2.5">
        @foreach($dailyView as $slot)
        @php
          $sv       = $slot->status instanceof \App\Enums\SlotStatus ? $slot->status->value : $slot->status;
          $bookable = $sv === 'available';
          $isBooked = $sv === 'booked' || $sv === 'partial';
          $link     = route('admin.bookings.create', ['slot_id' => $slot->id]);
        @endphp
        <a href="{{ $bookable ? $link : '#' }}"
           class="group flex flex-col items-center p-3.5 rounded-2xl border-2 text-center transition-all duration-200
                  {{ $bookable ? 'bg-emerald-50/40 border-emerald-100 hover:border-emerald-300 hover:bg-emerald-50 hover:-translate-y-0.5 hover:shadow-md cursor-pointer' : 'cursor-default' }}
                  {{ $isBooked ? 'border-blue-100 bg-blue-50/50' : '' }}
                  {{ $sv === 'blocked' ? 'border-slate-100 bg-slate-50 opacity-50' : '' }}">
          <span class="text-sm font-black text-slate-800 mb-1">
            {{ \Carbon\Carbon::createFromTimeString($slot->start_time)->format('g:i') }}
            <span class="text-[9px] font-bold text-slate-400">{{ \Carbon\Carbon::createFromTimeString($slot->start_time)->format('A') }}</span>
          </span>
          <span class="text-[9px] font-bold px-2 py-0.5 rounded-full mt-0.5
            {{ $bookable ? 'bg-green-100 text-green-600' : '' }}
            {{ $isBooked ? 'bg-blue-100 text-blue-600'  : '' }}
            {{ $sv==='blocked' ? 'bg-slate-100 text-slate-400' : '' }}">
            {{ ucfirst($sv) }}
          </span>
          @if($bookable)
          <span class="text-[9px] text-blue-500 mt-1 opacity-0 group-hover:opacity-100 transition font-bold">Book →</span>
          @endif
        </a>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  {{-- Venue Utilization --}}
  <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-50">
      <h2 class="text-base font-black text-slate-800">Venue Utilization</h2>
      <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ now()->format('F Y') }}</p>
    </div>
    <div class="p-6 space-y-6">
      @forelse($venueUtilization as $item)
      <div>
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2 min-w-0">
            <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $item['color'] ?? '#3b82f6' }}"></span>
            <span class="text-sm font-bold text-slate-700 truncate">{{ $item['venue'] }}</span>
          </div>
          <span class="text-sm font-black text-slate-800 ml-2">{{ $item['occupancy'] ?? 0 }}%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-700"
               style="width: {{ min($item['occupancy'] ?? 0, 100) }}%; background-color: {{ $item['color'] ?? '#3b82f6' }}"></div>
        </div>
        <div class="flex items-center justify-between mt-1.5">
          <p class="text-[10px] text-slate-400">{{ $item['booked_slots'] ?? 0 }} / {{ $item['total_slots'] ?? 0 }} slots</p>
          <p class="text-[10px] font-bold text-slate-500">৳{{ number_format($item['revenue'] ?? 0) }}</p>
        </div>
      </div>
      @empty
      <div class="text-center py-10">
        <p class="text-sm text-slate-400">No venue data available for this month.</p>
      </div>
      @endforelse
    </div>
  </div>
</div>

{{-- ── ROW 3: RECENT BOOKINGS ─────────────────────────────────────────────────── --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50">
    <div>
      <h2 class="text-base font-black text-slate-800">Recent Bookings</h2>
      <p class="text-[10px] text-slate-400 font-bold mt-0.5">Last 10 transactions</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}"
       class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition">
      View All
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50/70 border-b border-slate-100">
          <th class="text-left px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ref</th>
          <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Customer</th>
          <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Venue</th>
          <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date & Time</th>
          <th class="text-right px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Amount</th>
          <th class="text-right px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Due</th>
          <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
          <th class="text-left px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($recentBookings as $booking)
        <tr class="hover:bg-blue-50/30 transition group">
          <td class="px-6 py-4">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono text-xs font-bold text-blue-600 hover:underline">
              {{ $booking->booking_ref }}
            </a>
          </td>
          <td class="px-4 py-4">
            <p class="font-bold text-slate-800 text-sm">{{ $booking->customer?->name ?? '—' }}</p>
            <p class="text-[10px] text-slate-400">{{ $booking->customer?->phone }}</p>
          </td>
          <td class="px-4 py-4">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color:{{ $booking->venue?->color ?? '#94a3b8' }}"></span>
              <span class="text-slate-700 font-semibold text-sm">{{ $booking->venue?->name ?? '—' }}</span>
            </div>
          </td>
          <td class="px-4 py-4">
            <p class="text-sm font-semibold text-slate-700">{{ $booking->slot?->date?->format('d M Y') ?? '—' }}</p>
            <p class="text-[10px] text-slate-400">
              {{ $booking->slot ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A') : '' }}
            </p>
          </td>
          <td class="px-4 py-4 text-right">
            <span class="font-bold text-slate-800">৳{{ number_format($booking->total_amount, 0) }}</span>
          </td>
          <td class="px-4 py-4 text-right">
            <span class="font-bold {{ $booking->due_amount > 0 ? 'text-red-500' : 'text-slate-300' }}">
              {{ $booking->due_amount > 0 ? '৳'.number_format($booking->due_amount, 0) : '—' }}
            </span>
          </td>
          <td class="px-4 py-4">
            <x-status-pill :status="$booking->status" />
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-1.5">
              <a href="{{ route('admin.bookings.show', $booking) }}"
                 class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="View">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </a>
              @if($booking->status->value === 'pending')
              <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">@csrf
                <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition" title="Confirm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
              </form>
              @endif
              @if($booking->status->value === 'confirmed')
              <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}">@csrf
                <button class="w-8 h-8 flex items-center justify-center rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-100 transition" title="Check In">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-6 py-16 text-center">
            <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-slate-400 text-sm font-medium">No bookings found.</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
