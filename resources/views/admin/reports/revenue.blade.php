@extends('layouts.admin')
@section('title','Revenue Report')
@section('breadcrumb','Reports / Revenue')
@section('content')

@php
  $totalBookings = collect($data)->sum('bookings');
  $totalAmount = collect($data)->sum('total_amount');
  $totalCollected = collect($data)->sum('collected');
  $totalOutstanding = collect($data)->sum('outstanding');
@endphp

<div class="space-y-6">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
      <p class="text-[11px] font-black uppercase tracking-[0.24em] text-emerald-600">Reports</p>
      <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Revenue</h1>
      <p class="mt-1 text-sm text-slate-500">{{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</p>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-500 transition hover:text-slate-900">Back to reports</a>
  </div>

  <form method="GET" action="{{ route('admin.reports.revenue') }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
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
        <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'from' => $from->toDateString(), 'to' => $to->toDateString(), 'venue_id' => request('venue_id')]) }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-[10px] font-black uppercase tracking-widest text-emerald-700 transition hover:bg-emerald-100">Export</a>
      </div>
    </div>
  </form>

  <div class="grid grid-cols-2 gap-3 xl:grid-cols-4">
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Bookings</p>
      <p class="mt-2 text-2xl font-black text-slate-900">{{ number_format($totalBookings) }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Billed</p>
      <p class="mt-2 text-2xl font-black text-slate-900">BDT {{ number_format($totalAmount, 0) }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Collected</p>
      <p class="mt-2 text-2xl font-black text-emerald-600">BDT {{ number_format($totalCollected, 0) }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Outstanding</p>
      <p class="mt-2 text-2xl font-black {{ $totalOutstanding > 0 ? 'text-rose-600' : 'text-slate-900' }}">BDT {{ number_format($totalOutstanding, 0) }}</p>
    </div>
  </div>

  <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
      <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Revenue Breakdown</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <th class="px-5 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Venue</th>
            <th class="px-4 py-3 text-right">Bookings</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-right">Collected</th>
            <th class="px-4 py-3 text-right">Outstanding</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($data as $row)
            <tr class="hover:bg-slate-50">
              <td class="px-5 py-3 font-semibold text-slate-600">{{ \Carbon\Carbon::parse($row['date'] ?? now())->format('d M Y') }}</td>
              <td class="px-4 py-3 font-bold text-slate-900"><span class="mr-2 inline-block h-2.5 w-2.5 rounded-full" style="background-color: {{ $row['color'] ?? '#2563eb' }}"></span>{{ $row['venue'] ?? '-' }}</td>
              <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row['bookings'] ?? 0) }}</td>
              <td class="px-4 py-3 text-right font-black text-slate-900">BDT {{ number_format($row['total_amount'] ?? 0, 0) }}</td>
              <td class="px-4 py-3 text-right font-black text-emerald-600">BDT {{ number_format($row['collected'] ?? 0, 0) }}</td>
              <td class="px-4 py-3 text-right font-black {{ ($row['outstanding'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                {{ ($row['outstanding'] ?? 0) > 0 ? 'BDT ' . number_format($row['outstanding'], 0) : '-' }}
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">No data for selected period.</td></tr>
          @endforelse
        </tbody>
        @if(count($data) > 0)
          <tfoot>
            <tr class="border-t border-slate-200 bg-slate-50 font-black text-slate-900">
              <td colspan="2" class="px-5 py-3">Totals</td>
              <td class="px-4 py-3 text-right">{{ number_format($totalBookings) }}</td>
              <td class="px-4 py-3 text-right">BDT {{ number_format($totalAmount, 0) }}</td>
              <td class="px-4 py-3 text-right text-emerald-600">BDT {{ number_format($totalCollected, 0) }}</td>
              <td class="px-4 py-3 text-right {{ $totalOutstanding > 0 ? 'text-rose-600' : 'text-slate-400' }}">BDT {{ number_format($totalOutstanding, 0) }}</td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>
  </div>
</div>
@endsection
