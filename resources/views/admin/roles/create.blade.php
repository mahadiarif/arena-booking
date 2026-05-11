@extends('layouts.admin')
@section('title', 'Create Role')
@section('breadcrumb', 'New Role')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Create New Role</h1>
        <p class="text-sm text-slate-500 font-medium">Define a new access level and assign permissions</p>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-8">
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-3">Role Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Supervisor, Manager" required
                       class="w-full sm:w-1/2 text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50 font-bold">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Assign Permissions</label>
                    <button type="button" onclick="document.querySelectorAll('.perm-check').forEach(c=>c.checked=true)"
                            class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Select All</button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($permissions as $permission)
                    <label class="relative flex items-center gap-3 p-3 rounded-2xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50 cursor-pointer transition group">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="perm-check w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                        <span class="text-xs font-semibold text-slate-600 group-hover:text-blue-700 transition">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('permissions')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-3d btn-3d-primary flex-1 py-4">
                Create Role
            </button>
            <a href="{{ route('admin.roles.index') }}" class="px-8 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
