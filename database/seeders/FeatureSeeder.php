<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'title_en' => 'Ultimate Security',
                'title_ar' => 'أمان فائق',
                'description_en' => 'All transactions are protected and encrypted with the highest security standards',
                'description_ar' => 'جميع المعاملات محمية ومشفرة بأعلى معايير الأمان',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                'color_class' => 'red',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title_en' => 'Instant Auctions',
                'title_ar' => 'مزادات فورية',
                'description_en' => 'Follow auctions moment by moment with live price and offer updates',
                'description_ar' => 'تابع المزادات لحظة بلحظة مع تحديثات حية للأسعار والعروض',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                'color_class' => 'gold',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title_en' => 'Smart Interface',
                'title_ar' => 'واجهة ذكية',
                'description_en' => 'Modern and user-friendly design that works seamlessly on all devices',
                'description_ar' => 'تصميم عصري وسهل الاستخدام يعمل بسلاسة على جميع الأجهزة',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
                'color_class' => 'blue',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title_en' => 'Full Verification',
                'title_ar' => 'توثيق كامل',
                'description_en' => 'Integrated KYC system to verify user identity and ensure transaction credibility',
                'description_ar' => 'نظام KYC متكامل للتحقق من هوية المستخدمين وضمان مصداقية التعاملات',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                'color_class' => 'green',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title_en' => 'Secure Payment',
                'title_ar' => 'دفع آمن',
                'description_en' => 'Direct link with your bank accounts to facilitate payment and receiving',
                'description_ar' => 'ربط مباشر مع حساباتك البنكية لتسهيل عمليات الدفع والاستلام',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'color_class' => 'red',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title_en' => '24/7 Support',
                'title_ar' => 'دعم 24/7',
                'description_en' => 'Specialized support team ready to help you around the clock',
                'description_ar' => 'فريق دعم متخصص مستعد لمساعدتك على مدار الساعة',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                'color_class' => 'gold',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(['title_en' => $feature['title_en']], $feature);
        }
    }
}
