<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Display a listing of deposit requests.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = DepositRequest::with(['user', 'wallet', 'bankAccount']);

                // Filtering by search term
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->whereHas('bankAccount', function($qb) use ($search) {
                            $qb->where('bank_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function($qu) use ($search) {
                            $qu->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('name', 'like', "%{$search}%")
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
                $deposits = $query->latest()->paginate($perPage);

                $data = $deposits->map(function ($deposit) {
                    $statusClasses = [
                        'pending' => 'status-pending',
                        'approved' => 'status-approved',
                        'rejected' => 'status-rejected'
                    ];
                    $statusLabels = [
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected')
                    ];
                    $statusClass = $statusClasses[$deposit->status] ?? 'status-pending';
                    $statusLabel = $statusLabels[$deposit->status] ?? $deposit->status;
                    $statusHtml = '<span class="status-badge ' . $statusClass . '">' . $statusLabel . '</span>';

                    $userHtml = $deposit->user 
                        ? '<strong>' . $deposit->user->full_name . '</strong><br><small class="text-muted">' . $deposit->user->email . '</small>' 
                        : '---';

                    $actionsHtml = '
                        <button type="button" class="btn btn-sm btn-info d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold text-white shadow-sm" onclick="openDepositModal(' . $deposit->id . ')" title="' . __('معالجة الطلب') . '">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>' . __('عرض ومعالجة') . '</span>
                        </button>';

                    return [
                        'id' => $deposit->id,
                        'user_name' => $userHtml,
                        'bank_name' => $deposit->bankAccount?->bank_name ?? '---',
                        'amount' => '<strong class="text-success">' . number_format($deposit->amount, 2) . ' SAR</strong>',
                        'status' => $statusHtml,
                        'created_at' => $deposit->created_at->format('Y-m-d H:i'),
                        'actions' => $actionsHtml
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'pagination' => [
                        'current_page' => $deposits->currentPage(),
                        'last_page' => $deposits->lastPage(),
                        'total' => $deposits->total(),
                        'links' => $deposits->linkCollection()->toArray()
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        $stats = [
            'total_pending'  => DepositRequest::where('status', 'pending')->count(),
            'total_approved' => DepositRequest::where('status', 'approved')->sum('amount'),
            'total_requests' => DepositRequest::count(),
        ];

        return view('admin.deposits.index', compact('stats'));
    }

    /**
     * Show deposit request details.
     */
    public function show(DepositRequest $deposit)
    {
        $deposit->load(['user', 'wallet', 'bankAccount']);
        return response()->json(['data' => $deposit]);
    }

    /**
     * Process (approve/reject) a deposit request.
     * This is where the wallet actually gets credited!
     */
    public function process(Request $request, DepositRequest $deposit)
    {
        $request->validate([
            'status'     => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($deposit->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('This deposit request has already been processed.'),
            ], 422);
        }

        DB::transaction(function () use ($request, $deposit) {
            $deposit->update([
                'status'       => $request->status,
                'admin_note'   => $request->admin_note,
                'action_by'    => auth()->id(),
                'processed_at' => now(),
            ]);

            // ✅ Credit the wallet ONLY when approved
            if ($request->status === 'approved') {
                $this->walletService->adjustBalance(
                    wallet: $deposit->wallet,
                    amount: $deposit->amount,
                    type: 'credit',
                    description: __('Deposit approved by admin') . ($request->admin_note ? ': ' . $request->admin_note : ''),
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => $request->status === 'approved'
                ? __('Deposit approved and wallet credited successfully.')
                : __('Deposit request rejected.'),
        ]);
    }
}
