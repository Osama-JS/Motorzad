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
        Schema::table('auctions', function (Blueprint $table) {
            $table->string('location_ar')->nullable()->after('description_en');
            $table->string('location_en')->nullable()->after('location_ar');
        });

        // Copy existing data
        \Illuminate\Support\Facades\DB::table('auctions')->chunkById(100, function ($auctions) {
            foreach ($auctions as $auction) {
                \Illuminate\Support\Facades\DB::table('auctions')->where('id', $auction->id)->update([
                    'location_ar' => $auction->location,
                    'location_en' => $auction->location,
                ]);
            }
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description_en');
        });

        // Copy data back
        \Illuminate\Support\Facades\DB::table('auctions')->chunkById(100, function ($auctions) {
            foreach ($auctions as $auction) {
                \Illuminate\Support\Facades\DB::table('auctions')->where('id', $auction->id)->update([
                    'location' => $auction->location_ar ?? $auction->location_en,
                ]);
            }
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn(['location_ar', 'location_en']);
        });
    }
};
