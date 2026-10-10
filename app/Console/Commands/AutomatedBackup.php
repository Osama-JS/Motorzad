<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;
use Illuminate\Support\Facades\Log;

class AutomatedBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:auto {type=all : The type of backup to run (db, files, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run an automated scheduled backup (db, files, or all) professionaly';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        $type = $this->argument('type');
        $validTypes = ['db', 'files', 'all'];

        if (!in_array($type, $validTypes)) {
            $this->error("Invalid backup type '{$type}'. Allowed: db, files, all");
            return 1;
        }

        @set_time_limit(600);
        @ini_set('memory_limit', '1024M');

        $timestamp = date('Y-m-d_H-i-s');
        $this->info("Starting automated backup: {$type} at {$timestamp}");
        Log::info("AutomatedBackup: Starting backup type [{$type}].");

        try {
            switch ($type) {
                case 'db':
                    $filename = "backup_db_auto_{$timestamp}.zip";
                    $success = $backupService->createDatabaseBackupZip($filename, 0); // 0 means no user broadcasting
                    break;
                case 'files':
                    $filename = "backup_files_auto_{$timestamp}.zip";
                    $success = $backupService->createFilesBackupZip($filename, 0);
                    break;
                case 'all':
                default:
                    $filename = "backup_full_auto_{$timestamp}.zip";
                    $success = $backupService->createFullBackupZip($filename, 0);
                    break;
            }

            if ($success) {
                $this->info("Automated backup [{$filename}] created successfully.");
                Log::info("AutomatedBackup: Successfully created {$filename}.");
                
                // Smart Retention: Clean old backups to prevent disk full
                $cleanupResult = $backupService->cleanOldBackups();
                if ($cleanupResult['deleted'] > 0) {
                    $this->info("Smart Retention: Deleted {$cleanupResult['deleted']} old backup(s).");
                    Log::info("AutomatedBackup: Smart Retention removed {$cleanupResult['deleted']} old files.");
                }
                
                return 0;
            } else {
                $this->error("Failed to create automated backup.");
                Log::error("AutomatedBackup: Failed to create backup.");
                return 1;
            }

        } catch (\Throwable $e) {
            $this->error("Exception during automated backup: " . $e->getMessage());
            Log::error('AutomatedBackup: Error: ' . $e->getMessage(), ['exception' => $e]);
            return 1;
        }
    }
}
