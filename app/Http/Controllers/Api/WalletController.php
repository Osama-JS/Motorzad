<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositRequestResource;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Http\Resources\WithdrawalRequestResource;
use App\Models\BankAccount;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Get wallet summary.
     */
    public function show(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        return response()->json([
            'success' => true,
            'data'    => new WalletResource($wallet),
        ]);
    }

    /**
     * Get paginated wallet transactions with filters.
     */
    public function transactions(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;
        $query  = $wallet->transactions()->with('creator')->latest();

        if ($request->filled('type') && in_array($request->type, ['credit', 'debit'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => WalletTransactionResource::collection($transactions->items()),
            'meta'    => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'total'        => $transactions->total(),
                'per_page'     => $transactions->perPage(),
            ],
        ]);
    }

    /**
     * Submit a withdrawal request.
     */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $user   = $request->user();
        $wallet = $user->wallet;

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:bank_transfer,wallet',
        ]);

        if ($validated['amount'] > $wallet->balance) {
            return response()->json([
                'success' => false,
                'message' => __('Insufficient balance for this withdrawal.'),
            ], 422);
        }

        if (WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending withdrawal request.'),
            ], 422);
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id'         => $user->id,
            'wallet_id'       => $wallet->id,
            'requested_amount'=> $validated['amount'],
            'status'          => 'pending',
            'payment_method'  => $validated['payment_method'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Withdrawal request submitted successfully. It will be reviewed soon.'),
            'data'    => new WithdrawalRequestResource($withdrawal),
        ], 201);
    }

    /**
     * Submit a deposit proof (receipt image).
     */
    public function requestDeposit(Request $request): JsonResponse
    {
        $user   = $request->user();
        $wallet = $user->wallet;

        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'receipt'         => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $receiptPath = $request->file('receipt')->store('deposits/receipts', 'public');

        $deposit = DepositRequest::create([
            'user_id'         => $user->id,
            'wallet_id'       => $wallet->id,
            'bank_account_id' => $validated['bank_account_id'],
            'amount'          => $validated['amount'],
            'receipt_path'    => $receiptPath,
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Deposit request submitted successfully. It will be reviewed soon.'),
            'data'    => new DepositRequestResource($deposit->load('bankAccount')),
        ], 201);
    }

    /**
     * Get deposit history.
     */
    public function deposits(Request $request): JsonResponse
    {
        $deposits = DepositRequest::where('user_id', $request->user()->id)
            ->with('bankAccount')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => DepositRequestResource::collection($deposits->items()),
            'meta'    => [
                'current_page' => $deposits->currentPage(),
                'last_page'    => $deposits->lastPage(),
                'total'        => $deposits->total(),
            ],
        ]);
    }

    /**
     * Get withdrawal history.
     */
    public function withdrawals(Request $request): JsonResponse
    {
        $withdrawals = WithdrawalRequest::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => WithdrawalRequestResource::collection($withdrawals->items()),
            'meta'    => [
                'current_page' => $withdrawals->currentPage(),
                'last_page'    => $withdrawals->lastPage(),
                'total'        => $withdrawals->total(),
            ],
        ]);
    }

    /**
     * Get available platform bank accounts for deposit.
     */
    public function bankAccounts(): JsonResponse
    {
        $accounts = BankAccount::where('is_active', true)->get()->map(fn ($a) => [
            'id'               => $a->id,
            'bank_name'        => $a->bank_name,
            'iban'             => $a->iban,
            'beneficiary_name' => $a->beneficiary_name,
            'logo_url'         => $a->logo_path ? asset('storage/' . $a->logo_path) : null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $accounts,
        ]);
    }
}
