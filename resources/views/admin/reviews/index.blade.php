@extends('layouts.admin')
@section('title', 'Venue Reviews')
@section('breadcrumb', 'Reviews')

@section('content')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-700">All Reviews</h3>
        <span class="text-xs text-slate-400">{{ $reviews->total() }} total reviews</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    <th class="text-left px-5 py-3">Venue</th>
                    <th class="text-left px-4 py-3">Customer</th>
                    <th class="text-left px-4 py-3">Rating</th>
                    <th class="text-left px-4 py-3">Comment</th>
                    <th class="text-left px-4 py-3">Date</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($reviews as $review)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-5 py-4">
                        <span class="font-semibold text-slate-800">{{ $review->venue->name }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-slate-700 font-medium">{{ $review->customer->name }}</div>
                        <div class="text-[10px] text-slate-400">{{ $review->customer->phone }}</div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-0.5 text-amber-400">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'fill-slate-200' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-slate-600 text-xs line-clamp-2 max-w-xs">{{ $review->comment ?? 'No comment' }}</p>
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-400">
                        {{ $review->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-4">
                        @if($review->is_published)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-200">Published</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Hidden</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-1.5">
                            <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ $review->is_published ? 'Hide' : 'Publish' }}" 
                                        class="flex items-center justify-center w-7 h-7 rounded-lg {{ $review->is_published ? 'bg-slate-100 text-slate-600' : 'bg-blue-50 text-blue-600' }} hover:scale-105 transition shadow-sm border border-slate-200/60">
                                    @if($review->is_published)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.504-4.825m5.392.404a9.965 9.965 0 014.641 4.421m-4.421 4.421L15.536 15.536M11.264 11.264L15.536 15.536M3 3l18 18"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @endif
                                </button>
                            </form>
                            
                            <span title="Delete">
                                <x-confirm-dialog 
                                    :action="route('admin.reviews.destroy', $review)" 
                                    method="DELETE"
                                    message="Permanently delete this review?"
                                    btnLabel=""
                                    btnClass="flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition shadow-sm border border-red-200/60"
                                />
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-sm">No reviews found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($reviews->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection
