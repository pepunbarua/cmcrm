<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('locked_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('locked_at')->nullable()->after('locked_by');
            $table->timestamp('lock_expires_at')->nullable()->after('locked_at');
            
            $table->index(['locked_by', 'locked_at'], 'idx_locked');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropIndex('idx_locked');
            $table->dropColumn(['locked_by', 'locked_at', 'lock_expires_at']);
        });
    }
};
