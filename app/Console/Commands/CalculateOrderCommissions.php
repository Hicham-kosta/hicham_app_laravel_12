<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Admin\VendorCommissionService;
use App\Models\Order;

class CalculateOrderCommissions extends Command
{
    protected $signature = 'commissions:calculate {--order-id=} {--all}';
    protected $description = 'Calculate and save commissions for orders';

    public function handle()
    {
        $commissionService = new VendorCommissionService();
        
        if ($this->option('order-id')) {
            $order = Order::find($this->option('order-id'));
            if ($order) {
                $result = $commissionService->calculateAndSaveOrderCommission($order->id);
                $this->info("Commission calculated for Order #{$order->id}");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Order ID', $result['order_id']],
                        ['Total Commission', $result['total_commission']],
                        ['Vendor Payable', $result['total_vendor_payable']],
                        ['Admin Earnings', $result['total_admin_earnings']],
                    ]
                );
            } else {
                $this->error("Order not found.");
            }
        } elseif ($this->option('all')) {
            $orders = Order::where('status', '!=', 'cancelled')->get();
            $bar = $this->output->createProgressBar(count($orders));
            
            foreach ($orders as $order) {
                try {
                    $commissionService->calculateAndSaveOrderCommission($order->id);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("Error processing Order #{$order->id}: " . $e->getMessage());
                }
            }
            
            $bar->finish();
            $this->info("\nCommissions calculated for all orders.");
        } else {
            $this->info("Usage:");
            $this->line("  php artisan commissions:calculate --order-id=123");
            $this->line("  php artisan commissions:calculate --all");
        }
    }
}