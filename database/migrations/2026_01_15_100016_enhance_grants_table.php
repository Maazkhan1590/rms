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
            // Grant type
            if (!Schema::hasColumn('grants', 'grant_type')) {
                $table->enum('grant_type', [
                    'external_grant',
                    'external_consultancy',
                    'matching_grant',
                    'grg_urg',
                    'patent_copyright',
                    'grant_application'
                ])->nullable()->after('title');
            }
            
            // Role in grant
            if (!Schema::hasColumn('grants', 'role')) {
                $table->enum('role', [
                    'PI',
                    'Co_PI',
                    'Co_I',
                    'Advisor_Mentor',
                    'Applicant'
                ])->nullable()->after('grant_type');
            }
            
            // Financial details
            if (!Schema::hasColumn('grants', 'amount_omr')) {
                $table->decimal('amount_omr', 15, 2)->nullable()->after('amount')->comment('Amount in OMR');
            }
            
            // Keep existing amount field, but add OMR specific
            // Currency already exists, update default if needed
            if (Schema::hasColumn('grants', 'currency')) {
                // Update default to OMR if needed
            }
            
            if (!Schema::hasColumn('grants', 'units')) {
                $table->integer('units')->default(1)->after('amount_omr')->comment('Calculated: ceil(amount_omr / 10000)');
            }
            
            // Sponsor details
            if (!Schema::hasColumn('grants', 'sponsor_type')) {
                $table->enum('sponsor_type', [
                    'government',
                    'private',
                    'international',
                    'other'
                ])->nullable()->after('sponsor');
            }
            
            if (!Schema::hasColumn('grants', 'sponsor_name')) {
                $table->string('sponsor_name')->nullable()->after('sponsor_type');
            }
            
            // Reference codes
            if (!Schema::hasColumn('grants', 'matching_grant_moa')) {
                $table->string('matching_grant_moa')->nullable()->after('reference_code')->comment('MoA reference for matching grants');
            }
            
            // Patent specific fields
            if (!Schema::hasColumn('grants', 'patent_registration_number')) {
                $table->string('patent_registration_number')->nullable()->after('matching_grant_moa');
            }
            
            if (!Schema::hasColumn('grants', 'patent_su_registered')) {
                $table->boolean('patent_su_registered')->default(false)->after('patent_registration_number');
            }
            
            // Evidence
            if (!Schema::hasColumn('grants', 'award_letter_path')) {
                $table->string('award_letter_path')->nullable()->after('patent_su_registered')->comment('Evidence file');
            }
            
            // Points and policy
            if (!Schema::hasColumn('grants', 'points_allocated')) {
                $table->decimal('points_allocated', 8, 2)->default(0)->after('award_letter_path');
            }
            
            if (!Schema::hasColumn('grants', 'policy_version_id')) {
                $table->foreignId('policy_version_id')->nullable()->after('points_allocated')->constrained('policy_versions')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('grants', 'points_locked')) {
                $table->boolean('points_locked')->default(false)->after('policy_version_id');
            }
            
            // Evidence tracking
            if (!Schema::hasColumn('grants', 'evidence_required')) {
                $table->boolean('evidence_required')->default(true)->after('points_locked');
            }
            
            if (!Schema::hasColumn('grants', 'evidence_uploaded')) {
                $table->boolean('evidence_uploaded')->default(false)->after('evidence_required');
            }
            
            // Year fields
            if (!Schema::hasColumn('grants', 'award_year')) {
                $table->year('award_year')->nullable()->after('end_date')->comment('Year awarded');
            }
            
            if (!Schema::hasColumn('grants', 'submission_year')) {
                $table->year('submission_year')->nullable()->after('award_year')->comment('Year submitted to RMS');
            }
            
            // Indexes
            $table->index('grant_type');
            $table->index('role');
            $table->index(['award_year', 'submission_year']);
            $table->index('policy_version_id');
            $table->index('sponsor_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grants', function (Blueprint $table) {
            $table->dropForeign(['policy_version_id']);
            
            $table->dropColumn([
                'grant_type',
                'role',
                'amount_omr',
                'units',
                'sponsor_type',
                'sponsor_name',
                'matching_grant_moa',
                'patent_registration_number',
                'patent_su_registered',
                'award_letter_path',
                'points_allocated',
                'policy_version_id',
                'points_locked',
                'evidence_required',
                'evidence_uploaded',
                'award_year',
                'submission_year',
            ]);
        });
    }
};

