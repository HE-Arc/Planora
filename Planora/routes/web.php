<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('events', EventController::class);

// Route::post('/logout', [AuthController::class, 'logout'])
//     ->name('logout');
