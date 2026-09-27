<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Notifications',
    description: 'API endpoints for managing user in-app and push notifications'
)]
class NotificationController extends Controller
{
    /**
     * Get paginated user notifications.
     */
    #[OA\Get(
        path: '/api/notifications',
        summary: 'Get user notifications with pagination',
        description: 'Returns a paginated list of the authenticated user notifications, ordered by newest first, with support for filtering by unread/read.',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                in: 'query',
                required: false,
                description: 'Filter notifications: all, unread, or read',
                schema: new OA\Schema(type: 'string', enum: ['all', 'unread', 'read'], default: 'all')
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number for pagination',
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Items per page (default: 15, max: 50)',
                schema: new OA\Schema(type: 'integer', default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of notifications retrieved successfully',
                content: new OA\JsonContent(
                    example: [
                        'error' => false,
                        'message' => 'تم جلب الإشعارات بنجاح.',
                        'data' => [
                            [
                                'id' => '9d1a8e32-2023-4318-8f85-3b91fa876251',
                                'title' => '⚡ تم تجاوز عرضك في المزاد!',
                                'body' => 'قام مزايد آخر بتقديم عرض أعلى على تويوتا لاندكروزر بقيمة 155,000 ريال.',
                                'action_url' => 'https://motorzad.com/bidder/auctions/5',
                                'type' => 'outbid',
                                'extra_data' => [
                                    'auction_id' => 5,
                                    'new_price' => 155000,
                                    'type' => 'outbid'
                                ],
                                'read' => false,
                                'read_at' => null,
                                'created_at' => '2026-09-24T12:30:00.000000Z',
                                'created_at_human' => 'منذ 5 دقائق'
                            ]
                        ],
                        'meta' => [
                            'unread_count' => 3,
                            'current_page' => 1,
                            'last_page' => 3,
                            'per_page' => 15,
                            'total' => 38,
                            'has_more_pages' => true,
                            'next_page_url' => 'https://motorzad.com/api/notifications?page=2',
                            'prev_page_url' => null
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $user->notifications();

        $filter = $request->input('filter', 'all');
        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        } elseif ($filter === 'read') {
            $query = $user->readNotifications();
        }

        // Lightweight pagination (default 15, maximum 50 per page)
        $perPage = min(max((int)$request->input('per_page', 15), 1), 50);
        $paginated = $query->latest()->paginate($perPage);

        $unreadCount = $user->unreadNotifications()->count();

        $items = collect($paginated->items())->map(function ($notif) {
            $data = is_array($notif->data) ? $notif->data : (json_decode($notif->data, true) ?? []);
            return [
                'id'               => $notif->id,
                'title'            => $data['title'] ?? 'إشعار جديد',
                'body'             => $data['body'] ?? ($data['message'] ?? ''),
                'action_url'       => $data['action_url'] ?? null,
                'type'             => $data['data']['type'] ?? ($data['type'] ?? 'general'),
                'extra_data'       => $data['data'] ?? [],
                'read'             => !is_null($notif->read_at),
                'read_at'          => $notif->read_at?->toIso8601String(),
                'created_at'       => $notif->created_at?->toIso8601String(),
                'created_at_human' => $notif->created_at?->diffForHumans(),
            ];
        });

        return response()->json([
            'error'   => false,
            'message' => __('تم جلب الإشعارات بنجاح.'),
            'data'    => $items,
            'meta'    => [
                'unread_count'   => $unreadCount,
                'current_page'   => $paginated->currentPage(),
                'last_page'      => $paginated->lastPage(),
                'per_page'       => $paginated->perPage(),
                'total'          => $paginated->total(),
                'has_more_pages' => $paginated->hasMorePages(),
                'next_page_url'  => $paginated->nextPageUrl(),
                'prev_page_url'  => $paginated->previousPageUrl(),
            ]
        ], 200);
    }

    /**
     * Get the count of unread notifications.
     */
    #[OA\Get(
        path: '/api/notifications/unread-count',
        summary: 'Get unread notifications count',
        description: 'Returns the total number of unread notifications for badge display in the mobile app header/tabs.',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Unread count returned successfully',
                content: new OA\JsonContent(
                    example: [
                        'error' => false,
                        'data' => [
                            'unread_count' => 4
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            )
        ]
    )]
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json([
            'error' => false,
            'data'  => [
                'unread_count' => $count,
            ]
        ], 200);
    }

