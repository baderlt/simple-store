<?php

return [
    'theme' => [
        'default' => 'modern',
        'available' => ['minimal', 'modern', 'luxury', 'dark', 'fashion', 'electronics'],
    ],

    'homepage_sections' => [
        ['key' => 'hero_slider', 'name' => 'Hero Slider', 'layout' => 'full', 'position' => 10],
        ['key' => 'categories', 'name' => 'Categories', 'layout' => 'grid', 'position' => 20],
        ['key' => 'featured_products', 'name' => 'Featured Products', 'layout' => 'carousel', 'position' => 30],
        ['key' => 'flash_sales', 'name' => 'Flash Sales', 'layout' => 'carousel', 'position' => 40],
        ['key' => 'promotion_banners', 'name' => 'Promotion Banners', 'layout' => 'stack', 'position' => 50],
        ['key' => 'testimonials', 'name' => 'Testimonials', 'layout' => 'carousel', 'position' => 60],
        ['key' => 'brands', 'name' => 'Brands', 'layout' => 'slider', 'position' => 70],
        ['key' => 'faq', 'name' => 'FAQ', 'layout' => 'accordion', 'position' => 80],
        ['key' => 'newsletter', 'name' => 'Newsletter', 'layout' => 'inline', 'position' => 90],
        ['key' => 'contact_map', 'name' => 'Contact Map', 'layout' => 'split', 'position' => 100],
    ],
];
