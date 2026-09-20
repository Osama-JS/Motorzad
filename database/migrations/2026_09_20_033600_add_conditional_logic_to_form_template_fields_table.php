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
        Schema::table('form_template_fields', function (Blueprint $table) {
            $table->string('depends_on_field_name')->nullable()->after('validation_rules');
            $table->string('depends_on_value')->nullable()->after('depends_on_field_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_template_fields', function (Blueprint $table) {
            $table->dropColumn(['depends_on_field_name', 'depends_on_value']);
        });
    }
};
