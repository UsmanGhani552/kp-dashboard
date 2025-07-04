<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginActivityController;
use App\Http\Controllers\PackageController;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/send-password-reset-token', [PasswordResetController::class, 'sendPasswordResetToken'])->name('sendPasswordResetToken')->middleware('throttle:password-reset-limit');
Route::post('/verify-password-reset-token', [PasswordResetController::class, 'verifyPasswordResetToken'])->name('verifyPasswordResetToken');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('resetPassword');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::controller(PackageController::class)->prefix('packages')->name('packages.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
    });
    Route::controller(ClientController::class)->prefix('clients')->name('clients.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        Route::post('/edit-profile/{id}', 'editProfile')->name('edit-profile');
    });

    Route::controller(InvoiceController::class)->prefix('invoices')->name('invoices.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
    });

    Route::get('/login-activities',[LoginActivityController::class,'index'])->name('login-activities');
});
