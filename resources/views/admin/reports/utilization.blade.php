@extends('layouts.admin')
@section('title','Utilization Report')
@section('breadcrumb','Reports — Utilization')
@section('content')

{{-- Filters --}}
<form method="GET" action="{{ route('admin.reports.utilization') }}" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-4 mb-5 flex flex-wrap items-center gap-3">
  <div><label class="text-xs font-semibold text-slate-400 block mb-1">From</label>
    <input type="date" name="from" value="{{ $from->toDateString() }}" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></div>
  <div><label class="text-xs font-semibold text-slate-400 block mb-1">To</label>
    <input type="date" name="to" value="{{ $to->toDateString() }}" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></div>
  <div><label class="text-xs font-semibold text-slate-400 block mb-1">Venue</label>
    <select name="venue_id" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
      <option value="">All Venues</option>
      @foreach($venues as $v)<option value="{{ $v->id }}" {{ request('venue_id')==$v->id?'selected':'' }}>{{ $v->name }}</option>@endforeach
    </select>
  </div>
  <div class="flex items-end gap-2">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">Apply</button>
  </div>
  <div class="ml-auto flex gap-2">
    <a href="{{ route('admin.reports.export', ['type'=>'utilization','from'=>$from->toDateString(),'to'=>$to->toDateString()]) }}"
       class="text-xs font-semibold bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-xl transition flex items-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Export Excel
    </a>
  </div>
</form>

{{-- Bar Chart Visual --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-5">
  <h3 class="text-sm font-semibold text-slate-700 mb-5">Occupancy by Venue</h3>
  <div class="space-y-4">
    @foreach($data as $row)
    @php $pct = min(100, $row['occupancy'] ?? 0); @endphp
    <div>
      <div class="flex justify-between text-sm mb-1.5">
        <span class="font-medium text-slate-700">{{ $row['venue'] }}</span>
        <span class="font-bold text-slate-800">{{ $pct }}%</span>
      </div>
      <div class="relative w-full bg-slate-100 rounded-full h-6">
        <div class="h-6 rounded-full flex items-center justify-end pr-2 transition-all duration-700"
             style="width:{{ $pct }}%;background-color:{{ $row['color'] ?? '#3b82f6' }}">
          @if($pct > 15)<span class="text-[11px] font-bold text-white">{{ $row['booked_slots'] }}/{{ $row['total_slots'] }}</span>@endif
        </div>
        @if($pct <= 15)<span class="absolute left-2 top-1 text-[11px] font-semibold text-slate-500">{{ $row['booked_slots'] }}/{{ $row['total_slots'] }}</span>@endif
      </div>
    </div>
    @endforeach
  </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Detailed Breakdown</h3></div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead><tr class="bg-slate-50/50 border-b border-slate-100">
        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Venue</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Slots</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Booked</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Occupancy</th>
        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Revenue</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($data as $row)
        <tr class="hover:bg-slate-50/50 transition">
          <td class="px-5 py-3.5 font-semibold text-slate-800">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full" style="background-color:{{ $row['color']??'#3b82f6' }}"></span>
              {{ $row['venue'] }}
            </div>
          </td>
          <td class="px-4 py-3.5 text-right text-slate-600">{{ $row['total_slots'] ?? 0 }}</td>
          <td class="px-4 py-3.5 text-right text-slate-600">{{ $row['booked_slots'] ?? 0 }}</td>
          <td class="px-4 py-3.5 text-right font-bold {{ ($row['occupancy']??0)>=80?'text-green-700':(($row['occupancy']??0)>=50?'text-yellow-600':'text-slate-600') }}">
            {{ $row['occupancy'] ?? 0 }}%
          </td>
          <td class="px-4 py-3.5 text-right font-semibold text-slate-800">৳{{ number_format($row['revenue']??0,0) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">No data for selected period.</td></tr>
        @endforelse
      </tbody>
      @if(count($data)>0)
      <tfoot><tr class="bg-slate-50 border-t border-slate-200">
        <td class="px-5 py-3 font-bold text-slate-800 text-sm">Total</td>
        <td class="px-4 py-3 text-right font-bold text-slate-800">{{ collect($data)->sum('total_slots') }}</td>
        <td class="px-4 py-3 text-right font-bold text-slate-800">{{ collect($data)->sum('booked_slots') }}</td>
        <td class="px-4 py-3 text-right font-bold text-slate-800">
          @php $totSlots=collect($data)->sum('total_slots'); @endphp
          {{ $totSlots > 0 ? round(collect($data)->sum('booked_slots')/$totSlots*100) : 0 }}%
        </td>
        <td class="px-4 py-3 text-right font-bold text-green-700">৳{{ number_format(collect($data)->sum('revenue'),0) }}</td>
      </tr></tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
