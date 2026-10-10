<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout

    protected string $option;
    protected string $timestamp;
    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $option, string $timestamp, int $userId)
    {
        $this->option = $option;
        $this->timestamp = $timestamp;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(BackupService $backupService): void
    {
        // Set higher limits just in case, though queue worker usually handles this
        @set_time_limit(600);
        @ini_set('memory_limit', '1024M');

        try {
            broadcast(new \App\Events\BackupProgressUpdate($this->userId, 10, 'جاري تحضير بيئة النسخ الاحتياطي في الخلفية...'));
            
            $success = false;
            $message = '';
            
            switch ($this->option) {
                case 'db':
                    $filename = "backup_db_{$this->timestamp}.zip";
                    $success = $backupService->createDatabaseBackupZip($filename, $this->userId);
                    $message = 'تم إنشاء نسخة احتياطية لقاعدة البيانات بنجاح 💾';
                    break;

                case 'files':
                    $filename = "backup_files_{$this->timestamp}.zip";
                    $success = $backupService->createFilesBackupZip($filename, $this->userId);
                    $message = 'تم إنشاء نسخة احتياطية للملفات المرفوعة بنجاح 📁';
                    break;

                case 'all':
                default:
                    $filename = "backup_full_{$this->timestamp}.zip";
                    $success = $backupService->createFullBackupZip($filename, $this->userId);
                    $message = 'تم إنشاء نسخة احتياطية كاملة (قاعدة البيانات + الملفات) بنجاح 📦';
                    break;
            }

            if (!$success) {
                broadcast(new \App\Events\BackupProgressUpdate($this->userId, 100, 'حدث خطأ أثناء النسخ الاحتياطي.', 'failed'));
                Log::error("Backup Job Failed for option: {$this->option}");
                return;
            }

            // Run smart retention to clean up space
            $cleanup = $backupService->cleanOldBackups();
            if ($cleanup['deleted'] > 0) {
                Log::info("Smart Retention Policy removed {$cleanup['deleted']} old files.");
            }

            broadcast(new \App\Events\BackupProgressUpdate($this->userId, 100, $message, 'completed'));

        } catch (\Throwable $e) {
            Log::error('Backup Job exception: ' . $e->getMessage(), ['exception' => $e]);
            broadcast(new \App\Events\BackupProgressUpdate($this->userId, 100, 'فشل إنشاء النسخة: ' . $e->getMessage(), 'failed'));
        }
    }
}
