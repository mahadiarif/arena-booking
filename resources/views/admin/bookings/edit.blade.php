@extends('layouts.admin')
@section('title','Edit Booking')
@section('breadcrumb','Edit Booking')
@section('content')

<form method="POST" action="{{ route('admin.bookings.update', $booking) }}"
      x-data="{ participants: {{ json_encode($booking->participants->map(fn($p)=>['name'=>$p->name,'phone'=>$p->phone,'note'=>$p->note])->values()) }},
                addP(){ this.participants.push({name:'',phone:'',note:''}) },
                removeP(i){ this.participants.splice(i,1) } }">
@csrf @method('PUT')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
<div class="xl:col-span-2 space-y-5">

  {{-- Booking Summary (readonly) --}}
  <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4 flex items-center gap-4">
    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-lg">📋</div>
    <div>
      <p class="font-mono font-bold text-slate-800">{{ $booking->booking_ref }}</p>
      <p class="text-xs text-slate-500">{{ $booking->venue?->name }} · {{ $booking->slot?->date?->format('d M Y') }} · <x-status-pill :status="$booking->status" /></p>
    </div>
  </div>

  @can('approve bookings')
  {{-- Total Amount (admin only) --}}
  <div class="bg-white rounded-2xl border border-orange-200 shadow-sm p-5">
    <div class="flex items-start gap-3 mb-4">
      <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div>
        <h3 class="text-sm font-semibold text-slate-700">Total Amount</h3>
        <p class="text-xs text-orange-600 mt-0.5">Changing this will affect the outstanding balance shown to staff.</p>
      </div>
    </div>
    <input type="number" name="total_amount" value="{{ old('total_amount', $booking->total_amount) }}" step="0.01" min="0"
           class="w-48 text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
    @error('total_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>
  @endcan

  {{-- Notes --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <h3 class="text-sm font-semibold text-slate-700 mb-3">Notes</h3>
    <textarea name="notes" rows="4" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes', $booking->notes) }}</textarea>
    @error('notes')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- Participants --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-slate-700">Participants</h3>
      <button type="button" @click="addP()" class="text-xs font-semibold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">+ Add</button>
    </div>
    <template x-for="(p,i) in participants" :key="i">
      <div class="grid grid-cols-12 gap-2 mb-2">
        <input :name="`participants[${i}][name]`" x-model="p.name" placeholder="Name*" class="col-span-4 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <input :name="`participants[${i}][phone]`" x-model="p.phone" placeholder="Phone" class="col-span-4 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <input :name="`participants[${i}][note]`" x-model="p.note" placeholder="Note" class="col-span-3 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" @click="removeP(i)" class="col-span-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition">✕</button>
      </div>
    </template>
    <p x-show="participants.length===0" class="text-xs text-slate-400 text-center py-2">No participants.</p>
  </div>

</div>
<div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sticky top-20">
    <div class="space-y-2 text-sm mb-5">
      <div class="flex justify-between"><span class="text-slate-500">Total</span><span class="font-bold">৳{{ number_format($booking->total_amount, 2) }}</span></div>
      <div class="flex justify-between"><span class="text-slate-500">Paid</span><span class="text-green-700 font-semibold">৳{{ number_format($booking->paid_amount, 2) }}</span></div>
      <div class="flex justify-between border-t border-slate-100 pt-2 {{ $booking->due_amount > 0 ? 'text-red-600' : 'text-slate-400' }}">
        <span>Due</span><span class="font-bold">৳{{ number_format($booking->due_amount, 2) }}</span>
      </div>
    </div>
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 rounded-xl transition">Save Changes</button>
    <a href="{{ route('admin.bookings.show', $booking) }}" class="mt-2 block text-center text-xs text-slate-400 hover:text-slate-600">Cancel</a>
  </div>
</div>
</div>
</form>
@endsection