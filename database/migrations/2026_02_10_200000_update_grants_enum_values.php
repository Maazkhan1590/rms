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
        // Update grant_type enum to include all values
        DB::statement("ALTER TABLE `grants` MODIFY `grant_type` ENUM(
            'RG',
            'GRG', 
            'URG',
            'EJAAD',
            'external_grant',
            'external_consultancy',
            'matching_grant',
            'grg_urg',
            'patent_copyright',
            'grant_application',
            'other'
        ) NULL");

        // Ensure grant_status has a default
        DB::statement("ALTER TABLE `grants` MODIFY `grant_status` ENUM(
            'submitted',
            'accepted',
            'ongoing',
            'completed',
            'draft'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE `grants` MODIFY `grant_type` ENUM(
            'external_grant',
            'external_consultancy',
            'matching_grant',
            'grg_urg',
            'patent_copyright',
            'grant_application'
        ) NULL");
    }
};
