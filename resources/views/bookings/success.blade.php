@extends('layouts.app')

@section('title', 'Booking Successful')

@section('content')
<div class="bg-slate-50 min-h-screen py-20 flex items-center justify-center">
    <div class="max-w-md w-full px-4">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl overflow-hidden relative">
            {{-- Success Animation/Icon --}}
            <div class="pt-12 pb-8 text-center relative z-10">
                <div class="w-24 h-24 bg-green-100 text-green-600 rounded-[2rem] flex items-center justify-center mx-auto mb-6 animate-bounce shadow-lg shadow-green-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Success!</h1>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px] mt-1">Booking Confirmed</p>
            </div>

            {{-- Booking Details Card --}}
            <div class="px-8 pb-8">
                <div class="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100">
                    <div class="flex flex-col gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase">Ref #</span>
                            <span class="text-sm font-mono font-black text-blue-600 uppercase">{{ $booking->booking_ref }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase">Venue</span>
                            <span class="text-sm font-bold text-slate-800">{{ $booking->venue->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase">Date</span>
                            <span class="text-sm font-bold text-slate-800">{{ $booking->check_in_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-400 uppercase">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $booking->status->isTerminal() ? 'bg-slate-100 text-slate-600' : 'bg-blue-600 text-white' }} uppercase">
                                {{ $booking->status->value }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('home') }}" class="block w-full bg-slate-900 hover:bg-slate-800 text-white text-center py-4 rounded-2xl font-bold transition btn-3d">
                        Go to Home
                    </a>
                    <p class="text-center text-[10px] text-slate-400 font-medium px-4">
                        A confirmation email has been sent to your registered address. Please show this reference at the venue.
                    </p>
                </div>
            </div>

            {{-- Decorative elements --}}
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-blue-50 rounded-full opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-green-50 rounded-full opacity-50 blur-3xl"></div>
        </div>
    </div>
</div>
@endsection
