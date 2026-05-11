@extends('layouts.admin')
@section('title', 'Role Management')
@section('breadcrumb', 'Roles')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">System Roles</h1>
        <p class="text-sm text-slate-500 font-medium">Define access levels and permissions</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn-3d btn-3d-primary px-5 py-2.5 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Role
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($roles as $role)
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition">
                <a href="{{ route('admin.roles.edit', $role) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                @if(!in_array($role->name, ['super_admin', 'admin', 'staff']))
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <h3 class="text-lg font-black text-slate-800 capitalize mb-1">{{ str_replace('_', ' ', $role->name) }}</h3>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-4">{{ $role->permissions->count() }} Permissions</p>

        <div class="flex flex-wrap gap-1.5 h-20 overflow-hidden relative">
            @foreach($role->permissions->take(8) as $p)
            <span class="px-2 py-0.5 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg border border-slate-100">{{ $p->name }}</span>
            @endforeach
            @if($role->permissions->count() > 8)
            <span class="px-2 py-0.5 bg-slate-50 text-slate-400 text-[10px] font-bold rounded-lg">+{{ $role->permissions->count() - 8 }} more</span>
            @endif
            <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white to-transparent"></div>
        </div>

        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50/50 rounded-full"></div>
    </div>
    @endforeach
</div>
@endsection
