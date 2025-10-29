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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->date('follow_up_date');
            $table->time('follow_up_time')->nullable();
            $table->integer('call_duration')->nullable()->comment('in minutes');
            $table->enum('call_status', ['answered', 'not_answered', 'busy', 'switched_off', 'scheduled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->foreignId('contacted_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'completed', 'rescheduled', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
