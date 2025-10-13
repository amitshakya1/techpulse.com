<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');                      // Page title
            $table->string('slug')->unique();             // URL-friendly identifier
            $table->string('meta_title')->nullable();     // SEO meta title
            $table->text('meta_description')->nullable(); // SEO meta description
            $table->text('meta_keywords')->nullable(); // SEO meta keywords
            $table->longText('content')->nullable();      // Main page content (HTML/text)

            // Enum for page status: active, draft, archived
            $table->enum('status', ['active', 'draft', 'archived'])->default('draft');

            $table->unsignedBigInteger('created_by')->nullable(); // Who created it
            $table->unsignedBigInteger('updated_by')->nullable(); // Who last updated it
            $table->timestamps();
            $table->softDeletes(); // For soft deletion (adds deleted_at column)

            // Optional foreign key if users table exists
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
