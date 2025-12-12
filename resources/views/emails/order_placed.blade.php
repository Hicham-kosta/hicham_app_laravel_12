<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - {{ config('app.name') }}</title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 40px 20px;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .header-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .order-number {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        /* Content */
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        
        .order-status {
            background: #f0f7ff;
            border-left: 4px solid #4a90e2;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .status-icon {
            font-size: 24px;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        /* Order Summary Card */
        .summary-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .summary-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .summary-title::before {
            content: '📦';
            margin-right: 10px;
        }
        
        .order-items {
            width: 100%;
            border-collapse: collapse;
        }
        
        .order-items th {
            text-align: left;
            padding: 12px 0;
            border-bottom: 2px solid #eaeaea;
            color: #666;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .order-items td {
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .product-info {
            display: flex;
            align-items: center;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .product-details h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .product-variants {
            font-size: 14px;
            color: #666;
        }
        
        .qty {
            font-weight: 600;
        }
        
        .price {
            font-weight: 600;
            color: #2c3e50;
        }
        
        /* Totals */
        .totals {
            margin-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        
        .total-row.final {
            border-top: 2px solid #eaeaea;
            margin-top: 12px;
            padding-top: 15px;
            font-weight: 700;
            font-size: 18px;
            color: #2c3e50;
        }
        
        /* Shipping Info */
        .info-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .info-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .info-title::before {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .shipping::before { content: '🚚'; }
        .payment::before { content: '💳'; }
        
        /* Timeline */
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #eaeaea;
        }
        
        .timeline-step {
            display: flex;
            margin-bottom: 25px;
            position: relative;
        }
        
        .timeline-icon {
            width: 32px;
            height: 32px;
            background: #4a90e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            flex-shrink: 0;
            z-index: 1;
        }
        
        .timeline-content h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .timeline-content p {
            color: #666;
            font-size: 14px;
        }
        
        /* CTA Buttons */
        .cta-section {
            text-align: center;
            margin: 40px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin: 0 10px 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #555;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        /* Footer */
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eaeaea;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            color: #666;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .social-link:hover {
            color: #667eea;
        }
        
        .copyright {
            color: #999;
            font-size: 13px;
            margin-top: 20px;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            body {
                padding: 20px 10px;
            }
            
            .email-header,
            .email-content {
                padding: 25px 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
            
            .order-items th {
                font-size: 12px;
            }
            
            .product-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .product-image {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="header-icon">✨</div>
            <div class="logo">{{ config('app.name') }}</div>
            <h1>Thank You For Your Order!</h1>
            <p style="opacity: 0.9; margin-bottom: 20px;">Your order has been successfully placed</p>
            <div class="order-number">ORDER #{{ $order->id }}</div>
        </div>
        
        <!-- Main Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <p>Dear <strong>{{ $order->user->name ?? ($order->user?->email ?? 'Customer') }}</strong>,</p>
                <p>Thank you for shopping with us! We're excited to let you know that we've received your order and it's now being processed.</p>
            </div>
            
            <!-- Order Status -->
            <div class="order-status">
                <span class="status-icon">📋</span>
                <strong>Status:</strong> <span style="color: #4a90e2; font-weight: 600;">{{ ucfirst($order->status) }}</span>
                • <strong>Date:</strong> {{ $order->created_at->format('F d, Y \a\t g:i A') }}
            </div>
            
            <!-- Order Summary -->
            <div class="summary-card">
                <h3 class="summary-title">Order Summary</h3>
                
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $orderItems = $order->orderItems ?? $order->items ?? collect([]);
                        @endphp
                        
                        @forelse($orderItems as $item)
                        <tr>
                            <td>
                                <div class="product-info">
                                    <div class="product-image">
                                        {{ substr($item->product_name ?? 'P', 0, 1) }}
                                    </div>
                                    <div class="product-details">
                                        <h4>{{ $item->product_name ?? 'Product' }}</h4>
                                        <div class="product-variants">
                                            @if($item->size || $item->color)
                                                {{ $item->size ?? '' }}{{ $item->size && $item->color ? ' • ' : '' }}{{ $item->color ?? '' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="qty">{{ $item->qty ?? 1 }}</td>
                            <td class="price">${{ number_format($item->price ?? $item->unit_price ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #999;">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Totals -->
                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal ?? 0, 2) }}</span>
                    </div>
                    
                    @if($order->discount > 0)
                    <div class="total-row" style="color: #27ae60;">
                        <span>Discount</span>
                        <span>-${{ number_format($order->discount ?? 0, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->wallet > 0)
                    <div class="total-row" style="color: #e74c3c;">
                        <span>Wallet Used</span>
                        <span>-${{ number_format($order->wallet ?? 0, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->shipping > 0)
                    <div class="total-row">
                        <span>Shipping</span>
                        <span>${{ number_format($order->shipping ?? 0, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="total-row final">
                        <span>Total Amount</span>
                        <span>${{ number_format($order->total ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Shipping & Payment Info -->
            <div class="info-grid">
                <div class="info-card">
                    <h3 class="info-title shipping">Shipping Information</h3>
                    @if($order->address)
                    <div style="line-height: 1.8;">
                        <div><strong>{{ $order->address->first_name ?? '' }} {{ $order->address->last_name ?? '' }}</strong></div>
                        <div>{{ $order->address->address_line1 ?? '' }}</div>
                        @if(!empty($order->address->address_line2))
                        <div>{{ $order->address->address_line2 }}</div>
                        @endif
                        <div>{{ $order->address->city ?? '' }}, {{ $order->address->county ?? '' }}</div>
                        <div>{{ $order->address->postcode ?? '' }}, {{ $order->address->country ?? '' }}</div>
                        <div style="margin-top: 10px;">
                            <strong>Phone:</strong> {{ $order->address->mobile ?? $order->user->phone ?? 'N/A' }}
                        </div>
                    </div>
                    @else
                    <div style="color: #999;">Shipping address not provided</div>
                    @endif
                </div>
                
                <div class="info-card">
                    <h3 class="info-title payment">Payment Information</h3>
                    <div style="line-height: 1.8;">
                        <div><strong>Method:</strong> {{ strtoupper($order->payment_method ?? 'COD') }}</div>
                        <div><strong>Status:</strong> 
                            <span style="color: {{ $order->payment_status === 'paid' ? '#27ae60' : '#f39c12' }}; font-weight: 600;">
                                {{ ucfirst($order->payment_status ?? 'Pending') }}
                            </span>
                        </div>
                        <div><strong>Transaction ID:</strong> {{ $order->transaction_id ?? 'N/A' }}</div>
                        <div><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Order Timeline -->
            <div class="info-section">
                <h3 class="summary-title">Order Timeline</h3>
                <div class="timeline">
                    <div class="timeline-step">
                        <div class="timeline-icon">1</div>
                        <div class="timeline-content">
                            <h4>Order Placed</h4>
                            <p>Your order has been received</p>
                            <small>{{ $order->created_at->format('M d, g:i A') }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: #95a5a6;">2</div>
                        <div class="timeline-content">
                            <h4>Processing</h4>
                            <p>We're preparing your order</p>
                            <small>Expected: Today</small>
                        </div>
                    </div>
                    
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: #95a5a6;">3</div>
                        <div class="timeline-content">
                            <h4>Shipped</h4>
                            <p>Your order is on the way</p>
                            <small>Estimated: 2-3 business days</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Buttons -->
            <div class="cta-section">
                <a href="{{ url('/orders/' . $order->id) }}" class="btn" target="_blank">View Order Details</a>
                <a href="{{ url('/dashboard') }}" class="btn btn-secondary" target="_blank">Go to Dashboard</a>
            </div>
            
            <!-- Support Info -->
            <div style="text-align: center; color: #666; font-size: 14px; padding: 20px; border-top: 1px solid #eaeaea;">
                <p><strong>Need Help?</strong></p>
                <p>If you have any questions, please contact our support team at <a href="mailto:support@{{ config('app.name') }}.com" style="color: #667eea;">support@{{ config('app.name') }}.com</a></p>
                <p style="margin-top: 15px; font-size: 13px; color: #999;">
                    This is an automated email, please do not reply directly to this message.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="logo" style="color: #333; margin-bottom: 15px;">{{ config('app.name') }}</div>
            
            <div class="social-links">
                <a href="#" class="social-link">🌐 Website</a>
                <a href="#" class="social-link">📘 Facebook</a>
                <a href="#" class="social-link">📸 Instagram</a>
                <a href="#" class="social-link">🐦 Twitter</a>
            </div>
            
            <div class="copyright">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                This email was sent to you as a registered customer of {{ config('app.name') }}.
            </div>
        </div>
    </div>
</body>
</html>