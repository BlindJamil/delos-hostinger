@extends('admin.layout')

@section('title', 'Edit Employee')
@section('page-title', $employee->name_en)
@section('page-subtitle', 'Team · Edit employee')

@section('page-actions')
    <a href="{{ route('admin.employees.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Employees
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.employees._form', ['employee' => $employee])
    </form>
@endsection
