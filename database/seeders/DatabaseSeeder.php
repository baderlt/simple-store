<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@store.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // Create regular user
        User::create([
            'name' => 'Client Test',
            'email' => 'client@test.ma',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // Create brand-focused categories and sample products.
        $catalog = [
            [
                'category' => [
                    'name' => 'Miels d’Exception',
                    'slug' => 'miels-exception',
                    'description' => 'Miels purs et variétés de terroir soigneusement sélectionnés',
                    'is_active' => true,
                ],
                'product' => [
                    'name' => 'Miel de Thym Pur',
                    'slug' => 'miel-thym-pur',
                    'description' => 'Un miel de caractère aux notes aromatiques intenses, récolté avec soin.',
                    'price' => 145.00,
                    'stock_quantity' => 40,
                    'low_stock_alert' => 8,
                    'sku' => 'MIEL-THYM-001',
                    'is_active' => true,
                    'is_featured' => true,
                ],
            ],
            [
                'category' => [
                    'name' => 'Thés Originaux',
                    'slug' => 'thes-originaux',
                    'description' => 'Thés authentiques, mélanges parfumés et infusions de caractère',
                    'is_active' => true,
                ],
                'product' => [
                    'name' => 'Thé Vert à la Menthe',
                    'slug' => 'the-vert-menthe',
                    'description' => 'Un thé vert frais et équilibré, relevé par des feuilles de menthe.',
                    'price' => 78.00,
                    'stock_quantity' => 55,
                    'low_stock_alert' => 10,
                    'sku' => 'THE-MENTHE-001',
                    'is_active' => true,
                    'is_featured' => true,
                ],
            ],
            [
                'category' => [
                    'name' => 'Parfums',
                    'slug' => 'parfums',
                    'description' => 'Senteurs raffinées, eaux de parfum et créations orientales',
                    'is_active' => true,
                ],
                'product' => [
                    'name' => 'Eau de Parfum Ambre & Oud',
                    'slug' => 'eau-parfum-ambre-oud',
                    'description' => 'Une fragrance chaleureuse où l’ambre rencontre la profondeur du oud.',
                    'price' => 320.00,
                    'stock_quantity' => 24,
                    'low_stock_alert' => 6,
                    'sku' => 'PARF-OUD-001',
                    'is_active' => true,
                    'is_featured' => true,
                ],
            ],
            [
                'category' => [
                    'name' => 'Épicerie Bio',
                    'slug' => 'epicerie-bio',
                    'description' => 'Produits biologiques pour une cuisine naturelle et savoureuse',
                    'is_active' => true,
                ],
                'product' => [
                    'name' => 'Huile d’Argan Alimentaire Bio',
                    'slug' => 'huile-argan-alimentaire-bio',
                    'description' => 'Huile d’argan biologique au goût délicatement toasté.',
                    'price' => 165.00,
                    'stock_quantity' => 30,
                    'low_stock_alert' => 7,
                    'sku' => 'BIO-ARGAN-001',
                    'is_active' => true,
                    'is_featured' => true,
                ],
            ],
            [
                'category' => [
                    'name' => 'Beauté Naturelle',
                    'slug' => 'beaute-naturelle',
                    'description' => 'Rituels de beauté inspirés par des ingrédients d’origine naturelle',
                    'is_active' => true,
                ],
                'product' => [
                    'name' => 'Savon Noir à l’Eucalyptus',
                    'slug' => 'savon-noir-eucalyptus',
                    'description' => 'Un soin traditionnel à la texture onctueuse et au parfum frais.',
                    'price' => 69.00,
                    'stock_quantity' => 45,
                    'low_stock_alert' => 10,
                    'sku' => 'BEAUTE-SAVON-001',
                    'is_active' => true,
                    'is_featured' => false,
                ],
            ],
            [
                'category' => [
                    'name' => 'Coffrets Cadeaux',
                    'slug' => 'coffrets-cadeaux',
                    'description' => 'Compositions élégantes à offrir pour toutes les occasions',
                    'is_active' => true,
                ],
                'product' => [
                    'name' => 'Coffret Découverte Maison Dorée',
                    'slug' => 'coffret-decouverte-maison-doree',
                    'description' => 'Une sélection de miel, thé et douceurs présentée dans un coffret raffiné.',
                    'price' => 285.00,
                    'stock_quantity' => 18,
                    'low_stock_alert' => 5,
                    'sku' => 'COFFRET-001',
                    'is_active' => true,
                    'is_featured' => true,
                ],
            ],
        ];

        foreach ($catalog as $item) {
            $category = Category::create($item['category']);
            $category->products()->create($item['product']);
        }

        // Settings
        Setting::set('store_name', 'Maison Dorée');
        Setting::set('phone', '+212 5XX-XXXXXX');
        Setting::set('email', 'contact@maisondoree.ma');
        Setting::set('address', '123 Avenue Mohammed V, Casablanca');
        Setting::set('working_hours', 'Lun-Sam: 9h00-20h00 | Dim: 10h00-18h00');
        Setting::set('delivery_fee', '30');
        Setting::set('primary_color', '#B7791F');
        Setting::set('secondary_color', '#3D2B1F');
        Setting::set('accent_color', '#F4B400');
        Setting::set('background_color', '#FFFCF5');
        Setting::set('button_color', '#B7791F');
        Setting::set('hero_title_prefix', 'Saveurs');
        Setting::set('hero_title_emphasis', 'authentiques');
        Setting::set('hero_title_suffix', '& senteurs raffinées');
        Setting::set('hero_subtitle', 'Miels de terroir, thés originaux, parfums et produits bio réunis dans une sélection élégante.');
        Setting::set('seo_title', 'Maison Dorée | Miels, thés, parfums et produits bio');
        Setting::set('seo_description', 'Découvrez nos miels d’exception, thés originaux, parfums raffinés et produits bio sélectionnés avec soin.');
        Setting::set('footer_text', 'Miels, thés, parfums et produits bio sélectionnés avec soin et présentés avec élégance.');
        Setting::set('instagram_url', null);
    }
}
