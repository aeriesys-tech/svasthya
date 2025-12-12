<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Event;
use App\Models\Feedback;
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

    /**
     * Request OTP for password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Email not found',
                'errors' => [
                    'email' => ['This email is not registered.']
                ]
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes
        Cache::put('password_reset_otp_' . $request->email, [
            'otp' => $otp,
            'email' => $request->email,
        ], now()->addMinutes(10));

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new OtpMail($otp, 'password_reset'));
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
     * Resend OTP for password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendPasswordResetOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if OTP exists in cache
        $cachedData = Cache::get('password_reset_otp_' . $request->email);
        if (!$cachedData) {
            return response()->json([
                'message' => 'No pending password reset found. Please request a new OTP.',
                'errors' => [
                    'email' => ['No pending password reset found for this email.']
                ]
            ], 422);
        }

        // Generate new 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update OTP in cache
        Cache::put('password_reset_otp_' . $request->email, [
            'otp' => $otp,
            'email' => $request->email,
        ], now()->addMinutes(10));

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new OtpMail($otp, 'password_reset'));
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
     * Verify OTP for password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyPasswordResetOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Get cached OTP data
        $cachedData = Cache::get('password_reset_otp_' . $request->email);
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
        Cache::put('password_reset_verified_' . $request->email, true, now()->addMinutes(30));

        return response()->json([
            'message' => 'OTP verified successfully',
        ], 200);
    }

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verify OTP was verified
        if (!Cache::has('password_reset_verified_' . $request->email)) {
            return response()->json([
                'message' => 'Please verify OTP first',
                'errors' => [
                    'otp' => ['OTP verification required. Please verify OTP first.']
                ]
            ], 422);
        }

        // Get cached OTP data
        $cachedData = Cache::get('password_reset_otp_' . $request->email);
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

        // Update user password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'errors' => [
                    'email' => ['User not found.']
                ]
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clear cache
        Cache::forget('password_reset_otp_' . $request->email);
        Cache::forget('password_reset_verified_' . $request->email);

        return response()->json([
            'message' => 'Password reset successfully',
        ], 200);
    }

    /**
     * Get authenticated user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'kgid' => $user->kgid,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'dob' => $user->dob,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'designation' => $user->designation,
                'division' => $user->division,
                'current_working' => $user->current_working,
                'qualification' => $user->qualification,
            ],
        ], 200);
    }

    /**
     * Update authenticated user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'dob' => 'sometimes|date|nullable',
            'phone' => 'sometimes|string|max:20|nullable',
            'gender' => 'sometimes|in:Male,Female,Others|nullable',
            'designation' => 'sometimes|string|max:255|nullable',
            'division' => 'sometimes|string|max:255|nullable',
            'current_working' => 'sometimes|string|max:255|nullable',
            'qualification' => 'sometimes|string|max:255|nullable',
        ]);

        $user->update([
            'name' => $request->input('name', $user->name),
            'dob' => $request->input('dob', $user->dob),
            'phone' => $request->input('phone', $user->phone),
            'gender' => $request->input('gender', $user->gender),
            'designation' => $request->input('designation', $user->designation),
            'division' => $request->input('division', $user->division),
            'current_working' => $request->input('current_working', $user->current_working),
            'qualification' => $request->input('qualification', $user->qualification),
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'kgid' => $user->kgid,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'dob' => $user->dob,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'designation' => $user->designation,
                'division' => $user->division,
                'current_working' => $user->current_working,
                'qualification' => $user->qualification,
            ],
        ], 200);
    }

    /**
     * Save user mood
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveMood(Request $request): JsonResponse
    {
        $request->validate([
            'mood_label' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'date' => 'nullable|date',
        ]);

        $user = $request->user();

        $mood = \App\Models\Mood::create([
            'user_id' => $user->id,
            'mood_label' => $request->mood_label,
            'description' => $request->description,
            'date' => $request->date ?? now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Mood saved successfully',
            'mood' => $mood,
        ], 200);
    }

    /**
     * Save user reflection
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveReflection(Request $request): JsonResponse
    {
        $request->validate([
            'q1' => 'required|string|max:1000',
            'q2' => 'required|string|max:1000',
            'q3' => 'required|string|max:255',
            'q4' => 'required|string|max:1000',
            'q5' => 'required|string|max:1000',
            'q6' => 'required|integer|min:1|max:5',
            'date' => 'nullable|date',
        ]);

        $user = $request->user();

        $reflection = \App\Models\Reflection::create([
            'user_id' => $user->id,
            'q1' => $request->q1,
            'q2' => $request->q2,
            'q3' => $request->q3,
            'q4' => $request->q4,
            'q5' => $request->q5,
            'q6' => $request->q6,
            'date' => $request->date ?? now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Reflection saved successfully',
            'reflection' => $reflection,
        ], 200);
    }

    /**
     * Get published events for calendar
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvents(Request $request): JsonResponse
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $events = Event::where('is_published', true)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'events' => $events,
        ], 200);
    }

    /**
     * Book an appointment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookAppointment(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->validate([
            'officer_id' => 'required|integer',
            'officer_name' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'place' => 'required|string|max:255',
            'slot' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'officer_id' => $request->officer_id,
            'officer_name' => $request->officer_name,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'place' => $request->place,
            'slot' => $request->slot,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'appointment' => $appointment,
        ], 201);
    }

    /**
     * Submit feedback
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitFeedback(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'opinion' => 'nullable|string|max:1000',
        ]);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'rating' => $request->rating,
            'opinion' => $request->opinion,
        ]);

        return response()->json([
            'message' => 'Thank you for your valuable feedback!',
            'feedback' => $feedback,
        ], 201);
    }
}
