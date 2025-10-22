<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginActivityController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\Route;

// Route::post('/upload-to-base64', [AuthController::class, 'uploadToBase64'])->name('upload-to-base64');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/send-password-reset-token', [PasswordResetController::class, 'sendPasswordResetToken'])->name('sendPasswordResetToken')->middleware('throttle:password-reset-limit');
Route::post('/verify-password-reset-token', [PasswordResetController::class, 'verifyPasswordResetToken'])->name('verifyPasswordResetToken');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('resetPassword');
Route::post('/pay-with-square',[PaymentController::class,'payWithSquare'])->name('pay-with-square');
Route::post('/pay-with-paypal',[PaymentController::class,'payWithPaypal'])->name('pay-with-paypal');
Route::get('/get-invoice/{id}', [InvoiceController::class,'getInvoice'])->name('get-invoice');
Route::post('/verify2fa', [AuthController::class, 'verify2FA'])->name('verify2fa');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/get-user', [AuthController::class, 'getUser'])->name('get-user');
    Route::controller(PackageController::class)->prefix('packages')->name('packages.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::get('/show/{id}', 'show')->name('show');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        Route::post('/assign-package/{package_id}', 'assignPackage')->name('assign-package');
    });
    Route::get('/categories', [PackageController::class, 'categories'])->name('categories');
    Route::controller(ClientController::class)->prefix('clients')->name('clients.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        Route::get('/assigned-packages/{id}', 'assignedPackages')->name('assigned-packages');
        Route::post('/edit-profile', 'editProfile')->name('edit-profile');
    });

    Route::controller(InvoiceController::class)->prefix('invoices')->name('invoices.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        Route::get('/get-payment-types', 'getPaymentTypes')->name('get-payment-types');
        Route::get('/get-payment-history', 'getPaymentHistory')->name('get-payment-history');
        Route::get('/get-invoice-by-assignment/{assignment_id}', 'getInvoiceByAssignment')->name('get-invoice-by-assignment');
    });
    Route::controller(BrandController::class)->prefix('brands')->name('brands.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
    });
    Route::controller(UserController::class)->middleware('role:super admin')->prefix('users')->name('users.')->group(function() {
        Route::get('/','index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
    });

    Route::get('/login-activities',[LoginActivityController::class,'index'])->name('login-activities');
});

