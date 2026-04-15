@extends('admin.layout')

@section('title', 'New Service')
@section('page-title', 'New Service')
@section('page-subtitle', 'What Delos offers · Add service')

@section('page-actions')
    <a href="{{ route('admin.services.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Services
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.services._form', ['service' => $service])
    </form>
@endsection
