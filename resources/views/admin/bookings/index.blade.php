@extends('layouts.admin')
@section('title', 'Bookings')
@section('breadcrumb', 'Bookings')

@section('content')
{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.bookings.index') }}" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-4 mb-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="lg:col-span-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref / Name / Phone…"
                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-700 placeholder-slate-400">
        </div>
        <div>
            <select name="status" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-slate-700">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)
                <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="venue_id" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-slate-700">
                <option value="">All Venues</option>
                @foreach($venues as $v)
                <option value="{{ $v->id }}" {{ request('venue_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="flex-1 text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-slate-700">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="flex-1 text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-slate-700">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl px-4 py-2 transition">Filter</button>
            <a href="{{ route('admin.bookings.index') }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl px-4 py-2 transition">Clear</a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-700">Bookings <span class="text-slate-400 font-normal ml-1">({{ $bookings->total() }})</span></h2>
        @can('create bookings')
        <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl px-4 py-2 transition shadow-sm shadow-blue-900/20">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Booking
        </a>
        @endcan
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider w-8">#</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Ref</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Venue</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Date & Time</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Amount</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Due</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-slate-50/50 transition group">
                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $bookings->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono text-xs font-semibold text-blue-600 hover:text-blue-700">{{ $booking->booking_ref }}</a>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                {{ strtoupper(substr($booking->customer?->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 text-sm leading-none">{{ $booking->customer?->name }}</p>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $booking->customer?->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color:{{ $booking->venue?->color ?? '#94a3b8' }}"></span>
                            <span class="text-slate-700 text-sm">{{ $booking->venue?->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="text-sm text-slate-700 font-medium">{{ $booking->slot?->date?->format('d M Y') }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $booking->slot ? \Carbon\Carbon::createFromTimeString($booking->slot->start_time)->format('g:i A') : '—' }}
                            –
                            {{ $booking->slot ? \Carbon\Carbon::createFromTimeString($booking->slot->end_time)->format('g:i A') : '—' }}
                        </p>
                    </td>
                    <td class="px-4 py-3.5 text-right font-semibold text-slate-800 text-sm">৳{{ number_format($booking->total_amount,0) }}</td>
                    <td class="px-4 py-3.5 text-right font-semibold text-sm {{ $booking->due_amount > 0 ? 'text-red-600' : 'text-slate-300' }}">
                        {{ $booking->due_amount > 0 ? '৳'.number_format($booking->due_amount,0) : '—' }}
                    </td>
                    <td class="px-4 py-3.5"><x-status-pill :status="$booking->status" /></td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('admin.bookings.show', $booking) }}" title="View" class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100 transition shadow-sm border border-slate-200/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($booking->status->value === 'pending')
                                @can('approve bookings')
                                <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="inline">@csrf
                                    <button title="Confirm" class="flex items-center justify-center w-7 h-7 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition shadow-sm border border-green-200/60">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                @endcan
                            @endif
                            @if($booking->status->value === 'confirmed')
                                <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}" class="inline">@csrf
                                    <button title="Check In" class="flex items-center justify-center w-7 h-7 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition shadow-sm border border-purple-200/60">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($booking->status->value === 'checked_in')
                                <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}" class="inline">@csrf
                                    <button title="Check Out" class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition shadow-sm border border-blue-200/60">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if(in_array($booking->status->value, ['confirmed','completed']))
                                <a href="{{ route('admin.bookings.invoice', $booking) }}" target="_blank" title="Invoice" class="flex items-center justify-center w-7 h-7 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition shadow-sm border border-orange-200/60">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                            @endif
                            @if(!$booking->status->isTerminal())
                                @can('cancel bookings')
                                <span title="Cancel">
                                <x-confirm-dialog
                                    :action="route('admin.bookings.cancel', $booking)"
                                    method="POST"
                                    message="Cancel this booking? This action cannot be undone."
                                    btnLabel=""
                                    btnClass="flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition shadow-sm border border-red-200/60"
                                />
                                </span>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-16 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-slate-400 text-sm">No bookings found.</p>
                        <a href="{{ route('admin.bookings.index') }}" class="mt-2 inline-block text-xs text-blue-600 hover:underline">Clear filters</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
