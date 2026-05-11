@extends('layouts.admin')
@section('title', 'Reports')
@section('breadcrumb', 'Reports')
@section('content')

@php
    $statusRows = collect($summary['by_status'])->filter(fn ($count) => $count > 0);
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-blue-600">Business Intelligence</p>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">Reports</h1>
            <p class="mt-1 text-sm text-slate-500">Track bookings, collections, outstanding payments, and venue performance.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-emerald-700">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            Today: {{ \Carbon\Carbon::parse($summary['date'])->format('d M Y') }}
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Today's Bookings</p>
            <div class="mt-3 flex items-end gap-2">
                <span class="text-4xl font-black text-slate-900">{{ $summary['total_bookings'] }}</span>
                <span class="pb-1 text-xs font-bold uppercase tracking-widest text-slate-400">bookings</span>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($statusRows as $status => $count)
                    <div class="flex items-center justify-between rounded-md bg-slate-50 px-3 py-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="text-xs font-black text-slate-900">{{ $count }}</span>
                    </div>
                @empty
                    <p class="rounded-md bg-slate-50 px-3 py-3 text-xs font-semibold text-slate-400">No bookings recorded today.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Collected Today</p>
                    <p class="mt-3 text-4xl font-black text-emerald-600">BDT {{ number_format($summary['revenue_today']) }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 10v-1m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs font-semibold text-slate-500">Payments received against today's bookings.</p>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Outstanding Today</p>
                    <p class="mt-3 text-4xl font-black {{ $summary['due_today'] > 0 ? 'text-rose-600' : 'text-slate-900' }}">BDT {{ number_format($summary['due_today']) }}</p>
                </div>
                <div class="rounded-lg bg-rose-50 p-2 text-rose-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs font-semibold text-slate-500">Use revenue reports to find unpaid bookings quickly.</p>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <a href="{{ route('admin.reports.utilization') }}" class="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-lg">
            <div class="flex items-start justify-between gap-4">
                <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm6 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2zM9 19h6"/></svg>
                </div>
                <svg class="h-5 w-5 text-slate-300 transition group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
            <h2 class="mt-5 text-lg font-black text-slate-900">Venue Utilization</h2>
            <p class="mt-1 text-sm leading-6 text-slate-500">Compare slot supply, booked slots, occupancy percentage, and revenue by venue.</p>
        </a>

        <a href="{{ route('admin.reports.revenue') }}" class="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-lg">
            <div class="flex items-start justify-between gap-4">
                <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/></svg>
                </div>
                <svg class="h-5 w-5 text-slate-300 transition group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
            <h2 class="mt-5 text-lg font-black text-slate-900">Revenue Report</h2>
            <p class="mt-1 text-sm leading-6 text-slate-500">Review billed amount, collected payments, outstanding dues, and booking counts.</p>
        </a>
    </div>
</div>
@endsection
