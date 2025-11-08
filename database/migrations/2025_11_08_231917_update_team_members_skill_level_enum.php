<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL doesn't support direct ENUM modification, so we need to use raw SQL
        DB::statement("ALTER TABLE team_members MODIFY COLUMN skill_level ENUM('junior', 'mid_level', 'senior', 'expert') DEFAULT 'junior'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE team_members MODIFY COLUMN skill_level ENUM('junior', 'senior', 'expert') DEFAULT 'junior'");
    }
};
