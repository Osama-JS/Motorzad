<?php

namespace App\Health\Checks;

use Exception;
use Illuminate\Support\Facades\Storage;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class StorageDiskCheck extends Check
{
    protected float $minFreeGigabytes = 1.0;

    public function minFreeGigabytes(float $gb): static
    {
        $this->minFreeGigabytes = $gb;
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        // 1. Measure Free Disk Space
        $storagePath = storage_path('app/public');
        $freeBytes = @disk_free_space($storagePath);
        $totalBytes = @disk_total_space($storagePath);

        $freeGb = $freeBytes !== false ? round($freeBytes / (1024 * 1024 * 1024), 2) : null;
        $totalGb = $totalBytes !== false ? round($totalBytes / (1024 * 1024 * 1024), 2) : null;

        $result->meta([
            'free_gb' => $freeGb,
            'total_gb' => $totalGb,
            'storage_path' => $storagePath,
        ]);

        // 2. Test Write, Read, and Delete permissions on the public disk
        try {
            $testFileName = '_health_check_write_test_' . uniqid() . '.tmp';
            $testContent = 'Motorzad storage write test at ' . now()->toIso8601String();

            // Write
            $written = Storage::disk('public')->put($testFileName, $testContent);
            if (!$written) {
                return $result->failed('فشل في كتابة ملف اختباري داخل قرص التخزين العام (Permission Denied)');
            }

            // Read
            $readContent = Storage::disk('public')->get($testFileName);
            if ($readContent !== $testContent) {
                Storage::disk('public')->delete($testFileName);
                return $result->failed('فشل في قراءة الملف الاختباري بعد كتابته في قرص التخزين');
            }

            // Delete
            Storage::disk('public')->delete($testFileName);
        } catch (Exception $e) {
            return $result->failed('خطأ في أذونات وصلاحيات مجلد التخزين العام: ' . $e->getMessage());
        }

        // 3. Verify Symlink existence
        $symlinkPath = public_path('storage');
        $hasSymlink = file_exists($symlinkPath);
        if (!$hasSymlink) {
            return $result->warning('الرابط الرمزي للصور (storage:link) غير موجود في مجلد public');
        }

        // 4. Verify Free Disk Space
        if ($freeGb !== null && $freeGb < $this->minFreeGigabytes) {
            return $result->warning("المساحة التخزينية المتاحة منخفضة جداً ({$freeGb} GB متبقية فقط)");
        }

        $spaceText = $freeGb !== null ? " (المساحة المتاحة: {$freeGb} GB)" : '';
        return $result->ok("مجلد التخزين وصور المركبات سليم وجاهز للكتابة{$spaceText}");
    }
}
