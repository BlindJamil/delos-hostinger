<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/projects', [PageController::class, 'projects'])->name('projects');
Route::get('/brands', [PageController::class, 'brands'])->name('brands');
Route::get('/branches', [PageController::class, 'branches'])->name('branches');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
