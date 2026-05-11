@extends('layouts.admin')
@section('title','Revenue Report')
@section('breadcrumb','Reports — Revenue')
@section('content')

<form method="GET" action="{{ route('admin.reports.revenue') }}" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-4 mb-5 flex flex-wrap items-center gap-3">
  <div><label class="text-xs font-semibold text-slate-400 block mb-1">From</label>
    <input type="date" name="from" value="{{ $from->toDateString() }}" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></div>
  <div><label class="text-xs font-semibold text-slate-400 block mb-1">To</label>
    <input type="date" name="to" value="{{ $to->toDateString() }}" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></div>
  <div><label class="text-xs font-semibold text-slate-400 block mb-1">Venue</label>
    <select name="venue_id" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
      <option value="">All Venues</option>
      @foreach($venues as $v)<option value="{{ $v->id }}" {{ request('venue_id')==$v->id?'selected':'' }}>{{ $v->name }}</option>@endforeach
    </select></div>
  <div class="flex items-end gap-2">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">Apply</button>
  </div>
  <div class="ml-auto flex gap-2">
    <a href="{{ route('admin.reports.export', ['type'=>'revenue','from'=>$from->toDateString(),'to'=>$to->toDateString(),'venue_id'=>request('venue_id')]) }}"
       class="text-xs font-semibold bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-xl transition flex items-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Export Excel
    </a>
  </div>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  @php
    $totalBookings = collect($data)->sum('bookings');
    $totalAmount   = collect($data)->sum('total_amount');
    $totalCollected= collect($data)->sum('collected');
    $totalOutstanding = collect($data)->sum('outstanding');
  @endphp
  @foreach([['Bookings', number_format($totalBookings), 'blue'],['Total Billed','৳'.number_format($totalAmount,0),'indigo'],['Collected','৳'.number_format($totalCollected,0),'green'],['Outstanding','৳'.number_format($totalOutstanding,0),$totalOutstanding>0?'red':'slate']] as [$l,$v,$c])
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $l }}</p>
    <p class="text-xl font-bold mt-1 {{ $c==='red'?'text-red-700':($c==='green'?'text-green-700':'text-slate-800') }}">{{ $v }}</p>
  </div>
  @endforeach
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Revenue Breakdown</h3></div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead><tr class="bg-slate-50/50 border-b border-slate-100">
        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Date</th>
        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Venue</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Bookings</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Total</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Collected</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Outstanding</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($data as $row)
        <tr class="hover:bg-slate-50/50 transition">
          <td class="px-5 py-3 text-slate-600 text-xs font-medium">{{ \Carbon\Carbon::parse($row['date']??now())->format('d M Y') }}</td>
          <td class="px-4 py-3 font-medium text-slate-800">{{ $row['venue'] ?? '—' }}</td>
          <td class="px-4 py-3 text-right text-slate-600">{{ $row['bookings'] ?? 0 }}</td>
          <td class="px-4 py-3 text-right font-semibold text-slate-800">৳{{ number_format($row['total_amount']??0,0) }}</td>
          <td class="px-4 py-3 text-right font-semibold text-green-700">৳{{ number_format($row['collected']??0,0) }}</td>
          <td class="px-4 py-3 text-right font-semibold {{ ($row['outstanding']??0)>0?'text-red-600':'text-slate-300' }}">
            {{ ($row['outstanding']??0)>0?'৳'.number_format($row['outstanding'],0):'—' }}
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400 text-sm">No data for selected period.</td></tr>
        @endforelse
      </tbody>
      @if(count($data)>0)
      <tfoot><tr class="bg-slate-50 border-t-2 border-slate-200">
        <td colspan="2" class="px-5 py-3 font-bold text-slate-800">Totals</td>
        <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format($totalBookings) }}</td>
        <td class="px-4 py-3 text-right font-bold text-slate-800">৳{{ number_format($totalAmount,0) }}</td>
        <td class="px-4 py-3 text-right font-bold text-green-700">৳{{ number_format($totalCollected,0) }}</td>
        <td class="px-4 py-3 text-right font-bold {{ $totalOutstanding>0?'text-red-600':'text-slate-300' }}">৳{{ number_format($totalOutstanding,0) }}</td>
      </tr></tfoot>
      @endif
    </table>
  </div>
</div>
@endsection