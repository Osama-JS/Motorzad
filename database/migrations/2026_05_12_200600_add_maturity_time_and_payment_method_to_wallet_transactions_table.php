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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'maturity_time')) {
                $table->dateTime('maturity_time')->nullable()->after('created_by')->comment('تاريخ ووقت الاستحقاق');
            }
            if (!Schema::hasColumn('wallet_transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('maturity_time')->comment('وسيلة الدفع');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['maturity_time', 'payment_method']);
        });
    }
};
