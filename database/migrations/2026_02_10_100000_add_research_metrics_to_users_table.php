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
        Schema::table('users', function (Blueprint $table) {
            // Research metrics from Staff_Master sheet
            if (!Schema::hasColumn('users', 'citation_number')) {
                $table->integer('citation_number')->nullable()->after('research_gate')->comment('Google Scholar citations');
            }
            
            if (!Schema::hasColumn('users', 'h_index')) {
                $table->integer('h_index')->nullable()->after('citation_number')->comment('Google Scholar H-index');
            }
            
            if (!Schema::hasColumn('users', 'scopus_citation_number')) {
                $table->integer('scopus_citation_number')->nullable()->after('h_index')->comment('Scopus citations');
            }
            
            if (!Schema::hasColumn('users', 'scopus_h_index')) {
                $table->integer('scopus_h_index')->nullable()->after('scopus_citation_number')->comment('Scopus H-index');
            }
            
            if (!Schema::hasColumn('users', 'scopus_papers')) {
                $table->integer('scopus_papers')->nullable()->after('scopus_h_index')->comment('Number of papers in Scopus');
            }
            
            if (!Schema::hasColumn('users', 'sohar_affiliation')) {
                $table->enum('sohar_affiliation', ['Yes', 'No'])->default('Yes')->after('scopus_papers')->comment('Has Sohar University affiliation');
            }
            
            if (!Schema::hasColumn('users', 'orcid_connected')) {
                $table->enum('orcid_connected', ['Yes', 'No'])->default('No')->after('sohar_affiliation')->comment('ORCID connected status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'citation_number',
                'h_index',
                'scopus_citation_number',
                'scopus_h_index',
                'scopus_papers',
                'sohar_affiliation',
                'orcid_connected',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
