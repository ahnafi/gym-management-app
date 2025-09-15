<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\GymClassController;
use App\Http\Controllers\Api\PersonalTrainerController;
use App\Http\Controllers\Api\GymVisitController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Public data access
    Route::get('/membership/packages', [MembershipController::class, 'packages']);
    Route::get('/membership/packages/{id}', [MembershipController::class, 'showPackage']);
    Route::get('/gym-classes', [GymClassController::class, 'index']);
    Route::get('/gym-classes/{id}', [GymClassController::class, 'show']);
    Route::get('/gym-classes/{classId}/schedules', [GymClassController::class, 'schedules']);
    Route::get('/trainers', [PersonalTrainerController::class, 'index']);
    Route::get('/trainers/{id}', [PersonalTrainerController::class, 'show']);
    Route::get('/trainer-packages', [PersonalTrainerController::class, 'packages']);
    Route::get('/trainer-packages/{id}', [PersonalTrainerController::class, 'showPackage']);
    
    // Payment webhook (Midtrans notification)
    Route::post('/payment/notification', [PaymentController::class, 'handleNotification']);
});

// Protected routes (authentication required)
Route::prefix('v1')->middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Authentication & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    
    // User Management
    Route::apiResource('users', UserController::class);
    Route::get('/users-statistics', [UserController::class, 'statistics']);
    Route::get('/trainers-list', [UserController::class, 'trainers']);
    
    // Membership Management
    Route::prefix('membership')->group(function () {
        Route::get('/my-memberships', [MembershipController::class, 'myMemberships']);
        Route::get('/current', [MembershipController::class, 'currentMembership']);
        Route::post('/purchase', [MembershipController::class, 'purchaseMembership']);
        Route::get('/all', [MembershipController::class, 'allMemberships']);
        Route::put('/status/{id}', [MembershipController::class, 'updateMembershipStatus']);
        Route::get('/statistics', [MembershipController::class, 'statistics']);
        
        // Package management (admin routes)
        Route::post('/packages', [MembershipController::class, 'createPackage']);
        Route::put('/packages/{id}', [MembershipController::class, 'updatePackage']);
        Route::delete('/packages/{id}', [MembershipController::class, 'deletePackage']);
    });
    
    // Gym Classes Management
    Route::prefix('gym-classes')->group(function () {
        Route::post('/', [GymClassController::class, 'store']);
        Route::put('/{id}', [GymClassController::class, 'update']);
        Route::delete('/{id}', [GymClassController::class, 'destroy']);
        
        // Schedule management
        Route::post('/{classId}/schedules', [GymClassController::class, 'createSchedule']);
        Route::put('/{classId}/schedules/{scheduleId}', [GymClassController::class, 'updateSchedule']);
        Route::delete('/{classId}/schedules/{scheduleId}', [GymClassController::class, 'destroySchedule']);
        
        // Booking management
        Route::post('/book', [GymClassController::class, 'bookClass']);
        Route::delete('/bookings/{attendanceId}', [GymClassController::class, 'cancelBooking']);
        Route::get('/my-bookings', [GymClassController::class, 'myBookings']);
        Route::put('/attendance/{attendanceId}', [GymClassController::class, 'markAttendance']);
        
        Route::get('/statistics', [GymClassController::class, 'statistics']);
    });
    
    // Personal Trainer Management
    Route::prefix('trainers')->group(function () {
        Route::post('/', [PersonalTrainerController::class, 'store']);
        Route::put('/{id}', [PersonalTrainerController::class, 'update']);
        Route::delete('/{id}', [PersonalTrainerController::class, 'destroy']);
        
        // Package management
        Route::get('/{trainerId}/packages', [PersonalTrainerController::class, 'packages']);
        Route::post('/packages', [PersonalTrainerController::class, 'createPackage']);
        Route::put('/packages/{packageId}', [PersonalTrainerController::class, 'updatePackage']);
        Route::delete('/packages/{packageId}', [PersonalTrainerController::class, 'deletePackage']);
        Route::post('/packages/purchase', [PersonalTrainerController::class, 'purchasePackage']);
        
        // Assignment management
        Route::get('/assignments', [PersonalTrainerController::class, 'assignments']);
        Route::get('/{trainerId}/assignments', [PersonalTrainerController::class, 'assignments']);
        Route::get('/assignments/{assignmentId}', [PersonalTrainerController::class, 'showAssignment']);
        
        // Schedule management
        Route::get('/schedules', [PersonalTrainerController::class, 'schedules']);
        Route::get('/{trainerId}/schedules', [PersonalTrainerController::class, 'schedules']);
        Route::post('/schedules', [PersonalTrainerController::class, 'createSchedule']);
        Route::put('/schedules/{scheduleId}', [PersonalTrainerController::class, 'updateSchedule']);
        
        Route::get('/statistics', [PersonalTrainerController::class, 'statistics']);
        Route::get('/{trainerId}/statistics', [PersonalTrainerController::class, 'statistics']);
    });
    
    // Gym Visits Management
    Route::prefix('gym-visits')->group(function () {
        Route::get('/', [GymVisitController::class, 'index']);
        Route::get('/my-visits', [GymVisitController::class, 'myVisits']);
        Route::get('/{id}', [GymVisitController::class, 'show']);
        Route::post('/check-in', [GymVisitController::class, 'checkIn']);
        Route::post('/check-out', [GymVisitController::class, 'checkOut']);
        Route::get('/status/current', [GymVisitController::class, 'currentStatus']);
        Route::post('/manual-entry', [GymVisitController::class, 'manualEntry']);
        Route::put('/{id}', [GymVisitController::class, 'update']);
        Route::delete('/{id}', [GymVisitController::class, 'destroy']);
        Route::get('/statistics/admin', [GymVisitController::class, 'statistics']);
        Route::get('/statistics/my-stats', [GymVisitController::class, 'myStatistics']);
    });
    
    // Payment & Transactions
    Route::prefix('payments')->group(function () {
        Route::get('/transactions', [PaymentController::class, 'index']);
        Route::get('/my-transactions', [PaymentController::class, 'myTransactions']);
        Route::get('/transactions/{id}', [PaymentController::class, 'show']);
        
        // Purchase endpoints
        Route::post('/membership', [PaymentController::class, 'purchaseMembership']);
        Route::post('/gym-class', [PaymentController::class, 'purchaseGymClass']);
        Route::post('/trainer-package', [PaymentController::class, 'purchaseTrainerPackage']);
        
        // Payment management
        Route::get('/status/{transactionId}', [PaymentController::class, 'checkStatus']);
        Route::post('/cancel/{transactionId}', [PaymentController::class, 'cancel']);
        Route::post('/manual-approval/{transactionId}', [PaymentController::class, 'manualApproval']);
        Route::get('/statistics', [PaymentController::class, 'statistics']);
    });
});

// Fallback for undefined routes
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'API endpoint not found'
    ], 404);
});

