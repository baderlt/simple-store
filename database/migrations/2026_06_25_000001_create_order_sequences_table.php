<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_sequences', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->unsignedBigInteger('current_number')->default(0);
            $table->timestamps();
        });

        $highestOrderSequence = 0;

        if (Schema::hasTable('orders')) {
            $highestOrderSequence = DB::table('orders')
                ->where('order_number', 'like', 'ORD-%')
                ->pluck('order_number')
                ->reduce(function (int $highest, string $orderNumber): int {
                    if (! preg_match('/^ORD-(\d+)$/', $orderNumber, $matches)) {
                        return $highest;
                    }

                    return max($highest, (int) $matches[1]);
                }, 0);
        }

        DB::table('order_sequences')->insert([
            'name' => 'orders',
            'current_number' => $highestOrderSequence,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('order_sequences');
    }
};
