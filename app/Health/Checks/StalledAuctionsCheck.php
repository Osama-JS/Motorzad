<?php

namespace App\Health\Checks;

use App\Models\Auction;
use Exception;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class StalledAuctionsCheck extends Check
{
    protected int $gracePeriodMinutes = 2;

    public function gracePeriodMinutes(int $minutes): static
    {
        $this->gracePeriodMinutes = $minutes;
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        try {
            // Find live auctions whose end_time has passed the grace period
            $cutoff = now()->subMinutes($this->gracePeriodMinutes);
            
            $stalledQuery = Auction::where('status', 'live')
                ->where('end_time', '<', $cutoff);

            $stalledCount = $stalledQuery->count();

            $result->meta([
                'stalled_count' => $stalledCount,
                'grace_period_minutes' => $this->gracePeriodMinutes,
                'cutoff' => $cutoff->toDateTimeString(),
            ]);

            if ($stalledCount === 0) {
                return $result->ok('لا توجد مزادات عالقة، إغلاق وتسوية المزادات يعمل بانتظام');
            }

            return $result->failed("تنبيه حرج: يوجد {$stalledCount} مزاد متجاوز لوقت الانتهاء ولم يتم إغلاقه آلياً! تأكد من تشغيل جدول المهام.");
        } catch (Exception $e) {
            return $result->warning('تعذر فحص المزادات العالقة: ' . $e->getMessage());
        }
    }
}
