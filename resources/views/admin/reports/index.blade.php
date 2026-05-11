@extends('layouts.admin')
@section('title', 'Reports')
@section('breadcrumb', 'Reports')
@section('content')
<div class="space-y-5">
    <h1 class="text-2xl font-bold text-slate-800">Reports</h1>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Today's Bookings</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $summary['total_bookings'] }}</p>
            <div class="mt-3 space-y-1">
                @foreach($summary['by_status'] as $status => $count)
                @if($count > 0)
                <div class="flex justify-between text-xs text-slate-500">
                    <span class="capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    <span class="font-semibold">{{ $count }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Today's Revenue</p>
            <p class="text-3xl font-bold text-green-600 mt-1">৳{{ number_format($summary['revenue_today']) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <p class="text-xs font-semibold text-slate-400 uppercase">Outstanding Today</p>
            <p class="text-3xl font-bold text-red-500 mt-1">৳{{ number_format($summary['due_today']) }}</p>
        </div>
    </div>
    <!-- Links to sub-reports -->
    <div class="grid grid-cols-2 gap-5">
        <a href="{{ route('admin.reports.utilization') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:border-blue-300 transition group">
            <h2 class="font-bold text-slate-800 group-hover:text-blue-600 transition">Venue Utilization Report</h2>
            <p class="text-sm text-slate-500 mt-1">Occupancy rates, slot usage, and venue revenue by period.</p>
            <span class="mt-4 block text-xs text-blue-600 font-semibold">View Report →</span>
        </a>
        <a href="{{ route('admin.reports.revenue') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:border-green-300 transition group">
            <h2 class="font-bold text-slate-800 group-hover:text-green-600 transition">Revenue Report</h2>
            <p class="text-sm text-slate-500 mt-1">Daily revenue breakdown, payments, and outstanding dues.</p>
            <span class="mt-4 block text-xs text-green-600 font-semibold">View Report →</span>
        </a>
    </div>
</div>
@endsection
