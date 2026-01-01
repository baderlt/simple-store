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
            'email' => 'admin@parapharmacie.ma',
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

        // Create categories
        $categories = [
            [
                'name' => 'Soins Visage',
                'slug' => 'soins-visage',
                'description' => 'Produits de soin pour le visage',
                'is_active' => true,
            ],
            [
                'name' => 'Soins Corps',
                'slug' => 'soins-corps',
                'description' => 'Produits de soin pour le corps',
                'is_active' => true,
            ],
            [
                'name' => 'Cheveux',
                'slug' => 'cheveux',
                'description' => 'Produits pour les cheveux',
                'is_active' => true,
            ],
            [
                'name' => 'Hygiène',
                'slug' => 'hygiene',
                'description' => 'Produits d\'hygiène',
                'is_active' => true,
            ],
            [
                'name' => 'Bébé & Maman',
                'slug' => 'bebe-maman',
                'description' => 'Produits pour bébé et maman',
                'is_active' => true,
            ],
            [
                'name' => 'Vitamines & Compléments',
                'slug' => 'vitamines',
                'description' => 'Vitamines et compléments alimentaires',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create sample products
        $products = [
            [
                'category_id' => 1,
                'name' => 'Crème Hydratante Visage',
                'slug' => 'creme-hydratante-visage',
                'description' => 'Crème hydratante pour tous types de peaux',
                'price' => 150.00,
                'stock_quantity' => 50,
                'low_stock_alert' => 10,
                'sku' => 'CREAM-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Lait Corps Nourrissant',
                'slug' => 'lait-corps-nourrissant',
                'description' => 'Lait corps nourrissant et hydratant',
                'price' => 120.00,
                'stock_quantity' => 30,
                'low_stock_alert' => 10,
                'sku' => 'BODY-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Shampoing Réparateur',
                'slug' => 'shampoing-reparateur',
                'description' => 'Shampoing pour cheveux abîmés',
                'price' => 95.00,
                'stock_quantity' => 40,
                'low_stock_alert' => 10,
                'sku' => 'HAIR-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Gel Douche Doux',
                'slug' => 'gel-douche-doux',
                'description' => 'Gel douche pour toute la famille',
                'price' => 65.00,
                'stock_quantity' => 60,
                'low_stock_alert' => 15,
                'sku' => 'HYG-001',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => 5,
                'name' => 'Lingettes Bébé',
                'slug' => 'lingettes-bebe',
                'description' => 'Lingettes douces pour bébé',
                'price' => 45.00,
                'stock_quantity' => 80,
                'low_stock_alert' => 20,
                'sku' => 'BABY-001',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => 6,
                'name' => 'Vitamine C 1000mg',
                'slug' => 'vitamine-c-1000mg',
                'description' => 'Complément alimentaire vitamine C',
                'price' => 180.00,
                'stock_quantity' => 25,
                'low_stock_alert' => 10,
                'sku' => 'VIT-001',
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }

        // Settings
        Setting::set('store_name', 'Parapharmacie Santé');
        Setting::set('phone', '+212 5XX-XXXXXX');
        Setting::set('email', 'contact@parapharmacie.ma');
        Setting::set('address', '123 Avenue Mohammed V, Casablanca');
        Setting::set('working_hours', 'Lun-Sam: 9h00-20h00 | Dim: 10h00-18h00');
        Setting::set('delivery_fee', '30');
        Setting::set('facebook_url', '#');
        Setting::set('instagram_url', '#');
    }
}
