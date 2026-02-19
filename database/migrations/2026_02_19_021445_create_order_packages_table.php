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
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('package_name_snapshot');
            $table->enum('pricing_mode', ['fixed', 'item_sum', 'hybrid', 'custom'])->default('item_sum');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('adjustment', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->json('package_snapshot')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_packages');
    }
};
