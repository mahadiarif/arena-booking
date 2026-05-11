@extends('layouts.admin')
@section('title','Booking '.$booking->booking_ref)
@section('breadcrumb','Booking Details')
@section('content')

{{-- Header Card --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
  <div>
    <div class="flex items-center gap-3 mb-1">
      <span class="font-mono text-xl font-bold text-slate-800">{{ $booking->booking_ref }}</span>
      <x-status-pill :status="$booking->status" />
    </div>
    <p class="text-xs text-slate-400">Booked by {{ $booking->bookedBy?->name ?? '—' }} · {{ $booking->created_at->format('d M Y, g:i A') }}</p>
  </div>
  <div class="flex items-center gap-2 flex-wrap">
    @if($booking->status->value === 'pending')
      @can('approve bookings')
      <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}">@csrf
        <button class="px-4 py-2 text-xs font-semibold bg-green-600 hover:bg-green-700 text-white rounded-xl transition">Confirm</button>
      </form>
      @endcan
    @endif
    @if($booking->status->value === 'confirmed')
      <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}">@csrf
        <button class="px-4 py-2 text-xs font-semibold bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition">Check In</button>
      </form>
    @endif
    @if($booking->status->value === 'checked_in')
      <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}">@csrf
        <button class="px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition">Check Out</button>
      </form>
    @endif
    @if(in_array($booking->status->value, ['confirmed','completed']))
      <a href="{{ route('admin.bookings.invoice', $booking) }}" target="_blank"
         class="px-4 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition">Invoice</a>
    @endif
    @can('update', $booking)
      <a href="{{ route('admin.bookings.edit', $booking) }}"
         class="px-4 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition">Edit</a>
    @endcan
    @if(!$booking->status->isTerminal())
      @can('cancel', $booking)
      <x-confirm-dialog :action="route('admin.bookings.cancel', $booking)" method="POST"
         message="Cancel this booking? This cannot be undone." btnLabel="Cancel Booking"
         btnClass="px-4 py-2 text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition" />
      @endcan
    @endif
  </div>
</div>

