<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateCommissions extends Command
{
    
protected $signature = 'commissions:calculate {--vendor=} {--order=} {--all}';
protected $description = 'Calculate commissions for vendors';

public function handle()
{
    if ($this->option('all')) {
        $orders = Order::all();
        $this->info("Calculating commissions for all orders...");
    } elseif ($this->option('vendor')) {
        $vendorId = $this->option('vendor');
        $orders = Order::whereHas('orderItems', function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->get();
        $this->info("Calculating commissions for vendor {$vendorId}...");
    } elseif ($this->option('order')) {
        $orders = Order::where('id', $this->option('order'))->get();
        $this->info("Calculating commission for order {$this->option('order')}...");
    } else {
        $this->error("Please specify --all, --vendor=ID, or --order=ID");
        return;
    }

    $bar = $this->output->createProgressBar(count($orders));
    
    foreach ($orders as $order) {
        $this->commissionService->calculateAndSaveOrderCommission($order->id);
        $bar->advance();
    }
    
    $bar->finish();
    $this->newLine();
    $this->info("Commissions calculated successfully!");
}
}
