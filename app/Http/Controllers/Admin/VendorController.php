<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\VendorDetailRequest;
use App\Services\Admin\VendorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Models\VendorDetail;
use App\Models\CommissionHistory;
use App\Services\Admin\VendorCommissionService;

class VendorController extends Controller
{
    public function __construct(protected VendorService $vendorService, 
    protected VendorCommissionService $commissionService, )
    {}

    /**
     * Show Vendor details form
     */
    public function edit()
    {
        // Highlight sidebar menu
        Session::put('page', 'vendor-details');

        return view('admin.vendors.edit');
    }

    /**
     * Update Vendor details
     */
    public function update(VendorDetailRequest $request)
    {
        // Keep sidebar active after submit
        Session::put('page', 'vendor-details');


        $this->vendorService->updateVendorDetails($request);

        return redirect()->back()
            ->with('success_message', 'Vendor details updated successfully. Await admin approval');
    }

    public function deleteAddressProof(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $vendorDetail = $admin->vendorDetails;
        if($vendorDetail && $vendorDetail->address_proof_image){
            $path = public_path('front/images/vendor-docs/'.$vendorDetail->address_proof_image);
            if(File::exists($path)){
                File::delete($path);
            }
            $vendorDetail->update([
                'address_proof_image' => null,
                'is_verified' => 0
            ]);
        }
        return response()->json(['status' => true]);
    }

    /**
     * Show vendor commission dashboard
     */
// app/Http/Controllers/Admin/VendorController.php

public function commissions(Request $request)
{
    $vendor = Auth::guard('admin')->user();
    
    // Get period from request, default 'month'
    $period = $request->input('period', 'month');
    
    // Fetch all dashboard data from service
    $dashboard = $this->commissionService->getVendorDashboardData($vendor->id, $period);
    
    // Also get pending & paid totals for quick stats (already in dashboard->totals? we can add)
    $pending = CommissionHistory::where('vendor_id', $vendor->id)
        ->where('status', 'pending')
        ->sum('vendor_amount');
    $paid = CommissionHistory::where('vendor_id', $vendor->id)
        ->where('status', 'paid')
        ->sum('vendor_amount');
    
    return view('vendor.commissions.dashboard', compact('dashboard', 'pending', 'paid'));
}
    
    /**
     * Show commission history with filters
     */
    public function commissionHistory(Request $request)
{
    $vendor = Auth::guard('admin')->user();
    
    $query = CommissionHistory::where('vendor_id', $vendor->id)
        ->with(['order', 'product']);
    
    // Apply filters
    if ($request->status) {
        $query->where('status', $request->status);
    }
    
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('commission_date', [
            $request->start_date, 
            $request->end_date
        ]);
    }
    
    if ($request->product_id) {
        $query->where('product_id', $request->product_id);
    }
    
    if ($request->order_id) {
        $query->where('order_id', $request->order_id);
    }
    
    $history = $query->orderBy('created_at', 'desc')->paginate(20);
    
    // Summary for filtered results
    $summary = [
        'total_sales' => $history->sum('subtotal'),
        'total_commission' => $history->sum('commission_amount'),
        'total_earnings' => $history->sum('vendor_amount'),
        'pending' => $history->where('status', 'pending')->sum('vendor_amount'),
        'paid' => $history->where('status', 'paid')->sum('vendor_amount'),
    ];
    
    return view('vendor.commissions.history', compact('history', 'summary'));
}

    // In your VendorController or DashboardController

     public function dashboard()
    {
        $vendor = Auth::guard('admin')->user();
        
        Session::put('page', 'vendor-dashboard');
        
        // Get vendor stats
        $stats = [
            'total_products' => \App\Models\Product::where('vendor_id', $vendor->id)->count(),
            'total_orders' => \App\Models\OrderItem::where('vendor_id', $vendor->id)
                ->distinct('order_id')
                ->count('order_id'),
            'total_sales' => \App\Models\OrderItem::where('vendor_id', $vendor->id)->sum('subtotal'),
            'total_commission' => \App\Models\OrderItem::where('vendor_id', $vendor->id)->sum('commission_amount'),
            'total_payable' => \App\Models\OrderItem::where('vendor_id', $vendor->id)->sum('vendor_amount'),
            'pending_balance' => \App\Models\CommissionHistory::where('vendor_id', $vendor->id)
                ->where('status', 'pending')
                ->sum('vendor_amount'),
        ];
        
        // Recent products
        $recentProducts = \App\Models\Product::where('vendor_id', $vendor->id)
            ->with('category')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
            
        // Recent orders
        $recentOrders = \App\Models\Order::whereHas('orderItems', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['orderItems' => function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }, 'user'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('vendor.dashboard', compact('vendor', 'stats', 'recentProducts', 'recentOrders'));
    }
    
    // ... rest of your methods

public function calculateCommissions($vendorId)
{
    $vendor = Admin::findOrFail($vendorId);
    
    // Get all orders with this vendor's products
    $orders = Order::whereHas('orderItems.product', function($query) use ($vendorId) {
        $query->where('vendor_id', $vendorId);
    })->get();
    
    $commissionService = new VendorCommissionService();
    $processed = 0;
    
    foreach ($orders as $order) {
        $commissionService->calculateAndSaveOrderCommission($order->id);
        $processed++;
    }
    
    return back()->with('success', 
        "Commissions calculated for {$processed} orders for vendor {$vendor->name}"
    );
}

public function exportCommissions(Request $request)
{
    $vendor = Auth::guard('admin')->user();

    $commissions = CommissionHistory::where('vendor_id', $vendor->id)
        ->orderBy('commission_date', 'desc')
        ->get();

    $filename = "vendor_{$vendor->id}_commissions_" . date('Y-m-d') . ".csv";

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($commissions) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['Date', 'Order ID', 'Product', 'Subtotal', 'Commission %', 'Commission', 'You Received', 'Status', 'Payment Date', 'Reference']);

        foreach ($commissions as $row) {
            fputcsv($file, [
                $row->commission_date,
                $row->order_id,
                $row->product_name,
                $row->subtotal,
                $row->commission_percent . '%',
                $row->commission_amount,
                $row->vendor_amount,
                ucfirst($row->status),
                $row->payment_date,
                $row->payment_reference,
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}

