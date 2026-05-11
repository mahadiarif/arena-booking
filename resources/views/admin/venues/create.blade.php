@extends('layouts.admin')
@section('title','Add Venue')
@section('breadcrumb','Add Venue')
@section('content')
<form method="POST" action="{{ route('admin.venues.store') }}" enctype="multipart/form-data"
      x-data="{ requiresApproval: false, imageFiles: [], previewUrls: [],
        handleFiles(e){ Array.from(e.target.files).forEach(f=>{ this.imageFiles.push(f); const r=new FileReader(); r.onload=ev=>this.previewUrls.push(ev.target.result); r.readAsDataURL(f); }); },
        removeImage(i){ this.imageFiles.splice(i,1); this.previewUrls.splice(i,1); } }">
@csrf
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
<div class="xl:col-span-2 space-y-5">

  {{-- Basic Info --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-5">Basic Information</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="sm:col-span-2">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Venue Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Type <span class="text-red-500">*</span></label>
        <select name="type" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">— Select Type —</option>
          @foreach(['stadium'=>'Stadium','turf_indoor'=>'Indoor Turf','turf_outdoor'=>'Outdoor Turf','court'=>'Court','vip_box'=>'VIP Box','other'=>'Other'] as $v=>$l)
          <option value="{{ $v }}" {{ old('type')===$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
        @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Capacity</label>
        <input type="number" name="capacity" value="{{ old('capacity',20) }}" min="1" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Schedule</label>
        <select name="schedule_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">— No Schedule —</option>
          @foreach($schedules as $s)
          <option value="{{ $s->id }}" {{ old('schedule_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Hourly Rate (৳)</label>
        <input type="number" name="hourly_rate" value="{{ old('hourly_rate',0) }}" step="0.01" min="0" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Color</label>
        <div class="flex items-center gap-3">
          <input type="color" name="color" value="{{ old('color','#3b82f6') }}" class="w-10 h-10 rounded-xl border border-slate-200 cursor-pointer p-0.5">
          <span class="text-xs text-slate-400">Brand color for calendar & UI</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Booking Rules --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-5">Booking Rules</h3>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Min Duration (min)</label>
        <select name="min_duration_minutes" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          @foreach([60,90,120,180,240] as $m)
          <option value="{{ $m }}" {{ old('min_duration_minutes',60)==$m?'selected':'' }}>{{ $m }} min</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Max Duration (min)</label>
        <select name="max_duration_minutes" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          @foreach([120,180,240,360,480,720] as $m)
          <option value="{{ $m }}" {{ old('max_duration_minutes',240)==$m?'selected':'' }}>{{ $m }} min</option>
          @endforeach
        </select>
      </div>
      <div class="col-span-2 flex items-center justify-between bg-slate-50 rounded-xl px-4 py-3">
        <div>
          <p class="text-sm font-semibold text-slate-700">Requires Approval</p>
          <p class="text-xs text-slate-400">Bookings stay pending until manually confirmed</p>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="requires_approval" value="1" x-model="requiresApproval" class="sr-only">
          <div class="w-10 h-5 rounded-full transition" :class="requiresApproval?'bg-blue-600':'bg-slate-300'">
            <div class="mt-0.5 ml-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="requiresApproval?'translate-x-5':''"></div>
          </div>
        </label>
      </div>
    </div>
  </div>

  {{-- Description --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-3">Description</h3>
    <textarea name="description" rows="4" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Optional venue description…">{{ old('description') }}</textarea>
  </div>

  {{-- Images --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Images</h3>
    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-blue-300 transition cursor-pointer" @click="$refs.imgInput.click()">
      <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      <p class="text-sm text-slate-400">Click to upload images</p>
      <p class="text-xs text-slate-300 mt-0.5">JPG, PNG, WebP · Max 2MB each</p>
      <input type="file" name="images[]" multiple accept="image/*" x-ref="imgInput" @change="handleFiles($event)" class="sr-only">
    </div>
    <div class="grid grid-cols-4 gap-2 mt-3" x-show="previewUrls.length">
      <template x-for="(url,i) in previewUrls" :key="i">
        <div class="relative group">
          <img :src="url" class="w-full h-20 object-cover rounded-xl border border-slate-100">
          <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 rounded-xl opacity-0 group-hover:opacity-100 transition">
            <button type="button" @click="removeImage(i)" class="text-white text-xs font-bold bg-red-600 rounded-full w-6 h-6 flex items-center justify-center">✕</button>
          </div>
          <span x-show="i===0" class="absolute bottom-1 left-1 text-[9px] font-bold text-white bg-blue-600 px-1 rounded">PRIMARY</span>
        </div>
      </template>
    </div>
  </div>

</div>
<div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sticky top-20">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Save</h3>
    <label class="flex items-center gap-2 mb-4 cursor-pointer">
      <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
      <span class="text-sm text-slate-600">Active (visible in booking)</span>
    </label>
    <input type="number" name="sort_order" value="{{ old('sort_order',1) }}" min="0" placeholder="Sort order"
           class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 rounded-xl transition">Create Venue</button>
    <a href="{{ route('admin.venues.index') }}" class="mt-2 block text-center text-xs text-slate-400 hover:text-slate-600">Cancel</a>
  </div>
</div>
</div>
</form>
@endsection