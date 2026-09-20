<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
   public function index()
    {
        $stats = [
            'total_settings' => \App\Models\Setting::count(),
            'last_updated' => \App\Models\Setting::latest('updated_at')->first()?->updated_at->diffForHumans() ?? __('Never'),
        ];
        
        $liveAuctions = \App\Models\Auction::whereIn('status', ['live', 'scheduled'])->latest()->get();
        $formTemplates = \App\Models\FormTemplate::where('is_active', true)->get();

        return view('admin.settings.index', compact('stats', 'liveAuctions', 'formTemplates'));
    }

    public function update(Request $request)
    {
        \Log::info('Settings Update Request:', $request->all());
        try {
            // Validate request data
            $request->validate([
                'site_name_en' => 'nullable|string|max:255',
                'site_name_ar' => 'nullable|string|max:255',
                'site_description_en' => 'nullable|string',
                'site_description_ar' => 'nullable|string',
                'hero_title_en' => 'nullable|string',
                'hero_title_ar' => 'nullable|string',
                'hero_desc_en' => 'nullable|string',
                'hero_desc_ar' => 'nullable|string',
                'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
                'hero_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'page_header_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:20',
                'facebook_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'tiktok_url' => 'nullable|url',
                'snapchat_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'telegram_url' => 'nullable|url',
                'whatsapp_number' => 'nullable|string|max:50',
                'android_url' => 'nullable|url',
                'ios_url' => 'nullable|url',
                'android_version' => 'nullable|string|max:20',
                'ios_version' => 'nullable|string|max:20',
                'android_min_version' => 'nullable|string|max:20',
                'ios_min_version' => 'nullable|string|max:20',
                'support_email' => 'nullable|email',
                'support_whatsapp_url' => 'nullable|url',
                'primary_color' => 'nullable|string|max:7',
                'app_min_version' => 'nullable|string|max:20',
                'maintenance_mode' => 'nullable|in:0,1',
                'development_mode' => 'nullable|in:0,1',
                'development_mode_message' => 'nullable|string|max:500',
                'story_en' => 'nullable|string',
                'story_ar' => 'nullable|string',
                'mission_en' => 'nullable|string',
                'mission_ar' => 'nullable|string',
                'vision_en' => 'nullable|string',
                'vision_ar' => 'nullable|string',
                'show_homepage_stats' => 'nullable|in:0,1',
                'show_stat_bidders' => 'nullable|in:0,1',
                'show_stat_cars' => 'nullable|in:0,1',
                'show_stat_satisfaction' => 'nullable|in:0,1',
                'show_facebook' => 'nullable|in:0,1',
                'show_twitter' => 'nullable|in:0,1',
                'show_instagram' => 'nullable|in:0,1',
                'show_linkedin' => 'nullable|in:0,1',
                'show_tiktok' => 'nullable|in:0,1',
                'show_snapchat' => 'nullable|in:0,1',
                'show_youtube' => 'nullable|in:0,1',
                'show_whatsapp' => 'nullable|in:0,1',
                'show_telegram' => 'nullable|in:0,1',
                'stats_active_bidders' => 'nullable|string|max:50',
                'stats_active_bidders_unit' => 'nullable|string|max:20',
                'stats_cars_sold' => 'nullable|string|max:50',
                'stats_cars_sold_unit' => 'nullable|string|max:20',
                'stats_satisfaction' => 'nullable|string|max:50',
                'stats_satisfaction_unit' => 'nullable|string|max:20',
                'hero_auction_id' => 'nullable|exists:auctions,id',
                'seller_request_template_id' => 'nullable|exists:form_templates,id',
                'hyperpay_enabled' => 'nullable|in:0,1',
                'hyperpay_mode' => 'nullable|in:test,live',
                'hyperpay_access_token' => 'nullable|string',
                'hyperpay_entity_id_mada' => 'nullable|string',
                'hyperpay_entity_id_visa_master' => 'nullable|string',
                'hyperpay_entity_id_apple_pay' => 'nullable|string',
                'hyperpay_webhook_secret' => 'nullable|string',
                'hyperpay_min_deposit' => 'nullable|numeric|min:1',
                'hyperpay_max_deposit' => 'nullable|numeric|min:1',
                // SMTP & Mail Settings
                'mail_mailer' => 'nullable|in:smtp,log,sendmail',
                'mail_host' => 'nullable|string|max:255',
                'mail_port' => 'nullable|integer',
                'mail_username' => 'nullable|string|max:255',
                'mail_password' => 'nullable|string|max:255',
                'mail_encryption' => 'nullable|string|max:20',
                'mail_from_address' => 'nullable|email|max:255',
                'mail_from_name' => 'nullable|string|max:255',
            ]);

            $data = $request->except(['_token', 'site_logo', 'site_favicon', 'hero_bg', 'page_header_bg']);

            // Explicitly handle checkboxes that might be unchecked
            $checkboxes = [
                'maintenance_mode', 'development_mode', 'show_homepage_stats',
                'show_stat_bidders', 'show_stat_cars', 'show_stat_satisfaction',
                'show_facebook', 'show_twitter', 'show_instagram', 'show_linkedin',
                'show_tiktok', 'show_snapchat', 'show_youtube', 'show_whatsapp', 'show_telegram',
                'hyperpay_enabled'
            ];
            foreach ($checkboxes as $checkbox) {
                if (!array_key_exists($checkbox, $data)) {
                    $data[$checkbox] = '0';
                }
            }

            // Update text settings
            foreach ($data as $key => $value) {
                Setting::set($key, $value);
            }

            // Immediately apply any updated mail credentials
            \App\Services\MailConfigService::applySettings();

            // Handle File Uploads
            $logoPath = $this->handleFileUpload($request, 'site_logo');
            $faviconPath = $this->handleFileUpload($request, 'site_favicon');
            $heroBgPath = $this->handleFileUpload($request, 'hero_bg');
            $pageHeaderBgPath = $this->handleFileUpload($request, 'page_header_bg');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Settings updated successfully!'),
                    'logo_url' => $logoPath ? asset($logoPath) : null,
                    'favicon_url' => $faviconPath ? asset($faviconPath) : null,
                    'hero_bg_url' => $heroBgPath ? asset($heroBgPath) : null,
                    'page_header_bg_url' => $pageHeaderBgPath ? asset($pageHeaderBgPath) : null,
                ]);
            }

            return redirect()->back()->with('success', __('Settings updated successfully!'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while updating settings: ') . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', __('An error occurred while updating settings.'));
        }
    }

    /**
     * Send a test email to verify SMTP configuration from admin dashboard.
     */
    public function sendTestEmail(Request $request, \App\Services\MailService $mailService)
    {
        $request->validate([
            'test_email' => 'required|email',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:20',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        $overrides = [];
        $smtpFields = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
        foreach ($smtpFields as $field) {
            if ($request->filled($field)) {
                $val = $request->input($field);
                $overrides[$field] = $val;
                Setting::set($field, $val);
            }
        }

        // If from_address was empty or dummy example.com, fix it
        $username = $overrides['mail_username'] ?? Setting::get('mail_username');
        $fromAddress = $overrides['mail_from_address'] ?? Setting::get('mail_from_address');
        if (empty($fromAddress) || str_contains(strtolower($fromAddress), 'example.com')) {
            if (!empty($username) && filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $fromAddress = $username;
                $overrides['mail_from_address'] = $fromAddress;
                Setting::set('mail_from_address', $fromAddress);
            }
        }

        $result = $mailService->sendTestEmail($request->test_email, !empty($overrides) ? $overrides : null);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    private function handleFileUpload($request, $key)
    {
        if ($request->hasFile($key)) {
            // Delete old file if exists
            $oldFile = Setting::get($key);
            if ($oldFile && file_exists(public_path($oldFile))) {
                @unlink(public_path($oldFile));
            }

            // Upload new file
            $file = $request->file($key);
            $fileName = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/settings'), $fileName);

            // Save path to DB
            $path = 'images/settings/' . $fileName;
            Setting::set($key, $path);
            return $path;
        }
        return null;
    }
}
