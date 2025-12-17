<?php

namespace App\Services\Front;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Get the user's orders
     */
    public function getUserOrders($user, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', $user->id)
            ->with('orderItems.product', 
            'address', 
            'latestLog.status') // Fixed: 'orderItems' not 'items'
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get the order details
     */
    public function getOrderDetails($user, $orderId)
    {
        return Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with(['orderItems.product', 
            'address', 
            'logs' => function($q){
                $q->with(['status', 'updatedByAdmin'])->orderBy('created_at', 'desc');
            }
            ]) // Fixed: 'orderItems' not 'tems'
            ->first();
    }
}