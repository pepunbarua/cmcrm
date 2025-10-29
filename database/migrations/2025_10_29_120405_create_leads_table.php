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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->enum('event_type', ['wedding', 'birthday', 'corporate', 'portrait', 'other']);
            $table->date('event_date')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('package_interest')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['new', 'contacted', 'follow_up', 'qualified', 'converted', 'lost'])->default('new');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
