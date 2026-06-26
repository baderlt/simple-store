<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'products_active_created_idx');
            $table->index(['category_id', 'is_active', 'created_at'], 'products_category_active_created_idx');
            $table->index(['is_featured', 'is_active'], 'products_featured_active_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
            $table->index('created_at', 'orders_created_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['product_id', 'created_at'], 'order_items_product_created_idx');
            $table->index(['product_variant_id', 'created_at'], 'order_items_variant_created_idx');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->index(['product_id', 'is_active', 'start_date', 'end_date'], 'discounts_product_active_dates_idx');
            $table->index(['category_id', 'is_active', 'start_date', 'end_date'], 'discounts_category_active_dates_idx');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->index(['is_active', 'position', 'order'], 'banners_active_position_order_idx');
            $table->index(['is_active', 'start_at', 'end_at'], 'banners_active_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex('banners_active_dates_idx');
            $table->dropIndex('banners_active_position_order_idx');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropIndex('discounts_category_active_dates_idx');
            $table->dropIndex('discounts_product_active_dates_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_variant_created_idx');
            $table->dropIndex('order_items_product_created_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_created_idx');
            $table->dropIndex('orders_status_created_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_featured_active_idx');
            $table->dropIndex('products_category_active_created_idx');
            $table->dropIndex('products_active_created_idx');
        });
    }
};
