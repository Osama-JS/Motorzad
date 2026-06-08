<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Register a new user.
     */
    #[OA\Post(
        path: "/api/auth/register",
        summary: "Register a new customer",
        operationId: "registerCustomer",
        description: "Registers a new customer and sends an OTP to their email.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "500000000"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Registration successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Registration successful. Please complete your profile."),
                        new OA\Property(property: "token", type: "string"),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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

        // Generate 6-digit OTP for email verification
        $code = (string) mt_rand(100000, 999999);
        Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
        $this->mailService->sendVerificationOtp($user->email, $code);

        $token = $user->createToken('mobile')->plainTextToken;

        $dataPayload = [
            'token' => $token,
            'user' => new UserResource($user->load('wallet')),
        ];

        if (config('app.debug') || app()->environment('local')) {
            $dataPayload['otp'] = $code;
        }

        return response()->json([
            'success' => true,
            'message' => __('Registration successful. Please verify your email.'),
            'data' => $dataPayload
        ], 201);
    }

    /**
     * Login.
     */
    #[OA\Post(
        path: "/api/auth/login",
        summary: "Login user",
        operationId: "loginUser",
        description: "Authenticates a user and returns an access token.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Login successful."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid credentials."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => true,
                'message' => __('Invalid credentials.'),
                'data' => null,
            ], 401);
        }

        $user = Auth::user();

        // Revoke old tokens (optional: keep only latest)
        $user->tokens()->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'error' => false,
            'message' => __('Login successful.'),
            'data' => [
                'access_token' => $token,
                'user' => new UserResource($user->load(['wallet', 'latestKycRequest'])),
            ]
        ]);
    }

    /**
     * Logout.
     */
    #[OA\Post(
        path: "/api/auth/logout",
        summary: "Logout user",
        operationId: "logoutUser",
        description: "Revokes the user's current access token.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        responses: [
            new OA\Response(response: 200, description: "Logged out successfully")
        ]
    )]
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
    #[OA\Get(
        path: "/api/auth/me",
        summary: "Get current user profile",
        operationId: "getCurrentUser",
        description: "Returns the authenticated user's profile information.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Profile retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            )
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'error' => false,
            'message' => __('Profile retrieved successfully.'),
            'data' => [
                'user' => new UserResource(
                    $request->user()->load(['wallet', 'latestKycRequest'])
                ),
            ]
        ]);
    }

    /**
     * Update profile.
     */
    #[OA\Put(
        path: "/api/auth/profile",
        summary: "Update user profile",
        operationId: "updateUserProfile",
        description: "Updates the authenticated user's profile details.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "phone", type: "string", example: "500000000"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
                    new OA\Property(property: "country", type: "string", example: "Saudi Arabia"),
                    new OA\Property(property: "city", type: "string", example: "Riyadh"),
                    new OA\Property(property: "gender", type: "string", enum: ["male", "female"]),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Profile updated successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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
            'error' => false,
            'message' => __('Profile updated successfully.'),
            'data' => [
                'user' => new UserResource($user->fresh()->load(['wallet', 'latestKycRequest'])),
            ]
        ]);
    }

    /**
     * Change password.
     */
    #[OA\Put(
        path: "/api/auth/change-password",
        summary: "Change user password",
        operationId: "changeUserPassword",
        description: "Changes the authenticated user's password.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["current_password", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "current_password", type: "string", format: "password", example: "oldpassword123"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "newpassword123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "newpassword123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password changed successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password changed successfully."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error or incorrect old password",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Current password is incorrect."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            )
        ]
    )]
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => true,
                'message' => __('Current password is incorrect.'),
                'data' => null,
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'error' => false,
            'message' => __('Password changed successfully.'),
            'data' => null,
        ]);
    }

    /**
     * Upload profile photo.
     */
    #[OA\Post(
        path: "/api/auth/photo",
        summary: "Upload profile photo",
        operationId: "uploadProfilePhoto",
        description: "Uploads a new profile photo for the authenticated user.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "photo", type: "string", format: "binary", description: "Profile photo image file")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Photo uploaded successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Photo uploaded successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "photo_url", type: "string", example: "http://localhost/storage/profile_photos/xyz.png")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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
            'error' => false,
            'message' => __('Photo uploaded successfully.'),
            'data' => [
                'photo_url' => $user->profile_photo_url,
            ]
        ]);
    }

    /**
     * Forgot Password - Send OTP.
     */
    #[OA\Post(
        path: "/api/auth/forgot-password",
        summary: "Forgot Password",
        operationId: "forgotPassword",
        description: "Sends an OTP to the user's email to reset their password.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "OTP sent successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset OTP sent to your email."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "otp", type: "string", example: "123456", description: "Only in local/debug mode")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate a 6-digit OTP
        $code = (string) mt_rand(100000, 999999);

        // Store OTP in Cache for 15 minutes
        Cache::put('reset_pwd_' . $user->email, $code, now()->addMinutes(15));

        // Send OTP using MailService
        $this->mailService->sendOtp($user->email, $code);

        $responseData = new \stdClass();
        if (config('app.debug') || app()->environment('local')) {
            $responseData = [
                'otp' => $code
            ];
        }

        return response()->json([
            'error' => false,
            'message' => __('Password reset OTP sent to your email.'),
            'data' => $responseData
        ]);
    }

    /**
     * Reset Password - Verify OTP and Change Password.
     */
    #[OA\Post(
        path: "/api/auth/reset-password",
        summary: "Reset Password",
        operationId: "resetPassword",
        description: "Verifies the OTP and sets a new password.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "otp", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "otp", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "newpassword123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "newpassword123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password reset successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset successfully. You can now login."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $cachedCode = Cache::get('reset_pwd_' . $request->email);

        $isMasterCode = (config('app.debug') || app()->environment('local')) && $request->otp === '123456';

        if (!$cachedCode && !$isMasterCode) {
            return response()->json([
                'error' => true,
                'message' => __('Invalid or expired OTP code.'),
                'data' => null,
            ], 400);
        }

        if ($cachedCode !== $request->otp && !$isMasterCode) {
            return response()->json([
                'error' => true,
                'message' => __('Invalid or expired OTP code.'),
                'data' => null,
            ], 400);
        }

        // Verify successful
        Cache::forget('reset_pwd_' . $request->email);

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Revoke tokens so user has to log in again with new password
        $user->tokens()->delete();

        return response()->json([
            'error' => false,
            'message' => __('Password reset successfully. You can now login.'),
            'data' => null,
        ]);
    }

    /**
     * Verify email with OTP.
     */
    #[OA\Post(
        path: "/api/auth/email/verify",
        summary: "Verify user email",
        operationId: "verifyEmail",
        description: "Verifies the authenticated user's email using the 6-digit OTP code sent during registration.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["otp"],
                properties: [
                    new OA\Property(property: "otp", type: "string", example: "123456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Email verified successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Email verified successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'error' => false,
                'message' => __('Email is already verified.'),
                'data' => [
                    'user' => new UserResource($user->load(['wallet', 'latestKycRequest'])),
                ]
            ]);
        }

        $cachedCode = Cache::get('email_verify_' . $user->email);
        $isMasterCode = (config('app.debug') || app()->environment('local')) && $request->otp === '123456';

        if (!$cachedCode && !$isMasterCode) {
            return response()->json([
                'error' => true,
                'message' => __('Invalid or expired OTP code.'),
                'data' => null,
            ], 400);
        }

        if ($cachedCode !== $request->otp && !$isMasterCode) {
            return response()->json([
                'error' => true,
                'message' => __('Invalid or expired OTP code.'),
                'data' => null,
            ], 400);
        }

        // OTP verified successfully
        Cache::forget('email_verify_' . $user->email);
        $user->markEmailAsVerified();

        return response()->json([
            'error' => false,
            'message' => __('Email verified successfully.'),
            'data' => [
                'user' => new UserResource($user->load(['wallet', 'latestKycRequest'])),
            ]
        ]);
    }

    /**
     * Resend verification email.
     */
    #[OA\Post(
        path: "/api/auth/email/resend",
        summary: "Resend email verification OTP",
        operationId: "resendVerificationEmail",
        description: "Regenerates and resends the 6-digit OTP verification code to the authenticated user's email.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Verification OTP sent successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Verification OTP sent to your email."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "otp", type: "string", example: "123456", description: "Only in local/debug mode")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email is already verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Email is already verified."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            )
        ]
    )]
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'error' => true,
                'message' => __('Email is already verified.'),
                'data' => null,
            ], 400);
        }

        $code = (string) mt_rand(100000, 999999);
        Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
        $this->mailService->sendVerificationOtp($user->email, $code);

        $dataPayload = new \stdClass();
        if (config('app.debug') || app()->environment('local')) {
            $dataPayload = [
                'otp' => $code
            ];
        }

        return response()->json([
            'error' => false,
            'message' => __('Verification OTP sent to your email.'),
            'data' => $dataPayload,
        ]);
    }
}
