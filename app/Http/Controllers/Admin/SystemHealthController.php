<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Health;
use Spatie\Health\Models\HealthCheckResultHistoryItem;
use Spatie\Health\ResultStores\ResultStore;

class SystemHealthController extends Controller
{
    /**
     * Display the System Health Dashboard with real-time latency benchmarks,
     * incident history timeline, and maintenance controls.
     */
    public function index(Request $request, ResultStore $resultStore, Health $health): View
    {
        if ($request->has('fresh')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        // 1. Measure DB query latency
        $dbLatency = null;
        $dbStart = microtime(true);
        try {
            DB::select('SELECT 1');
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);
        } catch (\Throwable $e) {
            $dbLatency = null;
        }

        // 2. Measure Cache read/write latency
        $cacheLatency = null;
        $cacheStart = microtime(true);
        try {
            $testKey = '_system_health_latency_test_' . rand(100, 999);
            Cache::put($testKey, 'ok', 10);
            Cache::get($testKey);
            Cache::forget($testKey);
            $cacheLatency = round((microtime(true) - $cacheStart) * 1000, 2);
        } catch (\Throwable $e) {
            $cacheLatency = null;
        }

        // 3. Memory usage metrics
        $memoryUsageMb = round(memory_get_usage(true) / (1024 * 1024), 2);
        $memoryPeakMb = round(memory_get_peak_usage(true) / (1024 * 1024), 2);

        // 4. Incident History Timeline (Warnings & Failures)
        $incidentHistory = HealthCheckResultHistoryItem::whereIn('status', ['warning', 'failed', 'crashed'])
            ->latest('created_at')
            ->take(15)
            ->get();

        // 5. Maintenance Mode Status
        $isMaintenanceMode = app()->isDownForMaintenance() || (Setting::get('maintenance_mode', '0') === '1');
        $maintenanceMessage = Setting::get('maintenance_message', 'المنصة تخضع حالياً لأعمال صيانة طارئة لحماية المزايدات. سنعود خلال دقائق معدودة.');

        return view('vendor.health.list', [
            'lastRanAt' => $checkResults?->finishedAt ? new Carbon($checkResults->finishedAt) : null,
            'checkResults' => $checkResults,
            'assets' => $health->assets(),
            'theme' => config('health.theme', 'light'),
            'dbLatency' => $dbLatency,
            'cacheLatency' => $cacheLatency,
            'memoryUsageMb' => $memoryUsageMb,
            'memoryPeakMb' => $memoryPeakMb,
            'incidentHistory' => $incidentHistory,
            'isMaintenanceMode' => $isMaintenanceMode,
            'maintenanceMessage' => $maintenanceMessage,
        ]);
    }

    /**
     * Quick Action: Clear application, view, and route caches.
     */
    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم تفريغ وحذف التخزين المؤقت (Cache) وملفات القوالب بنجاح ⚡',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر تفريغ الكاش: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick Action: Restart queue workers.
     */
    public function restartQueue(): JsonResponse
    {
        try {
            Artisan::call('queue:restart');

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إشارة إعادة تشغيل طابور المهام (Queue Restart) لجميع المشغلين بنجاح 🔄',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر إعادة تشغيل الطابور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick Action: Clear and rebuild system optimizations.
     */
    public function optimizeClear(): JsonResponse
    {
        try {
            Artisan::call('optimize:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم تنظيف كافة تكوينات ومسارات النظام بنجاح 🚀',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنظيف التهيئات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick Action: Toggle Emergency Maintenance Mode.
     */
    public function toggleMaintenance(Request $request): JsonResponse
    {
        try {
            $isCurrentlyDown = app()->isDownForMaintenance() || (Setting::get('maintenance_mode', '0') === '1');

            if ($isCurrentlyDown) {
                // Deactivate maintenance mode
                Artisan::call('up');
                Setting::set('maintenance_mode', '0');

                return response()->json([
                    'success' => true,
                    'is_maintenance' => false,
                    'message' => 'تم إيقاف وضع الصيانة وإتاحة المنصة للجمهور بنجاح 🟢',
                ]);
            }

            // Activate maintenance mode
            $message = $request->input('message') ?: 'المنصة تخضع حالياً لأعمال صيانة طارئة لحماية المزايدات. سنعود خلال دقائق معدودة.';
            
            Artisan::call('down', [
                '--secret' => 'motorzad-admin-access',
                '--refresh' => 15,
            ]);

            Setting::set('maintenance_mode', '1');
            Setting::set('maintenance_message', $message);

            return response()->json([
                'success' => true,
                'is_maintenance' => true,
                'message' => 'تم تفعيل وضع الصيانة الطارئ للمنصة بنجاح 🔒',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر تغيير وضع الصيانة: ' . $e->getMessage(),
            ], 500);
        }
    }
}
