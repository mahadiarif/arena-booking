@extends('layouts.admin')
@section('title','Utilization Report')
@section('breadcrumb','Reports / Utilization')
@section('content')

@php
  $totalSlots = collect($data)->sum('total_slots');
  $bookedSlots = collect($data)->sum('booked_slots');
  $occupancy = $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100) : 0;
  $revenue = collect($data)->sum('revenue');
@endphp

<div class="space-y-6">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
      <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Reports</p>
      <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Utilization</h1>
      <p class="mt-1 text-sm text-slate-500">{{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</p>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-500 transition hover:text-slate-900">Back to reports</a>
  </div>

  <form method="GET" action="{{ route('admin.reports.utilization') }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
      <x-calendar-picker name="from" :value="$from->toDateString()" label="From" />
      <x-calendar-picker name="to" :value="$to->toDateString()" label="To" />
      <div>
        <label class="mb-2 block px-1 text-[10px] font-black uppercase tracking-widest text-slate-400">Venue</label>
        <select name="venue_id" class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
          <option value="">All Venues</option>
          @foreach($venues as $venue)
            <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-end gap-2">
        <button type="submit" class="inline-flex h-10 flex-1 items-center justify-center rounded-lg bg-blue-600 px-4 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-700">Apply</button>
        <a href="{{ route('admin.reports.export', ['type' => 'utilization', 'from' => $from->toDateString(), 'to' => $to->toDateString(), 'venue_id' => request('venue_id')]) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-[10px] font-black uppercase tracking-widest text-emerald-700 transition hover:bg-emerald-100">Export</a>
      </div>
    </div>
  </form>

  <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Slots</p>
      <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($totalSlots) }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Booked</p>
      <p class="mt-2 text-2xl font-black text-blue-600">{{ number_format($bookedSlots) }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Occupancy</p>
      <p class="mt-2 text-2xl font-black text-slate-900">{{ $occupancy }}%</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Revenue</p>
      <p class="mt-2 text-2xl font-black text-emerald-600">BDT {{ number_format($revenue, 0) }}</p>
    </div>
  </div>

  <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-5 flex items-center justify-between gap-4">
      <h2 class="text-sm font-black uppercase tracking-widest text-slate-700">Occupancy by Venue</h2>
      <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ count($data) }} venues</span>
    </div>
    <div class="space-y-5">
      @forelse($data as $row)
        @php $pct = min(100, $row['occupancy'] ?? 0); @endphp
        <div>
          <div class="mb-2 flex items-end justify-between gap-3">
            <div>
              <p class="text-sm font-black text-slate-900">{{ $row['venue'] }}</p>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $row['booked_slots'] }}/{{ $row['total_slots'] }} slots booked</p>
            </div>
            <p class="text-lg font-black text-slate-900">{{ $pct }}%</p>
          </div>
          <div class="h-3 overflow-hidden rounded-full bg-slate-100">
            <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: {{ $row['color'] ?? '#2563eb' }}"></div>
          </div>
        </div>
      @empty
        <p class="rounded-lg bg-slate-50 p-8 text-center text-sm font-semibold text-slate-400">No data for selected period.</p>
      @endforelse
    </div>
  </section>

  <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
      <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Detailed Breakdown</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <th class="px-5 py-3 text-left">Venue</th>
            <th class="px-4 py-3 text-right">Total Slots</th>
            <th class="px-4 py-3 text-right">Booked</th>
            <th class="px-4 py-3 text-right">Occupancy</th>
            <th class="px-4 py-3 text-right">Revenue</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($data as $row)
            <tr class="hover:bg-slate-50">
              <td class="px-5 py-3 font-bold text-slate-900"><span class="mr-2 inline-block h-2.5 w-2.5 rounded-full" style="background-color: {{ $row['color'] ?? '#2563eb' }}"></span>{{ $row['venue'] }}</td>
              <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row['total_slots'] ?? 0) }}</td>
              <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row['booked_slots'] ?? 0) }}</td>
              <td class="px-4 py-3 text-right font-black text-slate-900">{{ $row['occupancy'] ?? 0 }}%</td>
              <td class="px-4 py-3 text-right font-black text-emerald-600">BDT {{ number_format($row['revenue'] ?? 0, 0) }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">No data for selected period.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
