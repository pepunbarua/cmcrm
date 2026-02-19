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
        Schema::create('package_content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('package_content_id')->constrained('package_contents')->restrictOnDelete();
            $table->string('content_name_snapshot');
            $table->decimal('default_qty', 10, 2)->default(1);
            $table->decimal('default_unit_price', 10, 2)->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['package_id', 'package_content_id']);
            $table->index(['package_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_content_items');
    }
};
