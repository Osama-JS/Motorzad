<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SupportController extends Controller
{
    /**
     * List all tickets for the authenticated user.
     */
    #[OA\Get(
        path: "/api/support",
        summary: "List Support Tickets",
        description: "Returns a paginated list of support tickets for the authenticated user.",
        tags: ["Support"],
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
                                    'subject' => 'I have an issue with my deposit',
                                    'status' => 'open',
                                    'created_at' => '2023-11-10T12:00:00.000000Z',
                                    'updated_at' => '2023-11-10T12:00:00.000000Z',
                                    'messages_count' => 3
                                ]
                            ],
                            'last_page' => 1,
                            'per_page' => 15,
                            'total' => 1
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $tickets = Ticket::where('user_id', $request->user()->id)
            ->withCount('messages')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    /**
     * Create a new support ticket.
     */
    #[OA\Post(
        path: "/api/support",
        summary: "Create Support Ticket",
        description: "Creates a new support ticket and its first message.",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["subject", "message"],
                properties: [
                    new OA\Property(property: "subject", type: "string", example: "I have an issue with my deposit"),
                    new OA\Property(property: "message", type: "string", example: "Please help, my deposit is not showing.")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: "Ticket created successfully",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Ticket created successfully.',
                        'data' => [
                            'id' => 1,
                            'user_id' => 5,
                            'subject' => 'I have an issue with my deposit',
                            'status' => 'open',
                            'created_at' => '2023-11-10T12:00:00.000000Z',
                            'updated_at' => '2023-11-10T12:00:00.000000Z',
                            'messages' => [
                                [
                                    'id' => 1,
                                    'ticket_id' => 1,
                                    'user_id' => 5,
                                    'message' => 'Please help, my deposit is not showing.',
                                    'is_admin' => false,
                                    'created_at' => '2023-11-10T12:00:00.000000Z',
                                    'updated_at' => '2023-11-10T12:00:00.000000Z'
                                ]
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(example: ['message' => 'The subject field is required.', 'errors' => ['subject' => ['The subject field is required.']]])
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'status' => 'open'
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_admin' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Ticket created successfully.'),
            'data' => $ticket->load('messages')
        ], 201);
    }

    /**
     * Get a specific ticket with its messages.
     */
    #[OA\Get(
        path: "/api/support/{ticket}",
        summary: "Show Support Ticket",
        description: "Returns details of a specific ticket along with its chat messages.",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, description: "Ticket ID", schema: new OA\Schema(type: "integer"))
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
                            'subject' => 'I have an issue with my deposit',
                            'status' => 'open',
                            'created_at' => '2023-11-10T12:00:00.000000Z',
                            'updated_at' => '2023-11-10T12:00:00.000000Z',
                            'messages' => [
                                [
                                    'id' => 1,
                                    'ticket_id' => 1,
                                    'user_id' => 5,
                                    'message' => 'Please help, my deposit is not showing.',
                                    'is_admin' => false,
                                    'created_at' => '2023-11-10T12:00:00.000000Z',
                                    'updated_at' => '2023-11-10T12:00:00.000000Z',
                                    'sender' => [
                                        'id' => 5,
                                        'first_name' => 'محمد',
                                        'last_name' => 'أحمد',
                                        'profile_photo' => null
                                    ]
                                ]
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
    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized.')], 403);
        }

        $ticket->load(['messages.sender:id,first_name,last_name,profile_photo']);

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Send a new message in an existing ticket.
     */
    #[OA\Post(
        path: "/api/support/{ticket}/reply",
        summary: "Reply to Ticket",
        description: "Sends a new message in an existing ticket chat.",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "ticket", in: "path", required: true, description: "Ticket ID", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["message"],
                properties: [
                    new OA\Property(property: "message", type: "string", example: "Thank you for the help.")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: "Message sent successfully",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Message sent successfully.',
                        'data' => [
                            'id' => 2,
                            'ticket_id' => 1,
                            'user_id' => 5,
                            'message' => 'Thank you for the help.',
                            'is_admin' => false,
                            'created_at' => '2023-11-10T12:05:00.000000Z',
                            'updated_at' => '2023-11-10T12:05:00.000000Z',
                            'sender' => [
                                'id' => 5,
                                'first_name' => 'محمد',
                                'last_name' => 'أحمد',
                                'profile_photo' => null
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(example: ['message' => 'The message field is required.', 'errors' => ['message' => ['The message field is required.']]])
            ),
            new OA\Response(
                response: 403, 
                description: "Unauthorized",
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Unauthorized.'])
            )
        ]
    )]
    public function reply(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized.')], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string'
        ]);

        $message = $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_admin' => false
        ]);

        // Automatically reopen ticket if it was closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return response()->json([
            'success' => true,
            'message' => __('Message sent successfully.'),
            'data' => $message->load('sender:id,first_name,last_name,profile_photo')
        ]);
    }
}
