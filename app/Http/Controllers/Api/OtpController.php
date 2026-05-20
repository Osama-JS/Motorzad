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

class OtpController extends Controller
{
    #[OA\Post(
        path: "/api/otp/send",
        summary: "Send an OTP code",
        operationId: "sendOtp",
        description: "Generates a 6-digit OTP and sends it via phone or email. In local or debug mode, the code is returned in the response for easy testing.",
        tags: ["OTP Authentication"],
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

        // Generate a 6-digit OTP
        $code = (string) mt_rand(100000, 999999);

        // Store OTP in Cache for 5 minutes
        Cache::put('otp_' . $identifier, $code, now()->addMinutes(5));

        // Log OTP code (Simulating sending SMS/Email)
        Log::info("OTP generated for: {$identifier} -> Code: {$code}");

        // Prepare response data
        $responseData = [];
        if (config('app.debug') || app()->environment('local')) {
            $responseData['otp'] = $code;
        }

        return $this->apiResponse(false, __('OTP sent successfully.'), $responseData);
    }

    #[OA\Post(
        path: "/api/otp/verify",
        summary: "Verify OTP code and authenticate",
        operationId: "verifyOtp",
        description: "Verifies the OTP code. If correct and user exists, authenticates them. If they do not exist, automatically registers them as a bidder and returns access token.",
        tags: ["OTP Authentication"],
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

        // Allow '123456' as master code in local/debug environment for easy testing
        $isMasterCode = (config('app.debug') || app()->environment('local')) && $request->code === '123456';

        if (!$cachedCode && !$isMasterCode) {
            return $this->apiResponse(true, __('Invalid or expired OTP code.'), null, null, 400);
        }

        if ($cachedCode !== $request->code && !$isMasterCode) {
            return $this->apiResponse(true, __('Invalid or expired OTP code.'), null, null, 400);
        }

        // OTP verified successfully, remove from cache
        Cache::forget('otp_' . $identifier);

        // Find user
        $user = null;
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('phone', $request->phone)
                ->where('country_code', $request->country_code)
                ->first();
        }

        $isNewUser = false;
        if (!$user) {
            $isNewUser = true;

            // Auto-register user
            $email = $request->filled('email') ? $request->email : $request->phone . '@motorzad.com';
            $phone = $request->filled('phone') ? $request->phone : null;
            $countryCode = $request->filled('phone') ? $request->country_code : null;

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
