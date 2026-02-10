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
            if (!Schema::hasColumn('bonus_recognition', 'evidence_link')) {
                $table->string('evidence_link')->nullable()->after('evidence_files');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_recognition', function (Blueprint $table) {
            if (Schema::hasColumn('bonus_recognition', 'evidence_link')) {
                $table->dropColumn('evidence_link');
            }
        });
    }
};
