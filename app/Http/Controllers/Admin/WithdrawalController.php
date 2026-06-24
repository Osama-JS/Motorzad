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
            try {
                $query = WithdrawalRequest::with(['user', 'wallet']);

                // Filtering by search term
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('payment_method', 'like', "%{$search}%")
                          ->orWhereHas('user', function($qu) use ($search) {
                              $qu->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                          });
                    });
                }

                // Filtering by status
                if ($request->filled('status')) {
                    $status = $request->status;
                    $query->where('status', $status);
                }

                $perPage = $request->input('per_page', 10);
                $withdrawals = $query->latest()->paginate($perPage);

                $data = $withdrawals->map(function ($withdrawal) {
                    $statusClasses = [
                        'pending' => 'status-pending',
                        'processing' => 'status-processing',
                        'approved' => 'status-approved',
                        'completed' => 'status-completed',
                        'rejected' => 'status-rejected'
                    ];
                    $statusLabels = [
                        'pending' => __('Pending'),
                        'processing' => __('Processing'),
                        'approved' => __('Approved'),
                        'completed' => __('Completed'),
                        'rejected' => __('Rejected')
                    ];
                    $statusClass = $statusClasses[$withdrawal->status] ?? 'status-pending';
                    $statusLabel = $statusLabels[$withdrawal->status] ?? $withdrawal->status;
                    $statusHtml = '<span class="status-badge ' . $statusClass . '">' . $statusLabel . '</span>';

                    $userHtml = $withdrawal->user 
                        ? '<strong>' . $withdrawal->user->name . '</strong><br><small class="text-muted">' . $withdrawal->user->email . '</small>' 
                        : '---';

                    $actionsHtml = '
                        <button type="button" class="btn btn-sm btn-info d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold text-white shadow-sm" onclick="openWithdrawalModal(' . $withdrawal->id . ')" title="' . __('View Details') . '">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>' . __('View') . '</span>
                        </button>';

                    return [
                        'id' => $withdrawal->id,
                        'user' => $userHtml,
                        'requested_amount' => '<strong>' . number_format($withdrawal->requested_amount, 2) . '</strong>',
                        'approved_amount' => $withdrawal->approved_amount ? number_format($withdrawal->approved_amount, 2) : '---',
                        'status' => $statusHtml,
                        'created_at' => $withdrawal->created_at->format('Y-m-d H:i'),
                        'actions' => $actionsHtml
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'pagination' => [
                        'current_page' => $withdrawals->currentPage(),
                        'last_page' => $withdrawals->lastPage(),
                        'total' => $withdrawals->total(),
                        'links' => $withdrawals->linkCollection()->toArray()
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
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
