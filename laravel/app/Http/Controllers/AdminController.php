<?php

namespace App\Http\Controllers;

use App\Models\Admin;
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
}
