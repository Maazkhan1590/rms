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
        Schema::create('sdg_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('staffname')->nullable()->comment('For Excel import compatibility');
            $table->enum('type', ['paper', 'project', 'talk', 'other'])->nullable();
            $table->string('title');
            $table->integer('sdg')->comment('SDG number (1-17)');
            $table->date('date')->nullable();
            $table->string('evidence_link')->nullable();
            $table->string('related_type')->nullable()->comment('publication, grant, etc.');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID of related item');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'sdg']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sdg_contributions');
    }
};

