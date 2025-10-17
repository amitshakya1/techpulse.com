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
        Schema::create('metal_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Gold, Silver, etc.
            $table->string('metal', 10);      // XAU, XAG, etc.
            $table->string('currency', 10);   // USD, INR, etc.
            $table->string('exchange')->nullable();
            $table->string('symbol')->nullable();

            $table->decimal('prev_close_price', 15, 4)->nullable();
            $table->decimal('open_price', 15, 4)->nullable();
            $table->decimal('low_price', 15, 4)->nullable();
            $table->decimal('high_price', 15, 4)->nullable();
            $table->bigInteger('open_time')->nullable(); // timestamp in seconds
            $table->decimal('price', 15, 4)->nullable();
            $table->decimal('ch', 15, 4)->nullable();
            $table->decimal('chp', 10, 4)->nullable();
            $table->decimal('ask', 15, 4)->nullable();
            $table->decimal('bid', 15, 4)->nullable();

            $table->decimal('price_gram_24k', 15, 4)->nullable();
            $table->decimal('price_gram_22k', 15, 4)->nullable();
            $table->decimal('price_gram_21k', 15, 4)->nullable();
            $table->decimal('price_gram_20k', 15, 4)->nullable();
            $table->decimal('price_gram_18k', 15, 4)->nullable();
            $table->decimal('price_gram_16k', 15, 4)->nullable();
            $table->decimal('price_gram_14k', 15, 4)->nullable();
            $table->decimal('price_gram_10k', 15, 4)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metal_rates');
    }
};
