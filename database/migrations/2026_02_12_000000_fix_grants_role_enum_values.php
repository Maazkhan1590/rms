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
        // Update role enum to match the form values
        DB::statement("ALTER TABLE `grants` MODIFY `role` ENUM(
            'PI',
            'Co-PI',
            'Co-I',
            'Advisor',
            'Mentor',
            'Applicant'
        ) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE `grants` MODIFY `role` ENUM(
            'PI',
            'Co_PI',
            'Co_I',
            'Advisor_Mentor',
            'Applicant'
        ) NULL");
    }
};
