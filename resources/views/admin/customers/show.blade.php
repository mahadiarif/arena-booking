@extends('layouts.admin')
@section('title', $customer->name)
@section('breadcrumb', 'Customer Profile')
@section('content')

{{-- Profile Header --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-5" x-data="{creditModal:false}">
  <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between">
    <div class="flex items-center gap-4">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-2xl font-bold text-white flex-shrink-0">
        {{ strtoupper(substr($customer->name,0,2)) }}
      </div>
      <div>
        <h1 class="text-xl font-bold text-slate-800">{{ $customer->name }}</h1>
        <p class="text-slate-500 text-sm mt-0.5">{{ $customer->phone }}@if($customer->email) · {{ $customer->email }}@endif</p>
        @if($customer->organization)<p class="text-slate-400 text-xs mt-0.5">{{ $customer->organization }}</p>@endif
        @if($customer->nid)<p class="text-slate-400 text-xs">NID: {{ $customer->nid }}</p>@endif
      </div>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
      <div class="text-right">
        <p class="text-xs text-slate-400">Credit Balance</p>
        <p class="text-2xl font-bold {{ $customer->credit_balance > 0 ? 'text-green-700' : 'text-slate-400' }}">৳{{ number_format($customer->credit_balance, 2) }}</p>
      </div>
      @can('manage credits')
      <button @click="creditModal=true" class="text-xs font-semibold bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-xl transition">Adjust Credit</button>
      @endcan
      @can('update', $customer)
      <a href="{{ route('admin.customers.edit', $customer) }}" class="text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl transition">Edit</a>
      @endcan
    </div>
  </div>

  {{-- Credit Adjust Modal --}}
  <div x-show="creditModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 flex items-center justify-center p-4" @click.self="creditModal=false">
    <div x-show="creditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
      <h3 class="text-base font-semibold text-slate-800 mb-4">Adjust Credit Balance</h3>
      <form method="POST" action="{{ route('admin.customers.adjust-credit', $customer) }}" class="space-y-3">
        @csrf
        <div>
          <label class="text-xs font-semibold text-slate-500 block mb-1.5">Amount (positive = add, negative = deduct)</label>
          <input type="number" name="amount" step="0.01" required placeholder="e.g. 500 or -200"
                 class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-500 block mb-1.5">Payment Method</label>
                <select name="payment_method" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">N/A</option>
                    <option value="cash">💵 Cash</option>
                    <option value="bkash">📱 bKash</option>
                    <option value="nagad">📱 Nagad</option>
                    <option value="bank_transfer">🏦 Bank</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500 block mb-1.5">Ref #</label>
                <input type="text" name="reference_no" placeholder="Optional"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div>
          <label class="text-xs font-semibold text-slate-500 block mb-1.5">Reason / Note</label>
          <textarea name="note" rows="2" required class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>
        <div class="flex gap-2 pt-1">
          <button type="button" @click="creditModal=false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold py-2.5 rounded-xl transition">Cancel</button>
          <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 rounded-xl transition">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  @foreach([
    ['Total Bookings',  $stats['total_bookings'],  'blue'],
    ['Total Spent',     '৳'.number_format($stats['total_spent'],0), 'green'],
    ['Outstanding Due', '৳'.number_format($stats['outstanding'],0), $stats['outstanding']>0?'red':'slate'],
    ['Member Since',    $customer->created_at->format('M Y'), 'purple'],
  ] as [$label,$val,$color])
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center">
    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">{{ $label }}</p>
    <p class="text-xl font-bold text-slate-800">{{ $val }}</p>
  </div>
  @endforeach
</div>

{{-- Tabs --}}
<div x-data="{tab:'bookings'}" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="flex border-b border-slate-100">
    <button @click="tab='bookings'" :class="tab==='bookings'?'border-b-2 border-blue-600 text-blue-600':'text-slate-500'"
            class="px-6 py-3.5 text-sm font-semibold transition">Booking History</button>
    <button @click="tab='credits'" :class="tab==='credits'?'border-b-2 border-blue-600 text-blue-600':'text-slate-500'"
            class="px-6 py-3.5 text-sm font-semibold transition">Credit Transactions</button>
  </div>

  {{-- Bookings Tab --}}
  <div x-show="tab==='bookings'">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Ref</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Venue</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Date</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Amount</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Due</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($customer->bookings as $b)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="px-5 py-3"><a href="{{ route('admin.bookings.show', $b) }}" class="font-mono text-xs text-blue-600 hover:underline">{{ $b->booking_ref }}</a></td>
            <td class="px-4 py-3 text-slate-700">{{ $b->venue?->name }}</td>
            <td class="px-4 py-3 text-slate-500 text-xs">{{ $b->slot?->date?->format('d M Y') }}</td>
            <td class="px-4 py-3 text-right font-semibold text-slate-800">৳{{ number_format($b->total_amount,0) }}</td>
            <td class="px-4 py-3 text-right font-semibold {{ $b->due_amount>0?'text-red-600':'text-slate-300' }}">{{ $b->due_amount>0?'৳'.number_format($b->due_amount,0):'—' }}</td>
            <td class="px-4 py-3"><x-status-pill :status="$b->status" /></td>
          </tr>
          @empty
          <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400 text-sm">No bookings yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Credits Tab --}}
  <div x-show="tab==='credits'" x-cloak>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Date</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Type</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Method</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Amount</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Balance</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Note</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($customer->creditTransactions as $tx)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="px-5 py-3 text-slate-500 text-xs">{{ $tx->created_at->format('d M Y') }}</td>
            <td class="px-4 py-3"><span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ str_contains($tx->type,'manual')&&$tx->amount>0?'bg-green-100 text-green-700':'bg-slate-100 text-slate-600' }} uppercase">{{ str_replace('_',' ',$tx->type) }}</span></td>
            <td class="px-4 py-3">
                @if($tx->payment_method)
                    <span class="text-xs text-slate-700 capitalize">{{ $tx->payment_method }}</span>
                    @if($tx->reference_no)<p class="text-[9px] text-slate-400 font-mono">{{ $tx->reference_no }}</p>@endif
                @else
                    <span class="text-slate-300">—</span>
                @endif
            </td>
            <td class="px-4 py-3 text-right font-bold {{ $tx->amount>=0?'text-green-700':'text-red-600' }}">{{ $tx->amount>=0?'+':'' }}৳{{ number_format($tx->amount,0) }}</td>
            <td class="px-4 py-3 text-right font-semibold text-slate-800 text-xs">৳{{ number_format($tx->balance_after,0) }}</td>
            <td class="px-4 py-3 text-slate-500 text-xs truncate max-w-[150px]">{{ $tx->note }}</td>
          </tr>
          @empty
          <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400 text-sm">No transactions yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
