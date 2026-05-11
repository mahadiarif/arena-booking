@extends('layouts.auth')
@section('title','Login')
@section('content')

<h2 class="text-xl font-bold text-white text-center mb-1">Welcome back</h2>
<p class="text-slate-400 text-sm text-center mb-6">Sign in to your Metro ArenaBook account</p>

<form method="POST" action="{{ route('login') }}" class="space-y-4">
  @csrf
  <div>
    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="email" required
           class="w-full bg-white/5 border border-white/10 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror">
    @error('email')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
  </div>
  <div>
    <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
    <input type="password" id="password" name="password" required autocomplete="current-password"
           class="w-full bg-white/5 border border-white/10 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    @error('password')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
  </div>
  <div class="flex items-center justify-between">
    <label class="flex items-center gap-2 cursor-pointer select-none">
      <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-white/20 bg-white/5 text-blue-600 focus:ring-blue-500">
      <span class="text-sm text-slate-400">Remember me</span>
    </label>
  </div>
  <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-blue-900/30 text-sm mt-2">
    Sign In
  </button>

  <p class="text-center text-slate-400 text-sm mt-6">
    Don't have an account? <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-400 font-bold">Register</a>
  </p>
</form>
@endsection
