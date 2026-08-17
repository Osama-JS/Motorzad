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
    public function index()
    {
        $requests = SellerRequest::with('user')
            ->orderByRaw("FIELD(status, 'pending') DESC")
            ->latest()
            ->paginate(15);
            
        return view('admin.seller_requests.index', compact('requests'));
    }

    /**
     * Approve the specified seller request.
     */
    public function approve(Request $request, SellerRequest $sellerRequest)
    {
        if ($sellerRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Request is not pending.'));
        }

        // Assign role
        $sellerRequest->user->assignRole('seller');

        // Update request status
        $sellerRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->input('admin_notes')
        ]);

        // Optional: Send Notification to User
        \Illuminate\Support\Facades\Notification::send($sellerRequest->user, new \App\Notifications\GeneralNotification(
            'تم قبول طلبك!',
            'مبروك! تمت الموافقة على طلب الترقية، حسابك الآن بائع ويمكنك إطلاق مزادات لسياراتك.',
            ['database'],
            url('/bidder/seller-subscription')
        ));

        return redirect()->back()->with('success', __('Seller request approved successfully.'));
    }

    /**
     * Reject the specified seller request.
     */
    public function reject(Request $request, SellerRequest $sellerRequest)
    {
        if ($sellerRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Request is not pending.'));
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        // Update request status
        $sellerRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes')
        ]);

        // Optional: Send Notification to User
        \Illuminate\Support\Facades\Notification::send($sellerRequest->user, new \App\Notifications\GeneralNotification(
            'عذراً، تم رفض طلبك',
            'تم رفض طلب ترقية حسابك إلى بائع. ' . $request->input('admin_notes'),
            ['database'],
            url('/bidder/seller-subscription')
        ));

        return redirect()->back()->with('success', __('Seller request rejected successfully.'));
    }
}
