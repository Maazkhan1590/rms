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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('phone');
            }
            
            // Academic Information
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('profile_photo');
            }
            
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable()->after('department');
            }
            
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->unique()->nullable()->after('designation');
            }
            
            // Research Profiles
            if (!Schema::hasColumn('users', 'orcid')) {
                $table->string('orcid', 19)->nullable()->after('employee_id');
            }
            
            if (!Schema::hasColumn('users', 'google_scholar')) {
                $table->string('google_scholar')->nullable()->after('orcid');
            }
            
            if (!Schema::hasColumn('users', 'research_gate')) {
                $table->string('research_gate')->nullable()->after('google_scholar');
            }
            
            // Credentials
            if (!Schema::hasColumn('users', 'credentials_file')) {
                $table->string('credentials_file')->nullable()->after('research_gate');
            }
            
            // Account Status
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])
                    ->default('pending')
                    ->after('credentials_file');
            }
            
            // Last Login
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            }
            
            // Add indexes
            $table->index('status');
            $table->index('department');
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'phone',
                'profile_photo',
                'department',
                'designation',
                'employee_id',
                'orcid',
                'google_scholar',
                'research_gate',
                'credentials_file',
                'status',
                'last_login_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
