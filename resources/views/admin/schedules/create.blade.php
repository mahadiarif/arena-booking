@extends('layouts.admin')

@section('title', 'Add New Schedule')
@section('breadcrumb', 'Add Schedule')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Create <span class="text-blue-600">Schedule</span></h1>
            <p class="text-slate-500 font-medium">Define operating hours and slot intervals for your venues.</p>
        </div>
        <a href="{{ route('admin.schedules.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-2xl text-sm font-bold transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-8">
        @csrf
        
        {{-- Basic Info Card --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-10">
            <h3 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Schedule Name</label>
                    <input type="text" name="name" required placeholder="e.g. Regular Season 2026"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Timezone</label>
                    <select name="timezone" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="Asia/Dhaka">Asia/Dhaka (GMT+6)</option>
                        <option value="UTC">UTC</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Availability Settings Card --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-10">
            <h3 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Operating Hours & Intervals
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Start Time</label>
                    <input type="time" name="start_time" required value="08:00"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">End Time</label>
                    <input type="time" name="end_time" required value="23:00"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Slot Duration (Min)</label>
                    <select name="slot_interval_minutes" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="30">30 Minutes</option>
                        <option value="60" selected>60 Minutes (1 Hour)</option>
                        <option value="90">90 Minutes</option>
                        <option value="120">120 Minutes (2 Hours)</option>
                    </select>
                </div>
            </div>

            <div class="mb-10">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 block px-1">Working Days</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <label class="relative cursor-pointer group">
                        <input type="checkbox" name="allowed_days[]" value="{{ $day }}" checked class="sr-only peer">
                        <div class="px-6 py-3 rounded-2xl border-2 border-slate-100 bg-white text-slate-500 font-bold text-sm transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 shadow-sm group-hover:border-blue-200">
                            {{ $day }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Effective From</label>
                    <input type="date" name="availability_start" required value="{{ date('Y-m-d') }}"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block px-1">Effective Until</label>
                    <input type="date" name="availability_end" required value="{{ date('Y-m-d', strtotime('+1 month')) }}"
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>
        </div>

        {{-- Peak Pricing Rules Card --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-10"
             x-data="{
                rules: [{start:'18:00', end:'23:00', price:100}],
                addRule() { this.rules.push({start:'', end:'', price:0}) },
                removeRule(i) { this.rules.splice(i, 1) }
             }">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Peak Pricing Rules
                    <span class="text-[10px] font-bold text-slate-400 ml-1">(Optional)</span>
                </h3>
                <button type="button" @click="addRule()"
                        class="flex items-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-600 font-bold text-xs px-4 py-2 rounded-xl transition border border-amber-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Rule
                </button>
            </div>

            <p class="text-xs text-slate-400 mb-6">Add extra charges for peak hours (e.g. evening slots with floodlights). The extra price is added on top of the venue's base price.</p>

            <div class="space-y-4">
                <template x-for="(rule, i) in rules" :key="i">
                    <div class="grid grid-cols-4 gap-4 items-end bg-amber-50/50 border border-amber-100 rounded-2xl p-5">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Peak Start</label>
                            <input type="time" :name="'peak_start[' + i + ']'" x-model="rule.start"
                                   class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-400 transition">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Peak End</label>
                            <input type="time" :name="'peak_end[' + i + ']'" x-model="rule.end"
                                   class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-400 transition">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Extra Price (৳)</label>
                            <input type="number" :name="'peak_price[' + i + ']'" x-model="rule.price" min="0" step="50"
                                   class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-400 transition">
                        </div>
                        <div>
                            <button type="button" @click="removeRule(i)"
                                    class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-500 font-bold text-xs px-4 py-3 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Remove
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="rules.length === 0">
                    <div class="py-8 text-center text-slate-400 text-sm border-2 border-dashed border-slate-100 rounded-2xl">
                        No peak pricing rules. Click "Add Rule" to configure.
                    </div>
                </template>
            </div>
        </div>

        {{-- Submit Section --}}
        <div class="flex items-center justify-end gap-4">
            <button type="reset" class="px-8 py-4 rounded-2xl text-slate-500 font-bold hover:bg-slate-100 transition">Reset Form</button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-2xl font-black text-lg tracking-tight transition btn-3d shadow-xl shadow-blue-100">
                Save Schedule
            </button>
        </div>
    </form>
</div>
@endsection