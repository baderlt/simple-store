<?php

return [
    'theme' => [
        'default' => 'modern',
        'available' => ['minimal', 'modern', 'luxury', 'dark', 'fashion', 'electronics', 'grocery', 'restaurant'],
    ],

    'homepage_sections' => [
        [
            'key' => 'hero',
            'name' => 'Hero Section',
            'layout' => 'split',
            'position' => 10,
            'settings' => ['type' => 'hero'],
        ],
        [
            'key' => 'hero_slider',
            'name' => 'Hero Slider',
            'layout' => 'full',
            'position' => 20,
            'settings' => ['type' => 'slider', 'banner_position' => 'hero'],
        ],
        [
            'key' => 'categories',
            'name' => 'Categories',
            'layout' => 'grid',
            'position' => 30,
            'settings' => ['type' => 'categories', 'limit' => 8],
        ],
        [
            'key' => 'featured_products',
            'name' => 'Featured Products',
            'layout' => 'carousel',
            'position' => 40,
            'settings' => ['type' => 'featured_products', 'limit' => 8],
        ],
        [
            'key' => 'promotion_banners',
            'name' => 'Promotional Cards',
            'layout' => 'cards',
            'position' => 50,
            'settings' => ['type' => 'promotional_cards', 'banner_position' => 'middle'],
        ],
        [
            'key' => 'testimonials',
            'name' => 'Testimonials',
            'layout' => 'grid',
            'position' => 60,
            'settings' => ['type' => 'testimonials', 'items' => []],
        ],
        [
            'key' => 'brands',
            'name' => 'Brands',
            'layout' => 'slider',
            'position' => 70,
            'settings' => ['type' => 'brands', 'items' => []],
        ],
        [
            'key' => 'newsletter',
            'name' => 'Newsletter',
            'layout' => 'inline',
            'position' => 80,
            'settings' => ['type' => 'newsletter'],
        ],
        [
            'key' => 'contact_map',
            'name' => 'Contact & Map',
            'layout' => 'split',
            'position' => 90,
            'settings' => ['type' => 'contact_map'],
        ],
    ],
];
