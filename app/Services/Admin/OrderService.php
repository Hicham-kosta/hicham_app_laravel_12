<?php

namespace App\Services\Admin;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderLog;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderStatusUpdated;

class OrderService
{
    /**
     * Get all orders for Admin / Vendor listing
     */
    public function getAllOrders()
{
    $admin = Auth::guard('admin')->user();
    $status = 'success';
    $message = '';
    $ordersModule = [];

    /*-----------------------
    VENDOR FLOW
    -----------------------*/
    if($admin->role === 'vendor'){
        // Vendor KYC not approved
        if(!$admin->vendorDetails || (int)$admin->vendorDetails->is_verified === 0){
            return [
                'orders' => collect(),
                'ordersModule' => [],
                'status' => 'error',
                'message' => 'Your vendor account is not approved yet. You can not access orders.',
            ];
        }

        // Debug: Check vendor's products and order items
        $debugInfo = [
            'vendor_id' => $admin->id,
            'vendor_name' => $admin->name,
            'products_count' => \App\Models\Product::where('vendor_id', $admin->id)->count(),
            'order_items_assigned' => \App\Models\OrderItem::where('vendor_id', $admin->id)->count(),
        ];

        // Approved Vendor -> get orders that have his items
        $orders = Order::with('user', 'address', 'paymentTransactions')
            ->whereHas('orderItems', function($query) use ($admin) {
                $query->where('vendor_id', $admin->id);
            })
            ->orderBy('id', 'desc')
            ->get();

        // If no orders, check if there are any orders with vendor's products
        if($orders->count() === 0) {
            $potentialOrders = Order::whereHas('orderItems.product', function($query) use ($admin) {
                $query->where('vendor_id', $admin->id);
            })->count();
            
            $debugInfo['potential_orders'] = $potentialOrders;
            $debugInfo['message'] = $potentialOrders > 0 
                ? "Found {$potentialOrders} orders with your products but commissions not calculated. Run commissions:calculate command." 
                : "No orders found with your products.";
        }

        // Vendor permission only view
        $ordersModule = [
            'view_access' => 1,
            'edit_access' => 0,
            'full_access' => 0,
        ];

        return [
            'orders' => $orders,
            'ordersModule' => $ordersModule,
            'status' => 'success',
            'message' => $orders->count() === 0 ? 'No orders found.' : '',
            'debug_info' => $debugInfo, // Add debug info
        ];
    }
}

    // ... rest of the code remains the same

    /**
     * Get single order detail with items and address
     */

    public function getOrderDetail($id)
{
    $admin = Auth::guard('admin')->user();

    /*-----------------------
    VENDOR FLOW
    -----------------------*/
    if($admin->role === 'vendor'){
        // Vendor KYC not approved
        if(!$admin->vendorDetails || (int)$admin->vendorDetails->is_verified === 0){
            return [
                'status' => 'error',
                'message' => 'Your vendor account is not approved yet. You can not access orders.',
            ];
        }

        // Get order with only this vendor's items
        $order = Order::with([
            'user', 
            'address', 
            'orderItems' => function($query) use ($admin) {
                $query->where('vendor_id', $admin->id);
            },
            'orderItems.product',
            'paymentTransactions'
        ])
        ->whereHas('orderItems', function($query) use ($admin) {
            $query->where('vendor_id', $admin->id);
        })
        ->find($id);

        if(!$order){
            return [
                'status' => 'error',
                'message' => 'Order not found or you do not have access to this order.',
            ];
        }

        // Filter to show only vendor's items in the order
        // Note: We're already filtering in the query above

        // Vendor permission only view
        return [
            'status' => 'success', 
            'order' => $order,
            'ordersModule' => [
                'view_access' => 1,
                'edit_access' => 0,
                'full_access' => 0,
            ]
        ];  
    }

    /*-----------------------
    ADMIN / SUBADMIN
    -----------------------*/
    
    $order = Order::with(['user', 'address', 'orderItems.product', 'paymentTransactions'])
        ->find($id);
        
    if(!$order){
        return ['status' => 'error', 'message' => 'Order not found'];
    }

    $ordersModule = [
        'view_access' => 1,
        'edit_access' => ($admin->role === 'admin') ? 1 : 0,
        'full_access' => ($admin->role === 'admin') ? 1 : 0,
    ];

    return [
        'status' => 'success',  
        'order' => $order,
        'ordersModule' => $ordersModule,
    ];
}

    /**
     * Update order status Admin Only
     */

    public function updateOrderStatus($orderId, array $data)
{
    $admin = Auth::guard('admin')->user();

    //Extra safety: vendors should not update order status
    if($admin->role !== 'admin'){
        return [
            'status' => 'error', 
            'message' => 'Unauthorized action'
        ];
    }
    
    $order = Order::find($orderId);
    if(!$order){
        return ['status' => 'error', 'message' => 'Order not found'];
    }
    
    $status = OrderStatus::find($data['order_status_id']);
    if(!$status){
        return ['status' => 'error', 'message' => 'invalid order status'];
    }
    
    $order->update([
        'status' => $status->name,
        'tracking_number' => $data['tracking_number'] ?? null,
        'tracking_link' => $data['tracking_link'] ?? null,
        'shipping_partner' => $data['shipping_partner'] ?? null,
    ]);
    
    $log = OrderLog::create([
        'order_id' => $order->id,
        'order_status_id' => $status->id, // Use the NEW status ID
        'tracking_number' => $data['tracking_number'] ?? null,
        'tracking_link' => $data['tracking_link'] ?? null,
        'shipping_partner' => $data['shipping_partner'] ?? null,
        'remarks' => $data['remarks'] ?? null,
        'updated_by' => $admin->id,
    ]);

    try{
        if($order->user && !empty($order->user->email)){
            Mail::to($order->user->email)->queue(new OrderStatusUpdated($order, $log));
        }
        //\Log::info('OrderStatusUpdated email queued for order: ' . $order->id);
        
    }catch(\Throwable $e){
        \Log::error('Failed to queue OrderStatusUpdated email for order ' . $order->id . ': ' . $e->getMessage());
    }
    
    return ['status' => 'success', 'message' => 'Order status updated successfully', 'log' => $log];
}
}