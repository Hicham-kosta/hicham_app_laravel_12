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
    public function getAllOrders()
    {
        $orders = Order::with('user', 'address')
        ->orderBy('id', 'desc')
        ->get();
        $admin = Auth::guard('admin')->user();
        $status = 'success';
        $message = '';
        $ordersModule = [];

        if($admin->role == 'admin'){
            $ordersModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1,
            ];
        }else{
            $module = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'orders',
            ])->first();

            if(!$module){
                $status = 'error';
                $message = 'You do not have access to orders';
            }else{
                $ordersModule = $module->toArray();
            }
        }

        return compact('orders', 'ordersModule', 'status', 'message');
    }

    public function getOrderDetail($id)
    {
        $order = Order::with('user', 'address', 'orderItems.product')
        ->find($id);
        if(!$order){
            return ['status' => 'error', 'message' => 'Order not found'];
        }
        return [
            'status' => 'success', 
            'message' => 'Order found', 
            'order' => $order,
            'ordersModule' => [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1,
            ]
        ];
    }

    public function getAllOrderStatuses()
    {
        return OrderStatus::where('status', 1)->orderBy('sort')->get();
    }

    public function updateOrderStatus($orderId, array $data)
{
    $order = Order::find($orderId);
    if(!$order){
        return ['status' => 'error', 'message' => 'Order not found'];
    }
    
    $status = OrderStatus::find($data['order_status_id']);
    if(!$status){
        return ['status' => 'error', 'message' => 'invalid order status'];
    }
    
    // Get the current status before update (for logging if needed)
    $oldStatusId = $order->order_status_id;
    
    $order->update([
        'status' => $status->name,
        'order_status_id' => $status->id, // Add this to update the foreign key
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
        'updated_by' => Auth::guard('admin')->id(),
    ]);

    try{
        Mail::to($order->user->email)->queue(new OrderStatusUpdated($order, $log));
        \Log::info('OrderStatusUpdated email queued for order: ' . $order->id);
    }catch(\Throwable $e){
        \Log::error('Failed to queue OrderStatusUpdated email for order ' . $order->id . ': ' . $e->getMessage());
    }
    
    return ['status' => 'success', 'message' => 'Order status updated successfully', 'log' => $log];
}
}