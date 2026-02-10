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
        Schema::create('student_involvements', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['UG', 'Master', 'PhD', 'Undergraduate', 'Masters', 'Postgraduate'])->comment('Student category');
            $table->integer('count')->default(0);
            $table->text('notes')->nullable();
            $table->date('date')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category');
            $table->index('date');
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_involvements');
    }
};
