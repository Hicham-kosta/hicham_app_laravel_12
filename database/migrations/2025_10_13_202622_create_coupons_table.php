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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_option')->nullable(); // Manual or Automatic
            $table->string('coupon_code')->unique();
            $table->text('categories')->nullable(); // JSON array
            $table->text('brands')->nullable(); // JSON array for brands (we added)
            $table->text('users')->nullable(); // JSON array or CSV for users (emails)
            $table->string('coupon_type')->nullable(); // Single or Multiple
            $table->string('amount_type')->default('fixed'); // Fixed or Percentage
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('min_qty')->nullable();
            $table->integer('max_qty')->nullable();
            $table->decimal('min_cart_value', 10, 2)->nullable();
            $table->decimal('max_cart_value', 10, 2)->nullable();
            $table->integer('total_usage_limit')->default(0); // 0 for unlimited
            $table->integer('usage_limit_per_user')->default(0);
            $table->decimal('max_discount', 10, 2)->nullable(); // optional cap
            $table->date('expiry_date')->nullable();
            $table->tinyinteger('status')->default(1);
            $table->tinyinteger('visible')->default(0);
            $table->integer('used_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
