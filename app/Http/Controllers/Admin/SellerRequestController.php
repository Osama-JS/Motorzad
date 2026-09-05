<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerRequest;
use Illuminate\Http\Request;

class SellerRequestController extends Controller
{
    /**
     * Display a listing of the seller requests.
     */
    /**
     * Display a listing of the seller requests.
     */
    public function index(Request $request)
    {
        $query = SellerRequest::with(['user.wallet', 'user.roles']);

        // Search by user name, email, phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sorting: pending first, then latest
        $requests = $query->orderByRaw("FIELD(status, 'pending') DESC")
            ->latest()
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        // Statistics
        $stats = [
            'total' => SellerRequest::count(),
            'pending' => SellerRequest::where('status', 'pending')->count(),
            'approved' => SellerRequest::where('status', 'approved')->count(),
            'rejected' => SellerRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.seller_requests.index', compact('requests', 'stats'));
    }

    /**
     * Show seller request details modal/json
     */
    public function show(SellerRequest $sellerRequest)
    {
        $sellerRequest->load(['user.wallet', 'user.latestKycRequest']);

        return response()->json([
            'success' => true,
            'request' => [
                'id' => $sellerRequest->id,
                'status' => $sellerRequest->status,
                'created_at' => $sellerRequest->created_at->format('Y-m-d H:i'),
                'updated_at' => $sellerRequest->updated_at->format('Y-m-d H:i'),
                'admin_notes' => $sellerRequest->admin_notes,
                'user' => [
                    'id' => $sellerRequest->user->id,
                    'name' => $sellerRequest->user->full_name,
                    'email' => $sellerRequest->user->email,
                    'phone' => $sellerRequest->user->phone,
                    'city' => $sellerRequest->user->city ?? 'غير محدد',
                    'balance' => number_format($sellerRequest->user->wallet?->balance ?? 0, 2),
                    'profile_photo_url' => $sellerRequest->user->profile_photo_url,
                    'identity_verified' => (bool)$sellerRequest->user->identity_verified_at,
                    'kyc_status' => $sellerRequest->user->latestKycRequest?->status ?? 'none',
                ]
            ]
        ]);
    }

    /**
     * Approve the specified seller request.
     */
    public function approve(Request $request, SellerRequest $sellerRequest)
    {
        if ($sellerRequest->status !== 'pending') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('الطلب غير معلق حالياً.')], 400);
            }
            return redirect()->back()->with('error', __('الطلب غير معلق حالياً.'));
        }

        // Assign seller role
        $sellerRequest->user->assignRole('seller');

        // Update request status
        $sellerRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->input('admin_notes')
        ]);

        // Send Notification to User
        try {
            \Illuminate\Support\Facades\Notification::send($sellerRequest->user, new \App\Notifications\GeneralNotification(
                'تم قبول طلب الترقية لبائع!',
                'مبروك! تمت الموافقة على طلب ترقية حسابك إلى بائع، يمكنك الآن إضافة سياراتك وإطلاق المزادات فوراً.',
                ['database'],
                url('/bidder/seller-subscription')
            ));
        } catch (\Exception $e) {
            // Log if notification fails
            \Illuminate\Support\Facades\Log::warning('Seller approval notification failed: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('تم قبول طلب البائع وترقية المستخدم بنجاح.')]);
        }

        return redirect()->back()->with('success', __('تم قبول طلب البائع وترقية المستخدم بنجاح.'));
    }

    /**
     * Reject the specified seller request.
     */
    public function reject(Request $request, SellerRequest $sellerRequest)
    {
        if ($sellerRequest->status !== 'pending') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('الطلب غير معلق حالياً.')], 400);
            }
            return redirect()->back()->with('error', __('الطلب غير معلق حالياً.'));
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        // Update request status
        $sellerRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes')
        ]);

        // Send Notification to User
        try {
            \Illuminate\Support\Facades\Notification::send($sellerRequest->user, new \App\Notifications\GeneralNotification(
                'تحديث بخصوص طلب ترقية الحساب',
                'عذراً، تم رفض طلب ترقية حسابك إلى بائع. سبب الرفض: ' . $request->input('admin_notes'),
                ['database'],
                url('/bidder/seller-subscription')
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Seller rejection notification failed: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('تم رفض طلب البائع بنجاح وتسجيل السبب.')]);
        }

        return redirect()->back()->with('success', __('تم رفض طلب البائع بنجاح وتسجيل السبب.'));
    }
}
