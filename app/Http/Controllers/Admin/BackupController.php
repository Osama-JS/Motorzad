<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Directory path where backups are stored.
     */
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Display the backup dashboard page.
     */
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

                    // Determine type based on filename prefix
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

            // Sort backups by creation date descending
            usort($backups, fn($a, $b) => strcmp($b['created_at_exact'], $a['created_at_exact']));
        }

        // Get free disk space for storage drive
        $freeDiskSpace = @disk_free_space(storage_path());
        $freeDiskFormatted = $freeDiskSpace !== false ? $this->formatBytes($freeDiskSpace) : 'غير معروف';

        $stats = [
            'total_count' => count($backups),
            'total_size' => $this->formatBytes($totalSizeBytes),
            'free_disk_space' => $freeDiskFormatted,
            'last_backup' => $latestBackupTime ? Carbon::createFromTimestamp($latestBackupTime)->locale('ar')->diffForHumans() : 'لا يوجد',
        ];

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    /**
     * Create a new backup (Database, Files, or All).
     */
    public function store(Request $request)
    {
        $request->validate([
            'option' => 'required|in:db,files,all',
        ]);

        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $option = $request->input('option');
        $timestamp = date('Y-m-d_H-i-s');
        
        try {
            switch ($option) {
                case 'db':
                    $filename = "backup_db_{$timestamp}.zip";
                    $success = $this->createDatabaseBackupZip($filename);
                    $message = 'تم إنشاء نسخة احتياطية لقاعدة البيانات بنجاح 💾';
                    break;

                case 'files':
                    $filename = "backup_files_{$timestamp}.zip";
                    $success = $this->createFilesBackupZip($filename);
                    $message = 'تم إنشاء نسخة احتياطية للملفات المرفوعة بنجاح 📁';
                    break;

                case 'all':
                default:
                    $filename = "backup_full_{$timestamp}.zip";
                    $success = $this->createFullBackupZip($filename);
                    $message = 'تم إنشاء نسخة احتياطية كاملة (قاعدة البيانات + الملفات) بنجاح 📦';
                    break;
            }

            if (!$success) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'حدث خطأ أثناء إنشاء ملف النسخة الاحتياطية. يُرجى التحقق من الصلاحيات وذاكرة السيرفر.'
                    ], 500);
                }
                return back()->with('error', 'حدث خطأ أثناء إنشاء ملف النسخة الاحتياطية.');
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'filename' => $filename,
                ]);
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            Log::error('Backup creation failed: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إنشاء النسخة الاحتياطية: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'فشل إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * Download a specific backup file.
     */
    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        $cleanFilename = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            return back()->with('error', 'ملف النسخة الاحتياطية المطلوبة غير موجود.');
        }

        return response()->download($filePath, $cleanFilename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Delete a specific backup file.
     */
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

    /**
     * Create Zip backup containing only Database dump.
     */
    protected function createDatabaseBackupZip(string $zipFilename): bool
    {
        $tempSqlFile = $this->backupDir . DIRECTORY_SEPARATOR . 'temp_' . uniqid() . '.sql';
        
        if (!$this->dumpDatabase($tempSqlFile)) {
            return false;
        }

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

    /**
     * Create Zip backup containing uploaded files.
     */
    protected function createFilesBackupZip(string $zipFilename): bool
    {
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipFilename;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Storage public folder
            $storagePublic = storage_path('app/public');
            if (file_exists($storagePublic)) {
                $this->zipFolder($storagePublic, $zip, 'storage_public');
            }

            // Public uploads folder if exists
            $publicUploads = public_path('uploads');
            if (file_exists($publicUploads)) {
                $this->zipFolder($publicUploads, $zip, 'public_uploads');
            }

            $zip->close();
            return true;
        }

        return false;
    }

    /**
     * Create Full Zip backup containing Database dump + Files.
     */
    protected function createFullBackupZip(string $zipFilename): bool
    {
        $tempSqlFile = $this->backupDir . DIRECTORY_SEPARATOR . 'temp_' . uniqid() . '.sql';
        
        if (!$this->dumpDatabase($tempSqlFile)) {
            return false;
        }

        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipFilename;
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Add DB dump at root of zip
            $zip->addFile($tempSqlFile, 'database.sql');

            // Add public files
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

    /**
     * Export MySQL database schema and data into a .sql file.
     * Uses mysqldump binary if available, or falls back to pure PHP PDO dumper.
     */
    protected function dumpDatabase(string $sqlFilePath): bool
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');

        // 1. Try mysqldump command
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

        // 2. Pure PHP PDO Fallback
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

                // Dump table rows
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
                                if (is_null($val)) {
                                    return 'NULL';
                                }
                                if (is_bool($val)) {
                                    return $val ? '1' : '0';
                                }
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

    /**
     * Recursively zip a folder.
     */
    protected function zipFolder(string $sourceFolder, ZipArchive $zip, string $subFolderInZip = ''): void
    {
        if (!is_dir($sourceFolder)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceFolder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(realpath($sourceFolder)) + 1);
            
            // Skip the backups directory itself to avoid recursive loop!
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
     * Format raw byte sizes into human readable units.
     */
    protected function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), $precision) . ' ' . $units[$i];
    }
}
