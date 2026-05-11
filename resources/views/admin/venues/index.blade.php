@extends('layouts.admin')
@section('title','Venues')
@section('breadcrumb','Venues')
@section('content')

<div class="flex items-center justify-between mb-5">
  <h1 class="text-lg font-bold text-slate-800">Venues <span class="text-slate-400 font-normal text-base">({{ $venues->count() }})</span></h1>
  @can('create venues')
  <a href="{{ route('admin.venues.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add Venue
  </a>
  @endcan
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
  @forelse($venues as $venue)
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
    <div class="h-28 relative overflow-hidden" style="background-color:{{ $venue->color }}20">
      @if($venue->primaryImage)
        <img src="{{ $venue->primaryImage->url }}" alt="{{ $venue->name }}" class="w-full h-full object-cover">
      @else
        <div class="w-full h-full flex items-center justify-center">
          <svg class="w-10 h-10 opacity-20" style="color:{{ $venue->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
        </div>
      @endif
      <span class="absolute top-2 left-2 text-xs font-semibold px-2 py-0.5 rounded-full text-white" style="background-color:{{ $venue->color }}">{{ $venue->getTypeLabel() }}</span>
      <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full {{ $venue->is_active ? 'bg-green-400' : 'bg-slate-300' }}"></span>
    </div>
    <div class="p-4">
      <h3 class="font-bold text-slate-800">{{ $venue->name }}</h3>
      <p class="text-xs text-slate-500 mt-0.5">{{ number_format($venue->capacity) }} capacity · ৳{{ number_format($venue->hourly_rate,0) }}/hr</p>
      @php $slotCount = max(1, \App\Models\Slot::where('venue_id',$venue->id)->count()); $util = min(100, round($venue->bookings_count/$slotCount*100)); @endphp
      <div class="mt-3">
        <div class="flex justify-between text-[10px] text-slate-400 mb-1"><span>Utilization</span><span>{{ $util }}%</span></div>
        <div class="w-full bg-slate-100 rounded-full h-1.5"><div class="h-1.5 rounded-full" style="width:{{ $util }}%;background-color:{{ $venue->color }}"></div></div>
      </div>
      <div class="flex gap-2 mt-4 pt-3 border-t border-slate-50">
        <a href="{{ route('admin.slots.index', ['venue_id'=>$venue->id]) }}" class="flex-1 text-center text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 rounded-xl transition">Slots</a>
        @can('edit venues')
        <a href="{{ route('admin.venues.edit', $venue) }}" class="flex-1 text-center text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-xl transition">Edit</a>
        @endcan
        @can('delete venues')
        <x-confirm-dialog :action="route('admin.venues.destroy', $venue)" method="DELETE" message="Delete this venue?" btnLabel="Del" btnClass="text-xs font-semibold text-red-500 hover:bg-red-50 px-3 py-2 rounded-xl transition" />
        @endcan
      </div>
    </div>
  </div>
  @empty
  <div class="col-span-3 text-center py-16 text-slate-400 text-sm">No venues yet.</div>
  @endforelse
</div>
@endsection
