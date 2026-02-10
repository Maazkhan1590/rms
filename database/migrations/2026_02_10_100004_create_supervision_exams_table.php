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
        Schema::create('supervision_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('staff_name');
            $table->string('academic_year')->nullable();
            $table->enum('role', ['Main', 'Co-Supervisor', 'Co', 'External Examiner', 'External'])->default('Main');
            $table->enum('degree', ['MSc', 'PhD', 'MPhil', 'Other'])->default('MSc');
            $table->string('university')->nullable();
            $table->string('student_name');
            $table->text('thesis_title')->nullable();
            $table->year('start_year')->nullable();
            $table->year('end_year')->nullable();
            $table->enum('status', ['Ongoing', 'Completed', 'Discontinued'])->default('Ongoing');
            $table->string('evidence_link')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('staff_name');
            $table->index('student_name');
            $table->index('status');
            $table->index('degree');
            $table->index('start_year');
            $table->index('end_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervision_exams');
    }
};
