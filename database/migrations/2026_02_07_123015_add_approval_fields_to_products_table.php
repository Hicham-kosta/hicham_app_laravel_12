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
        Schema::table('products', function (Blueprint $table) {
            // Add approval status column (0=pending, 1=approved, 2=rejected)
            $table->tinyInteger('is_approved')
                ->default(0)
                ->after('status')
                ->comment('0 = Pending, 1 = Approved, 2 = Rejected');
            
            // Timestamp when product was approved
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            
            // Admin who approved the product
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            
            // Timestamp when product was rejected
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            
            // Admin who rejected the product
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            
            // Reason for rejection
            $table->text('rejection_reason')->nullable()->after('rejected_by');
            
            // Add foreign key constraints (optional but recommended)
            $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('admins')->onDelete('set null');
            
            // If you want to add vendor-specific fields
            $table->boolean('is_vendor_product')->default(false)->after('rejection_reason');
            $table->enum('vendor_product_status', ['draft', 'submitted', 'published', 'rejected'])
                ->default('draft')
                ->after('is_vendor_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            
            // Drop columns
            $table->dropColumn([
                'is_approved',
                'approved_at',
                'approved_by',
                'rejected_at',
                'rejected_by',
                'rejection_reason',
                'is_vendor_product',
                'vendor_product_status'
            ]);
        });
    }
};