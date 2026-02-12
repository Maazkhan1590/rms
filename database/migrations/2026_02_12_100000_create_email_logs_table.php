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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('notification_type')->nullable()->comment('Class name of notification');
            $table->string('status')->default('sent')->comment('sent, failed, queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('metadata')->nullable()->comment('JSON encoded additional data');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['recipient_email', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['notification_type', 'created_at']);
            $table->index('sent_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
