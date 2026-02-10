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
public function commissions(Request $request)
{
    $vendor = Auth::guard('admin')->user();
    
    // Get commission percentage
    $commissionPercent = $this->commissionService->getVendorCommission($vendor->id);
    
    // Get date filters
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $period = $request->input('period', 'all');
    
    // Get commission summary
    $commissionData = $this->commissionService->getVendorCommissionSummary(
        $vendor->id, 
        $startDate, 
        $endDate
    );
    
    // Monthly breakdown
    $monthlyBreakdown = CommissionHistory::where('vendor_id', $vendor->id)
    ->selectRaw('
        YEAR(commission_date) as year,
        MONTH(commission_date) as month,
        SUM(subtotal) as total_sales,
        SUM(commission_amount) as total_commission,
        SUM(vendor_amount) as total_earnings,
        SUM(CASE WHEN status = "pending" THEN vendor_amount ELSE 0 END) as pending,
        SUM(CASE WHEN status = "paid" THEN vendor_amount ELSE 0 END) as paid
    ')
    ->groupByRaw('YEAR(commission_date), MONTH(commission_date)') // Changed from groupBy('year', 'month')
    ->orderByRaw('YEAR(commission_date) DESC, MONTH(commission_date) DESC')
    ->get();
    
    // Top selling products
    $topProducts = CommissionHistory::where('vendor_id', $vendor->id)
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
    
    return view('vendor.commissions', compact(
        //'summary', 
        //'history', 
        'commissionPercent',
        'monthlyBreakdown',
        'topProducts',
        'period',
        'startDate',
        'endDate'
    ));
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
    
    return view('vendor.commission-history', compact('history', 'summary'));
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
}

