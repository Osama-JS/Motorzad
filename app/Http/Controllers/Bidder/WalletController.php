<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\DepositRequest;
use App\Models\BankAccount;
use App\Models\HyperpayTransaction;
use App\Services\HyperPayService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        protected HyperPayService $hyperPayService
    ) {}

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

        // إرسال إشعار للإدارة
        $admins = \App\Models\User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralNotification(
                'طلب سحب جديد',
                'قام المستخدم ' . $user->name . ' بطلب سحب مبلغ ' . $validated['amount'] . ' ريال.',
                ['database'],
                url('/admin/wallets/' . $wallet->id)
            ));
        }

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

        // Check if user has a pending deposit request
        $pendingExists = DepositRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending deposit request. Please wait for it to be reviewed before submitting another.'),
            ], 422);
        }

        $receiptPath = $request->file('receipt')->store('deposits/receipts', 'public');

        DepositRequest::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'bank_account_id' => $validated['bank_account_id'],
            'amount' => $validated['amount'],
            'receipt_path' => $receiptPath,
            'status' => 'pending',
        ]);

        // إرسال إشعار للإدارة
        $admins = \App\Models\User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralNotification(
                'طلب إيداع جديد',
                'قام المستخدم ' . $user->name . ' بطلب إيداع مبلغ ' . $validated['amount'] . ' ريال.',
                ['database'],
                url('/admin/wallets/' . $wallet->id)
            ));
        }

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

    /**
     * Start HyperPay checkout session from Bidder portal.
     */
    public function initiateHyperPay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'brand'  => 'required|in:mada,visa_master,apple_pay',
        ]);

        try {
            $user = auth()->user();
            $amount = (float) $request->amount;
            $brand = $request->brand;
            $returnUrl = route('bidder.wallet.hyperpay.callback');

            $checkoutData = $this->hyperPayService->prepareCheckout(
                user: $user,
                amount: $amount,
                brand: $brand,
                channel: 'web',
                returnUrl: $returnUrl
            );

            return response()->json([
                'success'      => true,
                'redirect_url' => route('bidder.wallet.hyperpay.checkout', $checkoutData['transaction_id']),
                'data'         => $checkoutData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Render the official HyperPay payment widget page.
     */
    public function hyperPayCheckout($transactionId)
    {
        $user = auth()->user();
        $transaction = HyperpayTransaction::where('user_id', $user->id)->findOrFail($transactionId);

        if ($transaction->status === 'paid') {
            return redirect()->route('bidder.wallet.index')->with('success', __('This payment has already been completed and added to your wallet.'));
        }

        $widgetBrands = $this->hyperPayService->getWidgetBrands($transaction->brand);
        $scriptUrl = $this->hyperPayService->getBaseUrl() . "/v1/paymentWidgets.js?checkoutId={$transaction->checkout_id}";
        $returnUrl = route('bidder.wallet.hyperpay.callback');

        return view('bidder.wallet.hyperpay-checkout', compact('transaction', 'widgetBrands', 'scriptUrl', 'returnUrl', 'user'));
    }

    /**
     * Handle user return after 3D Secure / payment processing on HyperPay.
     */
    public function hyperPayCallback(Request $request)
    {
        $checkoutId = $request->query('id');

        if (!$checkoutId) {
            return redirect()->route('bidder.wallet.index')->with('error', __('Invalid payment reference returned from gateway.'));
        }

        $transaction = HyperpayTransaction::where('checkout_id', $checkoutId)->firstOrFail();

        try {
            // Verify payment directly from HyperPay
            $paymentData = $this->hyperPayService->verifyPayment($checkoutId, $transaction->brand);
            $resultCode = $paymentData['result']['code'] ?? '';

            if ($this->hyperPayService->isSuccessCode($resultCode)) {
                $this->hyperPayService->processSuccessfulPayment($transaction, $paymentData);

                return redirect()->route('bidder.wallet.index')->with('success', __('Payment successful! An amount of :amount SAR has been credited to your wallet.', [
                    'amount' => number_format($transaction->amount, 2)
                ]));
            } else {
                $this->hyperPayService->markAsFailed($transaction, $paymentData);
                $errorDesc = $paymentData['result']['description'] ?? __('The payment could not be processed.');

                return redirect()->route('bidder.wallet.index')->with('error', __('Payment Failed: :desc', ['desc' => $errorDesc]));
            }
        } catch (\Exception $e) {
            return redirect()->route('bidder.wallet.index')->with('error', __('Verification error: :msg', ['msg' => $e->getMessage()]));
        }
    }
}

