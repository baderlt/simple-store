<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('sku');
            $table->string('product_type')->default('physical')->after('brand');
            $table->boolean('track_stock')->default(true)->after('product_type');
            $table->string('digital_file_path')->nullable()->after('track_stock');
            $table->json('attributes')->nullable()->after('digital_file_path');
            $table->json('specifications')->nullable()->after('attributes');
            $table->json('variants')->nullable()->after('specifications');
            $table->json('localized_content')->nullable()->after('variants');
            $table->json('seo')->nullable()->after('localized_content');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'product_type',
                'track_stock',
                'digital_file_path',
                'attributes',
                'specifications',
                'variants',
                'localized_content',
                'seo',
            ]);
        });
    }
};
