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
            // Publication type and classification
            if (!Schema::hasColumn('publications', 'publication_type')) {
                $table->enum('publication_type', [
                    'journal_paper',
                    'conference_paper',
                    'book',
                    'book_chapter',
                    'non_indexed_journal'
                ])->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('publications', 'journal_category')) {
                $table->enum('journal_category', [
                    'scopus',
                    'international_refereed',
                    'su_approved_arabic',
                    'non_indexed'
                ])->nullable()->after('publication_type');
            }
            
            if (!Schema::hasColumn('publications', 'quartile')) {
                $table->enum('quartile', ['Q1', 'Q2', 'Q3', 'Q4'])->nullable()->after('journal_category')->comment('For indexed journals');
            }
            
            // Authors information
            if (!Schema::hasColumn('publications', 'authors')) {
                $table->json('authors')->nullable()->after('quartile')->comment('Array of author objects: [{name, email, affiliation, is_primary}]');
            }
            
            if (!Schema::hasColumn('publications', 'primary_author_id')) {
                $table->foreignId('primary_author_id')->nullable()->after('authors')->constrained('users')->nullOnDelete()->comment('Submitter');
            }
            
            if (!Schema::hasColumn('publications', 'co_authors')) {
                $table->json('co_authors')->nullable()->after('primary_author_id')->comment('Other authors');
            }
            
            // Publication details
            if (!Schema::hasColumn('publications', 'isbn')) {
                $table->string('isbn')->nullable()->after('co_authors')->comment('For books/chapters');
            }
            
            if (!Schema::hasColumn('publications', 'publisher')) {
                $table->string('publisher')->nullable()->after('isbn');
            }
            
            // Journal/Conference specific fields
            if (!Schema::hasColumn('publications', 'journal_name')) {
                $table->string('journal_name')->nullable()->after('journal')->comment('Rename or keep both');
            }
            
            if (!Schema::hasColumn('publications', 'conference_name')) {
                $table->string('conference_name')->nullable()->after('journal_name');
            }
            
            if (!Schema::hasColumn('publications', 'proceedings_link')) {
                $table->string('proceedings_link')->nullable()->after('conference_name');
            }
            
            // Links and evidence
            if (!Schema::hasColumn('publications', 'published_link')) {
                $table->string('published_link')->nullable()->after('doi');
            }
            
            if (!Schema::hasColumn('publications', 'acceptance_letter_path')) {
                $table->string('acceptance_letter_path')->nullable()->after('published_link')->comment('Evidence file');
            }
            
            // Points and policy
            if (!Schema::hasColumn('publications', 'points_allocated')) {
                $table->decimal('points_allocated', 8, 2)->default(0)->after('acceptance_letter_path');
            }
            
            if (!Schema::hasColumn('publications', 'policy_version_id')) {
                $table->foreignId('policy_version_id')->nullable()->after('points_allocated')->constrained('policy_versions')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('publications', 'points_locked')) {
                $table->boolean('points_locked')->default(false)->after('policy_version_id')->comment('Lock after approval');
            }
            
            // Evidence tracking
            if (!Schema::hasColumn('publications', 'evidence_required')) {
                $table->boolean('evidence_required')->default(true)->after('points_locked');
            }
            
            if (!Schema::hasColumn('publications', 'evidence_uploaded')) {
                $table->boolean('evidence_uploaded')->default(false)->after('evidence_required');
            }
            
            // Year fields
            if (!Schema::hasColumn('publications', 'year')) {
                $table->year('year')->nullable()->after('publication_year')->comment('Publication year for reporting');
            }
            
            if (!Schema::hasColumn('publications', 'submission_year')) {
                $table->year('submission_year')->nullable()->after('year')->comment('Year submitted to RMS');
            }
            
            // Indexes
            $table->index('publication_type');
            $table->index(['journal_category', 'quartile']);
            $table->index('primary_author_id');
            $table->index(['year', 'submission_year']);
            $table->index('policy_version_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropForeign(['primary_author_id']);
            $table->dropForeign(['policy_version_id']);
            
            $table->dropColumn([
                'publication_type',
                'journal_category',
                'quartile',
                'authors',
                'primary_author_id',
                'co_authors',
                'isbn',
                'publisher',
                'journal_name',
                'conference_name',
                'proceedings_link',
                'published_link',
                'acceptance_letter_path',
                'points_allocated',
                'policy_version_id',
                'points_locked',
                'evidence_required',
                'evidence_uploaded',
                'year',
                'submission_year',
            ]);
        });
    }
};

