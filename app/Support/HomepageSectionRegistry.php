<?php

namespace App\Support;

class HomepageSectionRegistry
{
    public static function defaults(): array
    {
        return config('storefront.homepage_sections', []);
    }

    public static function types(): array
    {
        return [
            'hero' => 'Hero / introductory banner',
            'slider' => 'Image or marketing slider',
            'featured_products' => 'Featured products carousel/grid',
            'categories' => 'Category blocks',
            'promotional_cards' => 'Promotional cards',
            'testimonials' => 'Customer testimonials',
            'brands' => 'Brand/logo strip',
            'newsletter' => 'Newsletter signup',
            'custom_html' => 'Custom HTML block',
            'contact_map' => 'Contact and map section',
        ];
    }
}
