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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('brand_id')->nullable();
            $table->integer('admin_id');
            $table->string('admin_role');

            $table->string('product_name');
            $table->string('product_code');
            $table->string('product_color');
            $table->string('family_color');
            $table->string('group_code')->nullable();
            $table->float('product_price');
            $table->float('product_discount');
            $table->float('product_discount_amount');

            $table->string('discount_applied_on');
            $table->float('product_gst')->default(0);

            $table->float('final_price');
            $table->string('main_image')->nullable();

            $table->float('product_weight')->default(0);
            $table->string('product_video')->nullable();
            $table->text('description')->nullable();
            $table->text('wash_care')->nullable();
            $table->text('search_keywords')->nullable();
            $table->string('fabric')->nullable();
            $table->string('pattern')->nullable();
            $table->string('sleeve')->nullable();
            $table->string('fit')->nullable();
            $table->string('occasion')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('sort')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->enum('is_featured', ['No', 'Yes']);
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
