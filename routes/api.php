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
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ChatHistoryController;
use App\Http\Controllers\Api\CompanyChatController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OtpController;


Route::prefix('user')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::middleware('auth:user')->group(function () {
        Route::get('/profile', [UserAuthController::class, 'profile']);
        Route::post('/logout', [UserAuthController::class, 'logout']);
        Route::post('/change-password', [UserAuthController::class, 'changePassword']);

        Route::apiResource('goals', GoalController::class)->names([
            'index'   => 'user.goals.index',
            'create'  => 'user.goals.create',
            'store'   => 'user.goals.store',
            'show'    => 'user.goals.show',
            'edit'    => 'user.goals.edit',
            'update'  => 'user.goals.update',
            'destroy' => 'user.goals.destroy',
        ]);

        Route::apiResource('transactions', TransactionController::class)->names([
            'index'   => 'user.transactions.index',
            'create'  => 'user.transactions.create',
            'store'   => 'user.transactions.store',
            'show'    => 'user.transactions.show',
            'edit'    => 'user.transactions.edit',
            'update'  => 'user.transactions.update',
            'destroy' => 'user.transactions.destroy',
        ]);

        Route::apiResource('reminders', ReminderController::class)->names([
            'index'   => 'user.reminders.index',
            'create'  => 'user.reminders.create',
            'store'   => 'user.reminders.store',
            'show'    => 'user.reminders.show',
            'edit'    => 'user.reminders.edit',
            'update'  => 'user.reminders.update',
            'destroy' => 'user.reminders.destroy',
        ]);

        Route::get('/financialuser', [UserAuthController::class, 'financialuser'])->name('user.financialuser');
    });
});

Route::prefix('company')->group(function () {
    Route::post('/register', [CompanyAuthController::class, 'register']);
    Route::post('/login', [CompanyAuthController::class, 'login']);

    Route::middleware('auth:company')->group(function () {
        Route::get('/profile', [CompanyAuthController::class, 'profile']);
        Route::post('/logout', [CompanyAuthController::class, 'logout']);
        Route::post('/change-password', [CompanyAuthController::class, 'changePassword']);
        Route::get('/financialcompany', [CompanyAuthController::class, 'financialcompany']);

        Route::apiResource('goals', GoalController::class)->names([
            'index'   => 'company.goals.index',
            'create'  => 'company.goals.create',
            'store'   => 'company.goals.store',
            'show'    => 'company.goals.show',
            'edit'    => 'company.goals.edit',
            'update'  => 'company.goals.update',
            'destroy' => 'company.goals.destroy',
        ]);

        Route::apiResource('transactions', TransactionController::class)->names([
            'index'   => 'company.transactions.index',
            'create'  => 'company.transactions.create',
            'store'   => 'company.transactions.store',
            'show'    => 'company.transactions.show',
            'edit'    => 'company.transactions.edit',
            'update'  => 'company.transactions.update',
            'destroy' => 'company.transactions.destroy',
        ]);

        Route::apiResource('events', EventController::class)->names([
            'index'   => 'company.events.index',
            'create'  => 'company.events.create',
            'store'   => 'company.events.store',
            'show'    => 'company.events.show',
            'edit'    => 'company.events.edit',
            'update'  => 'company.events.update',
            'destroy' => 'company.events.destroy',
        ]);
    });
});

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('/profile', [AdminAuthController::class, 'profile']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);

        Route::get('/actions', [AdminActionController::class, 'index'])->name('admin.actions.index');

        Route::delete('/user/{id}', [AdminAuthController::class, 'deleteUser'])->name('admin.user.delete');
        Route::delete('/company/{id}', [AdminAuthController::class, 'deleteCompany'])->name('admin.company.delete');
        Route::put('/user/{id}', [AdminAuthController::class, 'updateUser'])->name('admin.user.update');
        Route::put('/company/{id}', [AdminAuthController::class, 'updateCompany'])->name('admin.company.update');
        Route::post('/user', [AdminAuthController::class, 'createUser'])->name('admin.user.create');
        Route::post('/company', [AdminAuthController::class, 'createCompany'])->name('admin.company.create');
    });
});
Route::get('/users/{id}/details', [UserAuthController::class, 'getDetails']);
Route::get('/companies/{id}/details', [CompanyAuthController::class, 'getDetails']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/chat/start-session', [ChatController::class, 'startSession']);
});

Route::get('/chat/context', [ChatController::class, 'getContext']);
Route::post('/chat-history', [ChatHistoryController::class, 'store']);


Route::get('/user/{id}/financial-context', [UserAuthController::class, 'financialuserById']);
Route::get('/company/{id}/financial-context', [CompanyAuthController::class, 'financialcompanyById']);

Route::post('/company-chat-history', [CompanyChatController::class, 'store']);
Route::middleware('auth:company')->group(function () {
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('employees', EmployeeController::class);
});
Route::middleware('auth:user,company')->group(function () {
    Route::get('/categories', [App\Http\Controllers\TransactionController::class, 'getCategories']);
});

Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
