<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Metro ArenaBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .btn-3d { 
            position: relative; 
            transition: all 0.2s ease; 
            box-shadow: 0 4px 0 rgba(0,0,0,0.1);
        }
        .btn-3d:active { 
            transform: translateY(2px); 
            box-shadow: 0 2px 0 rgba(0,0,0,0.1);
        }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
    </style>
</head>
<body class="text-slate-800">

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 glass border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Metro <span class="text-blue-600">ArenaBook</span></span>
                </div>

                <div class="hidden md:flex items-center gap-8 text-sm font-semibold">
                    <a href="{{ route('home') }}" class="{{ Route::is('home') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }} transition">Home</a>
                    <a href="{{ route('venues.index') }}" class="{{ Route::is('venues.*') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }} transition">Browse Turfs</a>
                    <a href="{{ route('about') }}" class="{{ Route::is('about') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }} transition">About Us</a>
                    <a href="{{ route('contact') }}" class="{{ Route::is('contact') ? 'text-blue-600' : 'text-slate-500 hover:text-blue-600' }} transition">Contact</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600">My Dashboard</a>
                        @if(auth()->user()->is_admin) {{-- Assuming you have an is_admin check --}}
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-blue-600">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-bold transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 transition btn-3d">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 pt-16 pb-8 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-lg font-bold">Metro ArenaBook</span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        The ultimate destination for booking your favorite sports arenas and turfs. Easy, fast, and reliable.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-blue-600 transition">Search Venues</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">How it Works</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Member Benefits</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-blue-600 transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Newsletter</h4>
                    <p class="text-xs text-slate-500 mb-4">Get the latest updates and offers.</p>
                    <div class="flex gap-2">
                        <input type="email" placeholder="Email" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-blue-100">Join</button>
                    </div>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-100 text-center text-slate-400 text-xs">
                &copy; {{ date('Y') }} Metro ArenaBook. All rights reserved. | Developed by MetroNet Bangladesh Ltd.
            </div>
        </div>
    </footer>

</body>
</html>
