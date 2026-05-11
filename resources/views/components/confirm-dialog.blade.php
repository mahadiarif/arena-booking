@props([
    'action',
    'method'   => 'DELETE',
    'message'  => 'Are you sure? This action cannot be undone.',
    'btnClass' => 'text-red-600 hover:text-red-700',
    'btnLabel' => 'Delete',
])

<div x-data="{ open: false }">

    {{-- Trigger --}}
    <button type="button" @click="open = true" class="{{ $btnClass }} text-sm font-medium transition flex items-center gap-1.5">
        @isset($triggerIcon)
            {!! $triggerIcon !!}
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        @endisset
        {{ $btnLabel }}
    </button>

    {{-- Overlay --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40"
         @click.self="open = false">
    </div>

    {{-- Dialog --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">

            {{-- Warning Icon --}}
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-xl mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <h3 class="text-base font-semibold text-slate-800 text-center">Confirm Action</h3>
            <p class="text-sm text-slate-500 text-center mt-2 leading-relaxed">{{ $message }}</p>

            {{-- Form --}}
            <form method="POST" action="{{ $action }}" class="mt-6 flex gap-3">
                @csrf
                @if(strtoupper($method) !== 'POST')
                    @method($method)
                @endif

                <button type="button" @click="open = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition shadow-sm shadow-red-900/20">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>
