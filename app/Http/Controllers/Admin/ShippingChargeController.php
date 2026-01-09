<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ShippingChargeService;
use App\Http\Requests\Admin\ShippingChargeRequest;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Session;

class ShippingChargeController extends Controller
{
    protected ShippingChargeService $shippingService;
    
    public function __construct(ShippingChargeService $shippingService)
    {
        $this->shippingService = $shippingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Session::put('module', 'shipping-charges');
        $result = $this->shippingService->list();
        if($result['status'] == 'error'){
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }
        $charges = $result['charges'];
        $shippingModule = $result['shippingModule'];
        return view('admin.shipping_charges.index', compact('charges', 'shippingModule'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = $this->shippingService->activeCountries();
        return view('admin.shipping_charges.add_edit', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShippingChargeRequest $request)
    {
        $data = $request->validated();
        $message = $this->shippingService->save($data);
        return redirect()->route('shipping-charges.index')->with('success_message', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $charge = ShippingCharge::findOrFail($id);
        $countries = $this->shippingService->activeCountries();
        return view('admin.shipping_charges.add_edit', compact('countries', 'charge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ShippingChargeRequest $request)
    {
        $data = $request->validated();
        $message = $this->shippingService->save($data, $id);
        return redirect()->route('shipping-charges.index')->with('success_message', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->shippingService->delete($id);
        if(!empty($result['status']) && $result['status'] === true){
        return redirect()->route('shipping-charges.index')
        ->with('success_message', $result['message']);
        }
        return redirect()->route('shipping-charges.index')
        ->with('error_message', $result['message'] ?? 'Failed to delete shipping charge');
    }

    public function updateStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            $status = $this->shippingService->toggleStatus($data);
            return response()->json([
                'status' => 'success',
                'shipping_charge_id' => $data['shipping_charge_id'],
                'status_value' => $status,
            ]);
        }
        abort(400);
    }
}
