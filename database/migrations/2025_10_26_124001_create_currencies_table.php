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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();  // GBP $USD EUR
            $table->string('symbol', 10)->nullable();  // £ $ €
            $table->string('name')->nullable();  // British Pound Sterling US Dollar Euro
            $table->decimal('rate', 16, 8)->default(1);  // relative to base (GBP = 1)
            $table->tinyInteger('status')->default(1);  // 1 = active, 0 = inactive
            $table->boolean('is_base')->default(false);  // only one true
            $table->string('flag', 64)->nullable();  // gb.png, us.png, eu.png
            $table->timestamps();
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
