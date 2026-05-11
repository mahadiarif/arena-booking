@extends('layouts.admin')
@section('title','Customers')
@section('breadcrumb','Customers')
@section('content')

<div class="flex items-center justify-between mb-5">
  <form method="GET" action="{{ route('admin.customers.index') }}" class="flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone…"
           class="text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 w-64">
    <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">Search</button>
    @if(request('search'))
    <a href="{{ route('admin.customers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition">Clear</a>
    @endif
  </form>
  @can('create customers')
  <a href="{{ route('admin.customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add Customer
  </a>
  @endcan
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50/50 border-b border-slate-100">
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Phone</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Organization</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Bookings</th>
          <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Credit</th>
          <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($customers as $c)
        <tr class="hover:bg-slate-50/50 transition">
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-300 to-slate-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                {{ strtoupper(substr($c->name,0,2)) }}
              </div>
              <div>
                <a href="{{ route('admin.customers.show', $c) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition">{{ $c->name }}</a>
                @if($c->email)<p class="text-xs text-slate-400">{{ $c->email }}</p>@endif
              </div>
            </div>
          </td>
          <td class="px-4 py-3.5 text-slate-700 font-mono text-sm">{{ $c->phone }}</td>
          <td class="px-4 py-3.5 text-slate-500 text-sm hidden md:table-cell">{{ $c->organization ?? '—' }}</td>
          <td class="px-4 py-3.5 text-right font-semibold text-slate-700">{{ $c->bookings_count }}</td>
          <td class="px-4 py-3.5 text-right font-semibold {{ $c->credit_balance > 0 ? 'text-green-700' : 'text-slate-300' }}">
            {{ $c->credit_balance > 0 ? '৳'.number_format($c->credit_balance,0) : '—' }}
          </td>
          <td class="px-4 py-3.5">
            <div class="flex items-center gap-1.5 flex-wrap">
              <a href="{{ route('admin.customers.show', $c) }}" title="View" class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition shadow-sm border border-blue-200/60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              </a>
              @can('update', $c)
              <a href="{{ route('admin.customers.edit', $c) }}" title="Edit" class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-100 transition shadow-sm border border-slate-200/60">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </a>
              @endcan
              @can('delete', $c)
              <span title="Delete">
              <x-confirm-dialog :action="route('admin.customers.destroy', $c)" method="DELETE"
                 message="Delete this customer? All related data will be preserved." btnLabel=""
                 btnClass="flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition shadow-sm border border-red-200/60" />
              </span>
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400 text-sm">No customers found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($customers->hasPages())
  <div class="px-5 py-4 border-t border-slate-100">{{ $customers->links() }}</div>
  @endif
</div>
@endsection
