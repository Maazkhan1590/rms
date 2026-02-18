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
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('award_name');
            $table->text('description')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('awarding_organization')->nullable();
            $table->date('award_date')->nullable();
            $table->enum('award_type', ['national', 'international', 'regional', 'institutional', 'other'])->nullable();
            $table->string('category')->nullable();
            $table->text('achievement_description')->nullable();
            $table->string('evidence_link')->nullable();
            $table->text('evidence_description')->nullable();
            $table->enum('status', ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('points_allocated', 8, 2)->default(0)->nullable();
            $table->boolean('points_locked')->default(false);
            $table->foreignId('policy_version_id')->nullable()->constrained('policy_versions')->nullOnDelete();
            $table->boolean('evidence_required')->default(true);
            $table->boolean('evidence_uploaded')->default(false);
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['submitted_by', 'status']);
            $table->index(['status', 'submitted_at']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
