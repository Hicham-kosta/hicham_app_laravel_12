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
            $item->qty,
            $vendorCommission
        );

        // **UPDATE: Include GST fields when updating order item**
        $item->commission_percent = $calculation['commission_percent'];
        $item->commission_amount = $calculation['commission_amount'];
        $item->vendor_amount = $calculation['vendor_payable'];
        $item->vendor_id = $vendor->id;
        
        // **ADD: Calculate GST if not already set**
        if (empty($item->product_gst) && !empty($item->product->product_gst)) {
            $item->product_gst = $item->product->product_gst;
            $item->product_gst_amount = ($item->price * $item->qty * $item->product->product_gst) / 100;
        }
        
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
     * Get vendor commission summary - FIXED: Use 'subtotal' instead of 'amount'
     */
    public function getVendorCommissionSummary($vendorId, $startDate = null, $endDate = null)
    {
        $query = CommissionHistory::where('vendor_id', $vendorId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('commission_history.created_at', [$startDate, $endDate]);
        }

        $history = $query->orderBy('created_at', 'desc')->get();

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

    // app/Services/Admin/VendorCommissionService.php

public function getVendorDashboardData($vendorId, $period = 'month')
{
    $now = Carbon::now();
    
    switch ($period) {
        case 'today':
            $startDate = $now->copy()->startOfDay();
            $endDate   = $now->copy()->endOfDay();
            break;
        case 'week':
            $startDate = $now->copy()->startOfWeek();
            $endDate   = $now->copy()->endOfWeek();
            break;
        case 'month':
            $startDate = $now->copy()->startOfMonth();
            $endDate   = $now->copy()->endOfMonth();
            break;
        case 'year':
            $startDate = $now->copy()->startOfYear();
            $endDate   = $now->copy()->endOfYear();
            break;
        default:
            $startDate = null;
            $endDate   = null;
    }

    // Totals for the selected period
    $totalsQuery = CommissionHistory::where('vendor_id', $vendorId);
    if ($startDate && $endDate) {
        $totalsQuery->whereBetween('commission_date', [$startDate, $endDate]);
    }
    $totals = $totalsQuery->selectRaw('
            COALESCE(SUM(subtotal), 0)         as total_sales,
            COALESCE(SUM(commission_amount), 0) as total_commission,
            COALESCE(SUM(vendor_amount), 0)     as total_earned,
            COUNT(DISTINCT order_id)            as total_orders
        ')->first();

    // 7‑day trend
    $trend = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $now->copy()->subDays($i)->format('Y-m-d');
        $day = CommissionHistory::where('vendor_id', $vendorId)
            ->whereDate('commission_date', $date)
            ->selectRaw('
                COALESCE(SUM(subtotal), 0) as sales,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(vendor_amount), 0) as earned
            ')
            ->first();
        $trend[$date] = [
            'sales'     => $day->sales ?? 0,
            'commission'=> $day->commission ?? 0,
            'earned'    => $day->earned ?? 0,
        ];
    }

    // Recent 10 commissions
    $recent = CommissionHistory::where('vendor_id', $vendorId)
        ->orderBy('commission_date', 'desc')
        ->limit(10)
        ->get();

    // Monthly breakdown (already in your controller)
    $monthly = CommissionHistory::where('vendor_id', $vendorId)
        ->selectRaw('
            YEAR(commission_date) as year,
            MONTH(commission_date) as month,
            SUM(subtotal) as total_sales,
            SUM(commission_amount) as total_commission,
            SUM(vendor_amount) as total_earnings,
            SUM(CASE WHEN status = "pending" THEN vendor_amount ELSE 0 END) as pending,
            SUM(CASE WHEN status = "paid" THEN vendor_amount ELSE 0 END) as paid
        ')
        ->groupByRaw('YEAR(commission_date), MONTH(commission_date)')
        ->orderByRaw('YEAR(commission_date) DESC, MONTH(commission_date) DESC')
        ->get();

    // Top products (already in your controller)
    $topProducts = CommissionHistory::where('vendor_id', $vendorId)
        ->selectRaw('
            product_id,
            product_name,
            COUNT(*) as order_count,
            SUM(qty) as total_qty,
            SUM(subtotal) as total_sales,
            SUM(commission_amount) as total_commission,
            SUM(vendor_amount) as total_earnings
        ')
        ->groupBy('product_id', 'product_name')
        ->orderBy('total_sales', 'desc')
        ->limit(10)
        ->get();

    return [
        'period'       => $period,
        'start_date'   => $startDate ? $startDate->format('Y-m-d') : null,
        'end_date'     => $endDate ? $endDate->format('Y-m-d') : null,
        'totals'       => $totals,
        'trend'        => $trend,
        'recent'       => $recent,
        'monthly'      => $monthly,
        'topProducts'  => $topProducts,
        'commission_percent' => $this->getVendorCommission($vendorId),
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
            $startDate = $now->copy()->subDays(30);
            $endDate = $now->copy();
    }

    // **FIXED: Use CommissionHistory model (plural)**
    $totalCommissions = CommissionHistory::query()
        ->whereBetween('commission_date', [$startDate, $endDate])
        ->selectRaw('
            COALESCE(SUM(subtotal), 0) as total_sales,
            COALESCE(SUM(commission_amount), 0) as total_commission,
            COALESCE(SUM(vendor_amount), 0) as total_vendor_payable,
            COUNT(DISTINCT order_id) as total_orders,
            COUNT(DISTINCT vendor_id) as total_vendors
        ')
        ->first();

    // **FIXED: Use CommissionHistory model with correct relationships**
    $vendorBreakdown = CommissionHistory::query()
        ->whereBetween('commission_date', [$startDate, $endDate])
        ->with(['vendor', 'vendor.vendorDetails'])
        ->selectRaw('
            vendor_id,
            COUNT(*) as item_count,
            COALESCE(SUM(subtotal), 0) as total_sales,
            COALESCE(SUM(commission_amount), 0) as total_commission,
            COALESCE(SUM(vendor_amount), 0) as vendor_earnings,
            COALESCE(SUM(CASE WHEN status = "paid" THEN vendor_amount ELSE 0 END), 0) as paid_amount,
            COALESCE(SUM(CASE WHEN status = "pending" THEN vendor_amount ELSE 0 END), 0) as pending_amount
        ')
        ->groupBy('vendor_id')
        ->orderBy('total_commission', 'desc')
        ->get()
        ->map(function ($item) {
            return (object)[
                'id' => $item->vendor_id,
                'name' => $item->vendor->name ?? 'Unknown Vendor',
                'shop_name' => $item->vendor->vendorDetails->shop_name ?? '',
                'commission_percent' => $item->vendor->vendorDetails->commission_percent ?? 0,
                'total_sales' => $item->total_sales,
                'total_commission' => $item->total_commission,
                'vendor_earnings' => $item->vendor_earnings,
                'pending_amount' => $item->pending_amount,
                'paid_amount' => $item->paid_amount,
            ];
        });

    // **FIXED: Commission trend for last 7 days**
    $trend = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $now->copy()->subDays($i)->format('Y-m-d');
        $dayCommission = CommissionHistory::whereDate('commission_date', $date)
            ->sum('commission_amount');
        $trend[$date] = $dayCommission ?? 0;
    }

    return [
        'period' => $period,
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $endDate->format('Y-m-d'),
        'totals' => $totalCommissions,
        'vendor_breakdown' => $vendorBreakdown,
        'trend' => $trend,
    ];
}

// Also update the recordCommissionHistory method
// In VendorCommissionService.php - CORRECTED METHOD
public function recordCommissionHistory($order)
{
    foreach ($order->orderItems as $item) {
        if ($item->vendor_id && $item->commission_amount > 0) {
            $subtotal = $item->price * $item->qty;
            
            // **CORRECTED: Use OrderItem model's actual GST field names**
            $gstPercent = $item->product_gst ?? 0; // From OrderItem model
            $gstAmount = $item->product_gst_amount ?? 0; // From OrderItem model
            
            // If GST amount is not set but GST percent is, calculate it
            if ($gstPercent > 0 && $gstAmount == 0) {
                $gstAmount = ($subtotal * $gstPercent) / 100;
            }
            
            CommissionHistory::create([
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'vendor_id' => $item->vendor_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name ?? ($item->product->product_name ?? 'Unknown Product'),
                'sku' => $item->sku ?? ($item->product->sku ?? null),
                'size' => $item->size ?? null,
                'item_price' => $item->price,
                'qty' => $item->qty,
                'subtotal' => $subtotal,
                'commission_percent' => $item->commission_percent ?? 0,
                'commission_amount' => $item->commission_amount ?? 0,
                'vendor_amount' => $item->vendor_amount ?? 0,
                'gst_percent' => $gstPercent,
                'gst_amount' => $gstAmount,
                'status' => 'pending',
                'commission_date' => now()->toDateString(),
                'month' => now()->month,
                'year' => now()->year,
            ]);
        }
    }
}
    /**
     * Process commission payment to vendor
     */
    // When processing a payment, these fields get filled
public function processVendorPayment($vendorId, $amount, $paymentMethod, $reference, $notes = null)
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
            'payment_notes' => $notes,
            'processed_by' => auth('admin')->id(),
        ]);

        $totalPaid += $payable;
        $processedRecords[] = $record->id;
    }

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