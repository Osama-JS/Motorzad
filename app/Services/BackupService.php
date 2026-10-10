<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function createDatabaseBackupZip(string $zipFilename, int $userId = 0): bool
    {
        if ($userId) broadcast(new \App\Events\BackupProgressUpdate($userId, 30, 'جاري تصدير جداول قاعدة البيانات...'));
        $tempSqlFile = $this->backupDir . DIRECTORY_SEPARATOR . 'temp_' . uniqid() . '.sql';
        
        if (!$this->dumpDatabase($tempSqlFile, $userId)) {
            return false;
        }

        if ($userId) broadcast(new \App\Events\BackupProgressUpdate($userId, 70, 'جاري ضغط ملف قاعدة البيانات...'));
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipFilename;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($tempSqlFile, 'database.sql');
            $zip->close();
            @unlink($tempSqlFile);
            return true;
        }

        @unlink($tempSqlFile);
        return false;
    }

    public function createFilesBackupZip(string $zipFilename, int $userId = 0): bool
    {
        if ($userId) broadcast(new \App\Events\BackupProgressUpdate($userId, 30, 'جاري تجميع وضغط الملفات... (قد يستغرق وقتاً)'));
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipFilename;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $storagePublic = storage_path('app/public');
            if (file_exists($storagePublic)) {
                $this->zipFolder($storagePublic, $zip, 'storage_public');
            }

            $publicUploads = public_path('uploads');
            if (file_exists($publicUploads)) {
                $this->zipFolder($publicUploads, $zip, 'public_uploads');
            }

            $zip->close();
            return true;
        }

        return false;
    }

    public function createFullBackupZip(string $zipFilename, int $userId = 0): bool
    {
        if ($userId) broadcast(new \App\Events\BackupProgressUpdate($userId, 20, 'جاري تصدير جداول قاعدة البيانات...'));
        $tempSqlFile = $this->backupDir . DIRECTORY_SEPARATOR . 'temp_' . uniqid() . '.sql';
        
        if (!$this->dumpDatabase($tempSqlFile, $userId)) {
            return false;
        }

        if ($userId) broadcast(new \App\Events\BackupProgressUpdate($userId, 50, 'جاري تجميع وضغط الملفات المرفوعة... (قد يستغرق وقتاً)'));
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipFilename;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($tempSqlFile, 'database.sql');

            $storagePublic = storage_path('app/public');
            if (file_exists($storagePublic)) {
                $this->zipFolder($storagePublic, $zip, 'files/storage_public');
            }

            $publicUploads = public_path('uploads');
            if (file_exists($publicUploads)) {
                $this->zipFolder($publicUploads, $zip, 'files/public_uploads');
            }

            $zip->close();
            @unlink($tempSqlFile);
            return true;
        }

        @unlink($tempSqlFile);
        return false;
    }

    protected function dumpDatabase(string $sqlFilePath, int $userId = 0): bool
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');

        if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
            $passParam = $password ? "--password=" . escapeshellarg($password) : "";
            $cmd = sprintf(
                'mysqldump --host=%s --port=%s --user=%s %s %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passParam,
                escapeshellarg($database),
                escapeshellarg($sqlFilePath)
            );

            $output = [];
            $returnVar = -1;
            @exec($cmd, $output, $returnVar);

            if ($returnVar === 0 && file_exists($sqlFilePath) && filesize($sqlFilePath) > 0) {
                return true;
            }
        }

        try {
            $pdo = DB::connection()->getPdo();
            $handle = fopen($sqlFilePath, 'w');

            if (!$handle) {
                return false;
            }

            fwrite($handle, "-- Motorzad Database Dump\n");
            fwrite($handle, "-- Backup Time: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Database: {$database}\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_" . $database;

            foreach ($tables as $tableObj) {
                $tableName = $tableObj->$tableKey ?? current((array)$tableObj);

                fwrite($handle, "\n-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for `{$tableName}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

                $createTableStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createTableStmt)) {
                    $createSql = ((array)$createTableStmt[0])['Create Table'] ?? null;
                    if ($createSql) {
                        fwrite($handle, $createSql . ";\n\n");
                    }
                }

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    fwrite($handle, "-- Data dumping for `{$tableName}`\n");
                    $allRows = $rows->toArray();
                    $chunks = array_chunk($allRows, 200);

                    foreach ($chunks as $chunk) {
                        $valuesList = [];
                        foreach ($chunk as $row) {
                            $rowArray = (array)$row;
                            $escapedValues = array_map(function ($val) use ($pdo) {
                                if (is_null($val)) return 'NULL';
                                if (is_bool($val)) return $val ? '1' : '0';
                                return $pdo->quote($val);
                            }, array_values($rowArray));

                            $valuesList[] = "(" . implode(", ", $escapedValues) . ")";
                        }

                        if (!empty($valuesList)) {
                            $columns = array_keys((array)$chunk[0]);
                            $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                            $insertSql = "INSERT INTO `{$tableName}` (" . implode(", ", $escapedColumns) . ") VALUES\n" . implode(",\n", $valuesList) . ";\n";
                            fwrite($handle, $insertSql);
                        }
                    }
                    fwrite($handle, "\n");
                }
            }

            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            return true;
        } catch (\Throwable $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            Log::error('PDO Backup Dump error: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    protected function zipFolder(string $sourceFolder, ZipArchive $zip, string $subFolderInZip = ''): void
    {
        if (!is_dir($sourceFolder)) return;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceFolder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(realpath($sourceFolder)) + 1);
            
            if (str_contains($filePath, 'app' . DIRECTORY_SEPARATOR . 'backups')) {
                continue;
            }

            $zipPath = $subFolderInZip ? $subFolderInZip . '/' . $relativePath : $relativePath;
            $zipPath = str_replace('\\', '/', $zipPath);

            if ($file->isDir()) {
                $zip->addEmptyDir($zipPath);
            } else {
                $zip->addFile($filePath, $zipPath);
            }
        }
    }

    /**
     * Smart Retention Policy
     * Deletes old backups exceeding the specified limits.
     */
    public function cleanOldBackups(?int $maxDb = null, ?int $maxFiles = null, ?int $maxFull = null): array
    {
        $maxDb = $maxDb ?? (int) \App\Models\Setting::get('backup_retention_db', 15);
        $maxFiles = $maxFiles ?? (int) \App\Models\Setting::get('backup_retention_files', 7);
        $maxFull = $maxFull ?? (int) \App\Models\Setting::get('backup_retention_all', 7);

        if (!is_dir($this->backupDir)) {
            return ['deleted' => 0];
        }

        $files = scandir($this->backupDir);
        $backups = ['db' => [], 'files' => [], 'full' => []];

        // Group by type and get file mtime
        foreach ($files as $file) {
            $path = $this->backupDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path) && pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $mtime = filemtime($path);
                if (str_starts_with($file, 'backup_db_')) {
                    $backups['db'][$file] = $mtime;
                } elseif (str_starts_with($file, 'backup_files_')) {
                    $backups['files'][$file] = $mtime;
                } elseif (str_starts_with($file, 'backup_full_')) {
                    $backups['full'][$file] = $mtime;
                }
            }
        }

        $deletedCount = 0;
        
        $processDeletion = function($list, $maxLimit) use (&$deletedCount) {
            // Sort ascending by modification time (oldest first)
            asort($list);
            
            if (count($list) > $maxLimit) {
                // Get the oldest files to delete
                $filesToDelete = array_slice(array_keys($list), 0, count($list) - $maxLimit);
                foreach ($filesToDelete as $fileToDelete) {
                    $path = $this->backupDir . DIRECTORY_SEPARATOR . $fileToDelete;
                    if (@unlink($path)) {
                        $deletedCount++;
                        Log::info("Smart Retention Policy: Deleted old backup -> " . $fileToDelete);
                    }
                }
            }
        };

        $processDeletion($backups['db'], $maxDb);
        $processDeletion($backups['files'], $maxFiles);
        $processDeletion($backups['full'], $maxFull);

        return ['deleted' => $deletedCount];
    }
}
