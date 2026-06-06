<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            // العلاقات الأساسية
            $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete()
                  ->comment('المزاد المرتبط بالعرض');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()
                  ->comment('المزايد الذي قدّم العرض');

            // تفاصيل العرض
            $table->decimal('amount', 15, 2)->comment('قيمة العرض');
            $table->boolean('is_auto_bid')->default(false)->comment('هل هو عرض تلقائي (Auto Bid)');
            $table->decimal('max_auto_bid', 15, 2)->nullable()->comment('الحد الأقصى للعرض التلقائي');

            // حالة العرض
            $table->enum('status', ['active', 'outbid', 'won', 'cancelled'])
                  ->default('active')->comment('حالة العرض');

            // IP والجهاز للحماية من التلاعب
            $table->string('ip_address', 45)->nullable()->comment('عنوان IP');
            $table->string('user_agent')->nullable()->comment('معلومات الجهاز/المتصفح');

            $table->timestamps();

            $table->index('auction_id');
            $table->index('user_id');
            $table->index('status');
            $table->index(['auction_id', 'amount']);
            $table->index(['auction_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
