<?php

namespace App\Services\Admin;

use App\Models\VendorDetail;
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CommissionHistory;
use Carbon\Carbon;

class VendorCommissionService
{
    /**
     * Get vendor commission percentage - FIXED
     */
    public function getVendorCommission($vendorId)
    {
        $vendorDetail = VendorDetail::where('admin_id', $vendorId)->first();
        return $vendorDetail ? (float)$vendorDetail->commission_percent : 10.00; // Default 10%
    }

    /**
     * Update vendor commission percentage
     */
    public function updateVendorCommission($vendorId, $commissionPercent)
    {
        $vendorDetail = VendorDetail::updateOrCreate(
            ['admin_id' => $vendorId],
            ['commission_percent' => $commissionPercent]
        );

        return $vendorDetail;
    }

    /**
     * Calculate commission for an order item
     */
    /**
     * Calculate commission for an order item - FIXED
     */
    public function calculateCommissionForItem($itemPrice, $qty, $commissionPercent)
    {
        $subtotal = $itemPrice * $qty;
        $commissionAmount = ($subtotal * $commissionPercent) / 100;
        $vendorPayable = $subtotal - $commissionAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'commission_percent' => round($commissionPercent, 2),
            'commission_amount' => round($commissionAmount, 2),
            'vendor_payable' => round($vendorPayable, 2)
        ];
    }

    /**
     * Calculate commission for an order - FIXED
     */
    public function calculateOrderCommissions($orderId)
    {
        $order = Order::with(['orderItems', 'orderItems.product.vendor'])->findOrFail($orderId);
        
        $commissionData = [];
        $totalCommission = 0;
        $totalVendorPayable = 0;
        $totalOrderAmount = 0;

        foreach ($order->orderItems as $item) {
            // Check if product exists
            if (!$item->product) {
                continue;
            }
            
            // Get vendor from product
            $vendor = null;
            if ($item->product->vendor) {
                $vendor = $item->product->vendor;
            } elseif ($item->vendor_id) {
                // Try to get vendor from order_item if not in product
                $vendor = Admin::find($item->vendor_id);
            }
            
            if (!$vendor) {
                continue;
            }
            
            // Get commission percentage
            $vendorCommission = $this->getVendorCommission($vendor->id);
            
            // Calculate commission
            $calculation = $this->calculateCommissionForItem(
                $item->price,
                $item->qty, // Use qty, not quantity
                $vendorCommission
            );

            $commissionData[] = [
                'order_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'price' => $item->price,
                'quantity' => $item->qty, // Changed to qty
                'gst' => $item->product_gst ?? 0,
                'subtotal' => $calculation['subtotal'],
                'commission_percent' => $calculation['commission_percent'],
                'commission_amount' => $calculation['commission_amount'],
                'vendor_payable' => $calculation['vendor_payable']
            ];

            $totalCommission += $calculation['commission_amount'];
            $totalVendorPayable += $calculation['vendor_payable'];
            $totalOrderAmount += $calculation['subtotal'];
        }

        return [
            'commission_data' => $commissionData,
            'summary' => [
                'total_order_amount' => round($totalOrderAmount, 2),
                'total_commission' => round($totalCommission, 2),
                'total_vendor_payable' => round($totalVendorPayable, 2)
            ]
        ];
    }

    /**
     * Get all vendors with their commission rates
     */
    public function getAllVendorsWithCommission()
    {
        return Admin::with('vendorDetails')
            ->where('role', 'vendor')
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'shop_name' => $vendor->vendorDetails->shop_name ?? 'N/A',
                    'commission_percent' => $vendor->vendorDetails->commission_percent ?? 0.00,
                    'is_verified' => $vendor->vendorDetails->is_verified ?? 0,
                    'status' => $vendor->status ? 'Active' : 'Inactive'
                ];
            });
    }

    /**
     * Update multiple vendor commissions
     */
    public function bulkUpdateCommissions($vendorCommissions)
    {
        $updated = [];

        foreach ($vendorCommissions as $vendorId => $commission) {
            $vendorDetail = VendorDetail::where('admin_id', $vendorId)->first();
            
            if ($vendorDetail) {
                $vendorDetail->update(['commission_percent' => $commission]);
                $updated[] = $vendorId;
            }
        }

        return $updated;
    }

    /**
     * Get commission report for a date range
     */
    public function getCommissionReport($startDate, $endDate)
    {
        // This would require order_items linked to products and vendors
        // Implement based on your order structure
        return [];
    }

    /**
     * Calculate and save commission for an order - FIXED
     */
    public function calculateAndSaveOrderCommission($orderId)
    {
        $order = Order::with(['orderItems', 'orderItems.product.vendor'])->findOrFail($orderId);
        
        $totalCommission = 0;
        $totalVendorPayable = 0;
        $totalAdminEarnings = 0;

        foreach ($order->orderItems as $item) {
            if (!$item->product) {
                continue;
            }
            
            $vendor = $item->product->vendor;
            if (!$vendor) {
                continue;
            }
            
            $vendorCommission = $this->getVendorCommission($vendor->id);
            
            $calculation = $this->calculateCommissionForItem(
                $item->price,
                $item->qty, // Use qty
                $vendorCommission
            );

            // Update order item with commission details
            $item->commission_percent = $calculation['commission_percent'];
            $item->commission_amount = $calculation['commission_amount'];
            $item->vendor_amount = $calculation['vendor_payable'];
            $item->vendor_id = $vendor->id;
            $item->save();

            $totalCommission += $calculation['commission_amount'];
            $totalVendorPayable += $calculation['vendor_payable'];
            $totalAdminEarnings += $calculation['commission_amount'];
        }

        // Update order with totals
        $order->total_commission = $totalCommission;
        $order->vendor_payable = $totalVendorPayable;
        $order->admin_earnings = $totalAdminEarnings;
        $order->save();

        // Record commission history
        $this->recordCommissionHistory($order);

        return [
            'order_id' => $order->id,
            'total_commission' => round($totalCommission, 2),
            'total_vendor_payable' => round($totalVendorPayable, 2),
            'total_admin_earnings' => round($totalAdminEarnings, 2)
        ];
    }

    /**
     * Record commission history for tracking
     */
    public function recordCommissionHistory($order)
{
    foreach ($order->orderItems as $item) {
        if ($item->vendor_id && $item->commission_amount > 0) {
            $subtotal = $item->price * $item->qty;
            
            CommissionHistory::create([
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'vendor_id' => $item->vendor_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'sku' => $item->product->sku ?? null,
                'size' => $item->size ?? null,
                'item_price' => $item->price,
                'qty' => $item->qty,
                'subtotal' => $subtotal,
                'commission_percent' => $item->commission_percent,
                'commission_amount' => $item->commission_amount,
                'vendor_amount' => $item->vendor_amount,
                'status' => 'pending',
                'commission_date' => now()->toDateString(),
                'month' => now()->month,
                'year' => now()->year,
                'created_at' => now(),
            ]);
        }
    }
}

    /**
     * Get vendor commission summary - FIXED: Use 'subtotal' instead of 'amount'
     */
    public function getVendorCommissionSummary($vendorId, $startDate = null, $endDate = null)
    {
        $query = CommissionHistory::where('vendor_id', $vendorId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('commission_history.created_at', [$startDate, $endDate]);
        }

        $history = $query->orderBy('commission_history.created_at', 'desc')->get();

        $summary = [
            'total_orders' => $history->count(),
            'total_amount' => $history->sum('subtotal'), // Changed from 'amount' to 'subtotal'
            'total_commission' => $history->sum('commission_amount'),
            'total_vendor_amount' => $history->sum('vendor_amount'),
            'pending_amount' => $history->where('status', 'pending')->sum('vendor_amount'),
            'paid_amount' => $history->where('status', 'paid')->sum('vendor_amount'),
        ];

        return [
            'summary' => $summary,
            'history' => $history,
        ];
    }

    /**
     * Get admin commission dashboard data - FIXED: Use 'subtotal' instead of 'amount'
     */
    public function getAdminCommissionDashboard($period = 'month')
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
        }

        // Total commission statistics - FIXED: Use 'subtotal' instead of 'amount'
        $totalCommissions = CommissionHistory::whereBetween('commission_history.created_at', [$startDate, $endDate])
            ->selectRaw('
                SUM(subtotal) as total_sales,
                SUM(commission_amount) as total_commission,
                SUM(vendor_amount) as total_vendor_payable,
                COUNT(DISTINCT order_id) as total_orders,
                COUNT(DISTINCT vendor_id) as total_vendors
            ')
            ->first();

        // Vendor-wise commission breakdown - FIXED: Use 'subtotal' instead of 'amount'
$vendorBreakdown = CommissionHistory::whereBetween('commission_history.created_at', [$startDate, $endDate])
    ->join('admins', 'commission_history.vendor_id', '=', 'admins.id')
    ->leftJoin('vendor_details', 'admins.id', '=', 'vendor_details.admin_id')
    ->selectRaw('
        admins.id,
        admins.name,
        vendor_details.shop_name,
        vendor_details.commission_percent,
        SUM(commission_history.subtotal) as total_sales,
        SUM(commission_history.commission_amount) as total_commission,
        SUM(commission_history.vendor_amount) as vendor_earnings,
        SUM(CASE WHEN commission_history.status = "paid" THEN commission_history.vendor_amount ELSE 0 END) as paid_amount
    ')
    ->groupBy('admins.id', 'admins.name', 'vendor_details.shop_name', 'vendor_details.commission_percent') // Add all non-aggregated columns
    ->orderBy('total_commission', 'desc')
    ->get();

// Total commissions query:
$totalCommissions = CommissionHistory::whereBetween('commission_history.created_at', [$startDate, $endDate])
    ->selectRaw('
        SUM(subtotal) as total_sales,
        SUM(commission_amount) as total_commission,
        SUM(vendor_amount) as total_vendor_payable,
        COUNT(DISTINCT order_id) as total_orders,
        COUNT(DISTINCT vendor_id) as total_vendors
    ')
    ->first();

        // Commission trend (last 7 days)
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $dayCommission = CommissionHistory::whereDate('commission_history.created_at', $date)
                ->sum('commission_amount');
            $trend[$date] = $dayCommission ?? 0;
        }

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'totals' => $totalCommissions ?? (object)[
                'total_sales' => 0,
                'total_commission' => 0,
                'total_vendor_payable' => 0,
                'total_orders' => 0,
                'total_vendors' => 0,
            ],
            'vendor_breakdown' => $vendorBreakdown,
            'trend' => $trend,
        ];
    }

    /**
     * Process commission payment to vendor
     */
    public function processVendorPayment($vendorId, $amount, $paymentMethod, $reference)
    {
        $vendor = Admin::findOrFail($vendorId);
        
        // Mark commission records as paid
        $unpaidCommissions = CommissionHistory::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->where('vendor_amount', '>', 0)
            ->orderBy('created_at')
            ->get();

        $totalPaid = 0;
        $processedRecords = [];

        foreach ($unpaidCommissions as $record) {
            if ($totalPaid >= $amount) break;

            $payable = min($record->vendor_amount, $amount - $totalPaid);
            
            $record->update([
                'status' => 'paid',
                'payment_date' => now(),
                'payment_method' => $paymentMethod,
                'payment_reference' => $reference,
                'paid_amount' => $payable,
            ]);

            $totalPaid += $payable;
            $processedRecords[] = $record->id;
        }

        // Record payment transaction
        // Note: You need to create VendorPayment model and table first
        /*
        VendorPayment::create([
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'commission_records' => json_encode($processedRecords),
            'status' => 'completed',
            'paid_at' => now(),
        ]);
        */

        return [
            'vendor_id' => $vendorId,
            'vendor_name' => $vendor->name,
            'amount_paid' => $totalPaid,
            'records_processed' => count($processedRecords),
        ];
    }

    // Add this method to your VendorCommissionService
