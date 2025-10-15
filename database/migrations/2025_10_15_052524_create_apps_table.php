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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();

            // Basic app info
            $table->string('name');
            $table->string('shopify_app_key')->nullable();
            $table->string('shopify_app_secret')->nullable();
            $table->string('api_version')->default('2025-01');

            // Auth and access
            $table->string('access_token')->nullable();

            // Status and activity
            $table->enum('status', ['active', 'draft', 'archived'])->default('draft');

            // Store relationship
            $table->unsignedBigInteger('store_id')->nullable()->index();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            // Metadata / configuration
            $table->json('config')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
