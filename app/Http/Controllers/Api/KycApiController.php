<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

class KycApiController extends Controller
{
    #[OA\Get(
        path: "/api/kyc/status",
        summary: "Get current user KYC status",
        operationId: "getKycStatus",
        description: "Returns the identity verification status of the currently authenticated user.",
        security: [["bearerAuth" => []]],
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
        responses: [
            new OA\Response(
                response: 200,
                description: "Verification status retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Verification status retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "kyc_status", type: "string", example: "pending", enum: ["not_submitted", "pending", "approved", "rejected"]),
                            new OA\Property(property: "kyc_level", type: "integer", example: 3),
                            new OA\Property(property: "identity_verified_at", type: "string", format: "date-time", nullable: true, example: "2026-05-23T00:00:00Z"),
                            new OA\Property(property: "user_status", type: "string", example: "approved", enum: ["pending", "approved", "rejected"]),
                            new OA\Property(property: "latest_request", type: "object", nullable: true)
                        ])
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
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $latestRequest = $user->latestKycRequest;

        $data = [
            'kyc_status' => $latestRequest ? $latestRequest->status : 'not_submitted',
            'kyc_level' => $user->kyc_level,
            'identity_verified_at' => $user->identity_verified_at ? $user->identity_verified_at->toIso8601String() : null,
            'user_status' => $user->status,
            'latest_request' => $latestRequest ? [
                'id' => $latestRequest->id,
                'full_name' => $latestRequest->full_name,
                'country' => $latestRequest->country,
                'id_number' => $latestRequest->id_number,
                'id_image_url' => asset('storage/' . $latestRequest->id_image),
                'selfie_image_url' => asset('storage/' . $latestRequest->selfie_image),
                'status' => $latestRequest->status,
                'admin_note' => $latestRequest->admin_note,
                'reviewed_at' => $latestRequest->reviewed_at ? $latestRequest->reviewed_at->toIso8601String() : null,
                'created_at' => $latestRequest->created_at->toIso8601String(),
            ] : null
        ];

        return response()->json([
            'success' => true,
            'message' => __('Verification status retrieved successfully.'),
            'data' => $data
        ]);
    }

    #[OA\Post(
        path: "/api/kyc/submit",
        summary: "Submit identity verification documents",
        operationId: "submitKycRequest",
        description: "Allows the authenticated user to upload national ID and selfie images to request identity verification.",
        security: [["bearerAuth" => []]],
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
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["full_name", "country", "id_number", "id_image", "selfie_image"],
                    properties: [
                        new OA\Property(property: "full_name", type: "string", example: "John Doe"),
                        new OA\Property(property: "country", type: "string", example: "Saudi Arabia"),
                        new OA\Property(property: "id_number", type: "string", example: "1234567890"),
                        new OA\Property(property: "id_image", type: "string", format: "binary", description: "Identity document image (JPEG, PNG, JPG, max 2MB)"),
                        new OA\Property(property: "selfie_image", type: "string", format: "binary", description: "Selfie holding document (JPEG, PNG, JPG, max 2MB)")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Identity verification request submitted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Identity verification request submitted successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Duplicate request or already verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "You already have a pending verification request.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation failed",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            )
        ]
    )]
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'id_number' => 'required|string|max:50',
            'id_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();
        $latestRequest = $user->latestKycRequest;

        // Prevent submission if already approved
        if ($user->status === 'approved' || ($latestRequest && $latestRequest->status === 'approved')) {
            return response()->json([
                'success' => false,
                'message' => __('Your identity is already verified.')
            ], 400);
        }

        // Prevent submission if there's a pending request
        if ($latestRequest && $latestRequest->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending verification request.')
            ], 400);
        }

        // Upload and store the files
        $idPath = $request->file('id_image')->store('kyc', 'public');
        $selfiePath = $request->file('selfie_image')->store('kyc', 'public');

        // Create the KYC request
        $kycRequest = KycRequest::create([
            'user_id' => $user->id,
            'full_name' => $validated['full_name'],
            'country' => $validated['country'],
            'id_number' => $validated['id_number'],
            'id_image' => $idPath,
            'selfie_image' => $selfiePath,
            'status' => 'pending'
        ]);

        // Keep or set user status to pending
        $user->update([
            'status' => 'pending'
        ]);

        $responseData = [
            'id' => $kycRequest->id,
            'full_name' => $kycRequest->full_name,
            'country' => $kycRequest->country,
            'id_number' => $kycRequest->id_number,
            'id_image_url' => asset('storage/' . $kycRequest->id_image),
            'selfie_image_url' => asset('storage/' . $kycRequest->selfie_image),
            'status' => $kycRequest->status,
            'created_at' => $kycRequest->created_at->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'message' => __('Identity verification request submitted successfully.'),
            'data' => $responseData
        ], 201);
    }

    #[OA\Get(
        path: "/api/kyc/history",
        summary: "Get KYC verification history",
        operationId: "getKycHistory",
        description: "Returns a paginated list of all past KYC verification requests submitted by the authenticated user.",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                description: "Number of items per page",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Page number",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Verification history retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Verification history retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "requests", type: "array", items: new OA\Items(type: "object")),
                            new OA\Property(property: "pagination", type: "object")
                        ])
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
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        $requests = $user->kycRequests()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        $formattedItems = collect($requests->items())->map(function ($req) {
            return [
                'id' => $req->id,
                'full_name' => $req->full_name,
                'country' => $req->country,
                'id_number' => $req->id_number,
                'id_image_url' => asset('storage/' . $req->id_image),
                'selfie_image_url' => asset('storage/' . $req->selfie_image),
                'status' => $req->status,
                'admin_note' => $req->admin_note,
                'reviewed_at' => $req->reviewed_at ? $req->reviewed_at->toIso8601String() : null,
                'created_at' => $req->created_at->toIso8601String(),
            ];
        });

        $data = [
            'requests' => $formattedItems,
            'pagination' => [
                'total' => $requests->total(),
                'per_page' => $requests->perPage(),
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => __('Verification history retrieved successfully.'),
            'data' => $data
        ]);
    }
}
