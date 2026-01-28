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
        Schema::table('grants', function (Blueprint $table) {
            // Grant Type - update enum to include RG/GRG/URG/EJAAD/Other
            if (Schema::hasColumn('grants', 'grant_type')) {
                // We'll handle this in application logic since enum modification is complex
            } else {
                $table->enum('grant_type', [
                    'RG',
                    'GRG',
                    'URG',
                    'EJAAD',
                    'external_grant',
                    'external_matching_grant',
                    'grg_urg_advisor',
                    'patent_copyright',
                    'grant_application',
                    'other'
                ])->nullable()->after('title');
            }
            
            // Grant Status - update to include Submitted/Accepted/Ongoing/Completed
            if (!Schema::hasColumn('grants', 'grant_status')) {
                $table->enum('grant_status', [
                    'submitted',
                    'accepted',
                    'ongoing',
                    'completed',
                    'draft'
                ])->default('draft')->after('status');
            }
            
            // Application Date
            if (!Schema::hasColumn('grants', 'application_date')) {
                $table->date('application_date')->nullable()->after('start_date');
            }
            
            // Amount Received To Date (OMR)
            if (!Schema::hasColumn('grants', 'amount_received_omr')) {
                $table->decimal('amount_received_omr', 15, 2)->nullable()->after('amount_omr');
            }
            
            // KT Income (Y/N)
            if (!Schema::hasColumn('grants', 'kt_income')) {
                $table->boolean('kt_income')->default(false)->after('amount_received_omr');
            }
            
            // SDG(s) - JSON array
            if (!Schema::hasColumn('grants', 'sdgs')) {
                $table->json('sdgs')->nullable()->after('kt_income');
            }
            
            // Reporting Period (Q1/Q2/Q3/Q4)
            if (!Schema::hasColumn('grants', 'reporting_period')) {
                $table->enum('reporting_period', ['Q1', 'Q2', 'Q3', 'Q4'])->nullable()->after('sdgs');
            }
            
            // Faculty/College/Department - link to user's college/department
            if (!Schema::hasColumn('grants', 'faculty')) {
                $table->string('faculty')->nullable()->after('submitted_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grants', function (Blueprint $table) {
            $table->dropColumn([
                'grant_status',
                'application_date',
                'amount_received_omr',
                'kt_income',
                'sdgs',
                'reporting_period',
                'faculty',
            ]);
        });
    }
};
