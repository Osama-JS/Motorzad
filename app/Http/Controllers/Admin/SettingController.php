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
        return view('admin.settings.index', compact('stats'));
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
                'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
                'hero_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'page_header_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:20',
                'facebook_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'android_url' => 'nullable|url',
                'ios_url' => 'nullable|url',
                'primary_color' => 'nullable|string|max:7',
                'app_min_version' => 'nullable|string|max:20',
                'maintenance_mode' => 'nullable|in:0,1',
                'show_hotels_page' => 'nullable|in:0,1',
                'story_en' => 'nullable|string',
                'story_ar' => 'nullable|string',
                'mission_en' => 'nullable|string',
                'mission_ar' => 'nullable|string',
                'vision_en' => 'nullable|string',
                'vision_ar' => 'nullable|string',
            ]);

            $data = $request->except(['_token', 'site_logo', 'site_favicon', 'hero_bg', 'page_header_bg']);

            // Update text settings
            foreach ($data as $key => $value) {
                Setting::set($key, $value);
            }

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
