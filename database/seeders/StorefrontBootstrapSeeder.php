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
            ['key' => 'tagline', 'value' => 'Commerce for every niche', 'type' => 'text'],
            ['key' => 'store_description', 'value' => 'A flexible ecommerce storefront for physical products, digital products, local businesses, restaurants, and future niches.', 'type' => 'textarea'],
            ['key' => 'seo_title', 'value' => 'Simple Store', 'type' => 'text'],
            ['key' => 'seo_description', 'value' => 'A configurable online store for any business niche.', 'type' => 'textarea'],
            ['key' => 'seo_keywords', 'value' => 'ecommerce, online store, products, shopping', 'type' => 'text'],
            ['key' => 'primary_color', 'value' => '#2563EB', 'type' => 'text'],
            ['key' => 'secondary_color', 'value' => '#0F172A', 'type' => 'text'],
            ['key' => 'accent_color', 'value' => '#F59E0B', 'type' => 'text'],
            ['key' => 'font_family', 'value' => 'Inter', 'type' => 'text'],
            ['key' => 'button_style', 'value' => 'rounded', 'type' => 'text'],
            ['key' => 'border_radius', 'value' => '1rem', 'type' => 'text'],
            ['key' => 'theme_mode', 'value' => 'light', 'type' => 'text'],
            ['key' => 'header_layout', 'value' => 'classic', 'type' => 'text'],
            ['key' => 'footer_layout', 'value' => 'classic', 'type' => 'text'],
            ['key' => 'product_card_style', 'value' => 'standard', 'type' => 'text'],
            ['key' => 'currency', 'value' => 'USD', 'type' => 'text'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'text'],
            ['key' => 'default_locale', 'value' => 'en', 'type' => 'text'],
            ['key' => 'supported_locales', 'value' => json_encode(['en', 'fr', 'ar']), 'type' => 'json'],
            ['key' => 'hero_badge', 'value' => 'Configurable ecommerce platform', 'type' => 'text'],
            ['key' => 'hero_title', 'value' => 'Build a storefront for any niche', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Customize branding, content, product data, homepage sections, and operations from one reusable commerce back office.', 'type' => 'textarea'],
            ['key' => 'hero_primary_button_text', 'value' => 'Shop products', 'type' => 'text'],
            ['key' => 'hero_primary_button_url', 'value' => '/products', 'type' => 'text'],
            ['key' => 'hero_secondary_button_text', 'value' => 'Browse categories', 'type' => 'text'],
            ['key' => 'hero_secondary_button_url', 'value' => '/categories', 'type' => 'text'],
            ['key' => 'newsletter_title', 'value' => 'Stay in the loop', 'type' => 'text'],
            ['key' => 'newsletter_description', 'value' => 'Get launches, offers, and store updates.', 'type' => 'textarea'],
            ['key' => 'footer_description', 'value' => 'Your configurable ecommerce partner for every niche.', 'type' => 'textarea'],
            ['key' => 'about_title', 'value' => 'About our store', 'type' => 'text'],
            ['key' => 'about_body', 'value' => 'Use the admin settings area to replace this content with your brand story, mission, services, policies, and local business details.', 'type' => 'textarea'],
            ['key' => 'about_seo_description', 'value' => 'Learn more about our store.', 'type' => 'textarea'],
            ['key' => 'email', 'value' => 'contact@example.com', 'type' => 'text'],
            ['key' => 'delivery_fee', 'value' => '0', 'type' => 'number'],
            ['key' => 'tax_rate', 'value' => '0', 'type' => 'number'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        foreach (config('storefront.homepage_sections', []) as $section) {
            HomepageSection::updateOrCreate(
                ['key' => $section['key']],
                $section + ['is_enabled' => true]
            );
        }
    }
}
