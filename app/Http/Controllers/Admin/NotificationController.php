<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Send the notification.
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'channels' => 'required|array|min:1',
            'target_audience' => 'required|string|in:all,sellers,bidders,specific',
            'specific_user_id' => 'required_if:target_audience,specific|exists:users,id|nullable'
        ]);

        $users = collect();

        switch ($request->target_audience) {
            case 'all':
                $users = User::all();
                break;
            case 'sellers':
                $users = User::role('seller')->get();
                break;
            case 'bidders':
                $users = User::role('bidder')->get();
                break;
            case 'specific':
                $users = collect([User::findOrFail($request->specific_user_id)]);
                break;
        }

        if ($users->isEmpty()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'لا يوجد مستخدمين مستهدفين للإرسال.'], 400);
            }
            return back()->with('error', 'لا يوجد مستخدمين مستهدفين للإرسال.');
        }

        // Send notifications via queue
        Notification::send($users, new GeneralNotification(
            $request->title,
            $request->message,
            $request->channels, // array of selected channels e.g., ['database', 'mail', 'fcm']
            $request->action_url ?? null
        ));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إرسال الإشعار بنجاح لـ ' . $users->count() . ' مستخدم/مستخدمين.']);
        }

        return back()->with('success', 'تم إرسال الإشعار بنجاح لـ ' . $users->count() . ' مستخدم/مستخدمين.');
    }

    /**
     * Mark a notification as read and redirect back.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    }

    /**
     * Get the latest unread notification for ajax polling.
     */
    public function getLatestUnread()
    {
        $notification = auth()->user()->unreadNotifications()->first();
        if ($notification) {
            return response()->json([
                'success' => true,
                'notification' => [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'تنبيه النظام',
                    'body' => $notification->data['body'] ?? 'لديك إشعار جديد.',
                    'action_url' => $notification->data['action_url'] ?? null,
                ]
            ]);
        }
        return response()->json(['success' => false]);
    }
}
