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
        Schema::create('commission_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('order_item_id')->nullable()->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('product_name')->nullable();
            $table->string('sku')->nullable();
            $table->string('size')->nullable();
            
            // Amounts
            $table->decimal('item_price', 10, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('vendor_amount', 10, 2)->default(0);
            
            // GST/Tax related
            $table->decimal('gst_percent', 5, 2)->nullable()->default(0);
            $table->decimal('gst_amount', 10, 2)->nullable()->default(0);
            
            // Payment status
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending');
            
            // Payment details (when paid)
            $table->timestamp('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('payment_notes')->nullable();
            
            // Admin who processed payment
            $table->unsignedBigInteger('processed_by')->nullable()->index();
            
            // Dates for tracking
            $table->timestamp('settled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // For filtering and reporting
            $table->date('commission_date')->nullable()->index();
            $table->integer('month')->nullable()->index();
            $table->integer('year')->nullable()->index();
            
            // Soft deletes
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('admins')->onDelete('set null');
            
            // Indexes for faster queries
            $table->index(['vendor_id', 'status']);
            $table->index(['vendor_id', 'commission_date']);
            $table->index(['order_id', 'vendor_id']);
            $table->index(['status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_history');
    }
};
