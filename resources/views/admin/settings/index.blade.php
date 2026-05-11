@extends('layouts.admin')
@section('title','Settings')
@section('breadcrumb','Settings')
@section('content')

<div x-data="{ tab: 'general' }">
  {{-- Tabs --}}
  <div class="flex gap-1 bg-white border border-slate-100 rounded-2xl shadow-sm p-1.5 mb-5 inline-flex">
    @foreach(['general'=>'General','booking'=>'Booking Rules','notifications'=>'Notifications','sms'=>'SMS'] as $key=>$label)
    <button @click="tab='{{ $key }}'"
            :class="tab==='{{ $key }}'?'bg-blue-600 text-white shadow-sm':'text-slate-500 hover:text-slate-700'"
            class="text-sm font-semibold px-5 py-2 rounded-xl transition">{{ $label }}</button>
    @endforeach
  </div>

  @foreach(['general'=>'General','booking'=>'Booking Rules','notifications'=>'Notifications','sms'=>'SMS'] as $groupKey=>$groupLabel)
  <div x-show="tab==='{{ $groupKey }}'" x-cloak>
    <form method="POST" action="{{ route('admin.settings.update') }}">
      @csrf
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
          <h3 class="text-sm font-semibold text-slate-700">{{ $groupLabel }} Settings</h3>
        </div>
        <div class="divide-y divide-slate-50">
          @forelse($settings->get($groupKey, collect()) as $setting)
          <div class="flex items-start sm:items-center gap-4 px-5 py-4 flex-col sm:flex-row">
            <div class="sm:w-64 flex-shrink-0">
              <p class="text-sm font-semibold text-slate-700">{{ ucwords(str_replace(['_','.'],' ', explode('.',$setting->key)[1]??$setting->key)) }}</p>
              <p class="text-xs text-slate-400 mt-0.5">Key: {{ $setting->key }}</p>
            </div>
            <div class="flex-1">
              @if($setting->type === 'boolean')
                <label class="flex items-center gap-3 cursor-pointer" x-data="{ on: {{ $setting->value === 'true' ? 'true' : 'false' }} }">
                  <input type="hidden" name="settings[{{ $setting->key }}]" :value="on ? 'true' : 'false'">
                  <div @click="on=!on" class="w-10 h-5 rounded-full transition cursor-pointer" :class="on?'bg-blue-600':'bg-slate-200'">
                    <div class="mt-0.5 ml-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="on?'translate-x-5':''"></div>
                  </div>
                  <span class="text-sm text-slate-500" x-text="on?'Enabled':'Disabled'"></span>
                </label>
              @elseif($setting->type === 'integer')
                <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                       class="w-32 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 font-mono">
              @else
                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                       class="w-full max-w-md text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
              @endif
            </div>
          </div>
          @empty
          <div class="px-5 py-8 text-center text-slate-400 text-sm">No settings in this group.</div>
          @endforelse
        </div>
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition shadow-sm shadow-blue-900/20">
            Save {{ $groupLabel }} Settings
          </button>
        </div>
      </div>
    </form>
  </div>
  @endforeach
</div>
@endsection