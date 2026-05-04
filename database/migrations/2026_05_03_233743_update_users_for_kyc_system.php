<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kyc_level')) {
                $table->integer('kyc_level')->default(0)->after('email_verified_at');
            }
            if (Schema::hasColumn('users', 'id_photo')) {
                $table->dropColumn('id_photo');
            }
        });

        // 1. Change to string first to allow any value
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN status VARCHAR(255)");

        // 2. Map existing status values to new ones
        \Illuminate\Support\Facades\DB::table('users')->where('status', 'active')->update(['status' => 'approved']);
        \Illuminate\Support\Facades\DB::table('users')->where('status', 'inactive')->update(['status' => 'pending']);
        
        // 3. Set default value for any other cases
        \Illuminate\Support\Facades\DB::table('users')->whereNotIn('status', ['pending', 'approved', 'rejected'])->update(['status' => 'pending']);

        // 4. Back to enum with new values
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
