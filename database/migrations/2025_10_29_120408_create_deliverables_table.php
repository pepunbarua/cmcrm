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
        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->enum('deliverable_type', ['raw_photos', 'edited_photos', 'raw_videos', 'edited_videos', 'album', 'highlights']);
            $table->integer('total_count')->nullable()->comment('total photos/videos');
            $table->enum('delivery_method', ['google_drive', 'usb', 'cloud', 'physical_album'])->default('google_drive');
            $table->string('delivery_link')->nullable();
            $table->enum('delivery_status', ['processing', 'ready', 'delivered'])->default('processing');
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverables');
    }
};
