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
        Schema::create('workflow_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['research_coordinator', 'dean', 'admin']);
            $table->string('college')->nullable()->comment('NULL means all colleges');
            $table->string('department')->nullable()->comment('NULL means all departments');
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('assigned_at');
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['role', 'college', 'department']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_assignments');
    }
};

