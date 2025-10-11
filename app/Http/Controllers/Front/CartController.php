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
use Illuminate\Support\Facades\View;
use App\Models\Cart;

class CartController extends Controller
{
    protected $service;
    public function __construct(CartService $service){
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    // Get /cart
    public function index()
    {

    $cart = $this->service->getCart();
    return view('front.cart.index', [
        'cartItems' => $cart['cartItems'],
        'subtotal' => $cart['subtotal'],
        'discount' => $cart['discount'],
        'total' => $cart['total'],
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
    // Post /add-to-cart
    public function store(CartRequest $request)
    {
        $data = $request->validated();
        $result = $this->service->addToCart($data);
        return response()->json($result);
    }

    // Get /cart/refresh (AJAX fragments)
    public function refresh(){
        $cart = $this->service->getCart();
        $itemHtml = View::make('front.cart.ajax_cart_items', [
            'cartItems' => $cart['cartItems']
        ])->render();
        $summaryHtml = View::make('front.cart.ajax_cart_summary', [
            'subtotal' => $cart['subtotal'],
            'discount' => $cart['discount'],
            'total' => $cart['total'],
        ])->render();
        return response()->json([
            'items_html' => $itemHtml,
            'summary_html' => $summaryHtml,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // PATCH /cart/{cart} update quantity
    public function update(CartRequest $request,  $cartId)
    {
        $data = $request->validated();
        $result = $this->service->updateQty((int)$cartId, (int)$data['qty']);
        if(!$result['status']){
            return response()->json($result, 422);
        }
        return $this->refresh();
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
     * Remove the specified resource from storage.
     */
    // DELETE /cart/{cart} delete item
    public function destroy($cartId)
    {
        $result = $this->service->removeItem((int)$cartId);
        if(!$result['status']){
            return response()->json($result, 422);
        }
        return $this->refresh();
    }
}
