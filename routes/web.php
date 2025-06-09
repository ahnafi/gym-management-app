<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PersonalTrainerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // General authenticated routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catalog routes (gym classes, trainers, packages, memberships, etc.)
    Route::controller(CatalogController::class)->group(function () {
        Route::get('membership-packages', 'membershipPackages')->name('membership-packages.index');
        Route::get('membership-packages/{membershipPackage:slug}', 'membershipPackageDetail')->name('membership-packages.detail');

        Route::get('gym-classes', 'gymClasses')->name('gym-classes.index');
        Route::get('gym-classes/{gymClass:slug}', 'gymClassDetail')->name('gym-classes.detail');

        Route::get('personal-trainers', 'personalTrainers')->name('personal-trainers.index');
        Route::get('personal-trainers/{personalTrainer:slug}', 'trainerDetail')->name('personal-trainers.package');
    });

    Route::controller(HistoryController::class)->group(function () {
       Route::get('payment-history','paymentHistory')->name('payment-history');
       Route::get('membership-history','membershipHistory')->name('membership-history');
       Route::get('gym-class-history','gymClassHistory')->name('gym-class-history');
       Route::get('personal-training-history','personalTrainingHistory')->name('personal-training-history');
    });

    // Payment route
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');

    // POST
    // initiate payment with Midtrans
    Route::post('payments/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');

    // update payment status
    Route::post('payments/update-status', [PaymentController::class, 'updatePaymentStatus'])->name('payments.update-status');

});


// Routes for trainers only
Route::middleware(['trainer-area'])->group(function () {
    Route::get('personal-trainer-dashboard', [PersonalTrainerController::class, 'dashboard'])
        ->name('personal-trainer-dashboard');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
