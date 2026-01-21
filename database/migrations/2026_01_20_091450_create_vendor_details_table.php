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
        Schema::create('vendor_details', function (Blueprint $table) {
            $table->id();

            // Link with admins table (vendor user)
            $table->unsignedBigInteger('admin_id')->unique();

            // Business Details
            $table->string('shop_name')->nullable();
            $table->string('shop_address')->nullable();
            $table->string('shop_city')->nullable();
            $table->string('shop_state')->nullable();
            $table->string('shop_country')->nullable();
            $table->string('shop_pincode')->nullable();
            $table->string('shop_mobile')->nullable();
            $table->string('shop_email')->nullable();
            $table->string('shop_website')->nullable();

            // Legal KYC Details
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('business_license_number')->nullable();
            $table->string('address_proof')->nullable();
            $table->string('address_proof_image')->nullable();

            // Bank Details
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();

            // Commission Future Use
            $table->decimal('commission_percent',5,2)->default(0.00);
            // Example: 10.50 = 10.5%

            // Approval Status
            $table->tinyInteger('is_verified')->default(0);
            // 0 = Pending  1 = Approved
            $table->timestamps();

            // Optional FK (recommended if admins table is stable)
            // $table->foreign('admin_id)->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_details');
    }
};
