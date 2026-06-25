<?php

namespace App\Http\Controllers;

use App\Models\KycRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    /**
     * Display the KYC verification page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $latestRequest = $user->latestKycRequest;
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('kyc.partials.content', compact('user', 'latestRequest'))->render()
            ]);
        }
        
        return view('kyc.index', compact('user', 'latestRequest'));
    }

    /**
     * Submit a new KYC request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'id_number' => 'required|string|max:50',
            'id_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        // Check if there's a pending request
        if ($user->kycRequests()->where('status', 'pending')->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('لديك طلب قيد المراجعة بالفعل.')
                ], 422);
            }
            return back()->with('error', 'لديك طلب قيد المراجعة بالفعل.');
        }

        $idPath = $request->file('id_image')->store('kyc', 'public');
        $selfiePath = $request->file('selfie_image')->store('kyc', 'public');

        KycRequest::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'country' => $request->country,
            'id_number' => $request->id_number,
            'id_image' => $idPath,
            'selfie_image' => $selfiePath,
            'status' => 'pending'
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('تم إرسال طلب التحقق بنجاح. سيتم مراجعته من قبل الإدارة.')
            ]);
        }

        return redirect()->route('kyc.index')->with('success', 'تم إرسال طلب التحقق بنجاح. سيتم مراجعته من قبل الإدارة.');
    }
}
