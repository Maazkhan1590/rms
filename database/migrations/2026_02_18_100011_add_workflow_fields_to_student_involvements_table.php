<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_involvements', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned'])->default('draft')->after('academic_year');
            $table->foreignId('submitted_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->after('submitted_by')->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable()->after('approver_id');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
            $table->decimal('points_allocated', 8, 2)->default(0)->nullable()->after('approved_at');
            $table->boolean('points_locked')->default(false)->after('points_allocated');
            $table->foreignId('policy_version_id')->nullable()->after('points_locked')->constrained('policy_versions')->nullOnDelete();
            $table->boolean('evidence_required')->default(true)->after('policy_version_id');
            $table->boolean('evidence_uploaded')->default(false)->after('evidence_required');
            $table->text('evidence_description')->nullable()->after('evidence_uploaded');
            $table->string('evidence_link')->nullable()->after('evidence_description');
            
            $table->index(['submitted_by', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('student_involvements', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['approver_id']);
            $table->dropForeign(['policy_version_id']);
            $table->dropIndex(['submitted_by', 'status']);
            $table->dropIndex(['status', 'submitted_at']);
            $table->dropColumn([
                'user_id',
                'status',
                'submitted_by',
                'approver_id',
                'submitted_at',
                'approved_at',
                'points_allocated',
                'points_locked',
                'policy_version_id',
                'evidence_required',
                'evidence_uploaded',
                'evidence_description',
                'evidence_link'
            ]);
        });
    }
};
