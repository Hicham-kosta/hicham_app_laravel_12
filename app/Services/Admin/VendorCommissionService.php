<?php

namespace App\Services\Admin;

use App\Models\VendorDetail;
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;

class VendorCommissionService
{
    /**
     * Get vendor commission percentage
     */
    public function getVendorCommission($vendorId)
    {
        $vendorDetail = VendorDetail::where('admin_id', $vendorId)->first();
        return $vendorDetail ? $vendorDetail->commission_percent : 0.00;
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
    public function calculateCommissionForItem($itemPrice, $quantity, $commissionPercent)
    {
        $subtotal = $itemPrice * $quantity;
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
     * Calculate commission for an order
     */
    public function calculateOrderCommissions($orderId)
    {
        $order = Order::with(['items', 'items.product.vendor'])->findOrFail($orderId);
        
        $commissionData = [];
        $totalCommission = 0;
        $totalVendorPayable = 0;

        foreach ($order->items as $item) {
            if ($item->product && $item->product->vendor) {
                $vendor = $item->product->vendor;
                $vendorCommission = $this->getVendorCommission($vendor->id);
                
                $calculation = $this->calculateCommissionForItem(
                    $item->price,
                    $item->quantity,
                    $vendorCommission
                );

                $commissionData[] = [
                    'order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->product_name,
                    'vendor_id' => $vendor->id,
                    'vendor_name' => $vendor->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'gst' => $item->gst ?? 0,
                    'subtotal' => $calculation['subtotal'],
                    'commission_percent' => $calculation['commission_percent'],
                    'commission_amount' => $calculation['commission_amount'],
                    'vendor_payable' => $calculation['vendor_payable']
                ];

                $totalCommission += $calculation['commission_amount'];
                $totalVendorPayable += $calculation['vendor_payable'];
            }
        }

        return [
            'commission_data' => $commissionData,
            'summary' => [
                'total_order_amount' => $order->grand_total,
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
}