<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            
            // Activity Type
            $table->enum('activity_type', [
                'call',
                'email',
                'sms',
                'meeting',
                'note',
                'whatsapp',
                'status_change'
            ])->default('call');
            
            // Call Specific Fields
            $table->enum('call_outcome', [
                'answered',
                'not_answered',
                'busy',
                'switched_off',
                'wrong_number',
                'number_not_available',
                'voicemail'
            ])->nullable();
            $table->integer('call_duration')->nullable()->comment('Duration in seconds');
            $table->timestamp('call_started_at')->nullable();
            $table->timestamp('call_ended_at')->nullable();
            
            // Lead Status Update
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            
            // Lead Response
            $table->enum('lead_interest_level', [
                'hot',
                'warm',
                'cold',
                'not_interested',
                'converted',
                'lost'
            ])->nullable();
            
            // Notes & Details
            $table->text('notes')->nullable();
            $table->json('discussion_points')->nullable()->comment('Structured data about what was discussed');
            
            // Follow-up
            $table->boolean('follow_up_required')->default(false);
            $table->date('next_follow_up_date')->nullable();
            $table->time('next_follow_up_time')->nullable();
            $table->text('follow_up_notes')->nullable();
            
            // Actions Taken
            $table->json('actions_taken')->nullable()->comment('Array of actions: sent_email, sent_sms, scheduled_meeting');
            
            // Assignment
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Metadata
            $table->boolean('is_completed')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('lead_id', 'idx_lead_id');
            $table->index('activity_type', 'idx_activity_type');
            $table->index('next_follow_up_date', 'idx_follow_up_date');
            $table->index('performed_by', 'idx_performed_by');
            $table->index(['lead_id', 'created_at'], 'idx_lead_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
