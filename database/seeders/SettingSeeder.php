<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $settings = [
            'site_name_en' => 'Motorzad',
            'site_name_ar' => 'موتورزاد',
            'site_description_en' => 'Your ultimate travel companion.',
            'site_description_ar' => 'رفيقك الأمثل في السفر.',
            'site_logo' => 'images/logo.png',
            'site_favicon' => 'images/favicon.png',
            'maintenance_mode' => '0',
            'contact_email' => 'support@Motorzad.com',
            'contact_phone' => '+966 500 000 000',
            'facebook_url' => 'https://facebook.com/Motorzad',
            'twitter_url' => 'https://twitter.com/Motorzad',
            'instagram_url' => 'https://instagram.com/Motorzad',
            'tiktok_url' => 'https://tiktok.com/@Motorzad',
            'snapchat_url' => 'https://snapchat.com/add/Motorzad',
            'linkedin_url' => 'https://linkedin.com/company/Motorzad',
            'youtube_url' => 'https://youtube.com/Motorzad',
            'whatsapp_url' => 'https://wa.me/966500000000',
            'telegram_url' => 'https://t.me/Motorzad',
            'primary_color' => '#3b4bd3',
            'app_min_version' => '1.0.0',
            'android_url' => 'https://play.google.com/store/apps/details?id=com.Motorzad',
            'ios_url' => 'https://apps.apple.com/app/Motorzad/id000000000',
            'story_en' => 'Turning Travel Dreams Into Reality Founded in 2015, Wjhtak started with a simple mission: to make premium travel experiences accessible to everyone. What began as a small team of passionate travelers has grown into one of the most trusted tourism platforms in the region.  Today, we partner with over 200 tourism companies and have helped more than 50,000 travelers explore the world. Our commitment to quality, safety, and customer satisfaction remains at the heart of everything we do.',
            'story_ar' => 'تحويل أحلام السفر إلى حقيقة تأسست وجهتك في عام 2015 بمهمة بسيطة: جعل تجارب السفر الفاخرة متاحة للجميع. ما بدأ كفريق صغير من المسافرين المتحمسين نما ليصبح أحد أكثر المنصات السياحية موثوقية في المنطقة. اليوم، نحن نتعاون مع أكثر من 200 شركة سياحية وساعدنا أكثر من 50,000 مسافر على استكشاف العالم. يظل التزامنا بالجودة والسلامة ورضا العملاء في جوهر كل ما نقوم به.',
            'mission_en' => 'To provide exceptional travel experiences that inspire, connect, and transform. We believe travel has the power to broaden horizons and create lasting memories.',
            'mission_ar' => 'تقديم تجارب سفر استثنائية تلهم وتواصل وتغير. نحن نؤمن بأن للسفر القدرة على توسيع الآفاق وخلق ذكريات دائمة.',
            'vision_en' => 'To become the leading tourism platform in the Middle East, known for innovation, reliability, and our commitment to making travel accessible to all.',
            'vision_ar' => 'أن نصبح المنصة السياحية الرائدة في الشرق الأوسط، والمعروفة بالابتكار والموثوقية والتزامنا بجعل السفر متاحاً للجميع.',
            
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    
    }
}
