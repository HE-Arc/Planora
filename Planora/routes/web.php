<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('home');
});
Route::resource('events', EventController::class);

// Route::post('/logout', [AuthController::class, 'logout'])
//     ->name('logout');
