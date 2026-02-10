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
        Schema::create('adjunct_professors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('google_scholar')->nullable();
            $table->integer('gs_citation_number')->nullable()->comment('Google Scholar citations');
            $table->integer('gs_h_index')->nullable()->comment('Google Scholar H-index');
            $table->integer('gs_papers_2025')->nullable()->comment('Papers in 2025 (Google Scholar)');
            $table->string('scopus_scholar')->nullable();
            $table->integer('scopus_citation_number')->nullable()->comment('Scopus citations');
            $table->integer('scopus_h_index')->nullable()->comment('Scopus H-index');
            $table->integer('scopus_papers_2025')->nullable()->comment('Papers in 2025 (Scopus)');
            $table->integer('publication_with_sohar')->nullable()->comment('Publications with Sohar affiliation');
            $table->date('appointment_from')->nullable()->comment('Appointment start date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('name');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjunct_professors');
    }
};
