<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Mood;
use App\Models\Reflection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
{
    /**
     * Get authenticated admin from token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Admin|null
     */
    private function getAuthenticatedAdmin(Request $request): ?Admin
    {
        // The middleware should have already authenticated and set the admin
        // But we keep this method for backward compatibility
        $user = $request->user();

        if ($user instanceof Admin) {
            return $user;
        }

        // Fallback: manually resolve if middleware didn't set it
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || $accessToken->tokenable_type !== Admin::class) {
            return null;
        }

        return Admin::find($accessToken->tokenable_id);
    }

    /**
     * Handle admin login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $admin->createToken('admin-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Get all moods with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMoods(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $moods = Mood::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'moods' => $moods->items(),
            'pagination' => [
                'current_page' => $moods->currentPage(),
                'last_page' => $moods->lastPage(),
                'per_page' => $moods->perPage(),
                'total' => $moods->total(),
                'from' => $moods->firstItem(),
                'to' => $moods->lastItem(),
            ],
        ], 200);
    }

    /**
     * Get all reflections with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReflections(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $reflections = Reflection::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'reflections' => $reflections->items(),
            'pagination' => [
                'current_page' => $reflections->currentPage(),
                'last_page' => $reflections->lastPage(),
                'per_page' => $reflections->perPage(),
                'total' => $reflections->total(),
                'from' => $reflections->firstItem(),
                'to' => $reflections->lastItem(),
            ],
        ], 200);
    }

    /**
     * Get all users with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = $request->get('per_page', 1);
        $page = $request->get('page', 1);

        $users = User::select('id', 'name', 'email', 'mobile', 'kgid', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
        ], 200);
    }

    /**
     * Get dashboard stats
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $totalUsers = User::count();
        $totalMoods = Mood::count();
        $totalReflections = Reflection::count();
        $todayMoods = Mood::whereDate('created_at', today())->count();
        $todayReflections = Reflection::whereDate('created_at', today())->count();

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
                'total_moods' => $totalMoods,
                'total_reflections' => $totalReflections,
                'today_moods' => $todayMoods,
                'today_reflections' => $todayReflections,
            ],
        ], 200);
    }

    /**
     * Handle admin logout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Delete the current access token
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $accessToken->delete();
            }
        }

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Get all events with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvents(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $events = Event::orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'events' => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'from' => $events->firstItem(),
                'to' => $events->lastItem(),
            ],
        ], 200);
    }

    /**
     * Create a new event
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createEvent(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->validate([
            'name' => 'required_if:activity_type,EVENT|nullable|string|max:255',
            'topic' => 'required_if:activity_type,WORKSHOP|nullable|string|max:255',
            'activity_type' => 'required|in:EVENT,WORKSHOP',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'created_by' => 'required_if:activity_type,EVENT|nullable|string|max:255',
            'instructor' => 'required_if:activity_type,WORKSHOP|nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'status' => 'nullable|string|in:Open,Confirmed,Cancelled',
            'max_pax' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $event = Event::create([
            'name' => $request->name,
            'topic' => $request->topic,
            'activity_type' => $request->activity_type,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'created_by' => $request->created_by,
            'instructor' => $request->instructor,
            'venue' => $request->venue,
            'status' => $request->status ?? 'Open',
            'max_pax' => $request->max_pax,
            'is_published' => $request->is_published ?? true,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event,
        ], 201);
    }

    /**
     * Update an event
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEvent(Request $request, $id): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $event = Event::findOrFail($id);

        $request->validate([
            'name' => 'required_if:activity_type,EVENT|nullable|string|max:255',
            'topic' => 'required_if:activity_type,WORKSHOP|nullable|string|max:255',
            'activity_type' => 'required|in:EVENT,WORKSHOP',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'created_by' => 'required_if:activity_type,EVENT|nullable|string|max:255',
            'instructor' => 'required_if:activity_type,WORKSHOP|nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'status' => 'nullable|string|in:Open,Confirmed,Cancelled',
            'max_pax' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $event->update([
            'name' => $request->name,
            'topic' => $request->topic,
            'activity_type' => $request->activity_type,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'created_by' => $request->created_by,
            'instructor' => $request->instructor,
            'venue' => $request->venue,
            'status' => $request->status ?? $event->status,
            'max_pax' => $request->max_pax,
            'is_published' => $request->is_published ?? $event->is_published,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event,
        ], 200);
    }

    /**
     * Delete an event
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteEvent(Request $request, $id): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
        ], 200);
    }

    /**
     * Get all appointments with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointments(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $appointments = Appointment::with('user:id,name,email,mobile')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'appointments' => $appointments->items(),
            'pagination' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
                'from' => $appointments->firstItem(),
                'to' => $appointments->lastItem(),
            ],
        ], 200);
    }

    /**
     * Update appointment status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAppointmentStatus(Request $request, $id): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->status = $request->status;
        $appointment->save();

        return response()->json([
            'message' => 'Appointment status updated successfully',
            'appointment' => $appointment,
        ], 200);
    }

    /**
     * Get all feedbacks with pagination
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeedbacks(Request $request): JsonResponse
    {
        $admin = $this->getAuthenticatedAdmin($request);

        if (!$admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $feedbacks = Feedback::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'feedbacks' => $feedbacks->items(),
            'pagination' => [
                'current_page' => $feedbacks->currentPage(),
                'last_page' => $feedbacks->lastPage(),
                'per_page' => $feedbacks->perPage(),
                'total' => $feedbacks->total(),
                'from' => $feedbacks->firstItem(),
                'to' => $feedbacks->lastItem(),
            ],
        ], 200);
    }
}
