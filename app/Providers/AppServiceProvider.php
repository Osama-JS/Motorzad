<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Vehicle;
use App\Models\Auction;
use App\Policies\VehiclePolicy;
use App\Policies\AuctionPolicy;

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;

use App\Health\Checks\HyperPayCheck;
use App\Health\Checks\ReverbServerCheck;
use App\Health\Checks\StalledAuctionsCheck;
use App\Health\Checks\PendingFinancialRequestsCheck;
use App\Health\Checks\SmtpMailCheck;
use App\Health\Checks\StorageDiskCheck;
use App\Health\Checks\SmsGatewayCheck;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Vehicle::class, VehiclePolicy::class);
        Gate::policy(Auction::class, AuctionPolicy::class);

        // Apply dynamic SMTP mail configuration from database settings
        \App\Services\MailConfigService::applySettings();

        Health::checks([
            DatabaseCheck::new()->label('اتصال قاعدة البيانات الرئيسية'),
            DatabaseConnectionCountCheck::new()
                ->label('عدد الاتصالات النشطة بقاعدة البيانات')
                ->warnWhenMoreConnectionsThan(50)
                ->failWhenMoreConnectionsThan(100),
            DatabaseSizeCheck::new()
                ->label('حجم مساحة قاعدة البيانات')
                ->failWhenSizeAboveGb(errorThresholdGb: 5.0),
            OptimizedAppCheck::new()
                ->label('تحسين التكوينات والمسارات (Optimized)')
                ->if(app()->isProduction()),
            DebugModeCheck::new()
                ->label('وضع تصحيح الأخطاء (Debug Mode)')
                ->expectedToBe(config('app.debug')),
            EnvironmentCheck::new()
                ->label('بيئة تشغيل التطبيق (Environment)')
                ->expectEnvironment(app()->environment()),
            CacheCheck::new()->label('سرعة واستجابة التخزين المؤقت (Cache)'),
            ScheduleCheck::new()->label('جدولة المهام التلقائية (Schedule Worker)'),

            // Motorzad Custom Checks
            HyperPayCheck::new()->label('بوابة الدفع الإلكتروني (HyperPay)'),
            ReverbServerCheck::new()->label('خادم البث المباشر للمزادات (Reverb WebSockets)'),
            StalledAuctionsCheck::new()->label('فحص المزادات العالقة (Stalled Auctions)'),
            PendingFinancialRequestsCheck::new()->label('مراجعة الطلبات المالية المعلقة (إيداع وسحب)'),
            SmtpMailCheck::new()->label('خادم البريد الإلكتروني (SMTP Mail Server)'),
            StorageDiskCheck::new()->label('مجلد التخزين وصور المركبات (Storage Disk)'),
            SmsGatewayCheck::new()->label('بوابة رسائل التحقق (SMS Gateway & OTP)'),
        ]);
    }
}
