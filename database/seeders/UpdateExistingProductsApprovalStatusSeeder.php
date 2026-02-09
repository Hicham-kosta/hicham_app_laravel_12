<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateExistingProductsApprovalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, check if the columns exist
        if (!\Schema::hasColumn('products', 'is_approved')) {
            $this->command->info('Approval columns do not exist. Please run migration first.');
            return;
        }

        $this->command->info('Updating existing products approval status...');
        
        // Update all existing products (assuming they are admin-created)
        $updated = Product::query()->update([
            'is_approved' => true,
            'approved_at' => now(),
            'is_vendor_product' => DB::raw('CASE WHEN vendor_id IS NOT NULL THEN true ELSE false END'),
            'vendor_product_status' => DB::raw("CASE 
                WHEN vendor_id IS NOT NULL THEN 'published' 
                ELSE 'published' 
            END"),
        ]);
        
        $this->command->info("Updated $updated products.");
        
        // Or if you want more specific updates:
        // 
        // // Approve admin products
        // $adminProducts = Product::whereNull('vendor_id')
        //     ->orWhere('vendor_id', 0)
        //     ->update([
        //         'is_approved' => true,
        //         'approved_at' => now(),
        //         'is_vendor_product' => false,
        //         'vendor_product_status' => 'published',
        //     ]);
        // 
        // $this->command->info("Approved $adminProducts admin products.");
        // 
        // // Set vendor products as pending or approved
        // $vendorProducts = Product::whereNotNull('vendor_id')
        //     ->where('vendor_id', '!=', 0)
        //     ->update([
        //         'is_approved' => true, // or false if you want them pending
        //         'is_vendor_product' => true,
        //         'vendor_product_status' => 'published', // or 'submitted' for pending
        //         'approved_at' => now(), // or null for pending
        //     ]);
        // 
        // $this->command->info("Updated $vendorProducts vendor products.");
    }
}