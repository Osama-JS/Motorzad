<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الضمان المالي للمشاركة في المزاد
        Schema::create('auction_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete()
                  ->comment('حركة المحفظة التي تمثل خصم الضمان');

            $table->decimal('amount', 15, 2)->comment('مبلغ الضمان المدفوع');

            // held = محجوز، released = مُعاد للمستخدم، forfeited = مُصادر (خسر المزاد وتأخر في الدفع)
            $table->enum('status', ['held', 'released', 'forfeited'])
                  ->default('held')->comment('حالة مبلغ الضمان');

            $table->timestamp('released_at')->nullable()->comment('وقت إعادة الضمان');
            $table->timestamps();

            $table->unique(['auction_id', 'user_id']);
            $table->index('status');
        });

        // قائمة المتابعة للمزادات
        Schema::create('auction_watchlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['auction_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_watchlist');
        Schema::dropIfExists('auction_deposits');
    }
};
