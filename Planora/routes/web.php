<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('home');
});

Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');

// Route::post('/logout', [AuthController::class, 'logout'])
//     ->name('logout');
