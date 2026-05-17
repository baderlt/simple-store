<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\StoreSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = app(StoreSettingsService::class)->all();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'working_hours' => 'nullable|string|max:255',
            'timezone' => 'nullable|timezone',
            'default_locale' => 'nullable|in:en,fr,ar',
            'supported_locales' => 'nullable|array',
            'supported_locales.*' => 'in:en,fr,ar',
            'currency' => 'nullable|string|max:10',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'accent_color' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:120',
            'button_style' => 'nullable|in:rounded,pill,square',
            'border_radius' => 'nullable|string|max:20',
            'theme_mode' => 'nullable|in:light,dark,system',
            'header_layout' => 'nullable|in:classic,centered,minimal',
            'footer_layout' => 'nullable|in:classic,compact,columns',
            'product_card_style' => 'nullable|in:standard,compact,overlay',
            'delivery_fee' => 'nullable|numeric|min:0',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
            'delivery_zone' => 'nullable|string|max:255',
            'delivery_time' => 'nullable|string|max:100',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'hero_primary_button_text' => 'nullable|string|max:100',
            'hero_primary_button_url' => 'nullable|string|max:500',
            'hero_secondary_button_text' => 'nullable|string|max:100',
            'hero_secondary_button_url' => 'nullable|string|max:500',
            'newsletter_title' => 'nullable|string|max:255',
            'newsletter_description' => 'nullable|string|max:500',
            'footer_description' => 'nullable|string|max:500',
            'about_title' => 'nullable|string|max:255',
            'about_body' => 'nullable|string|max:5000',
            'about_seo_description' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'tiktok_url' => 'nullable|url|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'maps_link' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,ico|max:1024',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $jsonFields = ['supported_locales'];
        $numberFields = ['delivery_fee', 'free_delivery_threshold', 'tax_rate', 'latitude', 'longitude'];
        $imageFields = ['logo', 'favicon', 'footer_logo'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $oldPath = Setting::get($field);
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }

                Setting::set($field, $request->file($field)->store('settings/brand', 'public'), 'image');
            }

            unset($validated[$field]);
        }

        foreach ($validated as $key => $value) {
            $type = 'text';
            if (in_array($key, $jsonFields, true)) {
                $type = 'json';
                $value = array_values($value ?? []);
            } elseif (in_array($key, $numberFields, true)) {
                $type = 'number';
            } elseif (str_contains($key, 'description') || in_array($key, ['address', 'footer_description', 'about_body'], true)) {
                $type = 'textarea';
            }

            Setting::set($key, in_array($key, $jsonFields, true) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value, $type);
        }

        app(StoreSettingsService::class)->flush();

        return back()->with('success', __('admin.settings_saved'));
    }

    public function deleteLogo(Request $request)
    {
        $key = $request->input('asset', 'logo');
        abort_unless(in_array($key, ['logo', 'favicon', 'footer_logo'], true), 404);

        $path = Setting::get($key);
        if ($path) {
            Storage::disk('public')->delete($path);
        }

        Setting::remove($key);
        app(StoreSettingsService::class)->flush();

        return back()->with('success', __('admin.logo_deleted'));
    }

    public function getJson()
    {
        return response()->json([
            'success' => true,
            'data' => app(StoreSettingsService::class)->all(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function getSetting($key)
    {
        $settings = app(StoreSettingsService::class)->all();

        if (! array_key_exists($key, $settings)) {
            return response()->json(['success' => false, 'message' => __('admin.setting_not_found')], 404);
        }

        return response()->json(['success' => true, 'data' => ['key' => $key, 'value' => $settings[$key]]]);
    }

    public function updateSingle(Request $request, $key)
    {
        $allowed = [
            'store_name', 'tagline', 'store_description', 'seo_title', 'seo_description', 'seo_keywords',
            'phone', 'email', 'whatsapp', 'address', 'working_hours', 'timezone', 'default_locale', 'currency',
            'primary_color', 'secondary_color', 'accent_color', 'font_family', 'button_style', 'border_radius',
            'theme_mode', 'header_layout', 'footer_layout', 'product_card_style', 'delivery_fee',
            'free_delivery_threshold', 'delivery_zone', 'delivery_time', 'tax_rate', 'hero_badge', 'hero_title',
            'hero_subtitle', 'hero_primary_button_text', 'hero_primary_button_url', 'hero_secondary_button_text',
            'hero_secondary_button_url', 'newsletter_title', 'newsletter_description', 'footer_description', 'about_title',
            'about_body', 'about_seo_description', 'facebook_url', 'instagram_url', 'twitter_url', 'linkedin_url', 'youtube_url', 'tiktok_url',
            'latitude', 'longitude', 'maps_link',
        ];

        abort_unless(in_array($key, $allowed, true), 400);

        $request->validate(['value' => 'nullable']);
        $numberFields = ['delivery_fee', 'free_delivery_threshold', 'tax_rate', 'latitude', 'longitude'];
        $type = in_array($key, $numberFields, true) ? 'number' : 'text';

        Setting::set($key, $request->input('value'), $type);
        app(StoreSettingsService::class)->flush();

        return response()->json(['success' => true, 'message' => __('admin.setting_updated')]);
    }
}
