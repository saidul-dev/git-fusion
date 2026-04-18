<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');


/**
 * API route to handle usernames
 */
Route::post('/merge', [HomeController::class, 'merge']);
Route::post('/save-dashboard', [DashboardController::class, 'save']);
Route::get('/{slug}', [DashboardController::class, 'view']);

