<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name_ar' => 'أحمد الشمري',
                'name_en' => 'Ahmed Al-Shamri',
                'role_ar' => 'مزايد متميز',
                'role_en' => 'Distinguished Bidder',
                'text_ar' => 'تجربة رائعة! حصلت على سيارتي بسعر ممتاز والعملية كانت سلسة جداً',
                'text_en' => 'Amazing experience! I got my car at a great price and the process was very smooth',
                'avatar_init_ar' => 'أ',
                'avatar_init_en' => 'A',
                'is_active' => true,
            ],
            [
                'name_ar' => 'سارة القحطاني',
                'name_en' => 'Sara Al-Qahtani',
                'role_ar' => 'عميلة جديدة',
                'role_en' => 'New Customer',
                'text_ar' => 'المنصة سهلة الاستخدام وفريق الدعم متعاون جداً. أنصح بها بشدة',
                'text_en' => 'The platform is easy to use and the support team is very helpful. I highly recommend it',
                'avatar_init_ar' => 'س',
                'avatar_init_en' => 'S',
                'is_active' => true,
            ],
            [
                'name_ar' => 'محمد العتيبي',
                'name_en' => 'Mohammed Al-Otaibi',
                'role_ar' => 'تاجر سيارات',
                'role_en' => 'Car Dealer',
                'text_ar' => 'موتورزاد غيرت طريقة بيع السيارات. نتائج ممتازة وشفافية عالية',
                'text_en' => 'Motorzad changed the way of selling cars. Excellent results and high transparency',
                'avatar_init_ar' => 'م',
                'avatar_init_en' => 'M',
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create([
                'name_ar' => $testimonial['name_ar'],
                'name_en' => $testimonial['name_en'],
                'role_ar' => $testimonial['role_ar'],
                'role_en' => $testimonial['role_en'],
                'text_ar' => $testimonial['text_ar'],
                'text_en' => $testimonial['text_en'],
                'avatar_init' => $testimonial['avatar_init_ar'], // We used avatar_init for Arabic in DB
                'avatar_init_en' => $testimonial['avatar_init_en'],
                'is_active' => $testimonial['is_active'],
            ]);
        }
    }
}
