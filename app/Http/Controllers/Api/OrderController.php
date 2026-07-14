<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    /**
     * List user's won auctions/orders.
     */
    #[OA\Get(
        path: "/api/orders",
        summary: "List User Orders (Won Auctions)",
        description: "Returns a paginated list of orders (auctions won by the authenticated user).",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Successful response",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'current_page' => 1,
                            'data' => [
                                [
                                    'id' => 1,
                                    'user_id' => 5,
                                    'auction_id' => 10,
                                    'vehicle_id' => 15,
                                    'winning_bid' => 55000,
                                    'deposit_amount' => 1000,
                                    'commission_amount' => 2750,
                                    'total_amount' => 56750,
                                    'payment_status' => 'pending',
                                    'status' => 'pending_payment',
                                    'created_at' => '2023-11-10T12:00:00.000000Z',
                                    'auction' => [
                                        'id' => 10,
                                        'title_ar' => 'تويوتا كامري 2022',
                                        'title_en' => 'Toyota Camry 2022',
                                        'end_time' => '2023-11-10T10:00:00.000000Z'
                                    ],
                                    'vehicle' => [
                                        'id' => 15,
                                        'make_ar' => 'تويوتا',
                                        'make_en' => 'Toyota',
                                        'model_ar' => 'كامري',
                                        'model_en' => 'Camry'
                                    ]
                                ]
                            ],
                            'last_page' => 1,
                            'per_page' => 15,
                            'total' => 1
                        ]
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['auction:id,title_ar,title_en,end_time', 'vehicle:id,make_ar,make_en,model_ar,model_en'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Show order details for checkout.
     */
    #[OA\Get(
        path: "/api/orders/{order}",
        summary: "Show Order Details",
        description: "Returns the details of a specific order (won auction), including deposit, commission, and total amounts.",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "order", in: "path", required: true, description: "Order ID", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Successful response",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'id' => 1,
                            'user_id' => 5,
                            'auction_id' => 10,
                            'vehicle_id' => 15,
                            'winning_bid' => 55000,
                            'deposit_amount' => 1000,
                            'commission_amount' => 2750,
                            'total_amount' => 56750,
                            'payment_status' => 'pending',
                            'status' => 'pending_payment',
                            'created_at' => '2023-11-10T12:00:00.000000Z',
                            'auction' => [
                                'id' => 10,
                                'title_ar' => 'تويوتا كامري 2022',
                                'title_en' => 'Toyota Camry 2022',
                                'end_time' => '2023-11-10T10:00:00.000000Z',
                                'deposit_amount' => 1000
                            ],
                            'vehicle' => [
                                'id' => 15,
                                'make_ar' => 'تويوتا',
                                'make_en' => 'Toyota',
                                'model_ar' => 'كامري',
                                'model_en' => 'Camry',
                                'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg'
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 403, 
                description: "Unauthorized",
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Unauthorized.'])
            )
        ]
    )]
    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized.')], 403);
        }

        $order->load(['auction:id,title_ar,title_en,end_time,deposit_amount', 'vehicle.primaryImage']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update checkout details (delivery & payment method).
     */
    #[OA\Put(
        path: "/api/orders/{order}/checkout",
        summary: "Checkout / Submit Order Details",
        description: "Submit checkout details (delivery and payment method) for a won auction order.",
        tags: ["Orders"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "order", in: "path", required: true, description: "Order ID", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["delivery_type", "payment_method"],
                properties: [
                    new OA\Property(property: "delivery_type", type: "string", enum: ["pickup", "delivery"], example: "delivery"),
                    new OA\Property(property: "delivery_address", type: "string", example: "Riyadh, Saudi Arabia"),
                    new OA\Property(property: "delivery_phone", type: "string", example: "+966500000000"),
                    new OA\Property(property: "payment_method", type: "string", enum: ["wallet", "bank_transfer"], example: "wallet"),
                    new OA\Property(property: "notes", type: "string", example: "Please deliver before 5 PM.")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "Checkout details updated successfully",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Checkout details updated successfully. Pending admin confirmation.',
                        'data' => [
                            'id' => 1,
                            'user_id' => 5,
                            'auction_id' => 10,
                            'vehicle_id' => 15,
                            'winning_bid' => 55000,
                            'deposit_amount' => 1000,
                            'commission_amount' => 2750,
                            'total_amount' => 56750,
                            'payment_status' => 'pending',
                            'status' => 'processing',
                            'delivery_type' => 'delivery',
                            'delivery_address' => 'Riyadh, Saudi Arabia',
                            'delivery_phone' => '+966500000000',
                            'payment_method' => 'wallet',
                            'notes' => 'Please deliver before 5 PM.',
                            'created_at' => '2023-11-10T12:00:00.000000Z',
                            'updated_at' => '2023-11-10T12:05:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 403, 
                description: "Unauthorized",
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Unauthorized.'])
            ),
            new OA\Response(
                response: 422, 
                description: "Validation error or already paid",
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Order is already paid.'])
            )
        ]
    )]
    public function checkout(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized.')], 403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => false, 'message' => __('Order is already paid.')], 422);
        }

        $validated = $request->validate([
            'delivery_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_type,delivery|string|nullable',
            'delivery_phone' => 'required_if:delivery_type,delivery|string|nullable',
            'payment_method' => 'required|in:wallet,bank_transfer',
            'notes' => 'nullable|string'
        ]);

        $order->update([
            'delivery_type' => $validated['delivery_type'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'delivery_phone' => $validated['delivery_phone'] ?? null,
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'processing'
        ]);

        // If payment method is wallet, attempt to deduct from wallet here if required,
        // or wait for admin approval. This depends on business logic. 
        // For now, we update the status so the admin can review.

        return response()->json([
            'success' => true,
            'message' => __('Checkout details updated successfully. Pending admin confirmation.'),
            'data' => $order
        ]);
    }
}
