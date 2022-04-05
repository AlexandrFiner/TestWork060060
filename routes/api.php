<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::any('/auth', [AuthController::class, 'authenticate']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('book', BookController::class);
});
