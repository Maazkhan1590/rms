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
        Schema::create('commercializations', function (Blueprint $table) {
            $table->id();
            $table->string('product_service_name');
            $table->foreignId('owner_team_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('owner_team')->nullable()->comment('For Excel import compatibility');
            $table->enum('type', ['product', 'service'])->nullable();
            $table->enum('stage', ['prototype', 'pilot', 'launched'])->nullable();
            $table->date('launch_date')->nullable();
            $table->decimal('revenue_omr', 15, 2)->nullable();
            $table->boolean('ip_patent')->default(false);
            $table->string('client_market')->nullable();
            $table->string('evidence_link')->nullable();
            $table->string('sdg_s')->nullable()->comment('Comma-separated SDG numbers');
            $table->enum('reporting_period', ['q1', 'q2', 'q3', 'q4'])->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['owner_team_id', 'year']);
            $table->index('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercializations');
    }
};

