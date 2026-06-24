<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }

            if (! Schema::hasColumn('products', 'meta_description')) {
                $table->string('meta_description', 170)->nullable();
            }

            if (! Schema::hasColumn('products', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable();
            }

            if (! Schema::hasColumn('products', 'canonical_url')) {
                $table->string('canonical_url', 2048)->nullable();
            }

            if (! Schema::hasColumn('products', 'og_title')) {
                $table->string('og_title')->nullable();
            }

            if (! Schema::hasColumn('products', 'og_description')) {
                $table->string('og_description', 170)->nullable();
            }

            if (! Schema::hasColumn('products', 'og_image')) {
                $table->string('og_image', 2048)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach ([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
                'og_title',
                'og_description',
                'og_image',
            ] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
