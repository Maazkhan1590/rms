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
        Schema::create('approval_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->enum('action', ['submitted', 'approved', 'rejected', 'returned', 'reassigned']);
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->text('comments')->nullable();
            $table->json('evidence_files')->nullable()->comment('References to evidence_files table');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['workflow_id', 'created_at']);
            $table->index(['performed_by', 'action']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_history');
    }
};

