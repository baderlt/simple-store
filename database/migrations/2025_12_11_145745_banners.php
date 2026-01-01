<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          Schema::create('banners', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable();
        $table->text('description')->nullable();
        $table->string('image_path');
        $table->enum('position', ['hero', 'middle', 'bottom', 'sidebar']);
        $table->string('cta_text')->nullable();
        $table->string('cta_link')->nullable();
        $table->integer('order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->dateTime('start_at')->nullable();
        $table->dateTime('end_at')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
