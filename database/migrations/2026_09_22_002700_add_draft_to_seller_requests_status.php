<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE seller_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'draft') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe fallback in case down is called, but might fail if there are existing draft records
        DB::statement("ALTER TABLE seller_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
