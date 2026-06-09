<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('unit', 50)->nullable()->after('sku');
            $table->string('price_type', 20)->default('fixed')->after('unit');
            $table->decimal('price_adjustment', 10, 2)->default(0)->after('price');
            $table->boolean('is_active')->default(true)->after('is_default');
            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'is_active']);
            $table->dropColumn(['unit', 'price_type', 'price_adjustment', 'is_active']);
        });
    }
};
