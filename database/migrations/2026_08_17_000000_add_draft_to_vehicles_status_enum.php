<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `vehicles` CHANGE `status` `status` ENUM('draft', 'pending', 'approved', 'rejected', 'sold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'حالة الإعلان'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `vehicles` CHANGE `status` `status` ENUM('pending', 'approved', 'rejected', 'sold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'حالة الإعلان'");
        }
    }
};
