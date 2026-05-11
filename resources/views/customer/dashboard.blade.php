@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Welcome Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Hello, <span class="text-blue-600">{{ $customer->name }}</span>!</h1>
                <p class="text-slate-500 font-medium">Welcome back to your ArenaBook dashboard.</p>
            </div>
            <div class="bg-white px-6 py-4 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Wallet Balance</p>
                    <p class="text-xl font-black text-slate-900">৳{{ number_format($customer->credit_balance, 0) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Bookings --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-800">My Bookings</h3>
                        <a href="{{ route('venues.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Book New Turf</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    <th class="text-left px-8 py-4">Venue</th>
                                    <th class="text-left px-4 py-4">Date & Time</th>
                                    <th class="text-right px-4 py-4">Amount</th>
                                    <th class="text-center px-8 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($bookings as $booking)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-slate-800">{{ $booking->venue->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $booking->booking_ref }}</div>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="font-semibold text-slate-700">{{ $booking->check_in_at->format('d M, Y') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $booking->check_in_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-4 py-5 text-right">
                                        <span class="font-bold text-slate-900">৳{{ number_format($booking->total_amount, 0) }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight 
                                            {{ $booking->status->value === 'confirmed' ? 'bg-green-100 text-green-600 border border-green-200' : 'bg-blue-50 text-blue-600 border border-blue-200' }}">
                                            {{ $booking->status->value }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                        </div>
                                        <p class="text-slate-400 font-medium text-sm">No bookings found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($bookings->hasPages())
                    <div class="px-8 py-4 border-t border-slate-50">
                        {{ $bookings->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Wallet Transactions --}}
            <div class="space-y-8">
                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-6">Recent Activity</h3>
                        <div class="space-y-6">
                            @forelse($transactions as $tx)
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $tx->amount > 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    @if($tx->amount > 0)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between gap-2">
                                        <p class="text-sm font-bold truncate">{{ str_replace('_', ' ', $tx->type) }}</p>
                                        <p class="text-sm font-black {{ $tx->amount > 0 ? 'text-green-400' : 'text-slate-200' }}">
                                            {{ $tx->amount > 0 ? '+' : '' }}৳{{ number_format(abs($tx->amount), 0) }}
                                        </p>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $tx->note }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-slate-500 text-xs text-center py-4">No recent transactions.</p>
                            @endforelse
                        </div>
                    </div>
                    {{-- Decorative blur --}}
                    <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-blue-600 rounded-full opacity-20 blur-3xl"></div>
                </div>

                {{-- Need Help? --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm text-center">
                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h4 class="font-bold text-slate-800 mb-2">Need Help?</h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">If you face any issues with your booking or wallet, please contact our support team.</p>
                    <a href="#" class="inline-block bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl text-sm font-bold transition">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
