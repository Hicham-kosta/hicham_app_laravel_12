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
        Schema::create('shipping_charges', function (Blueprint $table) {
            $table->id();
            // link to countries table
            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->string('name', 100)->default('Standard Shipping');
            $table->unsignedInteger('min_weight_g')->nullable();
            $table->unsignedInteger('max_weight_g')->nullable();
            $table->decimal('min_subtotal', 10, 2)->nullable();
            $table->decimal('max_subtotal', 10, 2)->nullable();
            $table->decimal('rate', 10, 2)->default(0);
            //Flags
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charges');
    }
};
