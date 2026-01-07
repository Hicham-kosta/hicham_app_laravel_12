<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            // Link to your order table 
            $table->unsignedBigInteger('order_id')->nullable()->index();
            // Generic gateway identifier (paypal, stripe, etc.)
            $table->string('gateway', 50)->index();
            // Gateway specific ids
            $table->string('gateway_order_id')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->string('type')->default('payment')->index();
            $table->string('status')->nullable()->index();
            // Monetary values
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('original_amount', 12, 2)->nullable();
            $table->string('original_currency', 10)->nullable();
            $table->decimal('converted_amount', 12, 2)->nullable();
            $table->decimal('conversion_rate', 18, 8)->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('fee', 12, 2)->nullable()->comment('Fee charged by gateway, if provided');
            // Payer information if available
            $table->string('payer_id')->nullable();
            $table->string('payer_email')->nullable();
            // JSON response from the gateway
            $table->json('raw_response')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->unique(['gateway', 'gateway_order_id'], 'gateway_gatewayorder_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};