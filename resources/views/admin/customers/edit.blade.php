@extends('layouts.admin')
@section('title','Edit Customer')
@section('breadcrumb','Edit Customer')
@section('content')
<div class="max-w-2xl">
<form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-5">
@csrf @method('PUT')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
  <h3 class="text-sm font-semibold text-slate-700 mb-5">Edit — {{ $customer->name }}</h3>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Full Name <span class="text-red-500">*</span></label>
      <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
             class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
      @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Phone <span class="text-red-500">*</span></label>
      <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required
             class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('phone') border-red-400 @enderror">
      @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Email</label>
      <input type="email" name="email" value="{{ old('email', $customer->email) }}"
             class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">NID</label>
      <input type="text" name="nid" value="{{ old('nid', $customer->nid) }}"
             class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Organization</label>
      <input type="text" name="organization" value="{{ old('organization', $customer->organization) }}"
             class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="sm:col-span-2">
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Address</label>
      <input type="text" name="address" value="{{ old('address', $customer->address) }}"
             class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="sm:col-span-2">
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Notes</label>
      <textarea name="notes" rows="3" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes', $customer->notes) }}</textarea>
    </div>
  </div>
</div>
<div class="flex gap-3">
  <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3 rounded-xl transition shadow-sm shadow-blue-900/20">Save Changes</button>
  <a href="{{ route('admin.customers.show', $customer) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-6 py-3 rounded-xl transition">Cancel</a>
</div>
</form>
</div>
@endsection