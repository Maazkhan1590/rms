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
        Schema::create('consultancies_kts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('project_consultancy_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('client_sponsor')->nullable();
            $table->decimal('amount_omr', 15, 2)->nullable();
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');
            $table->boolean('commercialized')->default(false);
            $table->enum('income_type', ['consultancy', 'service', 'product'])->nullable();
            $table->string('lead_staff')->nullable();
            $table->string('evidence_link')->nullable();
            $table->string('sdg_s')->nullable()->comment('Comma-separated SDG numbers');
            $table->enum('reporting_period', ['q1', 'q2', 'q3', 'q4'])->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'year']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultancies_kts');
    }
};

