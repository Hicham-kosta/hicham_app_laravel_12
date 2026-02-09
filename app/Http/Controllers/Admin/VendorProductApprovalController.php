<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class VendorProductApprovalController extends Controller
{
    public function pendingProducts()
    {
        $products = Product::with(['vendor', 'category'])
            ->where('is_approved', 0)
            ->whereNotNull('vendor_id')
            ->paginate(20);
            
        return view('admin.products.vendor_pending', compact('products'));
    }
    
    public function approveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'is_approved' => 1,
            'status' => 1, // Make active
            'approved_at' => now(),
            'approved_by' => auth('admin')->id()
        ]);
        
        // You can add notification to vendor here
        
        return redirect()->back()->with('success_message', 'Product approved successfully.');
    }
    
    public function rejectProduct(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $product = Product::findOrFail($id);
        $product->update([
            'is_approved' => 2, // Rejected
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => auth('admin')->id()
        ]);
        
        // Notify vendor about rejection
        
        return redirect()->back()->with('success_message', 'Product rejected successfully.');
    }
}