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
        Schema::create('research_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('staff_name')->nullable()->comment('For Excel import compatibility');
            $table->string('item');
            $table->enum('category', ['equipment', 'software', 'apc', 'travel', 'training', 'other'])->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount_omr', 15, 2)->nullable();
            $table->string('funding_source')->nullable();
            $table->string('evidence_link')->nullable();
            $table->text('notes')->nullable();
            $table->enum('reporting_period', ['q1', 'q2', 'q3', 'q4'])->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'year']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_investments');
    }
};

