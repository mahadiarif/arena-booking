@extends('layouts.admin')
@section('title', 'Waitlist')
@section('breadcrumb', 'Waitlist')
@section('content')
<h1 class='text-2xl font-bold text-slate-800 mb-4'>Waitlist</h1><div class='bg-white rounded-2xl p-5 shadow-sm border border-slate-100'><p class='text-slate-500'>{{ $entries->total() }} entries on waitlist.</p></div>
@endsection