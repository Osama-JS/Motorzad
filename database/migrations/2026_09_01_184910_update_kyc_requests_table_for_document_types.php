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
        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->enum('document_type', ['national_id', 'passport'])->default('national_id')->after('id_number');
            $table->renameColumn('id_image', 'id_front_image');
        });

        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->string('id_front_image')->nullable()->change();
            $table->string('id_back_image')->nullable()->after('id_front_image');
            $table->string('passport_image')->nullable()->after('id_back_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->dropColumn('document_type');
            $table->dropColumn('id_back_image');
            $table->dropColumn('passport_image');
        });

        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->renameColumn('id_front_image', 'id_image');
        });

        Schema::table('kyc_requests', function (Blueprint $table) {
            $table->string('id_image')->nullable(false)->change();
        });
    }
};
