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
                        'message' => 'Your vendor account is not approved yet. 
                        You can not access orders.',
                    ];
                }

                // Approved Vendor -> only his orders
                $orders = Order::with('user', 'address', 'paymentTransactions')
                ->where('admin_id', $admin->id)
                ->where('admin_role', 'vendor')
                ->orderBy('id', 'desc')
                ->get();

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
                    'message' => '',
                ];
            }

            /*-----------------------
            ADMIN / SUBADMIN FLOW
            -----------------------*/

        $orders = Order::with('user', 'address', 'paymentTransactions')
        ->orderBy('id', 'desc')
        ->get();

        if($admin->role === 'admin'){
            $ordersModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1,
            ];
        }else{
            $moduleData = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'orders',
            ])->first();

            if(!$moduleData){
                $status = 'error';
                $message = 'You do not have access to orders';
            }else{
                $ordersModule = $moduleData->toArray();
            }
        }

        return [
            'orders' => $orders,
            'ordersModule' => $ordersModule,
            'status' => $status,
            'message' => $message,
        ];
    }

    /**
     * Get single order detail with items and address
     */

    public function getOrderDetail($id)
    {
        $admin = Auth::guard('admin')->user();

        $order = Order::with(['user', 'address', 'orderItems.product', 'paymentTransactions'])
        ->find($id);
        if(!$order){
            return ['status' => 'error', 'message' => 'Order not found'];
        }

        /*-----------------------
            VENDOR SECURITY CHECK
            -----------------------*/
            if($admin->role === 'vendor'){
                // Vendor KYC not approved
                if(!$admin->vendorDetails || (int)$admin->vendorDetails->is_verified === 0){
                    return [
                        'status' => 'error',
                        'message' => 'Your vendor account is not approved yet. You can not access orders.',
                    ];
                }

                // Vendor trying to access another vendor's order
                if($order->admin_id !== $admin->id || $order->admin_role !== 'vendor'){
                    return [
                        'status' => 'error',
                        'message' => 'You can not access this order.',
                    ];
                }

                // Vendor permission only view
                return[
                    'status' => 'success', 
                    'order' => $order,
                $ordersModule = [
                    'view_access' => 1,
                    'edit_access' => 0,
                    'full_access' => 0,
                   ]
                ];  
            }

            /*-----------------------
            ADMIN / SUBADMIN
            -----------------------*/
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

    public function getAllOrderStatuses()
    {
        return OrderStatus::where('status', 1)->orderBy('sort')->get();
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