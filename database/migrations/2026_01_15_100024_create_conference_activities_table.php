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
        Schema::create('conference_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('staff_name')->nullable()->comment('For Excel import compatibility');
            $table->enum('activity_type', ['keynote', 'invited', 'regular', 'plenary'])->nullable();
            $table->string('conference');
            $table->string('country')->nullable();
            $table->date('date')->nullable();
            $table->string('evidence_link')->nullable();
            $table->text('notes')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'year']);
            $table->index('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_activities');
    }
};

