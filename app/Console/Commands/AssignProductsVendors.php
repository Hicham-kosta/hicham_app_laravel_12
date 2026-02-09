<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Admin;

class AssignProductsToVendors extends Command
{
    protected $signature = 'products:assign-vendors';
    protected $description = 'Assign existing products to vendors';

    public function handle()
    {
        $products = Product::whereNull('vendor_id')->get();
        
        if ($products->isEmpty()) {
            $this->info('No products without vendors found.');
            return;
        }
        
        $vendors = Admin::where('role', 'vendor')
            ->whereHas('vendorDetails', function($q) {
                $q->where('is_verified', 1);
            })
            ->get();
            
        if ($vendors->isEmpty()) {
            $this->error('No approved vendors found.');
            return;
        }
        
        $bar = $this->output->createProgressBar(count($products));
        
        foreach ($products as $product) {
            // Assign to a random vendor or based on some logic
            $randomVendor = $vendors->random();
            $product->update(['vendor_id' => $randomVendor->id]);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Products assigned to vendors successfully!');
    }
}