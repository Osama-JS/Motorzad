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
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('country_code', 10)->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('password');
            $table->string('country')->nullable()->after('status');
            $table->string('city')->nullable()->after('country');
            $table->text('address')->nullable()->after('city');
            $table->string('gender')->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('profile_photo')->nullable()->after('date_of_birth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'country_code',
                'status',
                'country',
                'city',
                'address',
                'gender',
                'date_of_birth',
                'profile_photo'
            ]);
        });
    }
};
