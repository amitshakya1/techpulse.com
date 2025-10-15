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
        Schema::create('stores', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED - same as unsignedBigInteger in foreign key

            $table->string('shop_name')->nullable();
            $table->string('shop_domain')->unique(); // e.g. myshop.myshopify.com
            $table->string('access_token')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();

            $table->enum('status', ['active', 'draft', 'archived'])->default('draft');
            $table->json('config')->nullable(); // optional settings
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
