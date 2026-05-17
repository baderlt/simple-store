<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(StorefrontBootstrapSeeder::class);

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'is_admin' => true]
        );

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Demo Customer', 'password' => bcrypt('password'), 'is_admin' => false]
        );

        $categories = [
            ['name' => 'Apparel', 'slug' => 'apparel', 'description' => 'Clothing, accessories, and lifestyle products.'],
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Devices, gadgets, and connected products.'],
            ['name' => 'Home & Living', 'slug' => 'home-living', 'description' => 'Furniture, decor, and everyday essentials.'],
            ['name' => 'Digital Goods', 'slug' => 'digital-goods', 'description' => 'Downloadable files, licenses, courses, and services.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
        }

        $products = [
            ['category_id' => 1, 'name' => 'Everyday Essential Item', 'slug' => 'everyday-essential-item', 'description' => 'A demo physical product that can represent any retail niche.', 'price' => 49.00, 'sku' => 'GEN-001', 'brand' => 'Generic Brand', 'is_featured' => true],
            ['category_id' => 2, 'name' => 'Smart Accessory', 'slug' => 'smart-accessory', 'description' => 'A flexible sample product for electronics or gadget catalogs.', 'price' => 89.00, 'sku' => 'GEN-002', 'brand' => 'Tech Brand', 'is_featured' => true],
            ['category_id' => 3, 'name' => 'Home Collection Product', 'slug' => 'home-collection-product', 'description' => 'A configurable sample product for furniture, decor, or local goods.', 'price' => 129.00, 'sku' => 'GEN-003', 'brand' => 'Home Brand', 'is_featured' => true],
            ['category_id' => 4, 'name' => 'Digital Download', 'slug' => 'digital-download', 'description' => 'A sample digital product with download support.', 'price' => 29.00, 'sku' => 'DIG-001', 'brand' => 'Digital Studio', 'product_type' => 'digital', 'track_stock' => false, 'is_featured' => true],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                $product + ['stock_quantity' => 50, 'low_stock_alert' => 10, 'is_active' => true]
            );
        }
    }
}
