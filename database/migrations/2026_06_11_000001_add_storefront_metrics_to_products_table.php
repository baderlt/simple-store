<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('review_rating', 2, 1)->default(0)->after('is_featured');
            $table->unsignedInteger('reviews_count')->default(0)->after('review_rating');
            $table->unsignedInteger('sales_count')->default(0)->after('reviews_count');
        });

        DB::table('products')
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $salesCount = random_int(50, 100);

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'review_rating' => random_int(42, 49) / 10,
                            'reviews_count' => random_int(10, min(80, $salesCount)),
                            'sales_count' => $salesCount,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['review_rating', 'reviews_count', 'sales_count']);
        });
    }
};
