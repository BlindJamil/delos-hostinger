@extends('admin.layout')

@section('title', 'New Project')
@section('page-title', 'New Project')
@section('page-subtitle', 'Portfolio · Add a new project')

@section('page-actions')
    <a href="{{ route('admin.projects.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Projects
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.projects._form', ['project' => $project])
    </form>
@endsection
