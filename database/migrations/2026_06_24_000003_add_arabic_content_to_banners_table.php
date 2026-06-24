<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (! Schema::hasColumn('banners', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }

            if (! Schema::hasColumn('banners', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }

            if (! Schema::hasColumn('banners', 'cta_text_ar')) {
                $table->string('cta_text_ar')->nullable()->after('cta_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            foreach (['title_ar', 'description_ar', 'cta_text_ar'] as $column) {
                if (Schema::hasColumn('banners', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
