<?php

use App\Http\Controllers\Frontend\ImageConverterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Публичные маршруты (без регистрации)
Route::get('/converter', [ImageConverterController::class, 'index'])->name('converter');
Route::post('/converter/process', [ImageConverterController::class, 'process'])->name('converter.process');
Route::get('/converter/check-premium', [ImageConverterController::class, 'checkPremium'])->name('converter.check-premium');

// Маршруты с авторизацией
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Вебхук (без авторизации)
Route::post('/yookassa/webhook', [PaymentController::class, 'webhook'])->name('yookassa.webhook');

require __DIR__.'/auth.php';
