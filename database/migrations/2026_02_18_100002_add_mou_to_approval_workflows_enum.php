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
        // For MySQL/MariaDB, we need to modify the enum
        DB::statement("ALTER TABLE approval_workflows MODIFY COLUMN submission_type ENUM('publication', 'grant', 'rtn', 'bonus', 'mou', 'commercialization', 'consultancy', 'award', 'research_investment', 'conference_activity', 'supervision_exam', 'editorial_appointment', 'student_involvement', 'research_fellow', 'sdg_contribution', 'rtn_course_detail', 'internal_funding', 'block_funding', 'adjunct_professor') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE approval_workflows MODIFY COLUMN submission_type ENUM('publication', 'grant', 'rtn', 'bonus') NOT NULL");
    }
};
