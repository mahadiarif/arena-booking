@extends('layouts.admin')
@section('title', 'Schedules')
@section('breadcrumb', 'Schedules')
@section('content')
<h1 class='text-2xl font-bold text-slate-800 mb-4'>Schedules</h1><div class='bg-white rounded-2xl p-5 shadow-sm border border-slate-100'><p class='text-slate-500'>{{ $schedules->count() }} schedules. <a href="{{ route('admin.schedules.create') }}" class='text-blue-600'>Add new</a>.</p></div>
@endsection