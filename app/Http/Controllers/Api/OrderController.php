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
            new OA\Response(response: 200, description: "Successful response")
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
            new OA\Response(response: 200, description: "Successful response"),
            new OA\Response(response: 403, description: "Unauthorized")
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
            new OA\Response(response: 200, description: "Checkout details updated successfully"),
            new OA\Response(response: 403, description: "Unauthorized"),
            new OA\Response(response: 422, description: "Validation error or already paid")
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
