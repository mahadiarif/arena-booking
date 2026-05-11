@props([
    'title',
    'value',
    'change'      => null,
    'changeType'  => 'up',
    'color'       => 'blue',
    'icon'        => '',
    'extra_class' => ''
])

@php
$colorMap = [
    'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'bg-blue-100',   'text' => 'text-blue-600'],
    'green'  => ['bg' => 'bg-green-50',  'icon' => 'bg-green-100',  'text' => 'text-green-600'],
    'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
    'red'    => ['bg' => 'bg-red-50',    'icon' => 'bg-red-100',    'text' => 'text-red-600'],
    'purple' => ['bg' => 'bg-purple-50', 'icon' => 'bg-purple-100', 'text' => 'text-purple-600'],
    'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
];
$c = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div class="bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200 {{ $extra_class ?: 'rounded-2xl border border-slate-100' }}">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $title }}</p>
            <p class="text-3xl font-bold text-slate-800 mt-1.5 leading-none">{{ $value }}</p>

            @if($change !== null)
            <div class="flex items-center gap-1 mt-2.5">
                @if($changeType === 'up')
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    <span class="text-xs font-semibold text-green-600">{{ $change }}</span>
                @elseif($changeType === 'down')
                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    <span class="text-xs font-semibold text-red-600">{{ $change }}</span>
                @else
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"/></svg>
                    <span class="text-xs font-semibold text-slate-500">{{ $change }}</span>
                @endif
                <span class="text-xs text-slate-400">vs last month</span>
            </div>
            @endif
        </div>

        {{-- Icon Box --}}
        <div class="w-11 h-11 {{ $c['icon'] }} {{ $c['text'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            {!! $icon !!}
        </div>
    </div>
</div>
