<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorDetailsApproved;
use App\Mail\VendorDetailsRejected;

class VendorApprovalController extends Controller
{
    /**
     * Approve vendor
     */
    public function approve($id)
    {
        try {
            // Find the vendor (Admin with role 'vendor')
            $vendor = Admin::with('vendorDetails')->findOrFail($id);
            
            // Check if this is actually a vendor
            if ($vendor->role !== 'vendor') {
                return response()->json([
                    'status' => false,
                    'message' => 'This user is not a vendor'
                ]);
            }
            
            // Update verification status
            if ($vendor->vendorDetails) {
                $vendor->vendorDetails->update(['is_verified' => 1]);
                
                // Send approval email
                $this->sendApprovalEmail($vendor);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Vendor approved successfully. Email sent.'
                ]);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'Vendor details not found'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Approve vendor error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reject vendor
     */
    public function reject(Request $request, $id)
    {
        try {
            $rejectionReason = $request->rejection_reason;
            
            // Find the vendor
            $vendor = Admin::with('vendorDetails')->findOrFail($id);
            
            // Check if this is actually a vendor
            if ($vendor->role !== 'vendor') {
                return response()->json([
                    'status' => false,
                    'message' => 'This user is not a vendor'
                ]);
            }
            
            // Update verification status
            if ($vendor->vendorDetails) {
                $vendor->vendorDetails->update([
                    'is_verified' => 2,
                    'rejection_reason' => $rejectionReason
                ]);
                
                // Send rejection email
                $this->sendRejectionEmail($vendor, $rejectionReason);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Vendor rejected successfully. Email sent.'
                ]);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'Vendor details not found'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reject vendor error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send approval email
     */
    private function sendApprovalEmail($vendor)
    {
        try {
            \Log::info('Attempting to send approval email to: ' . $vendor->email);
            
            // Send email - use vendorDetails relationship
            Mail::to($vendor->email)->send(new VendorDetailsApproved($vendor->vendorDetails));
            
            \Log::info('Approval email sent successfully to: ' . $vendor->email);
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send rejection email
     */
    private function sendRejectionEmail($vendor, $reason)
    {
        try {
            \Log::info('Attempting to send rejection email to: ' . $vendor->email);
            
            // Send email
            Mail::to($vendor->email)->send(new VendorDetailsRejected($vendor->vendorDetails, $reason));
            
            \Log::info('Rejection email sent successfully to: ' . $vendor->email);
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
            return false;
        }
    }
}