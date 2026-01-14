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
        Schema::create('scoring_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['publication', 'grant', 'rtn', 'bonus']);
            $table->string('category')->nullable()->comment('e.g., Journal, Conference, Book');
            $table->string('subcategory')->nullable()->comment('e.g., Q1, Q2, Scopus, Non-indexed');
            $table->decimal('points', 8, 2);
            $table->decimal('cap', 8, 2)->nullable()->comment('e.g., 120 for journals, 15 for conferences');
            $table->boolean('is_active')->default(true);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('version')->nullable();
            $table->json('conditions')->nullable()->comment('Additional conditions');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_active']);
            $table->index(['effective_from', 'effective_to']);
            $table->index(['category', 'subcategory']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_policies');
    }
};

