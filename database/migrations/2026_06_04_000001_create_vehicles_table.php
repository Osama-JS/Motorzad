<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // مالك المركبة (البائع / من أحضرها للمزاد)
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('المستخدم أو المشرف الذي أضاف المركبة');

            // بيانات المركبة الأساسية
            $table->string('make')->comment('الشركة المصنعة - مثل: Toyota, BMW');
            $table->string('model')->comment('الموديل - مثل: Camry, X5');
            $table->year('year')->comment('سنة الصنع');
            $table->string('color')->nullable()->comment('اللون');
            $table->string('vin_number', 17)->nullable()->unique()->comment('رقم الهيكل VIN');
            $table->unsignedBigInteger('mileage')->nullable()->comment('عداد الكيلومترات');
            $table->string('plate_number')->nullable()->comment('رقم اللوحة');
            $table->string('country_of_origin')->nullable()->comment('بلد المنشأ');

            // نوع الوقود والناقل
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid', 'other'])
                  ->default('petrol')->comment('نوع الوقود');
            $table->enum('transmission', ['automatic', 'manual', 'cvt'])
                  ->default('automatic')->comment('ناقل الحركة');
            $table->string('engine_capacity')->nullable()->comment('سعة المحرك - مثل: 2.0L, 3.5L');
            $table->unsignedSmallInteger('cylinders')->nullable()->comment('عدد الأسطوانات');

            // حالة المركبة
            $table->enum('condition', ['new', 'excellent', 'good', 'fair', 'damaged'])
                  ->default('good')->comment('حالة المركبة');

            // الأوصاف
            $table->text('description_ar')->nullable()->comment('وصف المركبة بالعربي');
            $table->text('description_en')->nullable()->comment('وصف المركبة بالإنجليزي');
            $table->text('features')->nullable()->comment('مميزات إضافية (JSON array)');
            $table->text('issues')->nullable()->comment('عيوب أو ملاحظات');

            // حالة الموافقة الإدارية
            $table->enum('status', ['pending', 'approved', 'rejected', 'sold'])
                  ->default('pending')->comment('حالة المركبة في النظام');
            $table->text('rejection_reason')->nullable()->comment('سبب الرفض إن وجد');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('المشرف الذي راجع الطلب');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('make');
            $table->index('model');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
