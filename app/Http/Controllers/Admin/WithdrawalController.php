<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawalRequest;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $withdrawals = WithdrawalRequest::with(['user', 'wallet'])->latest()->get();
            return response()->json(['data' => $withdrawals]);
        }

        $stats = [
            'total_pending' => WithdrawalRequest::where('status', 'pending')->count(),
            'total_approved' => WithdrawalRequest::whereIn('status', ['approved', 'completed'])->sum('approved_amount'),
            'total_requests' => WithdrawalRequest::count(),
        ];

        return view('admin.withdrawals.index', compact('stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(WithdrawalRequest $withdrawal)
    {
        $withdrawal->load(['user', 'wallet']);
        return response()->json(['data' => $withdrawal]);
    }

    /**
     * Process the withdrawal request.
     */
    public function process(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed,processing',
            'approved_amount' => 'required_if:status,approved|numeric|min:0',
            'admin_notes' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'هذا الطلب تمت معالجته مسبقاً'], 422);
        }

        DB::beginTransaction();

        try {
            $status = $request->status;
            $approvedAmount = $request->approved_amount ?? $withdrawal->requested_amount;

            // If approved, deduct from wallet
            if ($status === 'approved') {
                $wallet = $withdrawal->wallet;
                if ($wallet->balance < $approvedAmount) {
                    return response()->json(['message' => 'رصيد المحفظة غير كافٍ'], 422);
                }
                
                $wallet->decrement('balance', $approvedAmount);
            }

            $withdrawal->update([
                'status' => $status,
                'approved_amount' => $approvedAmount,
                'admin_notes' => $request->admin_notes,
                'payment_method' => $request->payment_method ?? $withdrawal->payment_method,
                'action_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم معالجة الطلب بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب'], 500);
        }
    }
}
