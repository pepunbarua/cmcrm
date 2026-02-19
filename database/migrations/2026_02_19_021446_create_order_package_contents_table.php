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
        Schema::create('order_package_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_package_id')->constrained('order_packages')->cascadeOnDelete();
            $table->foreignId('package_content_id')->nullable()->constrained('package_contents')->nullOnDelete();
            $table->string('content_name_snapshot');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['order_package_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_package_contents');
    }
};
