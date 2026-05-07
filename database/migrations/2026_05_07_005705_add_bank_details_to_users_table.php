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
        Schema::table('users', function (Blueprint $table) {
            $table->string('iban')->nullable();
            $table->string('bic_code')->nullable();
            $table->string('beneficiary_name')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('bank_city')->nullable();
            $table->string('bank_country')->nullable();
            $table->boolean('check_bank')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'iban', 'bic_code', 'beneficiary_name', 'address_1', 'address_2',
                'bank_city', 'bank_country', 'check_bank'
            ]);
        });
    }
};
