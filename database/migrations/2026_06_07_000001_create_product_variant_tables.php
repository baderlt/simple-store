<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['product_attribute_id', 'slug'], 'attribute_value_unique');
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'is_default']);
            $table->unique(['product_id', 'sku'], 'product_variant_sku_unique');
        });

        Schema::create('product_variant_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->foreignId('product_attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();

            $table->unique(['product_variant_id', 'product_attribute_id'], 'variant_attribute_unique');
            $table->index(['product_attribute_id', 'product_attribute_value_id'], 'variant_item_value_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_items');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
