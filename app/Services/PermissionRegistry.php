<?php

namespace App\Services;

class PermissionRegistry
{
    /**
     * Complete definition of system permissions grouped by module.
     * Includes Arabic and English labels, detailed bilingual descriptions, badges, and category metadata.
     */
    public static function definitions(): array
    {
        return [
            'core' => [
                'name_ar' => 'المنصة والواجهة الأساسية',
                'name_en' => 'Core & Platform',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
                'badge_color' => 'primary',
                'description_ar' => 'صلاحيات الوصول الأساسية والتصفح وإنشاء المزادات من قبل الأعضاء',
                'description_en' => 'Basic platform access, public auction browsing, and self-managed auction creation',
                'permissions' => [
                    'access-admin-panel' => [
                        'label_ar' => 'الدخول للوحة التحكم الإدارية',
                        'label_en' => 'Access Admin Panel',
                        'desc_ar' => 'السماح بتسجيل الدخول والوصول إلى لوحة الإدارة الرئيسية للمنصة',
                        'desc_en' => 'Allows logging into and navigating the main administrative dashboard',
                        'badge_ar' => 'إداري أساسي',
                        'badge_en' => 'Core Admin',
                    ],
                    'view-auctions' => [
                        'label_ar' => 'تصفح واستعراض المزادات',
                        'label_en' => 'View Public Auctions',
                        'desc_ar' => 'إمكانية تصفح غرف المزادات العامة والاطلاع على تفاصيل المركبات المتاحة',
                        'desc_en' => 'Allows browsing public auctions and viewing listed vehicle specifications',
                        'badge_ar' => 'عام',
                        'badge_en' => 'Public',
                    ],
                    'place-bids' => [
                        'label_ar' => 'المشاركة وتقديم المزايدات',
                        'label_en' => 'Place Bids',
                        'desc_ar' => 'السماح بالمزايدة وتقديم عروض أسعار تنافسية داخل المزادات الحية',
                        'desc_en' => 'Enables bidding and placing competitive offers in live & scheduled auctions',
                        'badge_ar' => 'مزايد',
                        'badge_en' => 'Bidder',
                    ],
                    'create-auctions' => [
                        'label_ar' => 'إنشاء مزادات جديدة',
                        'label_en' => 'Create Auctions',
                        'desc_ar' => 'إمكانية إدراج سيارة جديدة في مزاد وتحديد السعر الابتدائي وشروط البيع',
                        'desc_en' => 'Permits listing new vehicles for auction and setting starting prices and terms',
                        'badge_ar' => 'بائع',
                        'badge_en' => 'Seller',
                    ],
                    'manage-own-auctions' => [
                        'label_ar' => 'إدارة المزادات الخاصة بالبائع',
                        'label_en' => 'Manage Own Auctions',
                        'desc_ar' => 'متابعة وتعديل المزادات الخاصة بالبائع وقبول عروض الأسعار أو إنهائها',
                        'desc_en' => 'Allows managing, updating, accepting bids, or ending seller own auctions',
                        'badge_ar' => 'بائع',
                        'badge_en' => 'Seller',
                    ],
                ]
            ],

            'auctions_bids' => [
                'name_ar' => 'المزادات والمركبات والمزايدات',
                'name_en' => 'Auctions, Vehicles & Bids',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
                'badge_color' => 'warning',
                'description_ar' => 'التحكم الإداري الشامل بجميع المزادات، فحص واعتماد المركبات، وضبط المزايدات',
                'description_en' => 'Full administrative control over all auctions, vehicle inspections, and bidding logs',
                'permissions' => [
                    'manage-all-auctions' => [
                        'label_ar' => 'إدارة شاملة لكافة المزادات (صلاحية كاملة)',
                        'label_en' => 'Full Auctions Management (Master)',
                        'desc_ar' => 'التحكم الكامل بجميع المزادات (اعتماد، إيقاف مؤقت، تمديد، أو إنهاء قسري وحذف)',
                        'desc_en' => 'Complete authority over all platform auctions (approve, pause, extend, force-end, delete)',
                        'badge_ar' => 'إدارة عليا',
                        'badge_en' => 'High Authority',
                    ],
                    'view-admin-auctions' => [
                        'label_ar' => 'استعراض المزادات ولوحة التحليلات',
                        'label_en' => 'View Admin Auctions & Analytics',
                        'desc_ar' => 'الاطلاع على تفاصيل غرف المزادات في لوحة التحكم، تقارير الأداء، وتحليلات المبيعات دون تعديل',
                        'desc_en' => 'Browse auction listings, rooms, financial export reports, and analytics without editing rights',
                        'badge_ar' => 'قراءة',
                        'badge_en' => 'Read Only',
                    ],
                    'edit-auctions' => [
                        'label_ar' => 'تعديل المزادات وتمديد الوقت',
                        'label_en' => 'Edit Auctions & Extend Time',
                        'desc_ar' => 'تعديل بيانات المزادات القائمة، الأسعار الابتدائية، وتمديد وقت المزاد يدوياً',
                        'desc_en' => 'Update auction details, reserve prices, and manually extend auction duration',
                        'badge_ar' => 'عمليات',
                        'badge_en' => 'Operations',
                    ],
                    'pause-auctions' => [
                        'label_ar' => 'إيقاف مؤقت واستئناف المزادات',
                        'label_en' => 'Pause & Resume Auctions',
                        'desc_ar' => 'إيقاف المزايدة الحية مؤقتاً في حالات الطوارئ أو استئناف المزاد لاستقبال عروض الأسعار',
                        'desc_en' => 'Temporarily freeze live bidding during emergencies or resume auctions to receive bids',
                        'badge_ar' => 'تحكم ومراقبة',
                        'badge_en' => 'Control',
                    ],
                    'force-end-auctions' => [
                        'label_ar' => 'الإنهاء القسري للمزاد (إرساء أو إلغاء)',
                        'label_en' => 'Force End & Terminate Auctions',
                        'desc_ar' => 'إغلاق المزاد فورياً وإرساؤه على أعلى مزايد أو إلغاؤه ورد جميع مبالغ الضمانات للمشاركين',
                        'desc_en' => 'Force-close auction immediately to award winning bidder or cancel and refund all deposits',
                        'badge_ar' => 'حرج وقرار',
                        'badge_en' => 'Critical',
                    ],
                    'block-auction-bidders' => [
                        'label_ar' => 'حظر المزايدين المخالفين من المزاد',
                        'label_en' => 'Block Auction Bidders',
                        'desc_ar' => 'منع مزايد معين من الاستمرار بالمزايدة داخل مزاد محدد أو تعليق حسابه لمنع المزايدات الوهمية',
                        'desc_en' => 'Disqualify disruptive bidders from specific auctions or suspend account to prevent bid shilling',
                        'badge_ar' => 'أمان ورقابة',
                        'badge_en' => 'Security',
                    ],
                    'delete-auctions' => [
                        'label_ar' => 'حذف المزادات نهائياً',
                        'label_en' => 'Delete Auctions',
                        'desc_ar' => 'حذف سجل المزاد نهائياً من قاعدة البيانات مع مرفقاته',
                        'desc_en' => 'Permanently delete auction records and uploaded media from database',
                        'badge_ar' => 'حذف وحرج',
                        'badge_en' => 'Danger',
                    ],
                    'manage-vehicles' => [
                        'label_ar' => 'إدارة أسطول المركبات وفحصها (شامل)',
                        'label_en' => 'Full Vehicles Management (Master)',
                        'desc_ar' => 'مراجعة وتدقيق بيانات السيارات المضافة، فحص الصور، والاعتماد أو الرفض وتعديل المواصفات',
                        'desc_en' => 'Full authority to review, inspect, edit, approve, or reject vehicle listings',
                        'badge_ar' => 'عمليات',
                        'badge_en' => 'Operations',
                    ],
                    'approve-reject-vehicles' => [
                        'label_ar' => 'فحص واعتماد أو رفض المركبات',
                        'label_en' => 'Inspect, Approve & Reject Vehicles',
                        'desc_ar' => 'مراجعة تقارير فحص السيارات والصور، وقبول إدراجها في المزادات أو رفضها مع كتابة الأسباب',
                        'desc_en' => 'Review vehicle inspection reports, approve for platform auctions, or reject with notes',
                        'badge_ar' => 'فحص واعتماد',
                        'badge_en' => 'Inspection',
                    ],
                    'manage-bids' => [
                        'label_ar' => 'إدارة ومراقبة سجل المزايدات (شامل)',
                        'label_en' => 'Full Bids Monitoring (Master)',
                        'desc_ar' => 'مراقبة عروض الأسعار اللحظية وإلغاء المزايدات المخالفة أو المشبوهة',
                        'desc_en' => 'Track real-time bids and void irregular, suspicious, or erroneous bids',
                        'badge_ar' => 'رقابة',
                        'badge_en' => 'Audit',
                    ],
                    'void-bids' => [
                        'label_ar' => 'إلغاء المزايدات المشبوهة أو الخاطئة',
                        'label_en' => 'Void Irregular Bids',
                        'desc_ar' => 'إلغاء عرض سعر محدد لمزايد داخل المزاد وإعادة احتساب المزايدة الأعلى الحالية تلقائياً',
                        'desc_en' => 'Void a specific bid offer placed in auction and automatically recalculate top bid',
                        'badge_ar' => 'رقابة وتدقيق',
                        'badge_en' => 'Audit',
                    ],
                ]
            ],

            'users_roles' => [
                'name_ar' => 'المستخدمين والأدوار والتوثيق',
                'name_en' => 'Users, Roles & Verification',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                'badge_color' => 'info',
                'description_ar' => 'إدارة حسابات الأعضاء، توثيق الهوية KYC، وإدارة مصفوفة الصلاحيات والأدوار',
                'description_en' => 'User accounts management, identity verification (KYC), and roles permissions',
                'permissions' => [
                    'manage-users' => [
                        'label_ar' => 'إدارة شاملة لحسابات المستخدمين (كاملة)',
                        'label_en' => 'Full Users Management (Master)',
                        'desc_ar' => 'التحكم الإداري الكامل بجميع المستخدمين (إنشاء، تعديل، توثيق KYC، تفعيل أو حظر، وحذف)',
                        'desc_en' => 'Full authority to create, edit, verify KYC, activate, suspend, or delete users',
                        'badge_ar' => 'إداري شامل',
                        'badge_en' => 'Master Admin',
                    ],
                    'view-users' => [
                        'label_ar' => 'استعراض بيانات المستخدمين',
                        'label_en' => 'View Users List & Profiles',
                        'desc_ar' => 'صلاحية استعراض قائمة الأعضاء، الاطلاع على الملف الشخصي، وإحصائيات الحساب دون إمكانية التعديل',
                        'desc_en' => 'Permission to browse user listings, view user profiles and account statistics without editing',
                        'badge_ar' => 'قراءة',
                        'badge_en' => 'Read Only',
                    ],
                    'edit-users' => [
                        'label_ar' => 'تعديل بيانات وحسابات المستخدمين',
                        'label_en' => 'Edit Users & Assign Roles',
                        'desc_ar' => 'صلاحية تعديل بيانات المستخدم، تغيير البريد والهاتف، تعيين الأدوار، وإعادة ضبط كلمة المرور',
                        'desc_en' => 'Permission to update user details, change email/phone, assign roles, and reset passwords',
                        'badge_ar' => 'تعديل',
                        'badge_en' => 'Edit',
                    ],
                    'delete-users' => [
                        'label_ar' => 'حذف المستخدمين ونقلهم للمهملات',
                        'label_en' => 'Delete & Trash Users',
                        'desc_ar' => 'صلاحية حذف حسابات المستخدمين نهائياً أو نقلها لسلة المهملات (تتطلب تصفير الرصيد وعدم وجود مزادات نشطة)',
                        'desc_en' => 'Permission to delete or move user accounts to trash (requires zero wallet balance and no active bids/auctions)',
                        'badge_ar' => 'حذف وحرج',
                        'badge_en' => 'Critical',
                    ],
                    'verify-users-kyc' => [
                        'label_ar' => 'تدقيق واعتماد توثيق الهوية (KYC)',
                        'label_en' => 'Verify & Review Users KYC',
                        'desc_ar' => 'صلاحية فحص وثائق الهوية، جواز السفر، الصور الشخصية، وقبول أو رفض طلبات التحقق وتحديث المستوى',
                        'desc_en' => 'Review identity documents, passports, selfies, approve or reject KYC requests, and update verification level',
                        'badge_ar' => 'توثيق وتدقيق',
                        'badge_en' => 'Verification',
                    ],
                    'manage-roles' => [
                        'label_ar' => 'إدارة الأدوار ومصفوفة الصلاحيات',
                        'label_en' => 'Manage Roles & Permissions',
                        'desc_ar' => 'إنشاء وتعديل الأدوار الإدارية وتخصيص باقات الصلاحيات الممنوحة للموظفين',
                        'desc_en' => 'Create and customize administrative roles and assign permission packages',
                        'badge_ar' => 'أمان ونظام',
                        'badge_en' => 'Security',
                    ],
                    'manage-seller-requests' => [
                        'label_ar' => 'طلبات البائعين وقوالب النماذج',
                        'label_en' => 'Manage Seller Requests & Templates',
                        'desc_ar' => 'مراجعة طلبات الترقية لبائع واعتمادها، وتخصيص حقول نماذج التقديم',
                        'desc_en' => 'Review seller upgrade applications and configure custom dynamic form fields',
                        'badge_ar' => 'إشراف',
                        'badge_en' => 'Supervision',
                    ],
                ]
            ],

            'finance' => [
                'name_ar' => 'الإدارة المالية والمحافظ',
                'name_en' => 'Financial & Wallets',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
                'badge_color' => 'success',
                'description_ar' => 'إدارة العمليات النقدية، أرصدة المحافظ، طلبات السحب والإيداع، والحسابات البنكية',
                'description_en' => 'Manage financial transactions, balances, deposit/withdrawal requests, and bank accounts',
                'permissions' => [
                    'manage-wallets' => [
                        'label_ar' => 'إدارة محافظ المستخدمين والأرصدة (شامل)',
                        'label_en' => 'Full Wallets Management (Master)',
                        'desc_ar' => 'التحكم المالي الكامل: الاطلاع على أرصدة المحافظ، تعديل الأسقف الائتمانية، والخصم/الإيداع الإداري',
                        'desc_en' => 'Full control: inspect user balances, adjust debt ceilings, and execute manual credit/debit adjustments',
                        'badge_ar' => 'إدارة مالية',
                        'badge_en' => 'Finance Master',
                    ],
                    'view-wallets' => [
                        'label_ar' => 'استعراض المحافظ وسجلات الحركات',
                        'label_en' => 'View Wallets & Transaction History',
                        'desc_ar' => 'الاطلاع على أرصدة محافظ العملاء وسجل العمليات المالية والإيداعات والسحوبات دون صلاحية التعديل',
                        'desc_en' => 'View customer wallet balances, transaction logs, deposits and withdrawals without modification rights',
                        'badge_ar' => 'قراءة مالية',
                        'badge_en' => 'Read Only',
                    ],
                    'edit-user-balance' => [
                        'label_ar' => 'تعديل رصيد المحفظة (شحن / خصم يدوي)',
                        'label_en' => 'Adjust User Wallet Balance (Credit/Debit)',
                        'desc_ar' => 'إجراء عمليات الإيداع والخصم المباشر يدوياً على محافظ المستخدمين مع تدوين القيود المحاسبية',
                        'desc_en' => 'Execute manual deposit and debit transactions directly on user wallets with accounting notes',
                        'badge_ar' => 'مالي وحساس',
                        'badge_en' => 'Financial',
                    ],
                    'manage-debt-ceilings' => [
                        'label_ar' => 'تعديل السقف الائتماني للمحافظ',
                        'label_en' => 'Manage Wallet Debt Ceilings',
                        'desc_ar' => 'تحديد أو تعديل الحد الأقصى للمديونية والسقف الائتماني المسموح به للمزايدة للمستخدم',
                        'desc_en' => 'Set and adjust maximum debt ceiling limits allowed for user bidding',
                        'badge_ar' => 'ائتمان',
                        'badge_en' => 'Credit',
                    ],
                    'manage-deposits' => [
                        'label_ar' => 'معالجة طلبات الإيداع البنكي',
                        'label_en' => 'Manage Bank Deposit Requests',
                        'desc_ar' => 'مراجعة إيصالات التحويل البنكي وتأكيد إضافة المبالغ لمحفظة العميل',
                        'desc_en' => 'Verify bank wire receipts and approve incoming funds into user wallets',
                        'badge_ar' => 'مالية',
                        'badge_en' => 'Finance',
                    ],
                    'manage-withdrawals' => [
                        'label_ar' => 'معالجة واعتماد طلبات السحب',
                        'label_en' => 'Manage Withdrawal Requests',
                        'desc_ar' => 'مراجعة واعتماد أو رفض تحويل المبالغ المستردة للحسابات البنكية الشخصية',
                        'desc_en' => 'Review, approve, or reject user payout and refund requests to personal bank accounts',
                        'badge_ar' => 'مالية',
                        'badge_en' => 'Finance',
                    ],
                    'manage-bank-accounts' => [
                        'label_ar' => 'إدارة الحسابات البنكية للمنصة',
                        'label_en' => 'Manage Official Bank Accounts',
                        'desc_ar' => 'إضافة وتعديل حسابات المنصة البنكية الرسمية المعروضة للعملاء لإيداع التأمينات',
                        'desc_en' => 'Manage official platform bank accounts displayed for security deposit transfers',
                        'badge_ar' => 'مالية',
                        'badge_en' => 'Finance',
                    ],
                ]
            ],

            'content_marketing' => [
                'name_ar' => 'التواصل والمحتوى والتسويق',
                'name_en' => 'Content, Marketing & Communications',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
                'badge_color' => 'purple',
                'description_ar' => 'بث الإشعارات، إدارة المقالات، الصفحات الثابتة، والرد على استفسارات العملاء',
                'description_en' => 'Push notifications, article publishing, static pages, and visitor inquiry replies',
                'permissions' => [
                    'manage-notifications' => [
                        'label_ar' => 'مركز إدارة الإشعارات',
                        'label_en' => 'Manage Notifications Center',
                        'desc_ar' => 'استعراض سجل الإشعارات الصادرة ومتابعة حالات الاستلام والقراءة',
                        'desc_en' => 'Monitor sent system notifications log, delivery statuses, and read rates',
                        'badge_ar' => 'اتصال',
                        'badge_en' => 'Comms',
                    ],
                    'send-notifications' => [
                        'label_ar' => 'إرسال إشعارات جماعية ومخصصة',
                        'label_en' => 'Send Custom Notifications',
                        'desc_ar' => 'صياغة وبث إشعارات فورية وتنبيهات للمستخدمين أو فئات محددة',
                        'desc_en' => 'Compose and dispatch instant push notifications to targeted user groups',
                        'badge_ar' => 'اتصال',
                        'badge_en' => 'Comms',
                    ],
                    'manage-contacts' => [
                        'label_ar' => 'رسائل التواصل والاستفسارات',
                        'label_en' => 'Manage Contact Inquiries',
                        'desc_ar' => 'قراءة ومتابعة رسائل نموذج "اتصل بنا" والرد على استفسارات الزوار',
                        'desc_en' => 'Read and resolve incoming messages from the contact form and visitor inquiries',
                        'badge_ar' => 'خدمة عملاء',
                        'badge_en' => 'Support',
                    ],
                    'manage-pages' => [
                        'label_ar' => 'إدارة الصفحات التعريفية والشروط',
                        'label_en' => 'Manage Static Content Pages',
                        'desc_ar' => 'تعديل وإنشاء صفحات الموقع الثابتة (من نحن، الشروط، سياسة الخصوصية)',
                        'desc_en' => 'Create and update informational pages (About Us, Terms & Conditions, Privacy)',
                        'badge_ar' => 'محتوى',
                        'badge_en' => 'Content',
                    ],
                    'manage-news' => [
                        'label_ar' => 'إدارة الأخبار والمدونة',
                        'label_en' => 'Manage News & Blog Articles',
                        'desc_ar' => 'نشر وتحرير المقالات التثقيفية وأخبار قطاع مزادات السيارات والتصنيفات',
                        'desc_en' => 'Publish and edit automotive industry news, auction guides, and blog articles',
                        'badge_ar' => 'محتوى',
                        'badge_en' => 'Content',
                    ],
                    'manage-faqs' => [
                        'label_ar' => 'إدارة الأسئلة الشائعة',
                        'label_en' => 'Manage FAQs Database',
                        'desc_ar' => 'إضافة وترتيب بنك الأسئلة الشائعة وإجاباتها لتسهيل تجربة المستخدمين',
                        'desc_en' => 'Add, categorize, and reorder frequently asked questions and answers',
                        'badge_ar' => 'محتوى',
                        'badge_en' => 'Content',
                    ],
                    'manage-marketing' => [
                        'label_ar' => 'المميزات التسويقية وآراء العملاء',
                        'label_en' => 'Manage Marketing & Testimonials',
                        'desc_ar' => 'التحكم في قسم شركاء النجاح، آراء العملاء، والمميزات بالصفحة الرئيسية',
                        'desc_en' => 'Manage homepage feature highlights, customer testimonials, and marketing badges',
                        'badge_ar' => 'تسويق',
                        'badge_en' => 'Marketing',
                    ],
                ]
            ],

            'support' => [
                'name_ar' => 'الدعم الفني وخدمة العملاء',
                'name_en' => 'Technical Support',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>',
                'badge_color' => 'cyan',
                'description_ar' => 'متابعة تذاكر النزاعات، الشكاوى، وتقديم حلول الدعم المباشر للأعضاء',
                'description_en' => 'Track customer dispute tickets, complaints, and provide direct help desk solutions',
                'permissions' => [
                    'manage-tickets' => [
                        'label_ar' => 'إدارة تذاكر الدعم والشكاوى',
                        'label_en' => 'Manage Support Tickets',
                        'desc_ar' => 'الاطلاع على كافة تذاكر النزاعات والمساعدة المرفوعة من العملاء وتوزيعها',
                        'desc_en' => 'Access all customer help requests, dispute tickets, and assign to staff',
                        'badge_ar' => 'دعم فني',
                        'badge_en' => 'Support',
                    ],
                    'reply-tickets' => [
                        'label_ar' => 'الرد على التذاكر وإغلاقها',
                        'label_en' => 'Reply & Resolve Tickets',
                        'desc_ar' => 'صلاحية مراسلة العميل داخل التذكرة وتحديث حالتها أو إغلاقها عند الحل',
                        'desc_en' => 'Send responses to ticket threads, update progress status, and resolve issues',
                        'badge_ar' => 'دعم فني',
                        'badge_en' => 'Support',
                    ],
                ]
            ],

            'system' => [
                'name_ar' => 'النظام والصيانة والأمان',
                'name_en' => 'System & Maintenance',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82 1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'badge_color' => 'danger',
                'description_ar' => 'النسخ الاحتياطي، أدوات الصيانة، فحص الخوادم، والإعدادات العامة للمنصة',
                'description_en' => 'Database backups, server diagnostics, maintenance mode, and global platform settings',
                'permissions' => [
                    'manage-settings' => [
                        'label_ar' => 'إعدادات المنصة العامة',
                        'label_en' => 'Platform Global Settings',
                        'desc_ar' => 'ضبط اسم المنصة، بوابات الدفع الإلكتروني، إعدادات خادم البريد، والشروط',
                        'desc_en' => 'Configure site metadata, payment gateway credentials, SMTP mail server, and terms',
                        'badge_ar' => 'إدارة عليا',
                        'badge_en' => 'High Authority',
                    ],
                    'manage-backups' => [
                        'label_ar' => 'النسخ الاحتياطي والأرشيف',
                        'label_en' => 'Manage System Backups',
                        'desc_ar' => 'توليد، تنزيل، واسترجاع النسخ الاحتياطية لقواعد البيانات والملفات السحابية',
                        'desc_en' => 'Generate, download, and delete full database dumps and cloud file backups',
                        'badge_ar' => 'أمان وحماية',
                        'badge_en' => 'Security',
                    ],
                    'view-system-health' => [
                        'label_ar' => 'مراقبة مؤشرات صحة النظام',
                        'label_en' => 'Monitor System Health',
                        'desc_ar' => 'استعراض لوحة تشخيص الخوادم، أداء قواعد البيانات، ومساحات التخزين',
                        'desc_en' => 'Inspect real-time health checks, server memory, CPU load, and DB connections',
                        'badge_ar' => 'تقني',
                        'badge_en' => 'Technical',
                    ],
                    'manage-system-health' => [
                        'label_ar' => 'أدوات صيانة وتشخيص النظام',
                        'label_en' => 'System Maintenance & Diagnostics',
                        'desc_ar' => 'مسح الكاش، إعادة تشغيل طوابير المهام، وتفعيل أو إلغاء وضع الصيانة',
                        'desc_en' => 'Clear app cache, restart background queues, and toggle platform maintenance mode',
                        'badge_ar' => 'تقني وحرج',
                        'badge_en' => 'Critical',
                    ],
                ]
            ],
        ];
    }

