<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get user's notifications.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'الإشعار غير موجود'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => 'تم التحديد كمقروء']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true, 'message' => 'تم تحديد الكل كمقروء']);
    }

    /**
     * Update FCM Token for the user.
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json(['success' => true, 'message' => 'تم تحديث توكن الإشعارات بنجاح']);
    }
}
