@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Get in <span class="text-blue-600">Touch</span></h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">Have a question or need assistance with your booking? Our team is here to help you.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            {{-- Contact Information --}}
            <div class="space-y-12">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-8">Contact Information</h2>
                    <div class="space-y-6">
                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Address</h4>
                                <p class="text-slate-500">Dhaka, Bangladesh</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Email</h4>
                                <p class="text-slate-500">support@metroarenabook.com</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Phone</h4>
                                <p class="text-slate-500">+880 1XXX-XXXXXX</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 p-8 rounded-3xl text-white">
                    <h4 class="text-xl font-bold mb-4">Business Hours</h4>
                    <div class="space-y-2 text-slate-400 text-sm">
                        <div class="flex justify-between"><span>Mon - Fri</span><span>9:00 AM - 9:00 PM</span></div>
                        <div class="flex justify-between"><span>Sat - Sun</span><span>8:00 AM - 11:00 PM</span></div>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50">
                <form action="#" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                            <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                            <input type="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="john@example.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                        <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Booking Inquiry">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Message</label>
                        <textarea rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-bold shadow-lg shadow-blue-200 transition btn-3d">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
