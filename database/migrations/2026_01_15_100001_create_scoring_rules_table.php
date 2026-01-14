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
        Schema::create('scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('scoring_policies')->cascadeOnDelete();
            $table->string('rule_name');
            $table->string('rule_type')->comment('e.g., publication_type, grant_role, rtn_type');
            $table->decimal('points', 8, 2);
            $table->json('conditions')->nullable()->comment('Complex conditions');
            $table->integer('priority')->default(0)->comment('Rule priority order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['policy_id', 'is_active']);
            $table->index(['rule_type', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
    }
};