    /**
     * Map a single permission name to its enriched metadata.
     */
    public static function getMetadata(string $permissionName): array
    {
        $defs = self::definitions();
        foreach ($defs as $catKey => $catData) {
            if (isset($catData['permissions'][$permissionName])) {
                $meta = $catData['permissions'][$permissionName];
                $meta['category_key'] = $catKey;
                $meta['category_name_ar'] = $catData['name_ar'];
                $meta['category_name_en'] = $catData['name_en'];
                $meta['badge_color'] = $catData['badge_color'];
                return $meta;
            }
        }

        // Fallback for custom or unmapped permissions
        $formattedName = ucwords(str_replace(['-', '_'], ' ', $permissionName));
        return [
            'label_ar' => $formattedName,
            'label_en' => $formattedName,
            'desc_ar' => 'صلاحية نظام إضافية مخصصة داخل المنصة',
            'desc_en' => 'Custom additional system permission in the platform',
            'badge_ar' => 'مخصص',
            'badge_en' => 'Custom',
            'category_key' => 'other',
            'category_name_ar' => 'صلاحيات عامة وإضافية',
            'category_name_en' => 'Other & Custom Permissions',
            'badge_color' => 'secondary',
        ];
    }

    /**
     * Group a collection/array of Spatie Permission models by category.
     * Enriches each permission item with localized Arabic and English fields.
     */
    public static function groupPermissions($permissions, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $isEn = ($locale === 'en');

        $defs = self::definitions();
        $grouped = [];

        // Initialize predefined categories with empty permissions list
        foreach ($defs as $catKey => $catData) {
            $grouped[$catKey] = [
                'key' => $catKey,
                'name' => $isEn ? $catData['name_en'] : $catData['name_ar'],
                'sub_name' => $isEn ? $catData['name_ar'] : $catData['name_en'],
                'name_ar' => $catData['name_ar'],
                'name_en' => $catData['name_en'],
                'icon' => $catData['icon'],
                'badge_color' => $catData['badge_color'],
                'description' => $isEn ? ($catData['description_en'] ?? $catData['description_ar']) : $catData['description_ar'],
                'permissions' => [],
            ];
        }

        $otherGroup = [
            'key' => 'other',
            'name' => $isEn ? 'Other & Custom Permissions' : 'صلاحيات عامة وإضافية',
            'sub_name' => $isEn ? 'صلاحيات عامة وإضافية' : 'Other & Custom Permissions',
            'name_ar' => 'صلاحيات عامة وإضافية',
            'name_en' => 'Other & Custom Permissions',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>',
            'badge_color' => 'secondary',
            'description' => $isEn ? 'Dynamically registered system permissions outside standard modules' : 'أي صلاحيات إضافية يتم إنشاؤها ديناميكياً خارج القائمة القياسية',
            'permissions' => [],
        ];

        foreach ($permissions as $perm) {
            $meta = self::getMetadata($perm->name);

            $label = $isEn ? ($meta['label_en'] ?? $meta['label_ar']) : $meta['label_ar'];
            $desc = $isEn ? ($meta['desc_en'] ?? $meta['desc_ar']) : $meta['desc_ar'];
            $badge = $isEn ? ($meta['badge_en'] ?? $meta['badge_ar']) : $meta['badge_ar'];

            $enriched = (object) [
                'id' => $perm->id,
                'name' => $perm->name,
                'label' => $label,
                'label_ar' => $meta['label_ar'],
                'label_en' => $meta['label_en'],
                'desc' => $desc,
                'desc_ar' => $meta['desc_ar'],
                'desc_en' => $meta['desc_en'] ?? $meta['desc_ar'],
                'badge' => $badge,
                'badge_color' => $meta['badge_color'] ?? 'primary',
            ];

            $catKey = $meta['category_key'];
            if (isset($grouped[$catKey])) {
                $grouped[$catKey]['permissions'][] = $enriched;
            } else {
                $otherGroup['permissions'][] = $enriched;
            }
        }

        if (!empty($otherGroup['permissions'])) {
            $grouped['other'] = $otherGroup;
        }

        // Filter out any categories that have 0 permissions in the database
        return array_filter($grouped, function ($cat) {
            return count($cat['permissions']) > 0;
        });
    }

    /**
     * Automatically ensure all registered permissions exist in the database and are synced to the admin role.
     */
    public static function ensureSystemPermissions(): void
    {
        try {
            $defs = self::definitions();
            $allPermNames = [];
            foreach ($defs as $cat) {
                foreach ($cat['permissions'] as $name => $pMeta) {
                    $allPermNames[] = $name;
                }
            }

            $existing = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
            $missing = array_diff($allPermNames, $existing);

            if (!empty($missing)) {
                foreach ($missing as $m) {
                    \Spatie\Permission\Models\Permission::findOrCreate($m, 'web');
                }
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
                if ($adminRole) {
                    $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Permission auto-sync notice: ' . $e->getMessage());
        }
    }
}
