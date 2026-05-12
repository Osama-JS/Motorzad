<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * عملية إضافة أو خصم رصيد
     */
    public function adjustBalance(Wallet $wallet, $amount, $type, $description = null, $attachmentPath = null)
    {
        return DB::transaction(function () use ($wallet, $amount, $type, $description, $attachmentPath) {
            // 1. إنشاء سجل الحركة
            $transaction = $wallet->transactions()->create([
                'type' => $type, // 'credit' or 'debit'
                'amount' => $amount,
                'description' => $description,
                'attachment_path' => $attachmentPath,
                'created_by' => auth()->id(),
            ]);

            // 2. تحديث رصيد المحفظة الفعلي
            if ($type === 'credit') {
                $wallet->increment('balance', $amount);
                $wallet->increment('total_deposits', $amount);
            } else {
                $wallet->decrement('balance', $amount);
                $wallet->increment('total_withdrawals', $amount);
            }

            // 3. تحديث نسبة استهلاك الدين تلقائياً
            $this->updateDebtUsage($wallet);

            return $transaction;
        });
    }

    /**
     * تحديث نسبة استهلاك الدين تلقائياً
     */
    private function updateDebtUsage(Wallet $wallet)
    {
        if ($wallet->debt_ceiling > 0 && $wallet->balance < 0) {
            $usage = (abs($wallet->balance) / $wallet->debt_ceiling) * 100;
            $wallet->update(['debt_usage' => min($usage, 100)]);
        } else {
            $wallet->update(['debt_usage' => 0]);
        }
    }
}
