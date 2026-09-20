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
                example: [
                    "first_name" => "محمد",
                    "last_name" => "الغامدي",
                    "email" => "mohammed@example.com",
                    "phone" => "501112233",
                    "country_code" => "+966",
                    "password" => "P@ssw0rd123",
                    "password_confirmation" => "P@ssw0rd123",
                    "fcm_token" => "fcm_token_example_string..."
                ],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "محمد"),
                    new OA\Property(property: "last_name", type: "string", example: "الغامدي"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "mohammed@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "501112233"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "P@ssw0rd123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "P@ssw0rd123"),
                    new OA\Property(property: "fcm_token", type: "string", example: "fcm_token_string...", description: "Optional FCM device token for push notifications")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Registration successful - OTP sent to email",
                content: new OA\JsonContent(
                    example: [
                        "error" => false,
                        "requires_verification" => true,
                        "message" => "تم إنشاء الحساب بنجاح. تم إرسال رمز التحقق إلى بريدك الإلكتروني لتأكيد الحساب.",
                        "data" => [
                            "access_token" => "1|plainTextToken...",
                            "token" => "1|plainTextToken...",
                            "action" => "verify_otp",
                            "user" => [
                                "id" => 15,
                                "first_name" => "محمد",
                                "last_name" => "الغامدي",
                                "full_name" => "محمد الغامدي",
                                "email" => "mohammed@example.com",
                                "phone" => "501112233",
                                "country_code" => "+966",
                                "status" => "pending",
                                "kyc_level" => 0,
                                "email_verified" => false,
                                "identity_verified" => false,
                                "roles" => ["bidder"]
                            ]
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "requires_verification", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "تم إنشاء الحساب بنجاح. تم إرسال رمز التحقق إلى بريدك الإلكتروني لتأكيد الحساب."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "action", type: "string", example: "verify_otp"),
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 15),
                                new OA\Property(property: "first_name", type: "string", example: "محمد"),
                                new OA\Property(property: "last_name", type: "string", example: "الغامدي"),
                                new OA\Property(property: "full_name", type: "string", example: "محمد الغامدي"),
                                new OA\Property(property: "email", type: "string", example: "mohammed@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "501112233"),
                                new OA\Property(property: "country_code", type: "string", example: "+966"),
                                new OA\Property(property: "status", type: "string", example: "pending"),
                                new OA\Property(property: "kyc_level", type: "integer", example: 0),
                                new OA\Property(property: "email_verified", type: "boolean", example: false),
                                new OA\Property(property: "identity_verified", type: "boolean", example: false),
                                new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "bidder"))
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    example: [
                        "message" => "The email has already been taken.",
                        "errors" => [
                            "email" => ["The email has already been taken."]
                        ]
                    ]
                )
            )
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

        // Generate a cryptographically secure 6-digit OTP for email verification
        $code = sprintf('%06d', random_int(100000, 999999));
        Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
        Cache::put('otp_' . $user->email, $code, now()->addMinutes(15));
        Cache::put('otp_throttle_' . $user->email, now()->addSeconds(60)->timestamp, now()->addSeconds(60));
        Cache::put('otp_attempts_' . $user->email, 0, now()->addMinutes(15));

        $this->mailService->sendVerificationOtp($user->email, $code);

        $token = $user->createToken('mobile')->plainTextToken;

        // Save FCM token if provided in registration
        $fcmToken = $request->input('fcm_token', $request->input('device_token'));
        if (!empty($fcmToken)) {
            $user->forceFill(['fcm_token' => $fcmToken])->save();
            \Illuminate\Support\Facades\Log::info("FCM token saved on register for User #{$user->id} ({$user->email})");
        }

        $dataPayload = [
            'access_token' => $token,
            'token'        => $token,
            'action'       => 'verify_otp',
            'user'         => new UserResource($user->load('wallet')),
        ];

        return response()->json([
            'error'                 => false,
            'requires_verification' => true,
            'message'               => __('تم إنشاء الحساب بنجاح. تم إرسال رمز التحقق إلى بريدك الإلكتروني لتأكيد الحساب.'),
            'data'                  => $dataPayload
        ], 201);
    }

    /**
     * Login.
     */
    #[OA\Post(
        path: "/api/auth/login",
        summary: "Login user",
        operationId: "loginUser",
        description: "Authenticates a user and returns an access token. If account email is not verified, dispatches an OTP to email and returns 403 with requires_verification: true and action: verify_otp.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                example: [
                    "email" => "user@example.com",
                    "password" => "password123",
                    "fcm_token" => "fcm_token_example_string..."
                ],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "fcm_token", type: "string", example: "fcm_token_string...", description: "Optional FCM device token for push notifications")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    example: [
                        "error" => false,
                        "message" => "Login successful.",
                        "data" => [
                            "access_token" => "1|plainTextToken...",
                            "token" => "1|plainTextToken...",
                            "user" => [
                                "id" => 12,
                                "first_name" => "أحمد",
                                "last_name" => "المطيري",
                                "full_name" => "أحمد المطيري",
                                "email" => "user@example.com",
                                "phone" => "500000000",
                                "country_code" => "+966",
                                "status" => "active",
                                "kyc_level" => 0,
                                "email_verified" => true,
                                "identity_verified" => false,
                                "roles" => ["bidder"]
                            ]
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Login successful."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 12),
                                new OA\Property(property: "first_name", type: "string", example: "أحمد"),
                                new OA\Property(property: "last_name", type: "string", example: "المطيري"),
                                new OA\Property(property: "full_name", type: "string", example: "أحمد المطيري"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "500000000"),
                                new OA\Property(property: "country_code", type: "string", example: "+966"),
                                new OA\Property(property: "status", type: "string", example: "active"),
                                new OA\Property(property: "kyc_level", type: "integer", example: 0),
                                new OA\Property(property: "email_verified", type: "boolean", example: true),
                                new OA\Property(property: "identity_verified", type: "boolean", example: false),
                                new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "bidder"))
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Account not verified OR account suspended",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "requires_verification" => true,
                        "message" => "حسابك غير موثق بعد. تم إرسال رمز التحقق (OTP) إلى بريدك الإلكتروني لتوثيق وتفعيل الحساب.",
                        "data" => [
                            "email" => "user@example.com",
                            "action" => "verify_otp",
                            "resend_in" => 60
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "requires_verification", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "حسابك غير موثق بعد. تم إرسال رمز التحقق (OTP) إلى بريدك الإلكتروني لتوثيق وتفعيل الحساب."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "email", type: "string", example: "user@example.com"),
                            new OA\Property(property: "action", type: "string", example: "verify_otp"),
                            new OA\Property(property: "resend_in", type: "integer", example: 60)
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Invalid credentials.",
                        "data" => null
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid credentials."),
                        new OA\Property(property: "data", type: "object", nullable: true)
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
                'error'   => true,
                'message' => __('Invalid credentials.'),
                'data'    => null,
            ], 401);
        }

        $user = Auth::user();

        // 1. Check account status (blocked / suspended)
        if (in_array($user->status, ['blocked', 'suspended', 'rejected'])) {
            Auth::logout();
            return response()->json([
                'error'   => true,
                'message' => __('حسابك معطل أو محظور. يرجى التواصل مع إدارة المنصة.'),
                'data'    => null,
            ], 403);
        }

        // 2. Condition 1: Check if account email is verified
        if (is_null($user->email_verified_at)) {
            // Generate secure OTP
            $code = sprintf('%06d', random_int(100000, 999999));
            Cache::put('otp_' . $user->email, $code, now()->addMinutes(5));
            Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
            Cache::put('otp_throttle_' . $user->email, now()->addSeconds(60)->timestamp, now()->addSeconds(60));
            Cache::put('otp_attempts_' . $user->email, 0, now()->addMinutes(5));

            $this->mailService->sendOtp($user->email, $code, 'verification', 5);

            return response()->json([
                'error'                 => true,
                'requires_verification' => true,
                'message'               => __('حسابك غير موثق بعد. تم إرسال رمز التحقق (OTP) إلى بريدك الإلكتروني لتوثيق وتفعيل الحساب.'),
                'data'                  => [
                    'email'     => $user->email,
                    'action'    => 'verify_otp',
                    'resend_in' => 60,
                ]
            ], 403);
        }

        // Save FCM / device token if passed during login
        $fcmToken = $request->input('fcm_token', $request->input('device_token'));
        if (!empty($fcmToken)) {
            $user->forceFill(['fcm_token' => $fcmToken])->save();
            \Illuminate\Support\Facades\Log::info("FCM token saved on login for User #{$user->id} ({$user->email})");
        }

        // Revoke old tokens (optional: keep only latest)
        $user->tokens()->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'error'   => false,
            'message' => __('Login successful.'),
            'data'    => [
                'access_token' => $token,
                'token'        => $token,
                'user'         => new UserResource($user->load(['wallet', 'latestKycRequest'])),
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
            'error'   => false,
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
    #[OA\Post(
        path: "/api/auth/profile",
        summary: "Update user profile",
        operationId: "updateUserProfile",
        description: "Updates the authenticated user's profile details. Supports multipart/form-data for image uploads. Alternatively, you can send a base64 string in the 'image' field.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "John"),
                        new OA\Property(property: "last_name", type: "string", example: "Doe"),
                        new OA\Property(property: "phone", type: "string", example: "500000000"),
                        new OA\Property(property: "country_code", type: "string", example: "+966"),
                        new OA\Property(property: "country", type: "string", example: "Saudi Arabia"),
                        new OA\Property(property: "city", type: "string", example: "Riyadh"),
                        new OA\Property(property: "gender", type: "string", enum: ["male", "female"]),
                        new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01"),
                        new OA\Property(property: "image", type: "string", format: "binary", description: "Profile photo image file or base64 string"),
                        new OA\Property(property: "_method", type: "string", example: "PUT", description: "Optional: Use this if forcing a PUT request via POST")
                    ]
                )
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

        $rules = [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|nullable|string|unique:users,phone,' . $user->id,
            'country_code' => 'sometimes|nullable|string|max:10',
            'country' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'gender' => 'sometimes|nullable|in:male,female',
            'date_of_birth' => 'sometimes|nullable|date',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,webp|max:5120';
        }

        $validated = $request->validate($rules);

        if (isset($validated['first_name']) || isset($validated['last_name'])) {
            $validated['name'] = ($validated['first_name'] ?? $user->first_name)
                . ' '
                . ($validated['last_name'] ?? $user->last_name);
        }

        // Handle File Upload (form-data)
        if ($request->hasFile('image')) {
            if ($user->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('image')->store('profile_photos', 'public');
        } 
        // Handle Base64 Upload (json payload)
        elseif ($request->filled('image') && is_string($request->image)) {
            if (preg_match('/^data:image\/(\w+);base64,/', $request->image, $type)) {
                $image_data = substr($request->image, strpos($request->image, ',') + 1);
                $ext = strtolower($type[1]);
                
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $decoded_image = base64_decode($image_data);
                    
                    if ($decoded_image !== false) {
                        $filename = 'profile_photos/' . uniqid() . '.' . $ext;
                        if ($user->profile_photo) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
                        }
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded_image);
                        $validated['profile_photo'] = $filename;
                    }
                }
            }
        }
        
        unset($validated['image']);

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
                example: [
                    "email" => "user@example.com"
                ],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "OTP sent successfully to email",
                content: new OA\JsonContent(
                    example: [
                        "error" => false,
                        "message" => "Password reset OTP sent to your email.",
                        "data" => [
                            "resend_in" => 60,
                            "expires_in" => 900
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset OTP sent to your email."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "resend_in", type: "integer", example: 60),
                            new OA\Property(property: "expires_in", type: "integer", example: 900)
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: "Rate limit exceeded",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Please wait 45 seconds before requesting another code.",
                        "data" => [
                            "retry_after" => 45
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Please wait 45 seconds before requesting another code."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "retry_after", type: "integer", example: 45)
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "User not found",
                content: new OA\JsonContent(
                    example: [
                        "message" => "The selected email is invalid.",
                        "errors" => [
                            "email" => ["The selected email is invalid."]
                        ]
                    ]
                )
            )
        ]
    )]
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // 60-second throttle protection per email
        $throttleKey = 'otp_throttle_reset_' . $user->email;
        if (Cache::has($throttleKey)) {
            $secondsLeft = Cache::get($throttleKey) - now()->timestamp;
            if ($secondsLeft > 0) {
                return response()->json([
                    'error'   => true,
                    'message' => __('Please wait :seconds seconds before requesting another code.', ['seconds' => $secondsLeft]),
                    'data'    => ['retry_after' => $secondsLeft]
                ], 429);
            }
        }

        // Generate a cryptographically secure 6-digit OTP
        $code = sprintf('%06d', random_int(100000, 999999));

        // Store OTP in Cache for 15 minutes
        Cache::put('reset_pwd_' . $user->email, $code, now()->addMinutes(15));
        Cache::put($throttleKey, now()->addSeconds(60)->timestamp, now()->addSeconds(60));
        Cache::put('reset_pwd_attempts_' . $user->email, 0, now()->addMinutes(15));

        // Send OTP using MailService
        $this->mailService->sendPasswordResetOtp($user->email, $code);

        return response()->json([
            'error'   => false,
            'message' => __('Password reset OTP sent to your email.'),
            'data'    => [
                'resend_in'  => 60,
                'expires_in' => 900,
            ]
        ]);
    }

    /**
     * Reset Password - Verify OTP and Change Password.
     */
    #[OA\Post(
        path: "/api/auth/reset-password",
        summary: "Reset Password",
        operationId: "resetPassword",
        description: "Verifies the OTP and sets a new password. Enforces max 5 incorrect attempts before invalidating code.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "otp", "password", "password_confirmation"],
                example: [
                    "email" => "user@example.com",
                    "otp" => "482019",
                    "password" => "NewSecretPassword123!",
                    "password_confirmation" => "NewSecretPassword123!"
                ],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "otp", type: "string", example: "482019", description: "6-digit OTP code"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "NewSecretPassword123!"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NewSecretPassword123!")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password reset successfully",
                content: new OA\JsonContent(
                    example: [
                        "error" => false,
                        "message" => "Password reset successfully. You can now login with your new password.",
                        "data" => null
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset successfully. You can now login with your new password."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Invalid or expired OTP code.",
                        "data" => null
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: "Too many failed attempts",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Too many failed attempts. This code has been invalidated. Please request a new code.",
                        "data" => null
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Too many failed attempts. This code has been invalidated. Please request a new code."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    example: [
                        "message" => "The password confirmation does not match.",
                        "errors" => [
                            "password" => ["The password confirmation does not match."]
                        ]
                    ]
                )
            )
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
        $attemptsKey = 'reset_pwd_attempts_' . $request->email;
        $attempts = (int) Cache::get($attemptsKey, 0);

        if (!$cachedCode) {
            return response()->json([
                'error'   => true,
                'message' => __('Invalid or expired OTP code.'),
                'data'    => null,
            ], 400);
        }

        // Absolute verification against cached code - No master code allowed
        if ($cachedCode !== $request->otp) {
            $attempts++;
            if ($attempts >= 5) {
                Cache::forget('reset_pwd_' . $request->email);
                Cache::forget($attemptsKey);
                return response()->json([
                    'error'   => true,
                    'message' => __('Too many failed attempts. This code has been invalidated. Please request a new code.'),
                    'data'    => null,
                ], 429);
            }
            Cache::put($attemptsKey, $attempts, now()->addMinutes(15));

            return response()->json([
                'error'   => true,
                'message' => __('Invalid or expired OTP code.'),
                'data'    => null,
            ], 400);
        }

        // Verify successful
        Cache::forget('reset_pwd_' . $request->email);
        Cache::forget($attemptsKey);
        Cache::forget('otp_throttle_reset_' . $request->email);

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Revoke tokens so user has to log in again with new password
        $user->tokens()->delete();

        return response()->json([
            'error'   => false,
            'message' => __('Password reset successfully. You can now login with your new password.'),
            'data'    => null,
        ]);
    }

    /**
     * Verify email with OTP.
     */
    #[OA\Post(
        path: "/api/auth/email/verify",
        summary: "Verify user email",
        operationId: "verifyEmail",
        description: "Verifies the authenticated user's email using the 6-digit OTP code sent during registration. Max 5 attempts.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["otp"],
                example: [
                    "otp" => "482019"
                ],
                properties: [
                    new OA\Property(property: "otp", type: "string", example: "482019", description: "6-digit OTP code")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Email verified successfully",
                content: new OA\JsonContent(
                    example: [
                        "error" => false,
                        "message" => "Email verified successfully.",
                        "data" => [
                            "user" => [
                                "id" => 15,
                                "first_name" => "محمد",
                                "last_name" => "الغامدي",
                                "full_name" => "محمد الغامدي",
                                "email" => "mohammed@example.com",
                                "phone" => "501112233",
                                "country_code" => "+966",
                                "status" => "active",
                                "kyc_level" => 0,
                                "email_verified" => true,
                                "identity_verified" => false,
                                "roles" => ["bidder"]
                            ]
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Email verified successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 15),
                                new OA\Property(property: "first_name", type: "string", example: "محمد"),
                                new OA\Property(property: "last_name", type: "string", example: "الغامدي"),
                                new OA\Property(property: "full_name", type: "string", example: "محمد الغامدي"),
                                new OA\Property(property: "email", type: "string", example: "mohammed@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "501112233"),
                                new OA\Property(property: "country_code", type: "string", example: "+966"),
                                new OA\Property(property: "status", type: "string", example: "active"),
                                new OA\Property(property: "kyc_level", type: "integer", example: 0),
                                new OA\Property(property: "email_verified", type: "boolean", example: true),
                                new OA\Property(property: "identity_verified", type: "boolean", example: false),
                                new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "bidder"))
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Invalid or expired OTP code.",
                        "data" => null
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code."),
                        new OA\Property(property: "data", type: "object", nullable: true)
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: "Too many failed attempts",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Too many incorrect attempts. Please request a new verification code.",
                        "data" => null
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Too many incorrect attempts. Please request a new verification code."),
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
                'error'   => false,
                'message' => __('Email is already verified.'),
                'data'    => [
                    'user' => new UserResource($user->load(['wallet', 'latestKycRequest'])),
                ]
            ]);
        }

        // Brute-force protection: max 5 attempts
        $attemptsKey = 'otp_attempts_verify_' . $user->email;
        $attempts = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            return response()->json([
                'error'   => true,
                'message' => __('Too many incorrect attempts. Please request a new verification code.'),
                'data'    => null,
            ], 429);
        }

        $cachedCode = Cache::get('email_verify_' . $user->email) ?? Cache::get('otp_' . $user->email);

        if (!$cachedCode || $cachedCode !== $request->otp) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(15));
            return response()->json([
                'error'   => true,
                'message' => __('Invalid or expired OTP code.'),
                'data'    => null,
            ], 400);
        }

        // OTP verified successfully
        Cache::forget('email_verify_' . $user->email);
        Cache::forget('otp_' . $user->email);
        Cache::forget($attemptsKey);
        Cache::forget('otp_throttle_resend_' . $user->email);
        $user->markEmailAsVerified();

        return response()->json([
            'error'   => false,
            'message' => __('Email verified successfully.'),
            'data'    => [
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
                    example: [
                        "error" => false,
                        "message" => "Verification OTP sent to your email.",
                        "data" => [
                            "resend_in" => 60,
                            "expires_in" => 900
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Verification OTP sent to your email."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "resend_in", type: "integer", example: 60),
                            new OA\Property(property: "expires_in", type: "integer", example: 900)
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: "Rate limit exceeded",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Please wait 45 seconds before requesting another code.",
                        "data" => [
                            "retry_after" => 45
                        ]
                    ],
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Please wait 45 seconds before requesting another code."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "retry_after", type: "integer", example: 45)
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email is already verified",
                content: new OA\JsonContent(
                    example: [
                        "error" => true,
                        "message" => "Email is already verified.",
                        "data" => null
                    ],
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
                'error'   => true,
                'message' => __('Email is already verified.'),
                'data'    => null,
            ], 400);
        }

        // 60-second throttle protection
        $throttleKey = 'otp_throttle_resend_' . $user->email;
        if (Cache::has($throttleKey)) {
            $secondsLeft = (int) Cache::get($throttleKey) - now()->timestamp;
            if ($secondsLeft > 0) {
                return response()->json([
                    'error'   => true,
                    'message' => __('Please wait :seconds seconds before requesting another code.', ['seconds' => $secondsLeft]),
                    'data'    => ['retry_after' => $secondsLeft],
                ], 429);
            }
        }

        // Cryptographically secure random 6-digit OTP
        $code = sprintf('%06d', random_int(100000, 999999));

        Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
        Cache::put('otp_' . $user->email, $code, now()->addMinutes(15));
        Cache::put($throttleKey, now()->addSeconds(60)->timestamp, now()->addSeconds(60));
        Cache::put('otp_attempts_verify_' . $user->email, 0, now()->addMinutes(15));

        $this->mailService->sendVerificationOtp($user->email, $code);

        return response()->json([
            'error'   => false,
            'message' => __('Verification OTP sent to your email.'),
            'data'    => [
                'resend_in'  => 60,
                'expires_in' => 900,
            ],
        ]);
    }

    /**
     * Update auto bid settings for the user.
     */
    #[OA\Post(
        path: "/api/auth/auto-bid-settings",
        summary: "Update Auto Bid Settings",
        operationId: "updateAutoBidSettings",
        description: "Enables or disables auto bidding for the user's account.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "Accept-Language", in: "header", required: false, schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"]))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["auto_bid_enabled"],
                properties: [
                    new OA\Property(property: "auto_bid_enabled", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Settings updated successfully")
        ]
    )]
    public function updateAutoBidSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'auto_bid_enabled' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->update(['auto_bid_enabled' => $validated['auto_bid_enabled']]);

        return response()->json([
            'error' => false,
            'message' => __('Auto bid settings updated successfully.'),
            'data' => [
                'auto_bid_enabled' => $user->auto_bid_enabled
            ],
        ]);
    }
}
