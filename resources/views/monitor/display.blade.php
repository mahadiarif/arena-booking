@extends('layouts.monitor')
@section('content')

<div x-data="{
    data: { venues: [] },
    lastUpdated: 0,
    now: '',
    async fetchData() {
        try {
            const token = '{{ config('arenabook.monitor_token') }}';
            const r = await fetch('/monitor/data?token=' + token);
            this.data = await r.json();
            this.lastUpdated = 0;
        } catch(e) { console.error(e); }
    },
    tick() {
        const d = new Date();
        this.now = d.toLocaleTimeString('en-BD', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        this.lastUpdated++;
    },
    init() {
        this.fetchData();
        this.tick();
        setInterval(() => this.tick(), 1000);
        setInterval(() => this.fetchData(), 60000);
    }
}" class="min-h-screen flex flex-col p-6" style="background:#050d1a;">

  {{-- Top Bar --}}
  <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/5">
    <div class="flex items-center gap-3">
      <span class="text-2xl">⚽</span>
      <div>
        <h1 class="text-white font-bold text-xl tracking-tight">ArenaBook <span class="text-blue-400">LIVE</span></h1>
        <div class="flex items-center gap-1.5 mt-0.5">
          <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
          <span class="text-green-400 text-xs font-semibold">LIVE</span>
        </div>
      </div>
    </div>
    <div class="text-right">
      <p class="text-white text-3xl font-mono font-bold" x-text="now">--:--:--</p>
      <p class="text-slate-500 text-xs mt-0.5">{{ now()->format('d M Y') }}</p>
    </div>
  </div>

  {{-- Venue Grid --}}
  <div class="flex-1 grid gap-5" :class="`grid-cols-${Math.min(data.venues?.length||1,4)}`">
    <template x-for="venue in (data.venues||[])" :key="venue.id">
      <div class="rounded-2xl border border-white/5 overflow-hidden flex flex-col" style="background:#0d1829;">
        {{-- Venue Header --}}
        <div class="px-4 py-3 border-b border-white/5 flex items-center gap-2"
             :style="`border-left: 4px solid ${venue.color||'#3b82f6'}`">
          <span class="font-bold text-white text-sm" x-text="venue.name"></span>
          <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full text-white/70 bg-white/10" x-text="venue.type"></span>
        </div>

        {{-- Current Booking --}}
        <div class="p-4 border-b border-white/5">
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Current</p>
          <template x-if="venue.current">
            <div>
              <p class="text-white font-bold text-lg leading-tight" x-text="venue.current.customer_name"></p>
              <p class="text-slate-400 text-sm mt-0.5" x-text="venue.current.time_range"></p>
              <p class="text-slate-600 font-mono text-xs mt-1" x-text="venue.current.booking_ref"></p>
            </div>
          </template>
          <template x-if="!venue.current">
            <p class="text-slate-600 text-sm italic">No active booking</p>
          </template>
        </div>

        {{-- Upcoming --}}
        <div class="p-4 flex-1">
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Upcoming</p>
          <template x-if="venue.upcoming && venue.upcoming.length">
            <div class="space-y-2">
              <template x-for="(bk,i) in venue.upcoming.slice(0,3)" :key="i">
                <div class="flex items-center gap-3 py-2 border-b border-white/5 last:border-0">
                  <div class="w-1 h-8 rounded-full flex-shrink-0" :style="`background-color:${venue.color||'#3b82f6'}60`"></div>
                  <div class="flex-1">
                    <p class="text-white text-sm font-semibold" x-text="bk.customer_name"></p>
                    <p class="text-slate-400 text-xs" x-text="bk.time_range"></p>
                  </div>
                </div>
              </template>
            </div>
          </template>
          <template x-if="!venue.upcoming || !venue.upcoming.length">
            <p class="text-slate-600 text-sm italic">No upcoming bookings</p>
          </template>
        </div>
      </div>
    </template>

    <template x-if="!data.venues || data.venues.length === 0">
      <div class="col-span-4 flex items-center justify-center text-slate-600 text-lg">Loading live data…</div>
    </template>
  </div>

  {{-- Footer --}}
  <div class="mt-5 pt-3 border-t border-white/5 flex items-center justify-between">
    <p class="text-slate-600 text-xs">ArenaBook Monitor Display</p>
    <p class="text-slate-600 text-xs">Last updated: <span x-text="lastUpdated"></span>s ago</p>
  </div>
</div>
@endsection
