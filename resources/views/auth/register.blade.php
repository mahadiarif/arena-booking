@extends('layouts.app')

@section('title', 'Join Metro ArenaBook')

@section('content')
<div class="bg-slate-50 min-h-screen py-20 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl overflow-hidden p-10">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Create Account</h1>
                <p class="text-slate-400 text-sm mt-1">Join the community of athletes.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Full Name</label>
                    <input type="text" name="name" required placeholder="John Doe"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Password</label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Confirm</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-5 rounded-2xl font-black text-lg tracking-tight transition btn-3d shadow-xl shadow-blue-100 mt-4">
                    Sign Up
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-slate-400">Already have an account? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Sign In</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
