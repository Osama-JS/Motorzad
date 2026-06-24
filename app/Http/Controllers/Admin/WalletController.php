<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * عرض قائمة المحافظ
     */
    public function index()
    {
        $stats = [
            'total_balance' => Wallet::sum('balance'),
            'total_deposits' => Wallet::sum('total_deposits'),
            'total_withdrawals' => Wallet::sum('total_withdrawals'),
            'count' => Wallet::count(),
        ];
        return view('admin.wallets.index', compact('stats'));
    }

    /**
     * جلب البيانات للـ DataTable
     */
    public function getData(Request $request)
    {
        try {
            $query = Wallet::with('user');

            // Filtering
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 10);
            $wallets = $query->latest()->paginate($perPage);

            $data = $wallets->map(function ($wallet) {
                $userHtml = $wallet->user 
                    ? '<strong>' . $wallet->user->name . '</strong><br><small class="text-muted">' . $wallet->user->email . '</small>' 
                    : '---';

                $balanceCls = $wallet->balance >= 0 ? 'balance-positive' : 'balance-negative';
                $balanceHtml = '<span class="balance-badge ' . $balanceCls . '">' . number_format($wallet->balance, 2) . '</span>';

                $debtUsageHtml = '
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar ' . ($wallet->debt_usage > 80 ? 'bg-danger' : 'bg-primary') . '" role="progressbar" style="width: ' . $wallet->debt_usage . '%" aria-valuenow="' . $wallet->debt_usage . '" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small>' . $wallet->debt_usage . '%</small>';

                $baseUrl = url('admin/wallets');
                $actionsHtml = '
                    <div class="btn-group shadow-sm" style="border-radius: 8px;">
                        <a href="' . $baseUrl . '/' . $wallet->id . '/transactions" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold" title="' . __('Advanced Transactions History') . '">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4H10a2 2 0 0 1-2-2v-4"/><circle cx="18" cy="12" r="1.5"/></svg>
                            <span>' . __('Transactions') . '</span>
                        </a>
                        <button type="button" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold text-dark" onclick="openDebtModal(' . $wallet->id . ', ' . $wallet->debt_ceiling . ')" title="' . __('Debt Ceiling') . '">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                            <span>' . __('Ceiling') . '</span>
                        </button>
                    </div>';

                return [
                    'id' => $wallet->id,
                    'user' => $userHtml,
                    'balance' => $balanceHtml,
                    'debt_ceiling' => number_format($wallet->debt_ceiling, 2),
                    'debt_usage' => $debtUsageHtml,
                    'total_deposits' => number_format($wallet->total_deposits, 2),
                    'total_withdrawals' => number_format($wallet->total_withdrawals, 2),
                    'actions' => $actionsHtml
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $wallets->currentPage(),
                    'last_page' => $wallets->lastPage(),
                    'total' => $wallets->total(),
                    'links' => $wallets->linkCollection()->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * عرض تفاصيل محفظة محددة مع الحركات
     */
    public function show(Wallet $wallet)
    {
        $wallet->load(['user', 'transactions' => function ($query) {
            $query->latest();
        }]);
        return view('admin.wallets.show', compact('wallet'));
    }

    /**
     * إضافة حركة مالية يدوية
     */
    public function storeTransaction(Request $request, Wallet $wallet)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'description' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('wallet_attachments', 'public');
        }

        $this->walletService->adjustBalance(
            $wallet,
            $request->amount,
            $request->type,
            $request->description,
            $attachmentPath
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل العملية وتحديث الرصيد بنجاح',
                'wallet' => $wallet->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'تم تسجيل العملية وتحديث الرصيد بنجاح');
    }

    /**
     * تحديث سقف الدين
     */
    public function updateDebtCeiling(Request $request, Wallet $wallet)
    {
        $request->validate([
            'debt_ceiling' => 'required|numeric|min:0',
        ]);

        $wallet->update([
            'debt_ceiling' => $request->debt_ceiling
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث سقف الدين بنجاح',
                'wallet' => $wallet->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث سقف الدين بنجاح');
    }
}
