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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('venue');
            $table->text('venue_address')->nullable();
            $table->foreignId('photographer_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->foreignId('videographer_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->text('equipment_checklist')->nullable();
            $table->text('special_instructions')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->date('delivery_deadline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
