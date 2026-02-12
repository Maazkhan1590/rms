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
        // Change both role and grant_type from ENUM to VARCHAR for flexibility
        DB::statement("ALTER TABLE `grants` MODIFY `role` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `grants` MODIFY `grant_type` VARCHAR(50) NULL");
        
        // Update existing role data to convert underscores to hyphens
        DB::statement("UPDATE `grants` SET `role` = 'Co-PI' WHERE `role` = 'Co_PI'");
        DB::statement("UPDATE `grants` SET `role` = 'Co-I' WHERE `role` = 'Co_I'");
        DB::statement("UPDATE `grants` SET `role` = 'Advisor' WHERE `role` = 'Advisor_Mentor'");
        
        // Update grant_type data if needed
        DB::statement("UPDATE `grants` SET `grant_type` = 'external_matching_grant' WHERE `grant_type` = 'matching_grant'");
        DB::statement("UPDATE `grants` SET `grant_type` = 'grg_urg_advisor' WHERE `grant_type` = 'grg_urg'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert grant_type data
        DB::statement("UPDATE `grants` SET `grant_type` = 'matching_grant' WHERE `grant_type` = 'external_matching_grant'");
        DB::statement("UPDATE `grants` SET `grant_type` = 'grg_urg' WHERE `grant_type` = 'grg_urg_advisor'");
        
        // Revert role data
        DB::statement("UPDATE `grants` SET `role` = 'Co_PI' WHERE `role` = 'Co-PI'");
        DB::statement("UPDATE `grants` SET `role` = 'Co_I' WHERE `role` = 'Co-I'");
        DB::statement("UPDATE `grants` SET `role` = 'Advisor_Mentor' WHERE `role` = 'Advisor'");
        
        // Revert to original enum values
        DB::statement("ALTER TABLE `grants` MODIFY `role` ENUM(
            'PI',
            'Co_PI',
            'Co_I',
            'Advisor_Mentor',
            'Applicant'
        ) NULL");
        
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
    }
};
