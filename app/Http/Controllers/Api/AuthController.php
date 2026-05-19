<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use OpenApi\Attributes as OA;

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
