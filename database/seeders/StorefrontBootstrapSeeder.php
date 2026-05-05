<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class StorefrontBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Simple Store', 'type' => 'text'],
            ['key' => 'tagline', 'value' => 'Premium products for every lifestyle', 'type' => 'text'],
            ['key' => 'primary_color', 'value' => '#2563EB', 'type' => 'text'],
            ['key' => 'secondary_color', 'value' => '#0F172A', 'type' => 'text'],
            ['key' => 'accent_color', 'value' => '#F59E0B', 'type' => 'text'],
            ['key' => 'currency', 'value' => 'USD', 'type' => 'text'],
            ['key' => 'default_locale', 'value' => 'en', 'type' => 'text'],
            ['key' => 'supported_locales', 'value' => json_encode(['en', 'fr', 'es', 'ar']), 'type' => 'json'],
            ['key' => 'hero_title', 'value' => 'Discover products curated for your niche', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'A configurable commerce experience for fashion, electronics, beauty, and more.', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        foreach (config('storefront.homepage_sections', []) as $section) {
            HomepageSection::updateOrCreate(['key' => $section['key']], $section + ['is_enabled' => true]);
        }
    }
}
