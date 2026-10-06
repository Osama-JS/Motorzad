<?php

namespace App\Health\Checks;

use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;
use Exception;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class PendingFinancialRequestsCheck extends Check
{
    protected int $warningThreshold = 15;
    protected int $failureThreshold = 35;

    public function warnWhenMorePendingThan(int $count): static
    {
        $this->warningThreshold = $count;
        return $this;
    }

    public function failWhenMorePendingThan(int $count): static
    {
        $this->failureThreshold = $count;
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        try {
            $pendingDeposits = DepositRequest::where('status', 'pending')->count();
            $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->count();
            $totalPending = $pendingDeposits + $pendingWithdrawals;

            $result->meta([
                'pending_deposits' => $pendingDeposits,
                'pending_withdrawals' => $pendingWithdrawals,
                'total_pending' => $totalPending,
                'warning_threshold' => $this->warningThreshold,
                'failure_threshold' => $this->failureThreshold,
            ]);

            if ($totalPending >= $this->failureThreshold) {
                return $result->failed("تراكم حرج في المعاملات المالية: يوجد {$totalPending} طلب معلق بانتظار الإجراء ({$pendingDeposits} إيداع، {$pendingWithdrawals} سحب)!");
            }

            if ($totalPending >= $this->warningThreshold) {
                return $result->warning("ضغط مالي مرتفع: يوجد {$totalPending} طلب قيد الانتظار ({$pendingDeposits} إيداع، {$pendingWithdrawals} سحب) بحاجة لمراجعة.");
            }

            return $result->ok("الطلبات المالية مستقرة ({$totalPending} طلب معلق: {$pendingDeposits} إيداع، {$pendingWithdrawals} سحب)");
        } catch (Exception $e) {
            return $result->warning('تعذر فحص الطلبات المالية: ' . $e->getMessage());
        }
    }
}
