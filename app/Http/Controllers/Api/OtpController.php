<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use App\Http\Resources\UserResource;
use App\Services\MailService;

class OtpController extends Controller
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    #[OA\Post(
        path: "/api/otp/send",
        summary: "Send an OTP code",
        operationId: "sendOtp",
        description: "Generates a 6-digit OTP and sends it via phone or email. In local or debug mode, the code is returned in the response for easy testing.",
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
                    new OA\Property(property: "phone", type: "string", example: "500000000"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
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
                        new OA\Property(property: "message", type: "string", example: "OTP sent successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "otp", type: "string", example: "123456", description: "Only returned in debug/local environments")
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
    public function sendOtp(Request $request)
    {
        // Set local language dynamically from the Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required_without:email|nullable|string',
            'country_code' => 'required_with:phone|nullable|string',
            'email' => 'required_without:phone|nullable|email',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $identifier = $request->filled('email') 
            ? $request->email 
            : $request->country_code . $request->phone;

        // Rate limiting: 60 seconds between OTP requests per identifier
        $throttleKey = 'otp_throttle_' . $identifier;
        if (Cache::has($throttleKey)) {
            $secondsLeft = Cache::get($throttleKey) - now()->timestamp;
            if ($secondsLeft > 0) {
                return $this->apiResponse(
                    true,
                    __('Please wait :seconds seconds before requesting another code.', ['seconds' => $secondsLeft]),
                    null,
                    ['retry_after' => $secondsLeft],
                    429
                );
            }
        }

        // Generate a cryptographically secure 6-digit OTP
        $code = sprintf('%06d', random_int(100000, 999999));

        // If email was provided, dispatch real SMTP email
        if ($request->filled('email')) {
            $sendResult = $this->mailService->sendOtp($request->email, $code, 'login', 5);
            if (!$sendResult['success']) {
                return $this->apiResponse(true, $sendResult['message'], null, null, 500);
            }
        }

        // Store OTP in Cache for 5 minutes
        Cache::put('otp_' . $identifier, $code, now()->addMinutes(5));
        Cache::put($throttleKey, now()->addSeconds(60)->timestamp, now()->addSeconds(60));
        Cache::put('otp_attempts_' . $identifier, 0, now()->addMinutes(5));

        Log::info("OTP generated and sent to: {$identifier} -> Code: {$code}");

        $responseData = [
            'expires_in' => 300, // seconds
            'resend_in'  => 60,  // seconds
        ];

        return $this->apiResponse(false, __('OTP sent successfully to your email.'), $responseData);
    }

    #[OA\Post(
        path: "/api/otp/verify",
        summary: "Verify OTP code and authenticate",
        operationId: "verifyOtp",
        description: "Verifies the OTP code. If correct and user exists, authenticates them. If they do not exist, automatically registers them as a bidder and returns access token.",
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
                required: ["code"],
                properties: [
                    new OA\Property(property: "phone", type: "string", example: "500000000"),
                    new OA\Property(property: "country_code", type: "string", example: "+966"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "code", type: "string", example: "123456")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful authentication",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Verification successful."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or expired OTP code",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code."),
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
    public function verifyOtp(Request $request)
    {
        // Set local language dynamically from the Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required_without:email|nullable|string',
            'country_code' => 'required_with:phone|nullable|string',
            'email' => 'required_without:phone|nullable|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $identifier = $request->filled('email') 
            ? $request->email 
            : $request->country_code . $request->phone;

        $cachedCode = Cache::get('otp_' . $identifier);
        $attemptsKey = 'otp_attempts_' . $identifier;
        $attempts = (int) Cache::get($attemptsKey, 0);

        if (!$cachedCode) {
            return $this->apiResponse(true, __('Invalid or expired OTP code.'), null, null, 400);
        }

        if ($cachedCode !== $request->code) {
            $attempts++;
            if ($attempts >= 5) {
                Cache::forget('otp_' . $identifier);
                Cache::forget($attemptsKey);
                return $this->apiResponse(true, __('Too many failed attempts. This code has been invalidated. Please request a new code.'), null, null, 429);
            }
            Cache::put($attemptsKey, $attempts, now()->addMinutes(5));
            return $this->apiResponse(true, __('Invalid or expired OTP code.'), null, null, 400);
        }

        // OTP verified successfully, remove from cache
        Cache::forget('otp_' . $identifier);
        Cache::forget($attemptsKey);
        Cache::forget('otp_throttle_' . $identifier);

        // Find user
        $user = null;
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('phone', $request->phone)
                ->where('country_code', $request->country_code)
                ->first();
        }

        // If existing user verified via email, mark as verified
        if ($user && $request->filled('email') && is_null($user->email_verified_at)) {
            $updateData = ['email_verified_at' => now()];
            if ($user->kyc_level == 0) {
                $updateData['kyc_level'] = 1;
            }
            $user->update($updateData);
        }

        $isNewUser = false;
        if (!$user) {
            $isNewUser = true;

            // Auto-register user
            $email = $request->filled('email') ? $request->email : $request->phone . '@motorzad.com';
            $phone = $request->filled('phone') ? $request->phone : null;
            $countryCode = $request->filled('phone') ? $request->country_code : null;

            // Check if phone or email is already taken to avoid duplicate entry crashes
            if ($phone && User::where('phone', $phone)->where('country_code', $countryCode)->exists()) {
                return $this->apiResponse(true, __('The phone number is already registered with another account.'), null, null, 400);
            }
            if ($email && User::where('email', $email)->exists()) {
                return $this->apiResponse(true, __('The email is already registered with another account.'), null, null, 400);
            }

            // Generate temporary unique name
            $nameSuffix = $phone ? substr($phone, -4) : substr(md5($email), 0, 4);
            $name = 'User_' . $nameSuffix;

            $user = User::create([
                'name' => $name,
                'first_name' => 'User',
                'last_name' => $nameSuffix,
                'email' => $email,
                'phone' => $phone,
                'country_code' => $countryCode,
                'password' => Hash::make(Str::random(16)),
                'status' => 'pending',
                'kyc_level' => 0,
            ]);

            if ($request->filled('email')) {
                $user->email_verified_at = now();
                $user->kyc_level = 1;
                $user->save();
            }

            // Assign 'bidder' role
            $user->assignRole('bidder');
        }

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(
            false,
            $isNewUser ? __('Registration and login successful.') : __('Verification and login successful.'),
            [
                'access_token' => $token,
                'user' => new UserResource($user->load(['wallet', 'latestKycRequest'])),
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
