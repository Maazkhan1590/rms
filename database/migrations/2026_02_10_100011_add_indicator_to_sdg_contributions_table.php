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
        Schema::table('sdg_contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('sdg_contributions', 'indicator')) {
                $table->string('indicator')->nullable()->after('sdg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sdg_contributions', function (Blueprint $table) {
            if (Schema::hasColumn('sdg_contributions', 'indicator')) {
                $table->dropColumn('indicator');
            }
        });
    }
};
