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
        Schema::table('rtn_submissions', function (Blueprint $table) {
            // Faculty
            if (!Schema::hasColumn('rtn_submissions', 'faculty')) {
                $table->string('faculty')->nullable()->after('user_id');
            }
            
            // Units
            if (!Schema::hasColumn('rtn_submissions', 'units')) {
                $table->integer('units')->default(1)->after('points');
            }
            
            // Amount (OMR)
            if (!Schema::hasColumn('rtn_submissions', 'amount_omr')) {
                $table->decimal('amount_omr', 15, 2)->nullable()->after('units');
            }
            
            // Submission Year
            if (!Schema::hasColumn('rtn_submissions', 'submission_year')) {
                $table->year('submission_year')->nullable()->after('year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rtn_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'faculty',
                'units',
                'amount_omr',
                'submission_year',
            ]);
        });
    }
};
