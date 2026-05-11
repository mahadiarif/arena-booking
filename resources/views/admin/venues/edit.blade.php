@extends('layouts.admin')
@section('title','Edit Venue')
@section('breadcrumb','Edit Venue')
@section('content')
<form method="POST" action="{{ route('admin.venues.update', $venue) }}" enctype="multipart/form-data"
      x-data="{ requiresApproval: {{ $venue->requires_approval ? 'true' : 'false' }} }">
@csrf @method('PUT')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
<div class="xl:col-span-2 space-y-5">

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-5">Basic Information</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="sm:col-span-2">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Venue Name</label>
        <input type="text" name="name" value="{{ old('name',$venue->name) }}" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Type</label>
        <select name="type" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          @foreach(['stadium'=>'Stadium','turf_indoor'=>'Indoor Turf','turf_outdoor'=>'Outdoor Turf','court'=>'Court','vip_box'=>'VIP Box','other'=>'Other'] as $v=>$l)
          <option value="{{ $v }}" {{ old('type',$venue->type)===$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Capacity</label>
        <input type="number" name="capacity" value="{{ old('capacity',$venue->capacity) }}" min="1" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Schedule</label>
        <select name="schedule_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">— None —</option>
          @foreach($schedules as $s)
          <option value="{{ $s->id }}" {{ old('schedule_id',$venue->schedule_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Hourly Rate (৳)</label>
        <input type="number" name="hourly_rate" value="{{ old('hourly_rate',$venue->hourly_rate) }}" step="0.01" min="0" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Color</label>
        <input type="color" name="color" value="{{ old('color',$venue->color,'#3b82f6') }}" class="w-10 h-10 rounded-xl border border-slate-200 cursor-pointer p-0.5">
      </div>
      <div class="sm:col-span-2">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Description</label>
        <textarea name="description" rows="3" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description',$venue->description) }}</textarea>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Booking Rules</h3>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Min Duration</label>
        <select name="min_duration_minutes" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          @foreach([60,90,120,180,240] as $m)<option value="{{ $m }}" {{ old('min_duration_minutes',$venue->min_duration_minutes)==$m?'selected':'' }}>{{ $m }} min</option>@endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Max Duration</label>
        <select name="max_duration_minutes" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          @foreach([120,180,240,360,480,720] as $m)<option value="{{ $m }}" {{ old('max_duration_minutes',$venue->max_duration_minutes)==$m?'selected':'' }}>{{ $m }} min</option>@endforeach
        </select>
      </div>
      <div class="col-span-2 flex items-center justify-between bg-slate-50 rounded-xl px-4 py-3">
        <div><p class="text-sm font-semibold text-slate-700">Requires Approval</p><p class="text-xs text-slate-400">Bookings stay pending until confirmed</p></div>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="requires_approval" value="1" x-model="requiresApproval" class="sr-only">
          <div class="w-10 h-5 rounded-full transition" :class="requiresApproval?'bg-blue-600':'bg-slate-300'">
            <div class="mt-0.5 ml-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="requiresApproval?'translate-x-5':''"></div>
          </div>
        </label>
      </div>
    </div>
  </div>

  {{-- Existing Images --}}
  @if($venue->images->count())
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Current Images</h3>
    <div class="grid grid-cols-4 gap-2">
      @foreach($venue->images as $img)
      <div class="relative group">
        <img src="{{ $img->url }}" class="w-full h-20 object-cover rounded-xl border-2 {{ $img->is_primary ? 'border-blue-500' : 'border-transparent' }}">
        <div class="absolute inset-0 flex items-center justify-center gap-1 bg-slate-900/50 rounded-xl opacity-0 group-hover:opacity-100 transition">
          @if(!$img->is_primary)
          <form method="POST" action="{{ route('admin.venues.images.primary', [$venue, $img]) }}">@csrf
            <button class="text-[10px] bg-blue-600 text-white px-1.5 py-0.5 rounded font-semibold">Set Primary</button>
          </form>
          @endif
          <form method="POST" action="{{ route('admin.venues.images.delete', [$venue, $img]) }}">@csrf @method('DELETE')
            <button class="text-[10px] bg-red-600 text-white px-1.5 py-0.5 rounded font-semibold">Del</button>
          </form>
        </div>
        @if($img->is_primary)<span class="absolute bottom-1 left-1 text-[9px] font-bold text-white bg-blue-600 px-1 rounded">PRIMARY</span>@endif
      </div>
      @endforeach
    </div>
    <div class="mt-3">
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Upload More Images</label>
      <input type="file" name="images[]" multiple accept="image/*" class="text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
    </div>
  </div>
  @endif

</div>
<div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sticky top-20">
    <label class="flex items-center gap-2 mb-4 cursor-pointer">
      <input type="checkbox" name="is_active" value="1" {{ $venue->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600">
      <span class="text-sm text-slate-600">Active</span>
    </label>
    <input type="number" name="sort_order" value="{{ old('sort_order',$venue->sort_order) }}" min="0" placeholder="Sort order" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 rounded-xl transition">Save Changes</button>
    <a href="{{ route('admin.venues.index') }}" class="mt-2 block text-center text-xs text-slate-400 hover:text-slate-600">Cancel</a>
  </div>
</div>
</div>
</form>
@endsection