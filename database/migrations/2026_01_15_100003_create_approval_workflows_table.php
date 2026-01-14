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
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->enum('submission_type', ['publication', 'grant', 'rtn', 'bonus']);
            $table->unsignedBigInteger('submission_id')->comment('Polymorphic: publication_id, grant_id, etc.');
            $table->integer('current_step')->default(1)->comment('1=Faculty, 2=Coordinator, 3=Dean');
            $table->enum('status', [
                'draft',
                'submitted',
                'pending_coordinator',
                'pending_dean',
                'approved',
                'rejected',
                'returned'
            ])->default('draft');
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->comment('Current approver');
            $table->string('college')->nullable();
            $table->string('department')->nullable();
            $table->boolean('fallback_used')->default(false)->comment('If skipped coordinator');
            $table->boolean('auto_escalated')->default(false);
            $table->dateTime('escalation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['submission_type', 'submission_id']);
            $table->index(['status', 'current_step']);
            $table->index(['submitted_by', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['college', 'department']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_workflows');
    }
};

