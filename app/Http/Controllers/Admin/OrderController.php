<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\OrderService;
use App\Models\ColumnPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\CommissionHistory;
use App\Services\Admin\VendorCommissionService;



class OrderController extends Controller
{
    protected $orderService;
    protected $commissionService;

    public function __construct(OrderService $orderService, VendorCommissionService $commissionService)
    {
        $this->orderService = $orderService;
        $this->commissionService = $commissionService;
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

    if ($result['status'] === 'error') {
        return redirect('admin/orders')
            ->with('error_message', $result['message']);
    }

    $order = $result['order'];

    // Order statuses
    $statuses = $this->orderService->getAllOrderStatuses();

    // Order logs
    $logs = $order->logs()
        ->with('status', 'updatedByAdmin')
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculate commissions if missing
    if (!$order->total_commission) {
        $this->commissionService->calculateAndSaveOrderCommission($order->id);
        $order->refresh();
    }

    // Commission data
    $commissionData = $this->commissionService->calculateOrderCommissions($order->id);

    // Commission history
    $commissionHistory = CommissionHistory::where('order_id', $order->id)
        ->with('vendor')
        ->get();

    return view('admin.orders.show', [
        'order' => $order,
        'ordersModule' => $result['ordersModule'],
        'statuses' => $statuses,
        'logs' => $logs,
        'commissionData' => $commissionData,
        'commissionHistory' => $commissionHistory
    ]);
}


    public function processCommissionPayment(Request $request)
{
    $request->validate([
        'vendor_id' => 'required|exists:admins,id',
        'amount' => 'required|numeric|min:1',
        'payment_method' => 'required|string',
        'reference' => 'required|string',
        'payment_date' => 'required|date',
        'commission_ids' => 'array'
    ]);
    
    try {
        // If specific commission IDs are provided
        if ($request->commission_ids) {
            $commissions = CommissionHistory::whereIn('id', $request->commission_ids)
                ->where('vendor_id', $request->vendor_id)
                ->where('status', 'pending')
                ->get();
                
            foreach ($commissions as $commission) {
                $commission->markAsPaid(
                    $request->payment_method,
                    $request->reference,
                    $request->notes,
                    auth('admin')->id()
                );
            }
        } else {
            // Process payment for a specific amount
            $this->commissionService->processVendorPayment(
                $request->vendor_id,
                $request->amount,
                $request->payment_method,
                $request->reference
            );
        }
        
        // Log payment transaction (create a PaymentTransaction model if needed)
        PaymentTransaction::create([
            'vendor_id' => $request->vendor_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'status' => 'completed',
            'notes' => $request->notes,
            'processed_by' => auth('admin')->id(),
            'payment_date' => $request->payment_date
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_partner' => 'nullable|string|max:255',
            'tracking_link' => 'nullable|url|max:1000',
            'remarks' => 'nullable|string|max:1000',
        ]);
        $data = $request->only(['order_status_id', 'tracking_number', 'tracking_link', 'shipping_partner', 'remarks']);
        $result = $this->orderService->updateOrderStatus($id, $data);
        return redirect()->back()->with($result['status'] === 'success' 
        ? 'success_message' : 'error_message', $result['message']);
    }

    public function invoice($id){
        Session::put('page', 'orders');
        $result = $this->orderService->getOrderDetail($id);
        
        if($result['status'] === 'error'){
            return redirect()->route('orders.index')->with('error_message', $result['message']);
        }
        $order = $result['order'];

        // Allow invoice for only shipped orders
        if(strtolower($order->status !== 'shipped')){
            return redirect()->route('orders.index')->with('error_message', 'invoice available only for shipped orders');
        }
        $order->loadMissing(['orderItems.product', 'address', 'user']);

        // Generate barcode (CODE 128)
        $barcodeBase64 = null;
        try{
            $generator = new BarcodeGeneratorPNG();
            $raw = $generator->getBarcode((string)$order->id, $generator::TYPE_CODE_128, 2, 60);
            $barcodeBase64 = base64_encode($raw);
        }
        catch(\Throwable $e){
            Log::error('Failed to generate barcode for order ' . $order->id . ': ' . $e->getMessage());
        }
        
        
        return view('admin.orders.invoice', compact('order', 'barcodeBase64'));
    }

    public function invoicePdf($id){
        Session::put('page', 'orders');
        $result = $this->orderService->getOrderDetail($id);
        
        if($result['status'] === 'error'){
            return redirect()->route('orders.index')->with('error_message', $result['message']);
        }
        $order = $result['order'];

        // Allow invoice for only shipped orders
        if(strtolower($order->status !== 'shipped')){
            return redirect()->route('orders.index')->with('error_message', 'invoice available only for shipped orders');
        }
        $order->loadMissing(['orderItems.product', 'address', 'user']);

        // Generate barcode (CODE 128)
        $barcodeBase64 = null;
        try{
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode((string)$order->id, $generator::TYPE_CODE_128, 2, 60);
            $barcodeBase64 = base64_encode($barcode);
        }
        catch(\Throwable $e){}
            $data = compact('order', 'barcodeBase64');
            try{
                $pdf = \PDF::setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ])->loadView('admin.orders.invoice_pdf', $data)
                ->setPaper('a4', 'portrait');
                return $pdf->stream("invoice_{$order->id}.pdf");
            }
            catch(\Throwable $e){
                Log::error("PDF Generation Failed: " .$e->getMessage());
                return view('admin.orders.invoice_pdf', $data);
            }
    }
}