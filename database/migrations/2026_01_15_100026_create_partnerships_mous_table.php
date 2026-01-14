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
        Schema::create('partnerships_mous', function (Blueprint $table) {
            $table->id();
            $table->string('partner_organization');
            $table->enum('type', ['mou', 'moa', 'project', 'industry'])->nullable();
            $table->date('date_signed')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('scope_theme')->nullable();
            $table->foreignId('lead_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lead_staff')->nullable()->comment('For Excel import compatibility');
            $table->text('outputs_papers_grants_events')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->string('evidence_link')->nullable();
            $table->string('sdg_s')->nullable()->comment('Comma-separated SDG numbers');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['lead_staff_id', 'status']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnerships_mous');
    }
};

