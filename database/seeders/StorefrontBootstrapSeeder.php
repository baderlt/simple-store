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
            ['key' => 'store_name', 'value' => 'Maison Dorée', 'type' => 'text'],
            ['key' => 'tagline', 'value' => 'Natural elegance, crafted for every lifestyle', 'type' => 'text'],
            ['key' => 'primary_color', 'value' => '#B7791F', 'type' => 'text'],
            ['key' => 'secondary_color', 'value' => '#3D2B1F', 'type' => 'text'],
            ['key' => 'accent_color', 'value' => '#F4B400', 'type' => 'text'],
            ['key' => 'background_color', 'value' => '#FFFCF5', 'type' => 'text'],
            ['key' => 'button_color', 'value' => '#B7791F', 'type' => 'text'],
            ['key' => 'seo_title', 'value' => 'Maison Dorée | Premium Products', 'type' => 'text'],
            ['key' => 'seo_description', 'value' => 'Discover elegant honey, perfume, beauty, food, and lifestyle products.', 'type' => 'text'],
            ['key' => 'footer_text', 'value' => 'Premium products selected with care and presented with elegance.', 'type' => 'text'],
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
