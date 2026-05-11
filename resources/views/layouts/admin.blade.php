<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ArenaBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* ── Scrollbar ─────────────────────────── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        /* ── Page background — subtle grid ─────── */
        body {
            background-color: #eef2f7;
            background-image:
                linear-gradient(rgba(148,163,184,.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148,163,184,.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* ── 3D Card system ─────────────────────── */
        .card-3d {
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.9);
            box-shadow:
                0 1px 0   rgba(255,255,255,.8) inset,  /* top bevel */
                0 -1px 0  rgba(0,0,0,.04)     inset,  /* bottom inner shadow */
                0 4px 6   -1px rgba(0,0,0,.07),
                0 10px 20px -5px rgba(0,0,0,.08),
                0 1px 3px rgba(0,0,0,.06);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .card-3d:hover {
            transform: translateY(-1px);
            box-shadow:
                0 1px 0   rgba(255,255,255,.8) inset,
                0 -1px 0  rgba(0,0,0,.04)     inset,
                0 8px 16px -4px rgba(0,0,0,.12),
                0 20px 35px -8px rgba(0,0,0,.10),
                0 1px 3px rgba(0,0,0,.06);
        }

        /* ── 3D Stat card ───────────────────────── */
        .stat-card-3d {
            border-radius: 18px;
            border-top: 1px solid rgba(255,255,255,.5);
            box-shadow:
                0 2px 0  rgba(255,255,255,.6) inset,
                0 8px 16px -4px rgba(0,0,0,.12),
                0 20px 32px -8px rgba(0,0,0,.08);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card-3d:hover {
            transform: translateY(-1px);
            box-shadow:
                0 2px 0 rgba(255,255,255,.6) inset,
                0 14px 28px -6px rgba(0,0,0,.18),
                0 28px 48px -10px rgba(0,0,0,.12);
        }

        /* ── 3D Button system ───────────────────── */
        .btn-3d {
            position: relative;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13.5px;
            border: none;
            cursor: pointer;
            transition: transform .1s, box-shadow .1s;
        }
        .btn-3d-primary {
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            box-shadow:
                0 1px 0 rgba(255,255,255,.25) inset,
                0 -1px 0 rgba(0,0,0,.2) inset,
                0 4px 8px -2px rgba(37,99,235,.5),
                0 8px 20px -4px rgba(37,99,235,.3);
        }
        .btn-3d-primary:hover {
            background: linear-gradient(180deg, #60a5fa 0%, #3b82f6 100%);
            transform: translateY(-1px);
            box-shadow:
                0 1px 0 rgba(255,255,255,.25) inset,
                0 -1px 0 rgba(0,0,0,.2) inset,
                0 6px 14px -2px rgba(37,99,235,.55),
                0 12px 28px -4px rgba(37,99,235,.35);
        }
        .btn-3d-primary:active {
            transform: translateY(1px);
            box-shadow:
                0 1px 0 rgba(0,0,0,.15) inset,
                0 2px 4px -1px rgba(37,99,235,.3);
        }

        /* ── 3D Table ───────────────────────────── */
        .table-3d {
            border-radius: 18px;
            overflow: hidden;
            box-shadow:
                0 1px 0 rgba(255,255,255,.8) inset,
                0 4px 6px -1px rgba(0,0,0,.06),
                0 10px 20px -5px rgba(0,0,0,.07);
        }
        .table-3d thead {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }
        .table-3d tbody tr {
            transition: background .12s, transform .12s;
        }
        .table-3d tbody tr:hover {
            background: linear-gradient(180deg, #f0f7ff 0%, #e8f2ff 100%);
        }

        /* ── 3D Input ───────────────────────────── */
        .input-3d {
            background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
            border: 1px solid #e2e8f0;
            border-top-color: #cbd5e1;
            box-shadow:
                0 1px 0 rgba(255,255,255,.9) inset,
                0 2px 4px rgba(0,0,0,.05) inset;
            transition: border-color .15s, box-shadow .15s;
            border-radius: 12px;
        }
        .input-3d:focus {
            border-color: #3b82f6;
            box-shadow:
                0 1px 0 rgba(255,255,255,.9) inset,
                0 2px 4px rgba(0,0,0,.05) inset,
                0 0 0 3px rgba(59,130,246,.15);
            outline: none;
        }

        /* ── 3D Sidebar ─────────────────────────── */
        aside {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            box-shadow:
                1px 0 0 rgba(255,255,255,.04),
                4px 0 24px rgba(0,0,0,.3);
        }

        /* ── Nav items ──────────────────────────── */
        .sidebar-nav { height: calc(100vh - 140px); overflow-y: auto; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 12px;
            font-size: 13.5px; font-weight: 500; color: #94a3b8;
            transition: all .15s;
            border: 1px solid transparent;
        }
        .nav-item:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
            border-color: rgba(255,255,255,.05);
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
            transform: translateX(1px);
        }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(59,130,246,.25) 0%, rgba(99,102,241,.15) 100%);
            color: #93c5fd;
            border-color: rgba(59,130,246,.25);
            box-shadow:
                0 1px 0 rgba(255,255,255,.05) inset,
                0 2px 8px rgba(37,99,235,.25);
        }
        .nav-icon { width: 16px; height: 16px; flex-shrink: 0; }

        /* ── 3D Topbar ──────────────────────────── */
        header {
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,.7);
            box-shadow:
                0 1px 0 rgba(255,255,255,.8) inset,
                0 4px 16px rgba(0,0,0,.06);
        }

        /* ── Badge ──────────────────────────────── */
        .badge-3d {
            box-shadow:
                0 1px 0 rgba(255,255,255,.25) inset,
                0 2px 4px rgba(0,0,0,.15);
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50">

{{-- SIDEBAR --}}
<aside class="fixed inset-y-0 left-0 w-64 flex flex-col z-30 bg-slate-900 border-r border-white/5">
    <div class="flex items-center gap-3 px-5 h-16 border-b border-white/5 flex-shrink-0">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-base shadow-lg shadow-blue-900/50">⚽</div>
        <div>
            <p class="text-white font-bold text-sm leading-none">ArenaBook</p>
            <p class="text-slate-500 text-[10px] mt-0.5 uppercase tracking-wider">Booking System</p>
        </div>
    </div>

    <nav class="sidebar-nav px-3 py-3 space-y-0.5">
        <p class="px-3 py-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Main</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        <p class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Manage</p>

        @can('create bookings')
        <a href="{{ route('admin.bookings.create') }}" style="background:#2563eb;color:#fff;" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Booking
        </a>
        @endcan

        @can('view bookings')
        @php $todayCount = \App\Models\Booking::today()->count(); @endphp
        <a href="{{ route('admin.bookings.index') }}" class="nav-item {{ Route::is('admin.bookings.*') && !Route::is('admin.bookings.create') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Bookings
            @if($todayCount > 0)<span class="ml-auto bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $todayCount }}</span>@endif
        </a>

        <a href="{{ route('admin.reviews.index') }}" class="nav-item {{ Route::is('admin.reviews.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            Reviews
        </a>
        @endcan

        @can('view customers')
        <a href="{{ route('admin.customers.index') }}" class="nav-item {{ Route::is('admin.customers.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Customers
        </a>
        @endcan

        @can('view venues')
        <a href="{{ route('admin.venues.index') }}" class="nav-item {{ Route::is('admin.venues.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Venues
        </a>
        @endcan

        @can('manage slots')
        <a href="{{ route('admin.slots.index') }}" class="nav-item {{ Route::is('admin.slots.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Slots
        </a>
        <a href="{{ route('admin.schedules.index') }}" class="nav-item {{ Route::is('admin.schedules.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Schedules
        </a>
        @endcan

        @can('manage waitlist')
        <a href="{{ route('admin.waitlist.index') }}" class="nav-item {{ Route::is('admin.waitlist.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Waitlist
        </a>
        @endcan

        @can('view reports')
        <p class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Finance</p>
        <a href="{{ route('admin.reports.index') }}" class="nav-item {{ Route::is('admin.reports.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Reports
        </a>
        @endcan

        @can('manage settings')
        <p class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">System</p>
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ Route::is('admin.settings.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Settings
        </a>
        @endcan
    </nav>

    {{-- Bottom User Info --}}
    <div class="px-3 py-3 border-t border-white/5 flex-shrink-0">
        <div class="flex items-center gap-2.5 px-2 py-1.5">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-xs font-semibold truncate leading-none">{{ auth()->user()->name }}</p>
                <p class="text-slate-500 text-[10px] mt-0.5 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-1.5 text-slate-500 hover:text-red-400 hover:bg-white/5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- TOPBAR --}}
<header class="fixed top-0 left-64 right-0 h-16 bg-white border-b border-slate-200 z-20 flex items-center px-6 gap-4">
    <div class="flex-1 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-700 text-sm">@yield('breadcrumb', 'Dashboard')</span>
    </div>
    <span class="hidden lg:block text-xs text-slate-400">{{ now()->format('l, d M Y') }}</span>

    @php $dueCount = \App\Models\Booking::whereRaw('paid_amount < total_amount')->whereNotIn('status',['cancelled','no_show','completed'])->count(); @endphp
    <a href="{{ route('admin.bookings.index') }}" class="relative p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if($dueCount > 0)<span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ min($dueCount, 9) }}{{ $dueCount > 9 ? '+' : '' }}</span>@endif
    </a>

    <div x-data="{ open: false }" class="relative">
        <button @click="open=!open" @click.away="open=false" class="flex items-center gap-2 px-2 py-1 rounded-xl hover:bg-slate-100 transition">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-[11px] font-bold text-white">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</div>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1 z-50 origin-top-right">
            <div class="px-4 py-2.5 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

{{-- FLASH MESSAGES --}}
<div class="fixed top-20 left-64 right-0 px-6 z-10 space-y-2 pointer-events-none">
    @if(session('success'))
    <div x-data="{show:true}" x-show="show" x-cloak x-init="setTimeout(()=>show=false,4000)"
         x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="pointer-events-auto flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-sm text-sm font-medium">
        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="flex-1">{{ session('success') }}</span>
        <button @click="show=false" class="text-green-500 hover:text-green-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    @endif
    @if(session('error') || ($errors->any() && !session('success')))
    <div x-data="{show:true}" x-show="show" x-cloak x-init="setTimeout(()=>show=false,6000)"
         x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="pointer-events-auto bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm text-sm">
        <div class="flex items-start gap-3">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="flex-1">
                @if(session('error'))<p class="font-medium">{{ session('error') }}</p>@endif
                @if($errors->any())<ul class="space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
            </div>
            <button @click="show=false" class="text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
    @endif
</div>

{{-- MAIN CONTENT --}}
<main class="ml-64 pt-16 min-h-screen bg-gray-50">
    <div class="p-6">
        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
