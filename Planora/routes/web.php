<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;

Route::resource('events', EventController::class);
Route::get('/', [EventController::class, 'index'])->name('events.index');

// Route::post('/logout', [AuthController::class, 'logout'])
//     ->name('logout');
