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
        Schema::create('rtn_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('rtn_type', ['RTN_3', 'RTN_4']);
            $table->string('title');
            $table->text('description');
            $table->json('student_coauthors')->nullable()->comment('For RTN-3: [{name, email}]');
            $table->json('course_files_updated')->nullable()->comment('For RTN-4: [{course_code, course_name, file_path}]');
            $table->text('lecture_materials')->nullable()->comment('Description of how research informs teaching');
            $table->text('assessment_redesign')->nullable();
            $table->text('case_study_documentation')->nullable();
            $table->json('evidence_files')->nullable()->comment('References to evidence_files');
            $table->decimal('points', 8, 2)->default(0)->comment('RTN-3: 5, RTN-4: 5');
            $table->year('year');
            $table->enum('status', ['draft', 'submitted', 'pending', 'approved', 'rejected'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'year']);
            $table->index(['rtn_type', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtn_submissions');
    }
};

