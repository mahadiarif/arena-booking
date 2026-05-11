@extends('layouts.admin')
@section('title','Venues')
@section('breadcrumb','Venues')
@section('content')

@php
  $activeCount = $venues->where('is_active', true)->count();
  $totalCapacity = $venues->sum('capacity');
  $totalBookings = $venues->sum('bookings_count');
  $totalSlots = max(1, $venues->sum('slots_count'));
  $overallUtilization = min(100, round(($totalBookings / $totalSlots) * 100));
@endphp

<div class="space-y-6">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
      <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Venue Operations</p>
      <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Venues</h1>
      <p class="mt-1 text-sm text-slate-500">Manage courts, halls, pricing, slot capacity, and operational status.</p>
    </div>

    @can('create venues')
      <a href="{{ route('admin.venues.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Venue
      </a>
    @endcan
  </div>

  <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Venues</p>
      <p class="mt-2 text-2xl font-black text-slate-900">{{ $venues->count() }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Active</p>
      <p class="mt-2 text-2xl font-black text-emerald-600">{{ $activeCount }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Capacity</p>
      <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($totalCapacity) }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Utilization</p>
      <p class="mt-2 text-2xl font-black text-blue-600">{{ $overallUtilization }}%</p>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse($venues as $venue)
      @php
        $slotCount = max(1, $venue->slots_count);
        $utilization = min(100, round(($venue->bookings_count / $slotCount) * 100));
      @endphp

      <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
        <div class="relative h-40 overflow-hidden bg-slate-100">
          @if($venue->primaryImage)
            <img src="{{ $venue->primaryImage->url }}" alt="{{ $venue->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
          @else
            <div class="flex h-full w-full items-center justify-center" style="background-color: {{ $venue->color ?: '#e2e8f0' }}22">
              <svg class="h-14 w-14 opacity-30" style="color: {{ $venue->color ?: '#64748b' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
          @endif

          <div class="absolute left-3 top-3">
            <span class="inline-flex items-center rounded-md bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur">
              {{ $venue->getTypeLabel() }}
            </span>
          </div>

          <div class="absolute right-3 top-3">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-700 shadow-sm backdrop-blur">
              <span class="h-2 w-2 rounded-full {{ $venue->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
              {{ $venue->is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
        </div>

        <div class="p-4">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <h2 class="truncate text-base font-black text-slate-900">{{ $venue->name }}</h2>
              <p class="mt-1 text-xs font-semibold text-slate-500">{{ $venue->schedule?->name ?? 'No schedule assigned' }}</p>
            </div>
            <div class="shrink-0 text-right">
              <p class="text-sm font-black text-blue-600">BDT {{ number_format($venue->hourly_rate, 0) }}</p>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">per hour</p>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-3 gap-2 text-center">
            <div class="rounded-md bg-slate-50 px-2 py-2">
              <p class="text-sm font-black text-slate-900">{{ number_format($venue->capacity) }}</p>
              <p class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-slate-400">Seats</p>
            </div>
            <div class="rounded-md bg-slate-50 px-2 py-2">
              <p class="text-sm font-black text-slate-900">{{ number_format($venue->slots_count) }}</p>
              <p class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-slate-400">Slots</p>
            </div>
            <div class="rounded-md bg-slate-50 px-2 py-2">
              <p class="text-sm font-black text-slate-900">{{ number_format($venue->bookings_count) }}</p>
              <p class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-slate-400">Bookings</p>
            </div>
          </div>

          <div class="mt-4">
            <div class="mb-1.5 flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
              <span>Utilization</span>
              <span class="text-slate-600">{{ $utilization }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
              <div class="h-full rounded-full" style="width: {{ $utilization }}%; background-color: {{ $venue->color ?: '#2563eb' }}"></div>
            </div>
          </div>

          <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('admin.slots.index', ['venue_id' => $venue->id]) }}" class="inline-flex h-9 flex-1 items-center justify-center rounded-md bg-blue-600 px-3 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-700">Slots</a>
            @can('edit venues')
              <a href="{{ route('admin.venues.edit', $venue) }}" class="inline-flex h-9 flex-1 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-[10px] font-black uppercase tracking-widest text-slate-700 transition hover:bg-slate-50">Edit</a>
            @endcan
            @can('delete venues')
              <x-confirm-dialog :action="route('admin.venues.destroy', $venue)" method="DELETE" message="Delete this venue?" btnLabel="Delete" btnClass="h-9 rounded-md border border-rose-200 bg-rose-50 px-3 text-[10px] font-black uppercase tracking-widest text-rose-600 transition hover:bg-rose-100" />
            @endcan
          </div>
        </div>
      </article>
    @empty
      <div class="rounded-lg border-2 border-dashed border-slate-200 bg-white p-12 text-center sm:col-span-2 xl:col-span-3">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-slate-50">
          <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
        </div>
        <p class="mt-4 text-xs font-black uppercase tracking-widest text-slate-400">No venues found in system.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
