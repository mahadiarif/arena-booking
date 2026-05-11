@props(['status'])

{{-- $status is a SlotStatus enum instance --}}
<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $status->badgeClass() }}">
    {{ $status->label() }}
</span>
