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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role_type', ['photographer', 'videographer', 'drone_operator', 'editor']);
            $table->enum('skill_level', ['junior', 'mid_level', 'senior', 'expert'])->default('junior');
            $table->enum('availability_status', ['available', 'busy', 'on_leave'])->default('available');
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->text('equipment_owned')->nullable();
            $table->string('portfolio_link')->nullable();
            $table->boolean('is_default_assigned')->default(false);
            $table->integer('priority_order')->default(999);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
