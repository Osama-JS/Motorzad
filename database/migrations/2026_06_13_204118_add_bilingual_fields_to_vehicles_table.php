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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('make_ar')->nullable()->after('submitted_by');
            $table->string('make_en')->nullable()->after('make_ar');
            $table->string('model_ar')->nullable()->after('make_en');
            $table->string('model_en')->nullable()->after('model_ar');
            $table->string('color_ar')->nullable()->after('year');
            $table->string('color_en')->nullable()->after('color_ar');
            $table->text('issues_ar')->nullable()->after('features');
            $table->text('issues_en')->nullable()->after('issues_ar');
        });

        // Copy existing data
        \Illuminate\Support\Facades\DB::table('vehicles')->chunkById(100, function ($vehicles) {
            foreach ($vehicles as $vehicle) {
                \Illuminate\Support\Facades\DB::table('vehicles')->where('id', $vehicle->id)->update([
                    'make_ar' => $vehicle->make,
                    'make_en' => $vehicle->make,
                    'model_ar' => $vehicle->model,
                    'model_en' => $vehicle->model,
                    'color_ar' => $vehicle->color,
                    'color_en' => $vehicle->color,
                    'issues_ar' => $vehicle->issues,
                    'issues_en' => $vehicle->issues,
                ]);
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['make', 'model', 'color', 'issues']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('make')->nullable()->after('submitted_by');
            $table->string('model')->nullable()->after('make');
            $table->string('color')->nullable()->after('year');
            $table->text('issues')->nullable()->after('features');
        });

        // Copy data back
        \Illuminate\Support\Facades\DB::table('vehicles')->chunkById(100, function ($vehicles) {
            foreach ($vehicles as $vehicle) {
                \Illuminate\Support\Facades\DB::table('vehicles')->where('id', $vehicle->id)->update([
                    'make' => $vehicle->make_ar ?? $vehicle->make_en,
                    'model' => $vehicle->model_ar ?? $vehicle->model_en,
                    'color' => $vehicle->color_ar ?? $vehicle->color_en,
                    'issues' => $vehicle->issues_ar ?? $vehicle->issues_en,
                ]);
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'make_ar', 'make_en',
                'model_ar', 'model_en',
                'color_ar', 'color_en',
                'issues_ar', 'issues_en'
            ]);
        });
    }
};
