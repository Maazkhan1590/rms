<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update RTN submissions status enum to include workflow statuses
        DB::statement("ALTER TABLE `rtn_submissions` MODIFY `status` ENUM(
            'draft',
            'submitted',
            'pending',
            'pending_coordinator',
            'pending_dean',
            'approved',
            'rejected'
        ) DEFAULT 'draft'");

        // Update bonus recognition status enum to include workflow statuses
        DB::statement("ALTER TABLE `bonus_recognition` MODIFY `status` ENUM(
            'draft',
            'submitted',
            'pending',
            'pending_coordinator',
            'pending_dean',
            'approved',
            'rejected'
        ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE `rtn_submissions` MODIFY `status` ENUM(
            'draft',
            'submitted',
            'pending',
            'approved',
            'rejected'
        ) DEFAULT 'draft'");

        DB::statement("ALTER TABLE `bonus_recognition` MODIFY `status` ENUM(
            'draft',
            'submitted',
            'pending',
            'approved',
            'rejected'
        ) DEFAULT 'draft'");
    }
};
