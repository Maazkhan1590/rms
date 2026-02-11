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
        Schema::create('site_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('e.g., footer_description, footer_address, footer_phone, footer_email, footer_hours, social_twitter, social_linkedin, etc.');
            $table->string('type')->default('text')->comment('text, html, url, email, phone');
            $table->text('value')->nullable();
            $table->string('label')->nullable()->comment('Human readable label');
            $table->string('section')->default('footer')->comment('footer, header, general');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['section', 'is_active', 'order']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_contents');
    }
};