    /**
     * Mark a specific notification as read.
     */
    #[OA\Post(
        path: '/api/notifications/{id}/read',
        summary: 'Mark single notification as read',
        description: 'Marks a specific notification by UUID as read and returns the updated unread count.',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Notification UUID',
                schema: new OA\Schema(type: 'string', example: '9d1a8e32-2023-4318-8f85-3b91fa876251')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Notification marked as read successfully',
                content: new OA\JsonContent(
                    example: [
                        'error' => false,
                        'message' => 'تم تحديد الإشعار كمقروء.',
                        'data' => [
                            'id' => '9d1a8e32-2023-4318-8f85-3b91fa876251',
                            'read' => true,
                            'read_at' => '2026-09-24T12:35:10.000000Z',
                            'unread_count' => 2
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Notification not found',
                content: new OA\JsonContent(
                    example: [
                        'error' => true,
                        'message' => 'الإشعار غير موجود أو لا تملك صلاحية الوصول إليه.',
                        'data' => null
                    ]
                )
            )
        ]
    )]
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'error'   => true,
                'message' => __('الإشعار غير موجود أو لا تملك صلاحية الوصول إليه.'),
                'data'    => null
            ], 404);
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return response()->json([
            'error'   => false,
            'message' => __('تم تحديد الإشعار كمقروء.'),
            'data'    => [
                'id'           => $notification->id,
                'read'         => true,
                'read_at'      => $notification->read_at?->toIso8601String(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]
        ], 200);
    }

    /**
     * Mark all notifications as read.
     */
    #[OA\Post(
        path: '/api/notifications/read-all',
        summary: 'Mark all notifications as read',
        description: 'Marks all unread notifications of the user as read in one batch.',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All notifications marked as read',
                content: new OA\JsonContent(
                    example: [
                        'error' => false,
                        'message' => 'تم تحديد جميع الإشعارات كمقروءة بنجاح.',
                        'data' => [
                            'unread_count' => 0
                        ]
                    ]
                )
            )
        ]
    )]
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'error'   => false,
            'message' => __('تم تحديد جميع الإشعارات كمقروءة بنجاح.'),
            'data'    => [
                'unread_count' => 0
            ]
        ], 200);
    }

    /**
     * Update FCM / Device Push Token.
     */
    #[OA\Post(
        path: '/api/notifications/fcm-token',
        summary: 'Register or update FCM Device Token',
        description: 'Registers or updates the Firebase Cloud Messaging device push token for the authenticated user. Calling this is required for the user to receive mobile push notifications.',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fcm_token'],
                example: [
                    'fcm_token' => 'eKJ...fcm_device_token_from_firebase_sdk...'
                ],
                properties: [
                    new OA\Property(
                        property: 'fcm_token',
                        type: 'string',
                        description: 'FCM push registration token generated by FirebaseMessaging SDK on Android/iOS',
                        example: 'eKJ...fcm_device_token_from_firebase_sdk...'
                    ),
                    new OA\Property(
                        property: 'device_token',
                        type: 'string',
                        description: 'Alias for fcm_token',
                        example: 'eKJ...fcm_device_token_from_firebase_sdk...'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'FCM token updated successfully',
                content: new OA\JsonContent(
                    example: [
                        'error' => false,
                        'message' => 'تم تحديث رمز إشعارات الجهاز (FCM Token) بنجاح.',
                        'data' => [
                            'fcm_token' => 'eKJ...fcm_device_token_from_firebase_sdk...'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error - Token is missing',
                content: new OA\JsonContent(
                    example: [
                        'error' => true,
                        'message' => 'يجب إرسال رمز الجهاز (fcm_token).',
                        'data' => null
                    ]
                )
            )
        ]
    )]
    public function updateFcmToken(Request $request): JsonResponse
    {
        $token = $request->input('fcm_token', $request->input('device_token'));

        if (empty($token)) {
            return response()->json([
                'error'   => true,
                'message' => __('يجب إرسال رمز الجهاز (fcm_token).'),
                'data'    => null
            ], 422);
        }

        $request->user()->forceFill([
            'fcm_token' => $token,
        ])->save();

        Log::info("FCM Token updated via API for User #{$request->user()->id} ({$request->user()->email})");

        return response()->json([
            'error'   => false,
            'message' => __('تم تحديث رمز إشعارات الجهاز (FCM Token) بنجاح.'),
            'data'    => [
                'fcm_token' => $token
            ]
        ], 200);
    }
}
