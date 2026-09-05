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
        Schema::create('hyperpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->onDelete('set null');
            
            // Platform Reference & HyperPay IDs
            $table->string('merchant_transaction_id')->unique()->index();
            $table->string('checkout_id')->nullable()->index();
            $table->string('hyperpay_payment_id')->nullable()->index();
            
            // Brand & Entity
            $table->string('brand')->default('mada'); // mada, visa_master, apple_pay
            $table->string('entity_id')->nullable();
            
            // Financial details
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('SAR');
            
            // Status & Response
            $table->enum('status', ['initiated', 'pending', 'paid', 'failed', 'cancelled', 'refunded'])->default('initiated')->index();
            $table->string('result_code', 64)->nullable()->index();
            $table->text('result_description')->nullable();
            
            // Card details (non-sensitive / PCI compliant masked data)
            $table->string('card_bin', 8)->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('card_holder')->nullable();
            $table->string('card_expiry_month', 2)->nullable();
            $table->string('card_expiry_year', 4)->nullable();
            
            // Context & Metadata
            $table->enum('channel', ['web', 'api'])->default('web');
            $table->string('ip_address', 45)->nullable();
            $table->json('raw_response')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hyperpay_transactions');
    }
};
