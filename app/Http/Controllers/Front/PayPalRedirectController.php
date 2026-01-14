<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\CheckoutController;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use App\Services\Front\CheckoutService;
use App\Models\PaymentTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PayPalRedirectController extends Controller
{
    protected CheckoutService $checkoutService;
    
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Initialize PayPal provider
     */
    protected function getPayPalProvider(): PayPalClient
    {
        $provider = new PayPalClient;
        
        // Get config
        $config = config('paypal');
        
        // For XAMPP development, disable SSL verification
        $config['validate_ssl'] = false;
        
        // Set API credentials
        $provider->setApiCredentials($config);
        
        // Get access token
        try {
            $token = $provider->getAccessToken();
            Log::info('PayPal Access Token obtained successfully');
        } catch (\Exception $e) {
            Log::error('PayPal Access Token Error: ' . $e->getMessage(), [
                'config_mode' => $config['mode'] ?? 'not set',
                'client_id_set' => isset($config[$config['mode']]['client_id']),
            ]);
            throw $e;
        }
        
        return $provider;
    }

    /**
     * Direct PayPal API method (if package fails)
     */
    protected function createPayPalOrderDirect($total, $currency)
    {
        try {
            $provider = $this->getPayPalProvider();
            
           $response = $provider->createOrder([
    "intent" => "CAPTURE",
    "purchase_units" => [
        [
            "amount" => [
                "currency_code" => $currency,
                "value" => number_format($cart['total_numeric'], 2, '.', ''),
                "breakdown" => [
                    "item_total" => [
                        "currency_code" => $currency,
                        "value" => number_format($cart['subtotal'], 2, '.', ''),
                    ],
                    "shipping" => [
                        "currency_code" => $currency,
                        "value" => number_format($cart['shipping'], 2, '.', ''),
                    ],
                ],
            ],
            "description" => "Order from " . config('app.name'),
        ]
    ],
    "application_context" => [
        "return_url" => route('paypal.return'),
        "cancel_url" => route('paypal.cancel'),
        "user_action" => "PAY_NOW",
    ],
]);

            
            return $response;
        } catch (\Exception $e) {
            Log::error('Direct PayPal API error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Called when the user chooses PayPal and submits the checkout form
     * Creates a payment transaction and redirects to PayPal
     */
    public function redirectToPayPal(Request $request)
    {
        $user = Auth::user();
        
        // Ensure address selected
        $addressId = $request->input('address_id');
        if (!$addressId) {
            return redirect()->back()->with('error', 'Please select an address before proceeding to PayPal.');
        }

        // Get cart totals
        $cart = $this->checkoutService->getCartForCheckout($user, $addressId);
        if(empty($cart['cartItems'])){
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Prepare payment transaction
        $total = number_format($cart['total_numeric'] ?? 0, 2, '.', '');
        $currency = $cart['currency'] ?? 'USD';
        
        Log::info('PayPal Order Creation Attempt:', [
            'user_id' => $user->id,
            'total' => $total,
            'currency' => $currency,
            'address_id' => $addressId
        ]);

        // Log the return URLs for debugging
        $returnUrl = route('paypal.return');
        $cancelUrl = route('paypal.cancel');
        
        Log::info('PayPal Return URLs:', [
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl
        ]);

        try {
            $provider = $this->getPayPalProvider();
            
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => $currency,
                            "value" => $total,
                        ],
                        "description" => "Order from " . config('app.name'),
                    ]
                ],
                "application_context" => [
                    "return_url" => $returnUrl,
                    "cancel_url" => $cancelUrl,
                    "user_action" => "PAY_NOW",
                ],
            ]);
            
            Log::info('PayPal Package Response:', $response);
            
        } catch (\Exception $e) {
            Log::error('PayPal package failed: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Unable to connect to PayPal. Please try another payment method or contact support.');
        }

        if (isset($response['id']) && $response['id'] != null) {
            // Find approval link
            $approvalUrl = null;
            foreach($response['links'] as $link){
                if($link['rel'] === 'approve'){
                    $approvalUrl = $link['href'];
                    break;
                }
            }
            
            if(!$approvalUrl){
                Log::error('No approval link found in response', $response);
                return redirect()->back()->with('error', 'No approval link found in PayPal response');
            }
            
            // Store pending transaction
            PaymentTransaction::create([
                'order_id' => null,
                'gateway' => 'paypal',
                'gateway_order_id' => $response['id'],
                'amount' => $total,
                'currency' => $currency,
                'type' => 'payment',
                'status' => $response['status'] ?? 'CREATED',
                'raw_response' => $response,
            ]);
            
            // Save session data
            session([
                'checkout_address_id' => $addressId,
                'paypal_order_id' => $response['id'],
                'cart_total' => $cart['total_numeric'],
                'cart_shipping' => $cart['shipping'],
                'cart_subtotal' => $cart['subtotal'],
                'cart_currency' => $currency,
    'user_id' => $user->id,
]);

            
            Log::info('Session data before redirect:', [
                'checkout_address_id' => session('checkout_address_id'),
                'paypal_order_id' => session('paypal_order_id'),
                'cart_total' => session('cart_total'),
                'user_id' => session('user_id')
            ]);
            
            // Log the approval URL for debugging
            Log::info('Redirecting to PayPal approval URL:', ['url' => $approvalUrl]);
            
            // Redirect to PayPal
            return redirect()->away($approvalUrl);
        } else {
            Log::error('PayPal Order Creation Failed', $response);
            
            $errorMsg = 'Failed to create PayPal order. ';
            if (isset($response['error_description'])) {
                $errorMsg .= $response['error_description'];
            } elseif (isset($response['message'])) {
                $errorMsg .= $response['message'];
            } elseif (isset($response['error'])) {
                $errorMsg .= $response['error'];
            }
            
            return redirect()->back()->with('error', $errorMsg);
        }
    }

    /**
     * Handle the return from PayPal
     * Capture the payment and update the order
     */
    public function handleReturn(Request $request)
    {
        Log::info('=== PAYPAL RETURN HANDLER STARTED ===');
        
        // Get PayPal order ID
        $paypalOrderId = $request->input('token') ?? 
                         $request->query('token') ?? 
                         $request->input('orderID') ??
                         $request->query('orderID') ??
                         session('paypal_order_id');
        
        Log::info('PayPal Return Handler - Parameters:', [
            'all_query_params' => $request->query(),
            'all_input_params' => $request->all(),
            'paypal_order_id_from_request' => $request->input('token') ?? $request->query('token'),
            'paypal_order_id_from_session' => session('paypal_order_id'),
            'final_paypal_order_id' => $paypalOrderId,
            'session_id' => session()->getId()
        ]);

        if(!$paypalOrderId){
            Log::error('No PayPal order ID found in return handler');
            return redirect()->route('user.checkout.index')->with('error', 'No PayPal order ID found. Please contact support.');
        }

        try {
            // Get PayPal provider
            $provider = $this->getPayPalProvider();
            
            // Capture the payment
            Log::info('Attempting to capture PayPal order:', ['order_id' => $paypalOrderId]);
            $response = $provider->capturePaymentOrder($paypalOrderId);
            Log::info('PayPal Capture Response:', $response);

            // Check if capture was successful
            if (isset($response['error']) || isset($response['message'])) {
                Log::error('PayPal capture failed:', $response);
                return redirect()->route('user.checkout.index')
                    ->with('error', 'Payment capture failed: ' . ($response['message'] ?? 'Unknown error'));
            }

            // Extract data from response - Use array access for reliability
            $outerStatus = isset($response['status']) ? strtoupper($response['status']) : 'FAILED';
            
            // Get purchase units
            $purchaseUnits = isset($response['purchase_units']) ? $response['purchase_units'] : [];
            $firstPurchaseUnit = isset($purchaseUnits[0]) ? $purchaseUnits[0] : [];
            
            // Get capture data
            $payments = isset($firstPurchaseUnit['payments']) ? $firstPurchaseUnit['payments'] : [];
            $captures = isset($payments['captures']) ? $payments['captures'] : [];
            $capture = isset($captures[0]) ? $captures[0] : [];
            
            $captureId = isset($capture['id']) ? $capture['id'] : null;
            $captureStatus = isset($capture['status']) ? strtoupper($capture['status']) : 'FAILED';
            
            // Get amount
            $amount = isset($capture['amount']['value']) ? $capture['amount']['value'] : 
                     (isset($firstPurchaseUnit['amount']['value']) ? $firstPurchaseUnit['amount']['value'] : '0.00');
            $currency = isset($capture['amount']['currency_code']) ? $capture['amount']['currency_code'] : 
                       (isset($firstPurchaseUnit['amount']['currency_code']) ? $firstPurchaseUnit['amount']['currency_code'] : 'USD');
            
            // Extract payer info
            $payer = isset($response['payer']) ? $response['payer'] : [];
            $payerId = isset($payer['payer_id']) ? $payer['payer_id'] : null;
            $payerEmail = isset($payer['email_address']) ? $payer['email_address'] : null;
            
            // Calculate fee if available
            $feeAmount = null;
            if (isset($capture['seller_receivable_breakdown']['paypal_fee']['value'])) {
                $feeAmount = $capture['seller_receivable_breakdown']['paypal_fee']['value'];
            }

            Log::info('Extracted PayPal Data:', [
                'outerStatus' => $outerStatus,
                'captureStatus' => $captureStatus,
                'captureId' => $captureId,
                'amount' => $amount,
                'currency' => $currency,
                'payerId' => $payerId,
                'payerEmail' => $payerEmail,
                'feeAmount' => $feeAmount
            ]);

            // Find or create transaction
            $txn = PaymentTransaction::where('gateway', 'paypal')
                ->where('gateway_order_id', $paypalOrderId)
                ->first();

            if (!$txn) {
                Log::warning('Transaction not found, creating new one', ['paypal_order_id' => $paypalOrderId]);
                $txn = PaymentTransaction::create([
                    'gateway' => 'paypal',
                    'gateway_order_id' => $paypalOrderId,
                    'order_id' => null,
                    'transaction_id' => $captureId,
                    'type' => 'capture',
                    'status' => $outerStatus ?: $captureStatus,
                    'amount' => $amount,
                    'currency' => $currency,
                    'fee' => $feeAmount,
                    'payer_id' => $payerId,
                    'payer_email' => $payerEmail,
                    'raw_response' => $response,
                ]);
            } else {
                // Update existing transaction
                $txn->update([
                    'transaction_id' => $captureId,
                    'type' => 'capture',
                    'status' => $outerStatus ?: $captureStatus,
                    'amount' => $amount,
                    'currency' => $currency,
                    'fee' => $feeAmount,
                    'payer_id' => $payerId,
                    'payer_email' => $payerEmail,
                    'raw_response' => array_merge($txn->raw_response ?? [], $response),
                ]);
            }

            Log::info('Payment Transaction Saved/Updated:', [
                'transaction_id' => $txn->id,
                'order_id' => $txn->order_id,
                'gateway_order_id' => $txn->gateway_order_id
            ]);

            // Check if payment is completed
            $isCompleted = ($outerStatus === 'COMPLETED' || $captureStatus === 'COMPLETED');
            
            if(!$isCompleted){
                Log::warning('PayPal payment not completed', [
                    'status' => $captureStatus,
                    'outerStatus' => $outerStatus,
                    'paypal_order_id' => $paypalOrderId
                ]);
                
                return redirect()->route('user.checkout.index')
                    ->with('error', 'Payment not completed. Status: ' . $captureStatus);
            }

            // Check if we already have an order for this transaction
            if(!$txn->order_id){
                // Get address ID from session
                $addressId = session('checkout_address_id');
                $sessionUserId = session('user_id');
                $currentUserId = Auth::id();
                
                Log::info('Session data on return:', [
                    'checkout_address_id' => $addressId,
                    'paypal_order_id' => session('paypal_order_id'),
                    'cart_total' => session('cart_total'),
                    'session_user_id' => $sessionUserId,
                    'current_user_id' => $currentUserId,
                    'session_id' => session()->getId(),
                    'all_session_data' => session()->all()
                ]);
                
                if(!$addressId){
                    Log::error('No address ID found in session');
                    return redirect()->route('user.checkout.index')
                        ->with('error', 'Address information lost. Please contact support.');
                }
                
                // Verify user session
                if ($sessionUserId != $currentUserId) {
                    Log::error('User session mismatch', [
                        'session_user_id' => $sessionUserId,
                        'current_user_id' => $currentUserId
                    ]);
                    return redirect()->route('user.checkout.index')
                        ->with('error', 'Session expired. Please try again.');
                }

                // Prepare payload for order creation
                $payload = [
                    'address_id' => $addressId,
                    'payment_method' => 'paypal',
                    'transaction_id' => $captureId,
                    'paypal_order_id' => $paypalOrderId,
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ];

                Log::info('Creating order with payload:', $payload);

                // Call checkout service to create order
                $user = Auth::user();
                $result = $this->checkoutService->createOrderFromCart($user, $payload);
                
                if(!$result['success']){
                    Log::error('Failed to create order in handleReturn', [
                        'error' => $result['message'] ?? 'Unknown error',
                        'payload' => $payload,
                        'user_id' => $user->id
                    ]);
                    
                    // Store failed transaction for manual review
                    $txn->update([
                        'status' => 'ORDER_CREATION_FAILED',
                        'raw_response' => array_merge(
                            $txn->raw_response ?? [],
                            ['order_creation_error' => $result['message']]
                        )
                    ]);
                    
                    return redirect()->route('user.checkout.index')
                        ->with('error', 'Order creation failed: ' . ($result['message'] ?? 'Unknown error'));
                }
                
                // Get the created order
                $order = $result['order'];
                
                // Update transaction with order ID
                $txn->update(['order_id' => $order->id]);
                
                // Update order with PayPal details
                $order->update([
                    'transaction_id' => $captureId,
                    'paypal_order_id' => $paypalOrderId,
                ]);
                
                Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'transaction_id' => $txn->id
                ]);
                
                // Clear cart
                $this->checkoutService->clearCart($user);
                
            } else {
                // Order already exists, update it
                $order = Order::find($txn->order_id);
                if($order){
                    $order->update([
                        'payment_status' => 'paid',
                        'transaction_id' => $captureId,
                        'paypal_order_id' => $paypalOrderId,
                        'status' => 'processing'
                    ]);
                    
                    Log::info('Updated existing order', ['order_id' => $order->id]);
                }
            }

            // Clear session data
            session()->forget([
                'checkout_address_id', 
                'paypal_order_id', 
                'cart_total', 
                'cart_currency',
                'user_id'
            ]);

            Log::info('PayPal payment completed successfully', [
                'order_id' => $txn->order_id,
                'paypal_order_id' => $paypalOrderId,
                'capture_id' => $captureId
            ]);

            // Redirect to thank you page
            return redirect()->route('checkout.thanks', ['orderId' => $txn->order_id])
                ->with('success', 'Payment completed successfully!');
                
        } catch (\Exception $e) {
            Log::error('PayPal handleReturn exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'paypal_order_id' => $paypalOrderId
            ]);
            
            Log::info('Session data on error:', [
                'checkout_address_id' => session('checkout_address_id'),
                'paypal_order_id' => session('paypal_order_id'),
                'cart_total' => session('cart_total'),
                'session_id' => session()->getId()
            ]);
            
            return redirect()->route('checkout.index')
                ->with('error', 'Payment processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle PayPal cancel
     */
    public function handleCancel(Request $request) // Added Request parameter
    {
        Log::info('PayPal payment canceled by user',[
            'query_params' => $request->query(),
            'input_params' => $request->all(),
            'session' => session()->all(),
        ]);

        session()->forget([
            'checkout_address_id', 
            'paypal_order_id', 
            'cart_total', 
            'cart_currency',
            'user_id'
        ]);

        return redirect()->route('checkout.index')->with('info', 'Payment canceled. You can try again.');
    }
    
    /**
     * Test method for debugging
     */
    public function testHandler(Request $request)
    {
        Log::info('PayPal Test Handler Called', [
            'all_query' => $request->query(),
            'all_input' => $request->all(),
            'headers' => $request->headers->all()
        ]);
        
        return response()->json([
            'message' => 'Test handler working',
            'query_params' => $request->query(),
            'session' => session()->all(),
            'user' => Auth::user() ? Auth::user()->id : 'not logged in'
        ]);
    }
}