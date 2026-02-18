<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_fundings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('project_title');
            $table->string('funding_source')->nullable();
            $table->decimal('amount_omr', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned', 'active', 'completed'])->default('draft');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('points_allocated', 8, 2)->default(0)->nullable();
            $table->boolean('points_locked')->default(false);
            $table->foreignId('policy_version_id')->nullable()->constrained('policy_versions')->nullOnDelete();
            $table->boolean('evidence_required')->default(true);
            $table->boolean('evidence_uploaded')->default(false);
            $table->text('evidence_description')->nullable();
            $table->string('evidence_link')->nullable();
            $table->text('notes')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['submitted_by', 'status']);
            $table->index(['status', 'submitted_at']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_fundings');
    }
};