{{-- 3-column grid --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

  {{-- Col 1: Booking Details --}}
  <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Booking Details</h3></div>
    <dl class="divide-y divide-slate-50">
      @foreach([
        ['Customer',    $booking->customer?->name . ' · ' . $booking->customer?->phone],
        ['Venue',       $booking->venue?->name],
        ['Date',        $booking->slot?->date?->format('d M Y, l')],
        ['Time',        $booking->slot ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A').' – '.\Carbon\Carbon::createFromTimeString($booking->slot->end_time)->format('g:i A') : '—'],
        ['Duration',    $booking->slot ? $booking->slot->duration_minutes.' min' : '—'],
        ['Total',       '৳'.number_format($booking->total_amount, 2)],
        ['Paid',        '৳'.number_format($booking->paid_amount, 2)],
        ['Due',         '৳'.number_format($booking->due_amount, 2)],
      ] as [$label, $val])
      <div class="flex px-5 py-3 text-sm">
        <dt class="w-28 text-slate-400 flex-shrink-0">{{ $label }}</dt>
        <dd class="font-medium text-slate-800 {{ $label==='Due' && $booking->due_amount > 0 ? 'text-red-600 font-bold' : '' }}">{{ $val }}</dd>
      </div>
      @endforeach
    </dl>
    @if($booking->notes)
    <div class="px-5 py-3 border-t border-slate-50">
      <p class="text-xs text-slate-400 mb-1">Notes</p>
      <p class="text-sm text-slate-600">{{ $booking->notes }}</p>
    </div>
    @endif
  </div>

  {{-- Col 2: Payments --}}
  <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-semibold text-slate-700">Payments</h3>
      @if($booking->due_amount > 0 && !$booking->status->isTerminal())
      <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">৳{{ number_format($booking->due_amount,2) }} due</span>
      @endif
    </div>
    <div class="divide-y divide-slate-50">
      @forelse($booking->payments as $payment)
      <div class="px-5 py-3 flex items-center justify-between text-sm">
        <div>
          <p class="font-medium text-slate-800">{{ $payment->method->label() }} {{ $payment->method->icon() }}</p>
          <p class="text-xs text-slate-400">{{ $payment->paid_at?->format('d M Y') }} · {{ $payment->receivedBy?->name }}</p>
          @if($payment->reference_no)<p class="text-xs text-slate-400">Ref: {{ $payment->reference_no }}</p>@endif
        </div>
        <div class="text-right">
          <p class="font-bold text-green-700">৳{{ number_format($payment->amount, 2) }}</p>
          @can('delete payments')
          <x-confirm-dialog :action="route('admin.payments.destroy', $payment)" method="DELETE"
             message="Delete this payment record?" btnLabel="Delete"
             btnClass="text-xs text-red-400 hover:text-red-600 transition" />
          @endcan
        </div>
      </div>
      @empty
      <p class="px-5 py-4 text-sm text-slate-400">No payments recorded.</p>
      @endforelse
    </div>
    @if($booking->due_amount > 0 && !$booking->status->isTerminal())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
      <p class="text-xs font-semibold text-slate-600 mb-3">Add Payment</p>
      <form method="POST" action="{{ route('admin.payments.store', $booking) }}" class="space-y-2">
        @csrf
        <div class="grid grid-cols-2 gap-2">
          <input type="number" name="amount" step="0.01" placeholder="Amount" value="{{ $booking->due_amount }}"
                 class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
          <select name="method" class="text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            <option value="cash">💵 Cash</option>
            <option value="bkash">📱 bKash</option>
            <option value="nagad">📱 Nagad</option>
            <option value="bank_transfer">🏦 Bank</option>
            <option value="credit">💳 Credit</option>
          </select>
        </div>
        <input type="text" name="reference_no" placeholder="Reference # (optional)"
               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-xl transition">Record Payment</button>
      </form>
    </div>
    @endif
  </div>

  {{-- Col 3: Participants + Attributes + Siblings --}}
  <div class="xl:col-span-1 space-y-4">
    @if($booking->participants->count())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Participants ({{ $booking->participants->count() }})</h3></div>
      <div class="divide-y divide-slate-50">
        @foreach($booking->participants as $p)
        <div class="px-5 py-3"><p class="text-sm font-medium text-slate-800">{{ $p->name }}</p>
          <p class="text-xs text-slate-400">{{ $p->phone }}</p></div>
        @endforeach
      </div>
    </div>
    @endif
    @if($booking->attributeValues->count())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Custom Fields</h3></div>
      <dl class="divide-y divide-slate-50">
        @foreach($booking->attributeValues as $av)
        <div class="flex px-5 py-3 text-sm"><dt class="text-slate-400 w-32 flex-shrink-0">{{ $av->attribute?->label }}</dt><dd class="font-medium text-slate-700">{{ $av->value }}</dd></div>
        @endforeach
      </dl>
    </div>
    @endif
    @if($booking->childBookings->count())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Recurring Siblings ({{ $booking->childBookings->count() }})</h3></div>
      <div class="divide-y divide-slate-50">
        @foreach($booking->childBookings as $child)
        <div class="flex items-center justify-between px-5 py-3 text-sm">
          <span class="text-slate-600">{{ $child->slot?->date?->format('d M Y') }}</span>
          <x-status-pill :status="$child->status" />
          <a href="{{ route('admin.bookings.show', $child) }}" class="text-xs text-blue-600 hover:underline">View</a>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>

{{-- Activity Log --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100"><h3 class="text-sm font-semibold text-slate-700">Activity Log</h3></div>
  <div class="px-5 py-4">
    @forelse($activityLog as $log)
    <div class="flex gap-3 mb-4 last:mb-0">
      <div class="w-7 h-7 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-xs mt-0.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="flex-1">
        <p class="text-sm text-slate-700">{{ $log->description }}</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ $log->causer?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
      </div>
    </div>
    @empty
    <p class="text-sm text-slate-400">No activity recorded.</p>
    @endforelse
  </div>
</div>
@endsection
