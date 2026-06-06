<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();

            // المركبة والمشرف المنشئ
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete()
                  ->comment('المركبة المعروضة في المزاد');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()
                  ->comment('المشرف الذي أنشأ المزاد');

            // عنوان وموقع المزاد
            $table->string('title_ar')->comment('عنوان المزاد بالعربي');
            $table->string('title_en')->comment('عنوان المزاد بالإنجليزي');
            $table->text('description_ar')->nullable()->comment('وصف بالعربي');
            $table->text('description_en')->nullable()->comment('وصف بالإنجليزي');
            $table->string('location')->nullable()->comment('موقع المزاد');

            // أسعار وضوابط المزاد
            $table->decimal('start_price', 15, 2)->comment('سعر البداية');
            $table->decimal('reserve_price', 15, 2)->nullable()->comment('السعر الاحتياطي (أدنى سعر مقبول للبيع)');
            $table->decimal('min_bid_increment', 15, 2)->default(500)->comment('أقل زيادة مسموح بها في العرض');
            $table->decimal('buy_now_price', 15, 2)->nullable()->comment('سعر الشراء الفوري (اختياري)');

            // الضمان المالي للمشاركة
            $table->decimal('deposit_amount', 15, 2)->default(0)->comment('مبلغ الضمان المطلوب للمشاركة');
            $table->boolean('deposit_required')->default(false)->comment('هل الضمان إلزامي');

            // التوقيت
            $table->dateTime('start_time')->comment('وقت بدء المزاد');
            $table->dateTime('end_time')->comment('وقت انتهاء المزاد');
            $table->unsignedSmallInteger('auto_extend_minutes')->default(5)
                  ->comment('تمديد تلقائي بالدقائق عند تقديم عرض في آخر لحظة');

            // حالة المزاد
            $table->enum('status', ['draft', 'scheduled', 'live', 'ended', 'cancelled', 'sold'])
                  ->default('draft')->comment('حالة المزاد');

            // نتيجة المزاد
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('الفائز بالمزاد');
            $table->decimal('winning_bid_amount', 15, 2)->nullable()->comment('قيمة عرض الفائز');
            $table->timestamp('sold_at')->nullable()->comment('وقت إتمام البيع');

            // عمولة المنصة
            $table->decimal('commission_rate', 5, 2)->default(5.00)->comment('نسبة عمولة المنصة %');
            $table->decimal('commission_amount', 15, 2)->nullable()->comment('مبلغ العمولة المحسوب');

            // إعدادات الظهور
            $table->boolean('is_featured')->default(false)->comment('مزاد مميز');
            $table->unsignedInteger('views_count')->default(0)->comment('عدد المشاهدات');
            $table->unsignedInteger('bids_count')->default(0)->comment('عدد العروض المقدمة');

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('start_time');
            $table->index('end_time');
            $table->index(['status', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
