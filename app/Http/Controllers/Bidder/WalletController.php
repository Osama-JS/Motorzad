<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\DepositRequest;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Display the bidder's wallet profile page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        // Type filter
        $type = $request->input('type', 'all');
        $txQuery = $wallet->transactions()->with('creator')->latest();
        if ($type !== 'all') {
            $txQuery->where('type', $type);
        }

        // Wallet transactions (paginated by 10)
        $transactions = $txQuery->paginate(10)->withQueryString();

        // Withdrawal requests
        $withdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Monthly stats for the chart (last 6 months)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $depositsAmount = $wallet->transactions()
                ->where('type', 'credit')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $withdrawalsAmount = $wallet->transactions()
                ->where('type', 'debit')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $monthlyStats[] = [
                'month' => $date->translatedFormat('M'),
                'deposits' => (float) $depositsAmount,
                'withdrawals' => (float) $withdrawalsAmount,
            ];
        }

        // Deposit requests
        $deposits = DepositRequest::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Platform bank accounts for deposits
        if (BankAccount::where('is_active', true)->count() === 0) {
            BankAccount::create([
                'bank_name' => 'مصرف الراجحي',
                'beneficiary_name' => 'شركة موتورزاد للمزادات',
                'iban' => 'SA8080000000000000000001',
                'is_active' => true
            ]);
        }
        $platformBanks = BankAccount::where('is_active', true)->get();

        if ($request->ajax()) {
            if ($request->has('page') || $request->has('type')) {
                return response()->json([
                    'success' => true,
                    'html' => view('bidder.wallet.partials.transactions-list', compact('transactions'))->render()
                ]);
            }
            return response()->json([
                'success' => true,
                'html' => view('bidder.wallet.partials.content', compact('user', 'wallet', 'transactions', 'withdrawals', 'deposits', 'platformBanks', 'monthlyStats', 'type'))->render()
            ]);
        }

        return view('bidder.wallet.index', compact('user', 'wallet', 'transactions', 'withdrawals', 'deposits', 'platformBanks', 'monthlyStats', 'type'));
    }

    /**
     * Get transactions data for AJAX requests with filtering.
     */
    public function transactions(Request $request)
    {
        $wallet = auth()->user()->wallet;
        $query = $wallet->transactions()->with('creator')->latest();

        // Type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Submit a withdrawal request from the bidder side.
     */
    public function requestWithdrawal(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:bank_transfer,wallet',
        ]);

        // Check if user has sufficient available balance
        if ($validated['amount'] > $wallet->available_balance) {
            return response()->json([
                'success' => false,
                'message' => __('Insufficient available balance for this withdrawal. Some of your funds may be frozen in active bids.'),
            ], 422);
        }

        // Check if user has pending withdrawal
        $pendingExists = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending withdrawal request.'),
            ], 422);
        }

        // Create the withdrawal request
        WithdrawalRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'requested_amount' => $validated['amount'],
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Withdrawal request submitted successfully. It will be reviewed soon.'),
        ]);
    }

    /**
     * Submit a deposit proof from the bidder side.
     */
    public function requestDeposit(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'receipt' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
        ]);

        $receiptPath = $request->file('receipt')->store('deposits/receipts', 'public');

        DepositRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'bank_account_id' => $validated['bank_account_id'],
            'amount' => $validated['amount'],
            'receipt_path' => $receiptPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Deposit request submitted successfully. It will be reviewed soon.'),
        ]);
    }

    /**
     * Display a printable invoice for a specific transaction.
     */
    public function invoice($id)
    {
        $user = auth()->user();
        $transaction = \App\Models\WalletTransaction::where('wallet_id', $user->wallet->id)->findOrFail($id);

        return view('bidder.wallet.invoice', compact('user', 'transaction'));
    }
}
