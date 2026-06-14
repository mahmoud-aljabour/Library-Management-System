<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowingController;
use App\Http\Controllers\Api\MemberController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:api')
    ->name('api.login');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::apiResource('/books', BookController::class)->names('api.books');
    Route::apiResource('/members', MemberController::class)->names('api.members');
    Route::apiResource('/borrowings', BorrowingController::class)->names('api.borrowings');
});
