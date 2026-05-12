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
        Schema::create('platform_commissions', function (Blueprint $table) {
            // تفاصيل العمولة
            $table->decimal('amount', 15, 2)->comment('مبلغ العمولة');
            
            // الأنواع والحالات بناءً على الفلاتر الموجودة في الواجهة
            $table->enum('type', ['manual', 'dynamic', 'fixed'])
                  ->default('dynamic')
                  ->comment('نوع العمولة');
                  
            $table->enum('payment_status', ['pending', 'paid', 'canceled'])
                  ->default('pending')
                  ->comment('حالة سداد العمولة للمنصة');
            
            // التواريخ
            $table->timestamp('completed_at')->nullable()->comment('تاريخ اكتمال أو تحصيل العمولة');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_commissions');
    }
};
