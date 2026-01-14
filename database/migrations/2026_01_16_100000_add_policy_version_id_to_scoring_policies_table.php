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
        Schema::table('scoring_policies', function (Blueprint $table) {
            if (!Schema::hasColumn('scoring_policies', 'policy_version_id')) {
                $table->foreignId('policy_version_id')->nullable()->after('version')->constrained('policy_versions')->nullOnDelete();
                $table->index('policy_version_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scoring_policies', function (Blueprint $table) {
            if (Schema::hasColumn('scoring_policies', 'policy_version_id')) {
                $table->dropForeign(['policy_version_id']);
                $table->dropIndex(['policy_version_id']);
                $table->dropColumn('policy_version_id');
            }
        });
    }
};
