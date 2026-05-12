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
        Schema::create('wallet_transactions', function (Blueprint $table) {
          $table->id();
            
            // ربط العملية بالمحفظة الأساسية
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            
            // بيانات العملية المالية الأساسية
            $table->enum('type', ['credit', 'debit'])->comment('نوع المعاملة: credit (إيداع) أو debit (سحب)');
            $table->decimal('amount', 15, 2)->comment('مبلغ العملية');
            $table->text('description')->nullable()->comment('الوصف أو الملاحظات');
            $table->string('attachment_path')->nullable()->comment('مسار الملف المرفق (صورة أو PDF)');
            
            
            // لتسجيل من قام بإجراء هذه العملية (الأدمن أو النظام)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم/الأدمن الذي أضاف الحركة');
            
            // تاريخ الاستحقاق (إن وجد)
            $table->date('due_date')->nullable()->comment('تاريخ الاستحقاق');
            
            // التواريخ القياسية (تتضمن تاريخ الإنشاء الظاهر بالجدول)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
