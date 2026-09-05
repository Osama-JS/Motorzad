<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\AuctionImage;
use App\Models\BankAccount;
use App\Models\Bid;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\HyperpayTransaction;
use App\Models\KycRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\SellerRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the comprehensive demo data seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Demo Data Seeding for Motorzad...');

        // ───────────────────────────────────────────────────────────────────
        // 1. Users (Admins, Verified Sellers, Active Bidders)
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('1/10 Seeding Users & Wallets...');

        // Ensure Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@motorzad.com'],
            [
                'name' => 'مدير المنصة العام',
                'first_name' => 'محمد',
                'last_name' => 'الغامدي',
                'phone' => '+966500000001',
                'password' => Hash::make('password'),
                'city' => 'الرياض',
                'country' => 'السعودية',
                'status' => 'approved',
                'identity_verified_at' => Carbon::now()->subMonths(6),
            ]
        );
        $admin->syncRoles(['admin']);

        // Sellers
        $seller1 = User::firstOrCreate(
            ['email' => 'seller.riyadh@motorzad.com'],
            [
                'name' => 'شركة أوتوزاد للسيارات',
                'first_name' => 'عبدالله',
                'last_name' => 'القحطاني',
                'phone' => '+966551234567',
                'password' => Hash::make('password'),
                'city' => 'الرياض',
                'country' => 'السعودية',
                'status' => 'approved',
                'identity_verified_at' => Carbon::now()->subMonths(3),
            ]
        );
        $seller1->syncRoles(['seller']);

        $seller2 = User::firstOrCreate(
            ['email' => 'seller.jeddah@motorzad.com'],
            [
                'name' => 'معرض النخبة الفاخرة',
                'first_name' => 'سلطان',
                'last_name' => 'العتيبي',
                'phone' => '+966559876543',
                'password' => Hash::make('password'),
                'city' => 'جدة',
                'country' => 'السعودية',
                'status' => 'approved',
                'identity_verified_at' => Carbon::now()->subMonths(2),
            ]
        );
        $seller2->syncRoles(['seller']);

        // Bidders
        $bidderUsers = [];
        $bidderData = [
            ['email' => 'bidder1@motorzad.com', 'name' => 'فيصل بن حمد', 'first' => 'فيصل', 'last' => 'الشهري', 'phone' => '+966561112233', 'city' => 'الرياض', 'balance' => 250000],
            ['email' => 'bidder2@motorzad.com', 'name' => 'تركي السبيعي', 'first' => 'تركي', 'last' => 'السبيعي', 'phone' => '+966562223344', 'city' => 'الدمام', 'balance' => 180000],
            ['email' => 'bidder3@motorzad.com', 'name' => 'ماجد الدوسري', 'first' => 'ماجد', 'last' => 'الدوسري', 'phone' => '+966563334455', 'city' => 'الخبر', 'balance' => 320000],
            ['email' => 'bidder4@motorzad.com', 'name' => 'عمر المطيري', 'first' => 'عمر', 'last' => 'المطيري', 'phone' => '+966564445566', 'city' => 'جدة', 'balance' => 95000],
            ['email' => 'bidder5@motorzad.com', 'name' => 'فهد العنزي', 'first' => 'فهد', 'last' => 'العنزي', 'phone' => '+966565556677', 'city' => 'مكة المكرمة', 'balance' => 60000],
        ];

        foreach ($bidderData as $bd) {
            $u = User::firstOrCreate(
                ['email' => $bd['email']],
                [
                    'name' => $bd['name'],
                    'first_name' => $bd['first'],
                    'last_name' => $bd['last'],
                    'phone' => $bd['phone'],
                    'password' => Hash::make('password'),
                    'city' => $bd['city'],
                    'country' => 'السعودية',
                    'status' => 'approved',
                    'identity_verified_at' => Carbon::now()->subWeeks(rand(1, 12)),
                ]
            );
            $u->syncRoles(['bidder']);
            $bidderUsers[] = $u;

            // Initialize / update wallet
            $wallet = $u->wallet;
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $u->id,
                    'balance' => $bd['balance'],
                    'total_deposits' => $bd['balance'],
                    'total_withdrawals' => 0,
                    'debt_ceiling' => 50000,
                    'debt_usage' => 0,
                ]);
            } else {
                $wallet->update([
                    'balance' => $bd['balance'],
                    'total_deposits' => $bd['balance'],
                    'debt_ceiling' => 50000,
                ]);
            }
        }

        // ───────────────────────────────────────────────────────────────────
        // 2. KYC Requests & Seller Upgrade Requests
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('2/10 Seeding KYC & Seller Requests...');

        // KYC Requests
        foreach ($bidderUsers as $idx => $bUser) {
            KycRequest::updateOrCreate(
                ['user_id' => $bUser->id],
                [
                    'full_name' => $bUser->name,
                    'country' => 'المملكة العربية السعودية',
                    'id_number' => '10' . rand(10000000, 99999999),
                    'document_type' => 'national_id',
                    'id_front_image' => 'kyc/id_front_demo_' . $bUser->id . '.jpg',
                    'id_back_image' => 'kyc/id_back_demo_' . $bUser->id . '.jpg',
                    'selfie_image' => 'kyc/selfie_demo_' . $bUser->id . '.jpg',
                    'status' => $idx % 4 === 0 ? 'pending' : 'approved',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => Carbon::now()->subDays(rand(2, 30)),
                    'admin_note' => 'تم التحقق من بيانات الهوية الوطنية بنجاح.'
                ]
            );
        }

        // Seller Upgrade Requests
        SellerRequest::updateOrCreate(
            ['user_id' => $bidderUsers[0]->id],
            [
                'status' => 'pending',
                'admin_notes' => 'الطلب قيد مراجعة السجل التجاري ورخصة فال للمزادات.',
                'created_at' => Carbon::now()->subDays(2),
            ]
        );

        SellerRequest::updateOrCreate(
            ['user_id' => $bidderUsers[1]->id],
            [
                'status' => 'approved',
                'admin_notes' => 'تم التحقق من النشاط التجاري وتفعيل حساب التاجر.',
                'created_at' => Carbon::now()->subDays(10),
            ]
        );

        SellerRequest::updateOrCreate(
            ['user_id' => $bidderUsers[2]->id],
            [
                'status' => 'rejected',
                'admin_notes' => 'يرجى إرفاق رخصة النشاط التجاري سارية المفعول وإعادة التقديم.',
                'created_at' => Carbon::now()->subDays(15),
            ]
        );

        // ───────────────────────────────────────────────────────────────────
        // 3. Vehicles Database
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('3/10 Seeding Vehicles...');

        $vehiclesData = [
            [
                'make_ar' => 'مرسيدس بنز', 'make_en' => 'Mercedes-Benz',
                'model_ar' => 'G-Class G63 AMG', 'model_en' => 'G63 AMG',
                'year' => 2024, 'color_ar' => 'أسود مطفي', 'color_en' => 'Matte Black',
                'vin_number' => 'WDB4632761X' . rand(100000, 999999),
                'mileage' => 4500, 'plate_number' => 'ق م ر 1111', 'fuel_type' => 'petrol',
                'transmission' => 'automatic', 'engine_capacity' => '4.0L V8 BiTurbo', 'cylinders' => 8,
                'condition' => 'new', 'status' => 'approved', 'submitted_by' => $seller1->id,
            ],
            [
                'make_ar' => 'تويوتا', 'make_en' => 'Toyota',
                'model_ar' => 'لاند كروزر VXR', 'model_en' => 'Land Cruiser VXR',
                'year' => 2024, 'color_ar' => 'أبيض لؤلؤي', 'color_en' => 'Pearl White',
                'vin_number' => 'JTMCB7AJ5R' . rand(100000, 999999),
                'mileage' => 12000, 'plate_number' => 'س ع د 7777', 'fuel_type' => 'petrol',
                'transmission' => 'automatic', 'engine_capacity' => '3.5L Twin Turbo', 'cylinders' => 6,
                'condition' => 'excellent', 'status' => 'approved', 'submitted_by' => $seller1->id,
            ],
            [
                'make_ar' => 'بورش', 'make_en' => 'Porsche',
                'model_ar' => '911 كاريرا S', 'model_en' => '911 Carrera S',
                'year' => 2023, 'color_ar' => 'رمادي طباشيري', 'color_en' => 'Crayon Grey',
                'vin_number' => 'WP0AB2A97P' . rand(100000, 999999),
                'mileage' => 8900, 'plate_number' => 'هـ م س 911', 'fuel_type' => 'petrol',
                'transmission' => 'automatic', 'engine_capacity' => '3.0L Boxer Twin-Turbo', 'cylinders' => 6,
                'condition' => 'excellent', 'status' => 'approved', 'submitted_by' => $seller2->id,
            ],
            [
                'make_ar' => 'نيسان', 'make_en' => 'Nissan',
                'model_ar' => 'باترول تيتانيوم', 'model_en' => 'Patrol Titanium',
                'year' => 2023, 'color_ar' => 'فضي معدني', 'color_en' => 'Silver Metallic',
                'vin_number' => 'JN8AY2NC7P' . rand(100000, 999999),
                'mileage' => 28000, 'plate_number' => 'ن ف ط 500', 'fuel_type' => 'petrol',
                'transmission' => 'automatic', 'engine_capacity' => '5.6L V8', 'cylinders' => 8,
                'condition' => 'good', 'status' => 'approved', 'submitted_by' => $seller2->id,
            ],
            [
                'make_ar' => 'لكزس', 'make_en' => 'Lexus',
                'model_ar' => 'LX600 VIP', 'model_en' => 'LX600 VIP',
                'year' => 2024, 'color_ar' => 'تيتانيوم صوتي', 'color_en' => 'Sonic Titanium',
                'vin_number' => 'JTJHY7AX4R' . rand(100000, 999999),
                'mileage' => 6200, 'plate_number' => 'ع ز م 600', 'fuel_type' => 'petrol',
                'transmission' => 'automatic', 'engine_capacity' => '3.5L V6 Twin-Turbo', 'cylinders' => 6,
                'condition' => 'new', 'status' => 'approved', 'submitted_by' => $seller1->id,
            ],
            [
                'make_ar' => 'فورد', 'make_en' => 'Ford',
                'model_ar' => 'F-150 رابتر R', 'model_en' => 'F-150 Raptor R',
                'year' => 2024, 'color_ar' => 'أزرق كود', 'color_en' => 'Code Orange',
                'vin_number' => '1FTFW1R86R' . rand(100000, 999999),
                'mileage' => 3100, 'plate_number' => 'و ح ش 150', 'fuel_type' => 'petrol',
                'transmission' => 'automatic', 'engine_capacity' => '5.2L Supercharged V8', 'cylinders' => 8,
                'condition' => 'new', 'status' => 'approved', 'submitted_by' => $seller2->id,
            ],
        ];

        $createdVehicles = [];
        foreach ($vehiclesData as $vd) {
            $createdVehicles[] = Vehicle::updateOrCreate(
                ['vin_number' => $vd['vin_number']],
                $vd
            );
        }

        // ───────────────────────────────────────────────────────────────────
        // 4. Auctions (Live, Completed/Sold, Unsold, Draft)
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('4/10 Seeding Auctions & Images...');

        // 4.1 Live Auction 1 (Mercedes G63)
        $auctionLive1 = Auction::updateOrCreate(
            ['title_ar' => 'مزاد مرسيدس جي 63 إيه إم جي 2024 فل كاربون فايبر'],
            [
                'vehicle_id' => $createdVehicles[0]->id,
                'created_by' => $seller1->id,
                'title_en' => 'Mercedes-Benz G63 AMG 2024 Edition 1',
                'description_ar' => 'مزاد حصري ومباشر على سيارة مرسيدس G63 AMG موديل 2024 بلون أسود مطفي مع داخلية جلد أحمر ديسينيو وكاربون فايبر كامل، بدون حوادث بحالة الوكالة.',
                'description_en' => 'Exclusive live auction on 2024 Mercedes G63 AMG Matte Black with Designo Red interior.',
                'location_ar' => 'الرياض - حي المعذر',
                'location_en' => 'Riyadh - Al Mathar',
                'start_price' => 750000,
                'reserve_price' => 880000,
                'min_bid_increment' => 5000,
                'buy_now_price' => 950000,
                'deposit_amount' => 20000,
                'deposit_required' => true,
                'start_time' => Carbon::now()->subHours(12),
                'end_time' => Carbon::now()->addHours(36),
                'auto_extend_minutes' => 5,
                'status' => 'live',
                'is_featured' => true,
                'commission_rate' => 2.5,
                'views_count' => 1420,
                'bids_count' => 6,
            ]
        );

        // 4.2 Live Auction 2 (Toyota Land Cruiser)
        $auctionLive2 = Auction::updateOrCreate(
            ['title_ar' => 'مزاد تويوتا لاندكروزر VXR توين تيربو 2024'],
            [
                'vehicle_id' => $createdVehicles[1]->id,
                'created_by' => $seller1->id,
                'title_en' => 'Toyota Land Cruiser VXR Twin-Turbo 2024',
                'description_ar' => 'لاند كروزر VXR فل كامل وارد عبداللطيف جميل، هيد أب ديسبلاي، شاشات خلفية، ثلاجة، دفع رباعي متقدم.',
                'description_en' => 'Toyota Land Cruiser VXR full options 2024 in immaculate condition.',
                'location_ar' => 'الرياض - طريق خريص',
                'location_en' => 'Riyadh - Khurais Road',
                'start_price' => 320000,
                'reserve_price' => 380000,
                'min_bid_increment' => 2500,
                'buy_now_price' => 420000,
                'deposit_amount' => 10000,
                'deposit_required' => true,
                'start_time' => Carbon::now()->subHours(6),
                'end_time' => Carbon::now()->addHours(48),
                'auto_extend_minutes' => 3,
                'status' => 'live',
                'is_featured' => true,
                'commission_rate' => 2.5,
                'views_count' => 890,
                'bids_count' => 4,
            ]
        );

        // 4.3 Sold Auction 1 (Porsche 911 Carrera S)
        $auctionSold1 = Auction::updateOrCreate(
            ['title_ar' => 'مزاد بورش 911 كاريرا S 2023 رمادي طباشيري'],
            [
                'vehicle_id' => $createdVehicles[2]->id,
                'created_by' => $seller2->id,
                'winner_id' => $bidderUsers[0]->id,
                'title_en' => 'Porsche 911 Carrera S 2023 Crayon',
                'description_ar' => 'تمت الترسية والبيع بنجاح. بورش كاريرا S باقة سبورت كرونو وعادم رياضي.',
                'description_en' => 'Successfully auctioned and sold to the highest bidder.',
                'location_ar' => 'جدة - حي الأندلس',
                'location_en' => 'Jeddah - Al Andalus',
                'start_price' => 480000,
                'reserve_price' => 540000,
                'min_bid_increment' => 5000,
                'winning_bid_amount' => 565000,
                'sold_at' => Carbon::now()->subDays(4),
                'commission_rate' => 2.5,
                'commission_amount' => 14125.00,
                'deposit_amount' => 15000,
                'deposit_required' => true,
                'start_time' => Carbon::now()->subDays(8),
                'end_time' => Carbon::now()->subDays(4),
                'status' => 'sold',
                'is_featured' => false,
                'views_count' => 2340,
                'bids_count' => 11,
            ]
        );

        // 4.4 Sold Auction 2 (Lexus LX600 VIP)
        $auctionSold2 = Auction::updateOrCreate(
            ['title_ar' => 'مزاد لكزس LX600 VIP 2024 أربع مقاعد ملكية'],
            [
                'vehicle_id' => $createdVehicles[4]->id,
                'created_by' => $seller1->id,
                'winner_id' => $bidderUsers[2]->id,
                'title_en' => 'Lexus LX600 VIP 2024 Luxury 4-Seater',
                'description_ar' => 'لكزس VIP فئة كبار الشخصيات مع مساج وتبريد وتدفئة للمقاعد وشاشات مستقلة.',
                'description_en' => 'Lexus LX600 VIP Executive luxury edition.',
                'location_ar' => 'الرياض - حي حطين',
                'location_en' => 'Riyadh - Hittin',
                'start_price' => 600000,
                'reserve_price' => 670000,
                'min_bid_increment' => 5000,
                'winning_bid_amount' => 710000,
                'sold_at' => Carbon::now()->subDays(12),
                'commission_rate' => 2.5,
                'commission_amount' => 17750.00,
                'deposit_amount' => 20000,
                'deposit_required' => true,
                'start_time' => Carbon::now()->subDays(16),
                'end_time' => Carbon::now()->subDays(12),
                'status' => 'sold',
                'is_featured' => true,
                'views_count' => 3120,
                'bids_count' => 14,
            ]
        );

        // 4.5 Sold Auction 3 (Ford Raptor R)
        $auctionSold3 = Auction::updateOrCreate(
            ['title_ar' => 'مزاد فورد إف 150 رابتر R سوبرتشارج 2024'],
            [
                'vehicle_id' => $createdVehicles[5]->id,
                'created_by' => $seller2->id,
                'winner_id' => $bidderUsers[1]->id,
                'title_en' => 'Ford F-150 Raptor R Supercharged 2024',
                'description_ar' => 'رابتر R محرك V8 سوبرتشارج بقوة 700 حصان، إصدار الأداء الفائق.',
                'description_en' => 'Ford F-150 Raptor R 700 HP Supercharged Monster.',
                'location_ar' => 'الخبر - الكورنيش',
                'location_en' => 'Khobar - Corniche',
                'start_price' => 450000,
                'reserve_price' => 510000,
                'min_bid_increment' => 5000,
                'winning_bid_amount' => 530000,
                'sold_at' => Carbon::now()->subDays(24),
                'commission_rate' => 2.5,
                'commission_amount' => 13250.00,
                'deposit_amount' => 15000,
                'deposit_required' => true,
                'start_time' => Carbon::now()->subDays(28),
                'end_time' => Carbon::now()->subDays(24),
                'status' => 'sold',
                'is_featured' => false,
                'views_count' => 1950,
                'bids_count' => 9,
            ]
        );

        // 4.6 Ended/Unsold Auction (Nissan Patrol)
        $auctionEnded = Auction::updateOrCreate(
            ['title_ar' => 'مزاد نيسان باترول تيتانيوم V8 2023'],
            [
                'vehicle_id' => $createdVehicles[3]->id,
                'created_by' => $seller2->id,
                'title_en' => 'Nissan Patrol Titanium V8 2023',
                'description_ar' => 'انتهى وقت المزاد دون الوصول إلى السعر المحجوز (Reserve Price).',
                'description_en' => 'Auction ended without meeting reserve price.',
                'location_ar' => 'جدة - حي الروضة',
                'location_en' => 'Jeddah - Al Rawdah',
                'start_price' => 210000,
                'reserve_price' => 265000,
                'min_bid_increment' => 2000,
                'deposit_amount' => 5000,
                'deposit_required' => true,
                'start_time' => Carbon::now()->subDays(7),
                'end_time' => Carbon::now()->subDays(3),
                'status' => 'ended',
                'is_featured' => false,
                'views_count' => 640,
                'bids_count' => 3,
            ]
        );

        // Auction Images
        $allAuctions = [$auctionLive1, $auctionLive2, $auctionSold1, $auctionSold2, $auctionSold3, $auctionEnded];
        foreach ($allAuctions as $auc) {
            AuctionImage::firstOrCreate(
                ['auction_id' => $auc->id, 'sort_order' => 1],
                [
                    'image_path' => 'auctions/demo_car_' . ($auc->id % 6 + 1) . '.jpg',
                    'is_primary' => true,
                ]
            );
        }

        // ───────────────────────────────────────────────────────────────────
        // 5. Bids Log
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('5/10 Seeding Bids Log...');

        // Bids on Live Mercedes
        $liveBids = [
            ['user' => $bidderUsers[0], 'amount' => 760000, 'time' => 10],
            ['user' => $bidderUsers[1], 'amount' => 775000, 'time' => 8],
            ['user' => $bidderUsers[2], 'amount' => 790000, 'time' => 6],
            ['user' => $bidderUsers[0], 'amount' => 810000, 'time' => 4],
            ['user' => $bidderUsers[3], 'amount' => 825000, 'time' => 2],
            ['user' => $bidderUsers[2], 'amount' => 840000, 'time' => 1],
        ];
        foreach ($liveBids as $b) {
            Bid::create([
                'auction_id' => $auctionLive1->id,
                'user_id' => $b['user']->id,
                'amount' => $b['amount'],
                'is_auto_bid' => false,
                'status' => 'active',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subHours($b['time']),
            ]);
        }

        // Bids on Sold Porsche
        $soldBids = [
            ['user' => $bidderUsers[3], 'amount' => 490000],
            ['user' => $bidderUsers[4], 'amount' => 510000],
            ['user' => $bidderUsers[1], 'amount' => 530000],
            ['user' => $bidderUsers[0], 'amount' => 565000], // Winner
        ];
        foreach ($soldBids as $idx => $b) {
            Bid::create([
                'auction_id' => $auctionSold1->id,
                'user_id' => $b['user']->id,
                'amount' => $b['amount'],
                'is_auto_bid' => false,
                'status' => ($idx === count($soldBids) - 1) ? 'active' : 'outbid',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subDays(4)->subHours(rand(1, 40)),
            ]);
        }

        // ───────────────────────────────────────────────────────────────────
        // 6. Wallet Transactions & HyperPay Online Payments
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('6/10 Seeding Financials & HyperPay Transactions...');

        foreach ($bidderUsers as $idx => $bUser) {
            $w = $bUser->wallet;

            // Deposit 1 (HyperPay Visa/Mada)
            WalletTransaction::create([
                'wallet_id' => $w->id,
                'type' => 'credit',
                'amount' => 50000,
                'description' => 'شحن رصيد محفظة إلكتروني عبر بطاقة مدى (هايبر باي)',
                'created_by' => $bUser->id,
                'created_at' => Carbon::now()->subDays(rand(5, 30)),
            ]);

            // Deposit 2 (Bank Transfer)
            WalletTransaction::create([
                'wallet_id' => $w->id,
                'type' => 'credit',
                'amount' => 40000,
                'description' => 'إيداع بنكي حوالة سريعة معتمدة من الإدارة',
                'created_by' => $admin->id,
                'created_at' => Carbon::now()->subDays(rand(1, 15)),
            ]);

            // Withdrawal or Deductions
            if ($idx % 2 === 0) {
                WalletTransaction::create([
                    'wallet_id' => $w->id,
                    'type' => 'debit',
                    'amount' => 15000,
                    'description' => 'حجز مبلغ تأمين مزاد مركبة',
                    'created_by' => $admin->id,
                    'created_at' => Carbon::now()->subDays(2),
                ]);
            }

            // Record HyperPay Online Transactions
            HyperpayTransaction::create([
                'user_id' => $bUser->id,
                'wallet_id' => $w->id,
                'merchant_transaction_id' => 'TXN_DEMO_' . Str::upper(Str::random(12)),
                'checkout_id' => 'CHK_' . Str::upper(Str::random(18)),
                'hyperpay_payment_id' => 'PAY_' . Str::upper(Str::random(16)),
                'brand' => ($idx % 2 === 0) ? 'mada' : 'visa_master',
                'amount' => 50000.00,
                'currency' => 'SAR',
                'status' => 'paid',
                'result_code' => '000.100.110',
                'result_description' => 'Transaction succeeded (Demo)',
                'channel' => 'web',
                'card_holder' => $bUser->name,
                'card_bin' => ($idx % 2 === 0) ? '588845' : '411111',
                'card_last4' => ($idx % 2 === 0) ? '4008' : '1111',
                'raw_response' => ['code' => '000.100.110', 'description' => 'Approved transaction'],
                'paid_at' => Carbon::now()->subDays(rand(2, 25)),
                'created_at' => Carbon::now()->subDays(rand(2, 25)),
            ]);
        }

        // ───────────────────────────────────────────────────────────────────
        // 7. Official Bank Accounts
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('7/10 Seeding Official Bank Accounts...');

        BankAccount::updateOrCreate(
            ['iban' => 'SA0380000000608010167519'],
            [
                'bank_name' => 'مصرف الراجحي (Al Rajhi Bank)',
                'beneficiary_name' => 'شركة موتورزاد لتقنية المزادات المحدودة',
                'is_active' => true,
            ]
        );

        BankAccount::updateOrCreate(
            ['iban' => 'SA5510000001234567890123'],
            [
                'bank_name' => 'البنك الأهلي السعودي (SNB)',
                'beneficiary_name' => 'شركة موتورزاد لتقنية المزادات المحدودة',
                'is_active' => true,
            ]
        );

        BankAccount::updateOrCreate(
            ['iban' => 'SA9820000009876543210987'],
            [
                'bank_name' => 'بنك الرياض (Riyad Bank)',
                'beneficiary_name' => 'شركة موتورزاد لتقنية المزادات المحدودة',
                'is_active' => true,
            ]
        );

        // ───────────────────────────────────────────────────────────────────
        // 8. News & Categories
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('8/10 Seeding News & Articles...');

        $cat1 = NewsCategory::firstOrCreate(
            ['name_ar' => 'أخبار المزادات'],
            ['name_en' => 'Auction News', 'is_active' => true]
        );
        $cat2 = NewsCategory::firstOrCreate(
            ['name_ar' => 'سوق السيارات الفاخرة'],
            ['name_en' => 'Luxury Automotive', 'is_active' => true]
        );

        News::updateOrCreate(
            ['title_ar' => 'منصة موتورزاد تطلق مزادات حية بتقنية البث فائق السرعة'],
            [
                'category_id' => $cat1->id,
                'title_en' => 'Motorzad Launches Ultra-Low Latency Live Auctions',
                'content_ar' => 'أعلنت منصة موتورزاد الرائدة في مزادات السيارات في المملكة العربية السعودية عن إطلاق أحدث أنظمة البث المباشر والمزايدة التفاعلية بزمن تأخير لا يتجاوز أجزاء من الثانية.',
                'content_en' => 'Motorzad, the premier automotive auction platform in KSA, announces real-time live auction capabilities.',
                'tags_ar' => 'مزادات, سيارات, تقنية',
                'tags_en' => 'auctions, automotive, tech',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        News::updateOrCreate(
            ['title_ar' => 'نمو قياسي في حجم تداولات السيارات الفاخرة بالرياض'],
            [
                'category_id' => $cat2->id,
                'title_en' => 'Record Growth in Luxury Vehicle Trade in Riyadh',
                'content_ar' => 'سجلت مزادات السيارات الفارهة والنادرة في مدينة الرياض ارتفاعاً ملحوظاً في حجم الصفقات المبرمة خلال الربع الحالي بفضل سهولة الدفع الرقمي والضمانات البنكية.',
                'content_en' => 'Luxury vehicle transactions reach historic milestones in Riyadh through digital auctioning.',
                'tags_ar' => 'بورش, مرسيدس, لكزس, استثمار',
                'tags_en' => 'luxury, cars, investment',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        // ───────────────────────────────────────────────────────────────────
        // 9. FAQs & Static Pages
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('9/10 Seeding FAQs & Informational Pages...');

        $faqsData = [
            [
                'question_ar' => 'كيف يمكنني المشاركة في المزايدة على السيارات؟',
                'question_en' => 'How can I participate in vehicle bidding?',
                'answer_ar' => 'يمكنك المشاركة بسهولة من خلال إنشاء حساب، وتوثيق هويتك الوطنية، وشحن محفظتك بمبلغ التأمين المطلوب للمزاد عبر مدى أو البطاقات الائتمانية.',
                'answer_en' => 'Simply register, complete your KYC identity verification, and deposit the required auction guarantee into your wallet.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'question_ar' => 'ما هي طرق الدفع المعتمدة لشحن المحفظة وسداد المشتريات؟',
                'question_en' => 'What payment methods are supported for wallet top-up?',
                'answer_ar' => 'تدعم المنصة الدفع الإلكتروني الفوري عبر بوابة هايبر باي (مدى، فيزا، ماستركارد، وأبل باي) بالإضافة إلى التحويل البنكي المباشر للحسابات الرسمية.',
                'answer_en' => 'We support instant payments via HyperPay (Mada, Visa, Mastercard, Apple Pay) as well as wire transfers.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'question_ar' => 'هل يتم استرجاع مبلغ التأمين في حال لم أفز بالمزاد؟',
                'question_en' => 'Is the auction deposit refundable if I do not win?',
                'answer_ar' => 'نعم بالتأكيد، يتم فك حجز مبلغ التأمين وإعادته فوراً إلى رصيد محفظتك المتاح بمجرد انتهاء المزاد لصالح مزايد آخر، ويمكنك سحبه لحسابك البنكي في أي وقت.',
                'answer_en' => 'Yes, the deposit is instantly released back to your available balance upon auction completion.',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($faqsData as $faq) {
            Faq::updateOrCreate(
                ['question_ar' => $faq['question_ar']],
                $faq
            );
        }

        // Informational Pages
        Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title_ar' => 'عن موتورزاد',
                'title_en' => 'About Motorzad',
                'content_ar' => 'موتورزاد هي المنصة الرقمية السعودية الأولى المتخصصة في مزادات السيارات الموثوقة والفاخرة، نجمع بين الشفافية التامة وأحدث التقنيات لتقديم تجربة بيع وشراء استثنائية.',
                'content_en' => 'Motorzad is Saudi Arabia\'s leading digital automotive auction platform.',
                'is_active' => true,
                'show_in_footer' => true,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title_ar' => 'سياسة الخصوصية وحماية البيانات',
                'title_en' => 'Privacy Policy',
                'content_ar' => 'نلتزم في منصة موتورزاد بأعلى معايير حماية البيانات الشخصية والمالية وفقاً للأنظمة والتشريعات المعتمدة في المملكة العربية السعودية.',
                'content_en' => 'Motorzad adheres to the highest data protection and privacy standards in Saudi Arabia.',
                'is_active' => true,
                'show_in_footer' => true,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'terms-conditions'],
            [
                'title_ar' => 'الشروط والأحكام العامة للمزادات',
                'title_en' => 'Terms and Conditions',
                'content_ar' => 'تخضع جميع عمليات المزايدة والترسية للشروط والأحكام المعتمدة المنظمة لقطاع المزادات العلنية الإلكترونية وتوثيق ملكية المركبات.',
                'content_en' => 'All auction bids and purchases are governed by our official platform terms.',
                'is_active' => true,
                'show_in_footer' => true,
            ]
        );

        // ───────────────────────────────────────────────────────────────────
        // 10. Contact Messages & System Notifications
        // ───────────────────────────────────────────────────────────────────
        $this->command->info('10/10 Seeding Contact Messages & Notifications...');

        Contact::updateOrCreate(
            ['email' => 'client.saad@gmail.com'],
            [
                'name' => 'سعد المنصور',
                'subject' => 'استفسار عن معاينة سيارة قبل المزاد',
                'message' => 'السلام عليكم ورحمة الله، هل بالإمكان حجز موعد لمعاينة سيارة مرسيدس G63 فحصاً شخصياً في المعرض قبل تقديم المزايدة؟ وشكراً.',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(5),
            ]
        );

        Contact::updateOrCreate(
            ['email' => 'dealer.riyadh@yahoo.com'],
            [
                'name' => 'إبراهيم الصالح',
                'subject' => 'طلب فتح حساب تجاري لمعرض سيارات',
                'message' => 'نحن معرض سيارات في شرق الرياض ونرغب في عرض 10 سيارات أسبوعياً في المنصة. نرجو التواصل لتحديد الرسوم والاتفاقية.',
                'is_read' => true,
                'read_at' => Carbon::now()->subDay(),
                'created_at' => Carbon::now()->subDays(2),
            ]
        );

        // Sample notifications
        DB::table('notifications')->insertOrIgnore([
            [
                'id' => (string)Str::uuid(),
                'type' => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $bidderUsers[0]->id,
                'data' => json_encode([
                    'title' => '🎉 مبروك فوزك بالمزاد!',
                    'message' => 'تهانينا! لقد ربحت مزاد بورش 911 كاريرا S 2023 بأعلى عطاء قدره 565,000 ريال.',
                    'action_url' => url('/bidder/auctions'),
                    'channels' => ['database', 'fcm'],
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'id' => (string)Str::uuid(),
                'type' => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $bidderUsers[1]->id,
                'data' => json_encode([
                    'title' => '⚡ تم تجاوز مزايدتك!',
                    'message' => 'قام مزايد آخر بتقديم عرض أعلى على سيارة مرسيدس G63. يمكنك رفع عرضك الآن.',
                    'action_url' => url('/auctions'),
                    'channels' => ['database'],
                ]),
                'read_at' => Carbon::now()->subHours(2),
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(2),
            ]
        ]);

        $this->command->info('✅ Demo Data Seeding Completed Successfully with Full Platform Coverage!');
    }
}
