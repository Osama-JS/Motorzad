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
    public function getData()
    {
        $wallets = Wallet::with('user')->latest()->get();
        return response()->json(['data' => $wallets]);
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
