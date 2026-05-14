<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $withdrawals = \App\Models\WithdrawalRequest::with(['user', 'wallet'])->latest()->get();
            return response()->json(['data' => $withdrawals]);
        }

        $stats = [
            'total_pending' => \App\Models\WithdrawalRequest::where('status', 'pending')->count(),
            'total_approved' => \App\Models\WithdrawalRequest::where('status', 'approved')->orWhere('status', 'completed')->sum('approved_amount'),
            'total_requests' => \App\Models\WithdrawalRequest::count(),
        ];

        return view('admin.withdrawals.index', compact('stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\WithdrawalRequest $withdrawal)
    {
        $withdrawal->load(['user', 'wallet']);
        return response()->json(['data' => $withdrawal]);
    }

    /**
     * Process the withdrawal request.
     */
    public function process(Request $request, \App\Models\WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed,processing',
            'approved_amount' => 'required_if:status,approved|numeric|min:0',
            'admin_notes' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $withdrawal->update([
            'status' => $request->status,
            'approved_amount' => $request->approved_amount ?? $withdrawal->requested_amount,
            'admin_notes' => $request->admin_notes,
            'payment_method' => $request->payment_method ?? $withdrawal->payment_method,
            'action_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        if ($request->status === 'approved' || $request->status === 'completed') {
            // Update wallet balance if not already deducted. 
            // Depending on business logic, maybe it's deducted on request or on approval.
            // If we need to debit the wallet, we can use WalletService here.
            // For now, let's just log the transaction if needed, or leave it to business logic.
        }

        return response()->json([
            'success' => true,
            'message' => 'تم معالجة الطلب بنجاح',
        ]);
    }
}
