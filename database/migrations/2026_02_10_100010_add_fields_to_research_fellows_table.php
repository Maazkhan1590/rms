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
        Schema::table('research_fellows', function (Blueprint $table) {
            if (!Schema::hasColumn('research_fellows', 'journal')) {
                $table->string('journal')->nullable()->after('publication_title');
            }
            if (!Schema::hasColumn('research_fellows', 'doi')) {
                $table->string('doi')->nullable()->after('journal');
            }
            if (!Schema::hasColumn('research_fellows', 'status')) {
                $table->string('status')->nullable()->after('doi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_fellows', function (Blueprint $table) {
            $columns = ['journal', 'doi', 'status'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('research_fellows', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
