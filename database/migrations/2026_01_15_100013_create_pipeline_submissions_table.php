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
        Schema::create('pipeline_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('submission_type', ['publication', 'grant']);
            $table->string('title');
            $table->enum('status', ['submitted', 'under_review', 'in_process'])->default('submitted');
            $table->string('journal_conference_name')->nullable();
            $table->date('submission_date');
            $table->date('expected_decision_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'submission_type']);
            $table->index(['status', 'submission_date']);
            $table->index('submission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_submissions');
    }
};

