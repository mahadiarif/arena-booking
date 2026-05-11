@extends('layouts.admin')
@section('title', 'Marketing Configuration')
@section('breadcrumb', 'Marketing Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Marketing Channels</h1>
            <p class="text-sm text-slate-500 font-medium">Configure SMS, WhatsApp and Email gateways</p>
        </div>
    </div>

    <form action="{{ route('admin.marketing.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- SMS Configuration -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <h2 class="text-lg font-black text-slate-800">SMS Gateway Settings</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Primary Gateway</label>
                    <select name="sms_gateway" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                        <option value="twilio" {{ $settings['sms_gateway'] == 'twilio' ? 'selected' : '' }}>Twilio (Global)</option>
                        <option value="bulksmsbd" {{ $settings['sms_gateway'] == 'bulksmsbd' ? 'selected' : '' }}>BulkSMS BD (Local)</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Twilio From Number</label>
                    <input type="text" name="twilio_from" value="{{ $settings['twilio_from'] }}" placeholder="+1234567890"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Twilio Account SID</label>
                    <input type="text" name="twilio_sid" value="{{ $settings['twilio_sid'] }}"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Twilio Auth Token</label>
                    <input type="password" name="twilio_token" value="{{ $settings['twilio_token'] }}"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
            </div>
        </div>

        <!-- WhatsApp Configuration -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 border border-green-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h2 class="text-lg font-black text-slate-800">WhatsApp API Settings</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Provider</label>
                    <select name="whatsapp_provider" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                        <option value="meta" {{ $settings['whatsapp_provider'] == 'meta' ? 'selected' : '' }}>Meta Graph API (Direct)</option>
                        <option value="twilio" {{ $settings['whatsapp_provider'] == 'twilio' ? 'selected' : '' }}>Twilio for WhatsApp</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">API Token / Access Key</label>
                    <input type="password" name="whatsapp_token" value="{{ $settings['whatsapp_token'] }}"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
            </div>
        </div>

        <!-- Email Configuration -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-lg font-black text-slate-800">Email Gateway (SMTP)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ $settings['mail_host'] }}" placeholder="smtp.mailtrap.io"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">SMTP Port</label>
                    <input type="text" name="mail_port" value="{{ $settings['mail_port'] }}" placeholder="587"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Username</label>
                    <input type="text" name="mail_username" value="{{ $settings['mail_username'] }}"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Password</label>
                    <input type="password" name="mail_password" value="{{ $settings['mail_password'] }}"
                           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-3d btn-3d-primary flex-1 py-4">
                Save All Configurations
            </button>
            <a href="{{ route('admin.marketing.index') }}" class="px-6 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                Back to Dashboard
            </a>
        </div>
    </form>
</div>
@endsection
