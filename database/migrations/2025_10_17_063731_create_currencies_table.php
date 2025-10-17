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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('base_code', 10); // e.g., USD
            $table->string('code', 10);      // e.g., INR
            $table->decimal('exchange_rate', 15, 6)->default(1.0);
            $table->enum('status', ['active', 'draft', 'archived'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['base_code', 'code']); // composite unique key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
