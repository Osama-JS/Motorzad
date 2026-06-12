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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Winning bidder
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            
            $table->decimal('bid_amount', 15, 2);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2); // bid_amount + commission + vat - deposit
            
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable(); // wallet, bank_transfer
            $table->enum('delivery_type', ['pickup', 'delivery'])->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->text('notes')->nullable();
            
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
