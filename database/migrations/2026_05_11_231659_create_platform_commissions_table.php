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
        // Drop and recreate with proper structure
        Schema::dropIfExists('platform_commissions');

        Schema::create('platform_commissions', function (Blueprint $table) {
            $table->id();

            // ربط العمولة بالمزاد والفائز (سيتم ربطهما لاحقاً عند إنشاء جدول auctions)
            $table->unsignedBigInteger('auction_id')->nullable()->comment('المزاد المرتبط بالعمولة');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم الذي دفع العمولة (الفائز)');

            // تفاصيل العمولة
            $table->decimal('amount', 15, 2)->comment('مبلغ العمولة');
            $table->decimal('rate', 5, 2)->nullable()->comment('نسبة العمولة % إن كانت نسبية');

            // الأنواع والحالات
            $table->enum('type', ['manual', 'dynamic', 'fixed'])
                  ->default('dynamic')
                  ->comment('نوع العمولة');

            $table->enum('payment_status', ['pending', 'paid', 'canceled'])
                  ->default('pending')
                  ->comment('حالة سداد العمولة للمنصة');

            $table->text('notes')->nullable()->comment('ملاحظات');

            // التواريخ
            $table->timestamp('completed_at')->nullable()->comment('تاريخ اكتمال أو تحصيل العمولة');
            $table->timestamps();

            $table->index('auction_id');
            $table->index('user_id');
            $table->index('payment_status');
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
