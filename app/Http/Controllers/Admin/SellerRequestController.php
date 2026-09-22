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

        // Filter by template_id
        if ($request->filled('template_id')) {
            $query->where('form_template_id', $request->input('template_id'));
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
            'under_review' => SellerRequest::where('status', 'under_review')->count(),
            'action_required' => SellerRequest::where('status', 'action_required')->count(),
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
        $sellerRequest->load(['user.wallet', 'user.latestKycRequest', 'template.fields']);

        if ($sellerRequest->status === 'pending') {
            $sellerRequest->update(['status' => 'under_review']);
        }

        $dynamicAnswers = [];
        
        if ($sellerRequest->template && $sellerRequest->template->fields) {
            foreach ($sellerRequest->template->fields as $field) {
                // تخطي حقول الفواصل الإضافية والنصوص الثابتة لأنها ليست أسئلة
                if (in_array($field->type, ['html', 'step-divider'])) {
                    continue;
                }
                
                $value = isset($sellerRequest->data[$field->name]) ? $sellerRequest->data[$field->name] : null;

                $dynamicAnswers[] = [
                    'key' => $field->name,
                    'label' => $field->label,
                    'type' => $field->type,
                    'value' => $value
                ];
            }
        } elseif (is_array($sellerRequest->data)) {
            // حالة احتياطية إذا تم حذف القالب
            foreach ($sellerRequest->data as $key => $value) {
                $dynamicAnswers[] = [
                    'key' => $key,
                    'label' => $key,
                    'type' => 'unknown',
                    'value' => $value
                ];
            }
        }

        // Fetch history of previous requests
        $history = SellerRequest::where('user_id', $sellerRequest->user_id)
            ->where('id', '!=', $sellerRequest->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'status' => $req->status,
                    'admin_notes' => $req->admin_notes,
                    'created_at' => $req->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'request' => [
                'id' => $sellerRequest->id,
                'status' => $sellerRequest->status,
                'created_at' => $sellerRequest->created_at->format('Y-m-d H:i'),
                'updated_at' => $sellerRequest->updated_at->format('Y-m-d H:i'),
                'admin_notes' => $sellerRequest->admin_notes,
                'dynamic_answers' => $dynamicAnswers,
                'user' => [
                    'id' => $sellerRequest->user->id,
                    'name' => $sellerRequest->user->full_name,
                    'email' => $sellerRequest->user->email,
                    'phone' => $sellerRequest->user->phone,
                    'city' => $sellerRequest->user->city ?? 'غير محدد',
                    'balance' => number_format($sellerRequest->user->wallet?->balance ?? 0, 2),
                    'profile_photo_url' => $sellerRequest->user->profile_photo_url,
                    'identity_verified' => (bool)$sellerRequest->user->identity_verified_at || $sellerRequest->user->status === 'approved',
                    'kyc_status' => $sellerRequest->user->latestKycRequest?->status ?? ($sellerRequest->user->status === 'approved' ? 'approved' : 'none'),
                    'registration_date' => $sellerRequest->user->created_at->format('Y-m-d'),
                    'auctions_count' => \App\Models\Bid::where('user_id', $sellerRequest->user->id)->distinct('auction_id')->count('auction_id'),
                    'reports_count' => 0, // Placeholder as there's no reports table yet
                ],
                'history' => $history
            ]
        ]);
    }

    /**
     * Approve the specified seller request.
     */
    public function approve(Request $request, SellerRequest $sellerRequest)
    {
        if (!in_array($sellerRequest->status, ['pending', 'under_review'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('الطلب غير متاح للقبول حالياً.')], 400);
            }
            return redirect()->back()->with('error', __('الطلب غير متاح للقبول حالياً.'));
        }

        // Assign seller role and update kyc level
        $sellerRequest->user->assignRole('seller');
        $sellerRequest->user->update(['kyc_level' => 3]);

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
        if (!in_array($sellerRequest->status, ['pending', 'under_review'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('الطلب غير متاح للرفض حالياً.')], 400);
            }
            return redirect()->back()->with('error', __('الطلب غير متاح للرفض حالياً.'));
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

    /**
     * Request modification for the specified seller request.
     */
    public function requestModification(Request $request, SellerRequest $sellerRequest)
    {
        if (!in_array($sellerRequest->status, ['pending', 'under_review'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('الطلب غير متاح للتعديل حالياً.')], 400);
            }
            return redirect()->back()->with('error', __('الطلب غير متاح للتعديل حالياً.'));
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        // Update request status
        $sellerRequest->update([
            'status' => 'action_required',
            'admin_notes' => $request->input('admin_notes')
        ]);

        // Send Notification to User
        try {
            \Illuminate\Support\Facades\Notification::send($sellerRequest->user, new \App\Notifications\GeneralNotification(
                'مطلوب تعديل على طلب ترقية الحساب',
                'يرجى مراجعة طلب الترقية وتعديله حسب ملاحظات الإدارة: ' . $request->input('admin_notes'),
                ['database'],
                url('/bidder/seller-subscription')
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Seller request modification notification failed: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('تم إرسال طلب التعديل للمستخدم بنجاح.')]);
        }

        return redirect()->back()->with('success', __('تم إرسال طلب التعديل للمستخدم بنجاح.'));
    }
}
