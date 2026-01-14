<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // College and Department relationships
            if (!Schema::hasColumn('users', 'college_id')) {
                $table->foreignId('college_id')->nullable()->after('email')->constrained('colleges')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('college_id')->constrained('departments')->nullOnDelete();
            }
            
            // Note: employee_id, designation, phone, profile_photo, orcid, google_scholar, 
            // research_gate, credentials_file already exist from 2026_01_12_000000_add_auth_fields_to_users_table
            
            // Research points tracking
            if (!Schema::hasColumn('users', 'total_research_points')) {
                $table->decimal('total_research_points', 10, 2)->default(0)->after('status')->comment('Cached total');
            }
            if (!Schema::hasColumn('users', 'last_points_calculation')) {
                $table->dateTime('last_points_calculation')->nullable()->after('total_research_points');
            }
        });
        
        // Add indexes only if they don't already exist
        $this->addIndexIfNotExists('users', 'college_id', 'users_college_id_index');
        $this->addIndexIfNotExists('users', 'department_id', 'users_department_id_index');
        // Note: status index already exists from 2026_01_12_000000_add_auth_fields_to_users_table
        // Note: employee_id already has unique index from 2026_01_12_000000_add_auth_fields_to_users_table
    }
    
    /**
     * Add index if it doesn't already exist
     */
    private function addIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }
        
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        $indexExists = $connection->selectOne(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$dbName, $table, $indexName]
        );
        
        if ($indexExists->count == 0) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->index($column);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $this->dropIndexIfExists('users', 'users_college_id_index');
            $this->dropIndexIfExists('users', 'users_department_id_index');
            
            // Drop foreign keys
            if (Schema::hasColumn('users', 'college_id')) {
                $table->dropForeign(['college_id']);
            }
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
            }
            
            // Only drop columns added by this migration
            // Note: employee_id, designation, phone, profile_photo, orcid, google_scholar,
            // research_gate, credentials_file are from 2026_01_12_000000_add_auth_fields_to_users_table
            $columnsToDrop = ['college_id', 'department_id', 'total_research_points', 'last_points_calculation'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
    
    /**
     * Drop index if it exists
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        $indexExists = $connection->selectOne(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$dbName, $table, $indexName]
        );
        
        if ($indexExists->count > 0) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};