public function checkVendorProducts($vendorId)
{
    $products = \App\Models\Product::where('vendor_id', $vendorId)->count();
    $orderItems = \App\Models\OrderItem::where('vendor_id', $vendorId)->count();
    
    return [
        'products_count' => $products,
        'order_items_count' => $orderItems,
        'has_products' => $products > 0,
        'has_orders' => $orderItems > 0,
    ];
}

public function getPendingAmount($vendorId)
{
    return CommissionHistory::where('vendor_id', $vendorId)
        ->where('status', 'pending')
        ->sum('vendor_amount');
}

public function getCommissionSummaryByPeriod($period = 'month')
{
    $date = now();
    
    switch ($period) {
        case 'today':
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            break;
        case 'week':
            $startDate = $date->copy()->startOfWeek();
            $endDate = $date->copy()->endOfWeek();
            break;
        case 'month':
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            break;
        case 'year':
            $startDate = $date->copy()->startOfYear();
            $endDate = $date->copy()->endOfYear();
            break;
        default:
            $startDate = $date->copy()->subDays(30);
            $endDate = $date->copy();
    }
    
    return CommissionHistory::whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('
            DATE(created_at) as date,
            SUM(subtotal) as total_sales,
            SUM(commission_amount) as total_commission,
            SUM(vendor_amount) as total_payable,
            COUNT(DISTINCT order_id) as order_count
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
}

}