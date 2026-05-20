<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

#[OA\Info(
    version: "1.0.0",
    title: "Motorzad API Documentation",
    description: "API Documentation for Motorzad Car Auctions Platform.",
    contact: new OA\Contact(email: "support@motorzad.com")
)]
#[OA\Server(
    url: "http://localhost/Motorzad/public",
    description: "XAMPP Apache Server (Local)"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Artisan Development Server (php artisan serve)"
)]
#[OA\Server(
    url: "/",
    description: "Production Server (Root Domain)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Register a new bidder",
        operationId: "registerBidder",
        description: "Registers a new bidder and sends a verification link to their email.",
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
                required: ["first_name", "last_name", "email", "phone", "country_code", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
                    new OA\Property(property: "city", type: "string", example: "Riyadh"),
                    new OA\Property(property: "gender", type: "string", example: "male"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1995-05-15"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful registration",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Registration successful. Please verify your email via the link sent."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function register(Request $request)
    {
        // Set local language dynamically from the Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'country_code' => 'required|string|max:10',
            'city' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'city' => $request->city,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'status' => 'pending', // Account starts as pending
            'kyc_level' => 0,
        ]);

        // Assign 'bidder' role
        $user->assignRole('bidder');

        // Trigger Laravel standard registration event
        event(new Registered($user));

        // Create Sanctum PlainTextToken
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(
            false,
            __('Registration successful. Please verify your email via the link sent.'),
            [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country_code' => $user->country_code,
                    'city' => $user->city,
                    'gender' => $user->gender,
                    'date_of_birth' => $user->date_of_birth,
                    'status' => $user->status,
                    'kyc_level' => $user->kyc_level,
                ]
            ],
            null,
            200
        );
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Login a user",
        operationId: "loginUser",
        description: "Logs in a user and returns an authentication token. Supports login using either email or phone number.",
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
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful login",
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
                description: "Unauthorized / Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "These credentials do not match our records."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request)
    {
        // Set local language dynamically from the Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required_without:phone|nullable|email',
            'phone' => 'required_without:email|nullable|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = null;
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->filled('phone')) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->apiResponse(true, __('auth.failed'), null, null, 401);
        }

        // Generate Sanctum PlainTextToken
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(
            false,
            __('Login successful.'),
            [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country_code' => $user->country_code,
                    'city' => $user->city,
                    'gender' => $user->gender,
                    'date_of_birth' => $user->date_of_birth,
                    'status' => $user->status,
                    'kyc_level' => $user->kyc_level,
                ]
            ],
            null,
            200
        );
    }

    #[OA\Get(
        path: "/api/user",
        summary: "Get authenticated user details",
        operationId: "getAuthenticatedUser",
        description: "Returns the details of the currently authenticated user.",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Authenticated user details",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "User profile retrieved successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function me(Request $request)
    {
        return $this->apiResponse(
            false,
            __('User profile retrieved successfully.'),
            [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'country_code' => $request->user()->country_code,
                    'city' => $request->user()->city,
                    'gender' => $request->user()->gender,
                    'date_of_birth' => $request->user()->date_of_birth,
                    'status' => $request->user()->status,
                    'kyc_level' => $request->user()->kyc_level,
                ]
            ],
            null,
            200
        );
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Logout the authenticated user",
        operationId: "logoutUser",
        description: "Logs out the authenticated user by revoking their access token.",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful logout",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Successfully logged out."),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiResponse(false, __('Successfully logged out.'));
    }
    #[OA\Post(
        path: "/api/forgot-password",
        summary: "Request a password reset code",
        operationId: "forgotPassword",
        description: "Sends a 6-digit verification code to the user's email if it is registered in our records.",
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
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password reset code sent successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset code sent successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "token", type: "string", example: "123456", description: "Only returned in debug/local environments")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Email not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "User with this email does not exist."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function forgotPassword(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->apiResponse(true, __('User with this email does not exist.'), null, null, 404);
        }

        // Generate 6-digit pin
        $pin = (string) mt_rand(100000, 999999);

        // Store or update in password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($pin),
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
        try {
            Mail::raw("Your password reset code is: {$pin}", function ($message) use ($request) {
                $message->to($request->email)
                    ->subject("Password Reset Code");
            });
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email: " . $e->getMessage());
        }

        $responseData = [];
        if (config('app.debug') || app()->environment('local')) {
            $responseData['token'] = $pin;
        }

        return $this->apiResponse(false, __('Password reset code sent successfully.'), $responseData);
    }

    #[OA\Post(
        path: "/api/check-token",
        summary: "Check if reset code is valid",
        operationId: "checkToken",
        description: "Validates the 6-digit code sent to the email.",
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
                required: ["email", "token"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "token", type: "string", example: "123456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token is valid",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token is valid.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired token",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired code.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed.")
                    ]
                )
            )
        ]
    )]
    public function checkToken(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return $this->apiResponse(true, __('Invalid or expired code.'), null, null, 400);
        }

        // Token lifetime: 15 minutes
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return $this->apiResponse(true, __('Invalid or expired code.'), null, null, 400);
        }

        if (!Hash::check($request->token, $record->token)) {
            return $this->apiResponse(true, __('Invalid or expired code.'), null, null, 400);
        }

        return $this->apiResponse(false, __('Token is valid.'));
    }

    #[OA\Post(
        path: "/api/reset-password",
        summary: "Reset user password",
        operationId: "resetPassword",
        description: "Updates the user's password using the validated 6-digit reset code.",
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
                required: ["email", "token", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "token", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "NewSecret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NewSecret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password reset successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password has been reset successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired token",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired code.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed.")
                    ]
                )
            )
        ]
    )]
    public function resetPassword(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return $this->apiResponse(true, __('Invalid or expired code.'), null, null, 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return $this->apiResponse(true, __('Invalid or expired code.'), null, null, 400);
        }

        if (!Hash::check($request->token, $record->token)) {
            return $this->apiResponse(true, __('Invalid or expired code.'), null, null, 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->apiResponse(true, __('User with this email does not exist.'), null, null, 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return $this->apiResponse(false, __('Password has been reset successfully.'));
    }
    #[OA\Get(
        path: "/api/email/verify/{id}/{hash}",
        summary: "Verify user's email address",
        operationId: "verifyEmail",
        description: "Validates the signature of the email verification link sent to the user and marks email as verified.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "User ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "hash",
                in: "path",
                description: "SHA-1 hash of the user's email",
                required: true,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Email verified successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Email verified successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid verification link",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid verification link.")
                    ]
                )
            )
        ]
    )]
    public function verifyEmail(Request $request, $id, $hash)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return $this->apiResponse(true, __('Invalid verification link.'), null, null, 400);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->apiResponse(false, __('Email already verified.'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->apiResponse(false, __('Email verified successfully.'));
    }

    #[OA\Post(
        path: "/api/email/resend",
        summary: "Resend email verification notification",
        operationId: "resendEmailVerification",
        description: "Resends the signed verification link to the authenticated user's email address.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Verification link sent successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Email verification link sent.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email already verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Email already verified.")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
                    ]
                )
            )
        ]
    )]
    public function resendEmailVerification(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return $this->apiResponse(true, __('Email already verified.'), null, null, 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->apiResponse(false, __('Email verification link sent.'));
    }

    #[OA\Post(
        path: "/api/user/profile",
        summary: "Update authenticated user profile",
        operationId: "updateProfile",
        description: "Updates the profile details of the currently authenticated user.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
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
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "phone", type: "string", example: "500000000"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
                    new OA\Property(property: "city", type: "string", example: "Riyadh"),
                    new OA\Property(property: "gender", type: "string", example: "male", enum: ["male", "female"]),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1995-05-15")
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
                        new OA\Property(property: "message", type: "string", example: "User profile updated successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function updateProfile(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'country_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user->fill($request->only([
            'first_name',
            'last_name',
            'phone',
            'country_code',
            'city',
            'gender',
            'date_of_birth'
        ]));

        if ($request->filled('first_name') || $request->filled('last_name')) {
            $user->name = trim(($request->first_name ?? $user->first_name) . ' ' . ($request->last_name ?? $user->last_name));
        }

        $user->save();

        return $this->apiResponse(false, __('User profile updated successfully.'), $user);
    }

    #[OA\Post(
        path: "/api/user/change-password",
        summary: "Change authenticated user password",
        operationId: "changePassword",
        description: "Changes the password of the currently authenticated user after verifying the current password.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
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
                required: ["current_password", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "current_password", type: "string", format: "password", example: "OldSecret123"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "NewSecret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NewSecret123")
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
                        new OA\Property(property: "message", type: "string", example: "Password has been changed successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Incorrect current password",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "The provided password does not match your current password.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed.")
                    ]
                )
            )
        ]
    )]
    public function changePassword(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->apiResponse(true, __('The provided password does not match your current password.'), null, null, 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return $this->apiResponse(false, __('Password has been changed successfully.'));
    }

    #[OA\Delete(
        path: "/api/user/delete",
        summary: "Permanently delete user account",
        operationId: "deleteAccount",
        description: "Permanently deletes the currently authenticated user account and revokes all active tokens.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
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
                required: ["password"],
                properties: [
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Account deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Your account has been permanently deleted.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Incorrect password confirmation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "The provided password does not match your current password.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed.")
                    ]
                )
            )
        ]
    )]
    public function deleteAccount(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return $this->apiResponse(true, __('The provided password does not match your current password.'), null, null, 400);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Delete user
        $user->delete();

        return $this->apiResponse(false, __('Your account has been permanently deleted.'));
    }

    /**
     * Standard API response helper
     */
    protected function apiResponse(bool $error, string $message, $data = null, $extra = null, int $statusCode = 200)
    {
        return response()->json([
            'error' => $error,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
