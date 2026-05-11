@extends('layouts.admin')
@section('title', 'Send Promotional Message')
@section('breadcrumb', 'New Campaign')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Create Campaign</h1>
        <p class="text-sm text-slate-500 font-medium">Broadcast a message to your customers</p>
    </div>

    <form action="{{ route('admin.marketing.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Campaign Title</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Summer Discount Offer" required
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50 font-bold">
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Communication Channel</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="sms" class="sr-only peer" checked>
                            <div class="px-3 py-4 border border-slate-200 rounded-2xl flex flex-col items-center gap-2 transition peer-checked:border-blue-500 peer-checked:bg-blue-50/50 peer-checked:text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <span class="text-[10px] font-black uppercase">SMS</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="whatsapp" class="sr-only peer">
                            <div class="px-3 py-4 border border-slate-200 rounded-2xl flex flex-col items-center gap-2 transition peer-checked:border-green-500 peer-checked:bg-green-50/50 peer-checked:text-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span class="text-[10px] font-black uppercase">WhatsApp</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="email" class="sr-only peer">
                            <div class="px-3 py-4 border border-slate-200 rounded-2xl flex flex-col items-center gap-2 transition peer-checked:border-purple-500 peer-checked:bg-purple-50/50 peer-checked:text-purple-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-[10px] font-black uppercase">Email</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Target Audience</label>
                    <select name="target_audience" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50 font-bold">
                        <option value="all">All Customers ({{ $customerCount }})</option>
                        <option value="selected" disabled>Specific Group (Coming Soon)</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Message Content</label>
                    <textarea name="message" rows="5" required
                              placeholder="Type your message here..."
                              class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50"></textarea>
                    <p class="text-[10px] text-slate-400 mt-2 italic">* For SMS, try to keep it under 160 characters for a single unit.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-3d btn-3d-primary flex-1 py-4 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Launch Campaign
            </button>
            <a href="{{ route('admin.marketing.index') }}" class="px-6 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
