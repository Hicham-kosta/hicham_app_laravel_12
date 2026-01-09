<?php

namespace App\Services\Admin;

use App\Models\ShippingCharge;
use App\Models\Country;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;

class ShippingChargeService
{
    /**
     * List all shipping charges with permissions
     */
    public function list()
    {
        $charges = ShippingCharge::with('country')
        ->orderBy('country_id')
        ->orderBy('sort_order')
        ->get();

        $admin = Auth::guard('admin')->user();
        $status = "success";
        $message = "";
        $shippingModule = [];

        if($admin->role === "admin"){
            $shippingModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1,
            ];
        }else{
            $moduleCount = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'shipping_charges'
            ])->count();
            if($moduleCount == 0){
                $status = "error";
                $message = "You do not have permission to view shipping charges";
            }else{
                $shippingModule = AdminsRole::where([
                    'subadmin_id' => $admin->id,
                    'module' => 'shipping_charges',
                ])->first()->toArray();
                
            }
        }
        return [
            'status' => $status,
            'message' => $message,
            'shippingModule' => $shippingModule,
            'charges' => $charges,
        ];   
    }

    /**
 * Active countries list for dropdown
     */
    public function activeCountries()
    {
        return Country::where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Create or update a shipping charge
     */
    public function save(array $data, $id=null): string
    {
        if($id){
            $charge = ShippingCharge::findOrFail($id);
            $message = "Shipping Charge updated successfully";
        }else{
            $charge = new ShippingCharge();
            $message = "Shipping Charge added successfully";
        }
        $charge->country_id = $data['country_id'];
        $charge->name = $data['name'] ?? 'Standard Shipping';
        $charge->min_weight_g = $data['min_weight_g'] ?? null;
        $charge->max_weight_g = $data['max_weight_g'] ?? null;
        $charge->min_subtotal = $data['min_subtotal'] ?? null;
        $charge->max_subtotal = $data['max_subtotal'] ?? null;
        $charge->rate = $data['rate'];
        $charge->sort_order = $data['sort_order'] ?? 0;
        $charge->status = $data['status'] ? (int)$data['status'] : 1;
        $charge->is_default = !empty($data['is_default']) ? 1 : 0;
        $charge->save();

        // Only one default per country
        if($charge->is_default){
            ShippingCharge::where('country_id', $charge->country_id)
            ->where('id', '!=', $charge->id)
            ->update(['is_default' => 0]);
        }
        return $message;
    }

    /**
     * Toggle status via AJAX
     */
    public function toggleStatus(array $data): int
    {
        $charge = ShippingCharge::findOrFail($data['shipping_charge_id']);
        $charge->status = $charge->status ? 0 : 1;
        $charge->save();
        return (int)$charge->status;
    }

    /**
     * Delete a shipping charge
     */
    public function delete($id): array
    {
        $charge = ShippingCharge::findOrFail($id);
        $charge->delete();
        return ['status'=> true, 'message' => 'Shipping Charge deleted successfully'];
    }

}