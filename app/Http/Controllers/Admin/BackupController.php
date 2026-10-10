<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    protected string $backupDir;
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupDir = storage_path('app/backups');
        $this->backupService = $backupService;
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function index(): View
    {
        $backups = [];
        $totalSizeBytes = 0;
        $latestBackupTime = null;

        if (file_exists($this->backupDir)) {
            $files = array_diff(scandir($this->backupDir), ['.', '..']);
            
            foreach ($files as $file) {
                $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $file;
                
                if (is_file($filePath) && in_array(pathinfo($filePath, PATHINFO_EXTENSION), ['zip', 'sql'])) {
                    $size = filesize($filePath);
                    $mtime = filemtime($filePath);
                    $totalSizeBytes += $size;

                    if ($latestBackupTime === null || $mtime > $latestBackupTime) {
                        $latestBackupTime = $mtime;
                    }

                    $type = 'all';
                    if (str_contains($file, 'backup_db_')) {
                        $type = 'db';
                    } elseif (str_contains($file, 'backup_files_')) {
                        $type = 'files';
                    } elseif (str_contains($file, 'backup_full_')) {
                        $type = 'all';
                    }

                    $backups[] = [
                        'name' => $file,
                        'size' => $this->formatBytes($size),
                        'size_bytes' => $size,
                        'type' => $type,
                        'created_at' => Carbon::createFromTimestamp($mtime)->locale('ar')->diffForHumans(),
                        'created_at_exact' => date('Y-m-d H:i:s', $mtime),
                    ];
                }
            }

            usort($backups, fn($a, $b) => strcmp($b['created_at_exact'], $a['created_at_exact']));
        }

        $freeDiskSpace = @disk_free_space(storage_path());
        $freeDiskFormatted = $freeDiskSpace !== false ? $this->formatBytes($freeDiskSpace) : 'غير معروف';

        $stats = [
            'total_count' => count($backups),
            'total_size' => $this->formatBytes($totalSizeBytes),
            'free_disk_space' => $freeDiskFormatted,
            'last_backup' => $latestBackupTime ? Carbon::createFromTimestamp($latestBackupTime)->locale('ar')->diffForHumans() : 'لا يوجد',
        ];

        $settings = [
            'db_enabled' => \App\Models\Setting::get('backup_auto_db_enabled', '1'),
            'db_time' => \App\Models\Setting::get('backup_auto_db_time', '02:00'),
            'files_enabled' => \App\Models\Setting::get('backup_auto_files_enabled', '1'),
            'files_time' => \App\Models\Setting::get('backup_auto_files_time', '03:00'),
            'files_day' => \App\Models\Setting::get('backup_auto_files_day', 0),
            'all_enabled' => \App\Models\Setting::get('backup_auto_all_enabled', '1'),
            'all_time' => \App\Models\Setting::get('backup_auto_all_time', '04:00'),
            'all_day' => \App\Models\Setting::get('backup_auto_all_day', 1),
            'retention_db' => \App\Models\Setting::get('backup_retention_db', '15'),
            'retention_files' => \App\Models\Setting::get('backup_retention_files', '7'),
            'retention_all' => \App\Models\Setting::get('backup_retention_all', '7'),
        ];

        return view('admin.backups.index', compact('backups', 'stats', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'option' => 'required|in:db,files,all',
        ]);

        $option = $request->input('option');
        $timestamp = date('Y-m-d_H-i-s');
        $userId = auth()->id();
        
        try {
            // Dispatch the job to the queue
            \App\Jobs\CreateBackupJob::dispatch($option, $timestamp, $userId);

            $message = 'تم وضع طلب النسخ الاحتياطي في طابور المعالجة. يرجى الانتظار، سيتم تحديث شريط التقدم فوراً 🚀';
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            Log::error('Backup job dispatch failed: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل بدء النسخة الاحتياطية: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'فشل بدء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function download(string $filename, Request $request)
    {
        $cleanFilename = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'ملف النسخة الاحتياطية المطلوبة غير موجود.'
                ], 404);
            }
            return back()->with('error', 'ملف النسخة الاحتياطية المطلوبة غير موجود.');
        }

        return response()->download($filePath, $cleanFilename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function destroy(string $filename, Request $request)
    {
        $cleanFilename = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;

        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف النسخة الاحتياطية بنجاح 🗑️',
                ]);
            }

            return back()->with('success', 'تم حذف النسخة الاحتياطية بنجاح.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'ملف النسخة الاحتياطية غير موجود.',
            ], 404);
        }

        return back()->with('error', 'ملف النسخة الاحتياطية غير موجود.');
    }

    public function bulkDestroy(Request $request)
    {
        if (!$request->wantsJson()) abort(404);

        $files = $request->input('files', []);
        if (empty($files) || !is_array($files)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي ملفات.']);
        }

        $deleted = 0;
        foreach ($files as $filename) {
            $cleanFilename = basename($filename);
            $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;
            if (file_exists($filePath) && is_file($filePath)) {
                if (@unlink($filePath)) {
                    $deleted++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deleted} نسخة احتياطية بنجاح 🗑️"
        ]);
    }

    /**
     * Update automated backup settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'backup_auto_db_time' => 'nullable|date_format:H:i',
            'backup_auto_files_time' => 'nullable|date_format:H:i',
            'backup_auto_files_day' => 'nullable|integer|between:0,6',
            'backup_auto_all_time' => 'nullable|date_format:H:i',
            'backup_auto_all_day' => 'nullable|integer|between:1,28',
            'backup_retention_db' => 'nullable|integer|min:1|max:365',
            'backup_retention_files' => 'nullable|integer|min:1|max:365',
            'backup_retention_all' => 'nullable|integer|min:1|max:365',
        ]);

        \App\Models\Setting::set('backup_auto_db_enabled', $request->has('backup_auto_db_enabled') ? '1' : '0');
        \App\Models\Setting::set('backup_auto_db_time', $request->input('backup_auto_db_time', '02:00'));
        \App\Models\Setting::set('backup_retention_db', $request->input('backup_retention_db', '15'));

        \App\Models\Setting::set('backup_auto_files_enabled', $request->has('backup_auto_files_enabled') ? '1' : '0');
        \App\Models\Setting::set('backup_auto_files_time', $request->input('backup_auto_files_time', '03:00'));
        \App\Models\Setting::set('backup_auto_files_day', $request->input('backup_auto_files_day', 0));
        \App\Models\Setting::set('backup_retention_files', $request->input('backup_retention_files', '7'));

        \App\Models\Setting::set('backup_auto_all_enabled', $request->has('backup_auto_all_enabled') ? '1' : '0');
        \App\Models\Setting::set('backup_auto_all_time', $request->input('backup_auto_all_time', '04:00'));
        \App\Models\Setting::set('backup_auto_all_day', $request->input('backup_auto_all_day', 1));
        \App\Models\Setting::set('backup_retention_all', $request->input('backup_retention_all', '7'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ إعدادات الأتمتة والنسخ الاحتياطي بنجاح ⚙️'
            ]);
        }

        return back()->with('success', 'تم حفظ إعدادات الأتمتة والنسخ الاحتياطي بنجاح ⚙️');
    }

    protected function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), $precision) . ' ' . $units[$i];
    }
}
