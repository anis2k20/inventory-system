<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('products/archived', [ProductController::class, 'archived'])->name('products.archived');
Route::patch('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');

Route::resource('products', ProductController::class);
