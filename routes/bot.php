<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bot Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Bot\IndexController;

Route::post('/', [IndexController::class, 'handle']);
Route::get('/getMe', [IndexController::class, 'getMe']);
