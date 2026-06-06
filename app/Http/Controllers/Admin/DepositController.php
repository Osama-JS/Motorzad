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
            $deposits = DepositRequest::with(['user', 'wallet', 'bankAccount'])
                ->latest()
                ->get()
                ->map(function ($deposit) {
                    return [
                        'id'               => $deposit->id,
                        'user_name'        => $deposit->user?->full_name,
                        'user_email'       => $deposit->user?->email,
                        'bank_name'        => $deposit->bankAccount?->bank_name ?? '---',
                        'amount'           => number_format($deposit->amount, 2),
                        'status'           => $deposit->status,
                        'receipt_url'      => $deposit->receipt_path
                            ? asset('storage/' . $deposit->receipt_path)
                            : null,
                        'processed_at'     => $deposit->processed_at?->format('Y-m-d H:i'),
                        'created_at'       => $deposit->created_at->format('Y-m-d H:i'),
                    ];
                });

            return response()->json(['data' => $deposits]);
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
