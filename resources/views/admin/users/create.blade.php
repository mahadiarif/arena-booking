@extends('layouts.admin')
@section('title', 'Create User')
@section('breadcrumb', 'New User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Create New User</h1>
        <p class="text-sm text-slate-500 font-medium">Add a new administrative team member</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Assign Role</label>
                    <select name="role" required
                            class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                        <option value="">— Select Role —</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', ucfirst($role->name)) }}
                        </option>
                        @endforeach
                    </select>
                    @error('role')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Password</label>
                        <input type="password" name="password" required
                               class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                        @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-3d btn-3d-primary flex-1 py-3.5">
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3.5 text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
