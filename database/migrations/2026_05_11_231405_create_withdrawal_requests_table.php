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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            // الروابط (العلاقات)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('السائق طالب السحب');
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete()->comment('رقم المحفظة');
            
            // المبالغ المالية
            $table->decimal('requested_amount', 15, 2)->comment('المبلغ المطلوب سحبه');
            $table->decimal('approved_amount', 15, 2)->nullable()->comment('المبلغ المعتمد من الإدارة');
            
            // حالة الطلب وطريقة الدفع
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])
                  ->default('pending')
                  ->comment('حالة طلب السحب');
            $table->string('payment_method')->nullable()->comment('طريقة الدفع/التحويل');
            
            // تفاصيل المعالجة الإدارية
            $table->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete()->comment('الأدمن الذي عالج الطلب');
            $table->timestamp('processed_at')->nullable()->comment('تاريخ ووقت المعالجة');
            $table->text('admin_notes')->nullable()->comment('ملاحظات الإدارة');
            
            // التواريخ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
