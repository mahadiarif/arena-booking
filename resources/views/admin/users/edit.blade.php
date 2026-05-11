@extends('layouts.admin')
@section('title', 'Edit User')
@section('breadcrumb', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit User</h1>
            <p class="text-sm text-slate-500 font-medium">Update profile and permissions for {{ $user->name }}</p>
        </div>
        <div class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100">
            ID: #{{ $user->id }}
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Assign Role</label>
                        <select name="role" required
                                class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucfirst($role->name)) }}
                            </option>
                            @endforeach
                        </select>
                        @error('role')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Account Status</label>
                        <div class="flex items-center gap-3 py-3">
                             <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-slate-600">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 italic">Change Password (leave blank to keep current)</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-400 block mb-2">New Password</label>
                            <input type="password" name="password"
                                   class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                            @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-400 block mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-3d btn-3d-primary flex-1 py-3.5">
                Update User
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3.5 text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
