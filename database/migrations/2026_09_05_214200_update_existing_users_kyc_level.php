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
        // Downgrade any users with kyc_level = 3 to 2 (Since 3 is now Seller, and 2 is KYC Verified)
        DB::table('users')->where('kyc_level', 3)->update(['kyc_level' => 2]);
        
        // Ensure any users who have verified email but kyc_level = 0 are bumped to 1
        DB::table('users')->whereNotNull('email_verified_at')->where('kyc_level', 0)->update(['kyc_level' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 2 back to 3
        DB::table('users')->where('kyc_level', 2)->update(['kyc_level' => 3]);
    }
};
