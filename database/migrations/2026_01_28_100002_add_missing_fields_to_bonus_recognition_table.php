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
        Schema::table('bonus_recognition', function (Blueprint $table) {
            // Submission Year
            if (!Schema::hasColumn('bonus_recognition', 'submission_year')) {
                $table->year('submission_year')->nullable()->after('year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_recognition', function (Blueprint $table) {
            $table->dropColumn([
                'submission_year',
            ]);
        });
    }
};
