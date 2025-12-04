<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\OrderService;
use App\Models\ColumnPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        Session::put('page', 'orders');
        $result = $this->orderService->getAllOrders();
        if($result['status'] === 'error')
        {
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }
        $orders = $result['orders'];
        $ordersModule = $result['ordersModule'];
        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
        ->where('table_name', 'orders')
        ->first();

        $ordersSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $orderHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];

        return view('admin.orders.index', compact('orders', 'ordersModule', 'ordersSavedOrder', 'orderHiddenCols'));
    }

    public function show($id)
    {
        Session::put('page', 'orders');
        $result = $this->orderService->getOrderDetail($id);
        if($result['status'] === 'error')
        {
            return redirect('admin/orders')->with('error_message', $result['message']);
        }
        $statuses = $this->orderService->getAllOrderStatuses();
        $logs = $result['order']->logs()->with('status', 'updatedByAdmin')
        ->orderBy('created_at', 'desc')
        ->get();
        return view('admin.orders.show', [
            'order' => $result['order'],
            'ordersModule' => $result['ordersModule'],
            'statuses' => $statuses,
            'logs' => $logs,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_partner' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);
        $data = $request->only(['order_status_id', 'tracking_number', 'shipping_partner', 'remarks']);
        $result = $this->orderService->updateOrderStatus($id, $data);
        return redirect()->back()->with($result['status'] === 'success' 
        ? 'success_message' : 'error_message', $result['message']);
    }
}
