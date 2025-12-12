<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/request-otp', [AuthController::class, 'requestOtp']);
Route::post('/register/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/forgot-password/resend-otp', [AuthController::class, 'resendPasswordResetOtp']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyPasswordResetOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Admin public routes
Route::post('/admin/login', [AdminController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/mood', [AuthController::class, 'saveMood']);
    Route::post('/reflection', [AuthController::class, 'saveReflection']);
    Route::get('/events', [AuthController::class, 'getEvents']);
    Route::post('/appointment', [AuthController::class, 'bookAppointment']);
});

// Admin protected routes
Route::middleware('admin.auth')->prefix('admin')->group(function () {
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::get('/moods', [AdminController::class, 'getMoods']);
    Route::get('/reflections', [AdminController::class, 'getReflections']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/events', [AdminController::class, 'getEvents']);
    Route::post('/events', [AdminController::class, 'createEvent']);
    Route::put('/events/{id}', [AdminController::class, 'updateEvent']);
    Route::delete('/events/{id}', [AdminController::class, 'deleteEvent']);
    Route::get('/appointments', [AdminController::class, 'getAppointments']);
    Route::put('/appointments/{id}/status', [AdminController::class, 'updateAppointmentStatus']);
    Route::get('/dashboard-stats', [AdminController::class, 'getDashboardStats']);
});
