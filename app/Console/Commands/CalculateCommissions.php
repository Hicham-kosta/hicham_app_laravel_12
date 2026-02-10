<?php

// app/Console/Commands/CalculateCommissions.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\Admin\VendorCommissionService;

class CalculateCommissions extends Command
{
    protected $signature = 'commissions:calculate {orderId?} {--all}';
    protected $description = 'Calculate commissions for orders';
    
    public function handle()
    {
        $orderId = $this->argument('orderId');
        $all = $this->option('all');
        
        if ($all) {
            // Calculate for all orders without commissions
            $orders = Order::whereDoesntHave('commissionHistories')
                ->whereHas('orderItems.product', function($q) {
                    $q->whereNotNull('vendor_id');
                })
                ->get();
                
            $this->info("Found {$orders->count()} orders without commissions");
            
            $bar = $this->output->createProgressBar($orders->count());
            
            $service = new VendorCommissionService();
            $totalCommission = 0;
            
            foreach ($orders as $order) {
                try {
                    $result = $service->calculateAndSaveOrderCommission($order->id);
                    $totalCommission += $result['total_commission'] ?? 0;
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("Error processing order {$order->id}: " . $e->getMessage());
                }
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Total commission calculated: ₹" . number_format($totalCommission, 2));
            
        } elseif ($orderId) {
            // Calculate for specific order
            $service = new VendorCommissionService();
            $result = $service->calculateAndSaveOrderCommission($orderId);
            
            $this->info("Commission calculated for Order {$orderId}:");
            $this->info("Total Commission: ₹" . number_format($result['total_commission'], 2));
            $this->info("Vendor Payable: ₹" . number_format($result['total_vendor_payable'], 2));
            
        } else {
            $this->error("Please specify an order ID or use --all option");
            $this->info("Usage:");
            $this->info("  php artisan commissions:calculate --all");
            $this->info("  php artisan commissions:calculate 123");
        }
    }
}