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
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('news_categories')->nullOnDelete()->after('id');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->text('tags_ar')->nullable()->after('content_ar');
            $table->text('tags_en')->nullable()->after('content_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'is_featured', 'tags_ar', 'tags_en']);
        });
    }
};
