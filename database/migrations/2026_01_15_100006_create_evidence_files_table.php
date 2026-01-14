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
        Schema::create('evidence_files', function (Blueprint $table) {
            $table->id();
            $table->enum('submission_type', ['publication', 'grant', 'rtn', 'bonus']);
            $table->unsignedBigInteger('submission_id')->comment('Polymorphic reference');
            $table->string('file_path')->comment('Storage path');
            $table->string('file_name')->comment('Original filename');
            $table->string('file_type')->comment('MIME type');
            $table->unsignedBigInteger('file_size')->comment('Bytes');
            $table->enum('file_category', [
                'acceptance_letter',
                'award_letter',
                'doi_link',
                'certificate',
                'appointment_letter',
                'agenda',
                'other'
            ])->default('other');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('uploaded_at');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['submission_type', 'submission_id']);
            $table->index(['uploaded_by', 'uploaded_at']);
            $table->index(['is_verified', 'verified_by']);
            $table->index('file_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_files');
    }
};

