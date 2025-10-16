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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id'); // link to store
            $table->string('api_key', 100)->unique(); // unique API key
            $table->enum('status', ['active', 'draft', 'archived'])->default('draft');
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at column for soft delete

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
