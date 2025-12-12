<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Handle a logout request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Request OTP for registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kgid' => 'required|string|max:255|unique:users,kgid',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
        ]);

        // Check if user already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email already registered',
                'errors' => [
                    'email' => ['This email is already registered. Please login instead.']
                ]
            ], 422);
        }

        // Check if KGID already exists
        if (User::where('kgid', $request->kgid)->exists()) {
            return response()->json([
                'message' => 'KGID already registered',
                'errors' => [
                    'kgid' => ['This KGID is already registered.']
                ]
            ], 422);
        }

        // Check if Mobile already exists
        if (User::where('mobile', $request->mobile)->exists()) {
            return response()->json([
                'message' => 'Mobile number already registered',
                'errors' => [
                    'mobile' => ['This mobile number is already registered.']
                ]
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP and initial data in cache for 10 minutes
        Cache::put('registration_otp_' . $request->email, [
            'otp' => $otp,
            'name' => $request->name,
            'kgid' => $request->kgid,
            'mobile' => $request->mobile,
        ], now()->addMinutes(10));

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP email',
                'errors' => [
                    'email' => ['Unable to send OTP. Please try again later.']
                ]
            ], 500);
        }

        return response()->json([
            'message' => 'OTP sent successfully to your email',
        ], 200);
    }

    /**
     * Resend OTP for registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if OTP exists in cache
        $cachedData = Cache::get('registration_otp_' . $request->email);
        if (!$cachedData) {
            return response()->json([
                'message' => 'No pending registration found. Please start registration again.',
                'errors' => [
                    'email' => ['No pending registration found for this email.']
                ]
            ], 422);
        }

        // Generate new 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update OTP in cache (preserve existing data)
        Cache::put('registration_otp_' . $request->email, [
            'otp' => $otp,
            'name' => $cachedData['name'],
            'kgid' => $cachedData['kgid'],
            'mobile' => $cachedData['mobile'],
        ], now()->addMinutes(10));

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP email',
                'errors' => [
                    'email' => ['Unable to send OTP. Please try again later.']
                ]
            ], 500);
        }

        return response()->json([
            'message' => 'OTP resent successfully to your email',
        ], 200);
    }

    /**
     * Verify OTP for registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Get cached OTP data
        $cachedData = Cache::get('registration_otp_' . $request->email);
        if (!$cachedData) {
            return response()->json([
                'message' => 'OTP expired or invalid',
                'errors' => [
                    'otp' => ['OTP has expired. Please request a new one.']
                ]
            ], 422);
        }

        // Verify OTP
        if ($cachedData['otp'] !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP',
                'errors' => [
                    'otp' => ['The OTP you entered is incorrect.']
                ]
            ], 422);
        }

        // Store verification status in cache
        Cache::put('registration_verified_' . $request->email, true, now()->addMinutes(30));

        return response()->json([
            'message' => 'OTP verified successfully',
        ], 200);
    }

    /**
     * Complete registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'otp' => 'required|string|size:6',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Others',
            'designation' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'currentWorking' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
        ]);

        // Verify OTP was verified
        if (!Cache::has('registration_verified_' . $request->email)) {
            return response()->json([
                'message' => 'Please verify OTP first',
                'errors' => [
                    'otp' => ['OTP verification required. Please verify OTP first.']
                ]
            ], 422);
        }

        // Get cached OTP data
        $cachedData = Cache::get('registration_otp_' . $request->email);
        if (!$cachedData) {
            return response()->json([
                'message' => 'OTP expired or invalid',
                'errors' => [
                    'otp' => ['OTP has expired. Please request a new one.']
                ]
            ], 422);
        }

        // Verify OTP again
        if ($cachedData['otp'] !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP',
                'errors' => [
                    'otp' => ['The OTP you entered is incorrect.']
                ]
            ], 422);
        }

        // Create user
        $user = User::create([
            'name' => $cachedData['name'],
            'kgid' => $cachedData['kgid'],
            'email' => $request->email,
            'mobile' => $cachedData['mobile'],
            'password' => Hash::make($request->password),
            'dob' => $request->dob,
            'phone' => $cachedData['mobile'], // Use mobile as phone number
            'gender' => $request->gender,
            'designation' => $request->designation,
            'division' => $request->division,
            'current_working' => $request->currentWorking,
            'qualification' => $request->qualification,
            'email_verified_at' => now(),
        ]);

        // Clear cache
        Cache::forget('registration_otp_' . $request->email);
        Cache::forget('registration_verified_' . $request->email);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }
}
