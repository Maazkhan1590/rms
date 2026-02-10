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
        // First, drop the unique index on employee_id only if it actually exists.
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();

        $indexExists = $connection->selectOne(
            "SELECT COUNT(*) AS count
             FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = 'users' AND index_name = 'users_employee_id_unique'",
            [$dbName]
        );

        if ($indexExists && $indexExists->count > 0) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_employee_id_unique');
            });
        }

        // Then, ensure the column itself is nullable and not constrained
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't try to recreate the unique index automatically to avoid failures.
        // If you need it back, you can add a dedicated migration.
    }
};


