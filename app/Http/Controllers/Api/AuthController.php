<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'country_code' => 'nullable|string|max:10',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'country_code' => $validated['country_code'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
            'kyc_level' => 0,
        ]);

        $user->assignRole('bidder');

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('Registration successful. Please complete your profile.'),
            'token' => $token,
            'user' => new UserResource($user->load('wallet')),
        ], 201);
    }

    /**
     * Login.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid credentials.'),
            ], 401);
        }

        $user = Auth::user();

        // Revoke old tokens (optional: keep only latest)
        $user->tokens()->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('Login successful.'),
            'token' => $token,
            'user' => new UserResource($user->load(['wallet', 'latestKycRequest'])),
        ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => __('Logged out successfully.'),
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource(
                $request->user()->load(['wallet', 'latestKycRequest'])
            ),
        ]);
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|nullable|string|unique:users,phone,' . $user->id,
            'country_code' => 'sometimes|nullable|string|max:10',
            'country' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'gender' => 'sometimes|nullable|in:male,female',
            'date_of_birth' => 'sometimes|nullable|date',
        ]);

        if (isset($validated['first_name']) || isset($validated['last_name'])) {
            $validated['name'] = ($validated['first_name'] ?? $user->first_name)
                . ' '
                . ($validated['last_name'] ?? $user->last_name);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Profile updated successfully.'),
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => __('Current password is incorrect.'),
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => __('Password changed successfully.'),
        ]);
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();

        if ($user->profile_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile_photos', 'public');
        $user->update(['profile_photo' => $path]);

        return response()->json([
            'success' => true,
            'message' => __('Photo uploaded successfully.'),
            'photo_url' => $user->profile_photo_url,
        ]);
    }
}
