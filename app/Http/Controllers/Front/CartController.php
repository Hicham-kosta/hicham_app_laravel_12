<?php

namespace App\Http\Controllers\Front;
/**
 * Class CartController
 * @package App\Http\Controllers\Front
 */
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CartRequest;
use Illuminate\Http\Request;
use App\Services\Front\CartService;

class CartController extends Controller
{
    protected $service;
    public function __construct(CartService $service){
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = $this->service->getCart();
        return view('front.cart.index', [
            'cartItems' => $cart['items'],
            'subtotal' => $cart['subtotal'],
            'discount' => $cart['discount'],
            'total' => $cart['total']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {
        $result = $this->service->addToCart($request->all());
        return response()->json($result);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
