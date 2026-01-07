<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Front\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // Add this import
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        //$this->middleware('auth');
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the users orders
     */
    public function index(Request $request): View
{
    $user = Auth::user();
    $perPage = (int) $request->get('per_page', 10);
    $orders = $this->orderService->getUserOrders($user, $perPage);
    
    return view('front.orders.index', compact('orders'));
}

    /**
     * Display the specified order
     */
    public function show($orderId): View // Add return type
    {
        $user = Auth::user();
        $orders = $this->orderService->getOrderDetails($user, $orderId);

        if(!$orders) {
            abort(Response::HTTP_NOT_FOUND);
        }
        return view('front.orders.show', compact('orders'));
    }
}