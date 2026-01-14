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
        Schema::create('bonus_recognition', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('recognition_type', [
                'editorial_board',
                'external_examiner',
                'regulatory_body',
                'workshop_seminar',
                'keynote_plenary',
                'journal_reviewer'
            ]);
            $table->string('title')->comment('e.g., "Editorial Board Member - Journal of X"');
            $table->string('organization')->comment('Journal/Conference/Organization name');
            $table->text('role_description')->nullable();
            $table->string('journal_conference_name')->nullable();
            $table->string('event_name')->nullable()->comment('For workshops/seminars');
            $table->date('event_date')->nullable();
            $table->json('evidence_files')->nullable()->comment('References to evidence_files');
            $table->decimal('points', 8, 2)->comment('Varies by type (5-9 points)');
            $table->year('year');
            $table->enum('status', ['draft', 'submitted', 'pending', 'approved', 'rejected'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'year']);
            $table->index(['recognition_type', 'status']);
            $table->index(['status', 'submitted_at']);
            // Note: Total bonus points capped at 25 per user per year (enforced in application logic)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_recognition');
    }
};

