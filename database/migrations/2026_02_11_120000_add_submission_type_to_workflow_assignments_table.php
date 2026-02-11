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
        Schema::table('workflow_assignments', function (Blueprint $table) {
            $table->enum('submission_type', ['publication', 'grant', 'rtn', 'bonus'])
                ->nullable()
                ->after('role');

            $table->index(['role', 'submission_type', 'college', 'department'], 'workflow_assignments_role_type_scope_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_assignments', function (Blueprint $table) {
            $table->dropIndex('workflow_assignments_role_type_scope_index');
            $table->dropColumn('submission_type');
        });
    }
};

