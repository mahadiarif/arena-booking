@props(['status'])

{{-- $status is a BookingStatus enum instance --}}
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $status->color() }}">
    {{ $status->label() }}
</span>
