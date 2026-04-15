@extends('admin.layout')

@section('title', 'New Branch')
@section('page-title', 'New Branch')
@section('page-subtitle', 'Showrooms across Iraq · Add branch')

@section('page-actions')
    <a href="{{ route('admin.branches.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Branches
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.branches.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.branches._form', ['branch' => $branch])
    </form>
@endsection
