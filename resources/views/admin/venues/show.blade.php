@extends('layouts.admin')
@section('title', $venue->name)
@section('breadcrumb', $venue->name)
@section('content')
<h1 class='text-2xl font-bold text-slate-800'>{{ $venue->name }}</h1>
@endsection