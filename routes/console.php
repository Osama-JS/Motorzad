<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('auctions:check-ended')->everyMinute();
Schedule::command(\Spatie\Health\Commands\RunHealthChecksCommand::class)->everyMinute();

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

// Automated Scheduled Backups (النسخ الاحتياطي المجدول باحترافية)
try {
    if (Schema::hasTable('settings')) {
        // 1. Daily Database Backup
        if (Setting::get('backup_auto_db_enabled', '1') == '1') {
            Schedule::command('backup:auto db')->dailyAt(Setting::get('backup_auto_db_time', '02:00'))->withoutOverlapping();
        }

        // 2. Weekly Files Backup
        if (Setting::get('backup_auto_files_enabled', '1') == '1') {
            Schedule::command('backup:auto files')->weeklyOn((int)Setting::get('backup_auto_files_day', 0), Setting::get('backup_auto_files_time', '03:00'))->withoutOverlapping();
        }

        // 3. Full Monthly Backup
        if (Setting::get('backup_auto_all_enabled', '1') == '1') {
            Schedule::command('backup:auto all')->monthlyOn((int)Setting::get('backup_auto_all_day', 1), Setting::get('backup_auto_all_time', '04:00'))->withoutOverlapping();
        }
    }
} catch (\Exception $e) {
    // Ignore DB errors during early bootstrap/artisan commands
}
