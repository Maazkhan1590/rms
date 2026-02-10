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
        Schema::table('publications', function (Blueprint $table) {
            if (!Schema::hasColumn('publications', 'indexing_db')) {
                $table->string('indexing_db')->nullable()->after('points_allocated');
            }
            if (!Schema::hasColumn('publications', 'sohar_affiliation')) {
                $table->boolean('sohar_affiliation')->nullable()->after('indexing_db');
            }
            if (!Schema::hasColumn('publications', 'percent_contribution')) {
                $table->decimal('percent_contribution', 5, 2)->nullable()->after('sohar_affiliation');
            }
            if (!Schema::hasColumn('publications', 'su_author_type')) {
                $table->string('su_author_type')->nullable()->after('percent_contribution');
            }
            if (!Schema::hasColumn('publications', 'student_coauthor')) {
                $table->boolean('student_coauthor')->nullable()->after('su_author_type');
            }
            if (!Schema::hasColumn('publications', 'student_level')) {
                $table->string('student_level')->nullable()->after('student_coauthor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $columns = [
                'indexing_db',
                'sohar_affiliation',
                'percent_contribution',
                'su_author_type',
                'student_coauthor',
                'student_level',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('publications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
