<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminActionController;


Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::middleware('auth:user')->group(function () {
        Route::get('/profile', [UserAuthController::class, 'profile']);
        Route::post('/logout', [UserAuthController::class, 'logout']);
        Route::post('/change-password', [UserAuthController::class, 'changePassword']);

        Route::apiResource('goals', GoalController::class);

        Route::apiResource('transactions', TransactionController::class);

        Route::apiResource('reminders', ReminderController::class);
    });
});



Route::prefix('company')->group(function () {
    Route::post('/register', [CompanyAuthController::class, 'register']);
    Route::post('/login', [CompanyAuthController::class, 'login']);

    Route::middleware('auth:company')->group(function () {
        Route::get('/profile', [CompanyAuthController::class, 'profile']);
        Route::post('/logout', [CompanyAuthController::class, 'logout']);
        Route::post('/change-password', [CompanyAuthController::class, 'changePassword']);

        Route::apiResource('goals', GoalController::class);

        Route::apiResource('transactions', TransactionController::class);

        Route::apiResource('events', EventController::class);
    });
});



Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('/profile', [AdminAuthController::class, 'profile']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);

        Route::get('/actions', [AdminActionController::class, 'index']);

        Route::delete('/user/{id}', [AdminAuthController::class, 'deleteUser']);
        Route::delete('/company/{id}', [AdminAuthController::class, 'deleteCompany']);
        Route::put('/user/{id}', [AdminAuthController::class, 'updateUser']);
        Route::put('/company/{id}', [AdminAuthController::class, 'updateCompany']);
        Route::post('/user', [AdminAuthController::class, 'createUser']);
        Route::post('/company', [AdminAuthController::class, 'createCompany']);
    });
});

Route::middleware('auth:user')->group(function () {
    Route::apiResource('goals', GoalController::class);
});

Route::middleware('auth:company')->group(function () {
    Route::apiResource('goals', GoalController::class);
});
