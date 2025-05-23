<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PersonalTrainerController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // General authenticated routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('dashboard/membership-history', [DashboardController::class, 'membershipHistory'])->name('dashboard.history');

    // Catalog routes (gym classes, trainers, packages, memberships, etc.)
    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('gym-classes', [CatalogController::class, 'gymClasses'])->name('gym-classes.index');
        Route::get('gym-classes/schedule', [CatalogController::class, 'gymClassSchedule'])->name('gym-classes.schedule');

        Route::get('personal-trainers', [CatalogController::class, 'personalTrainers'])->name('personal-trainers.index');
        Route::get('trainer-packages', [CatalogController::class, 'trainerPackages'])->name('trainer-packages.index');

        Route::get('membership-packages', [CatalogController::class, 'membershipPackages'])->name('membership-packages.index');
    });

    // Payment route
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
});


// 👇 Routes for trainers only
Route::middleware(['auth', 'verified', 'trainer'])->group(function () {
    Route::get('personal-trainer-dashboard', [PersonalTrainerController::class, 'dashboard'])
        ->name('personal-trainer-dashboard');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
