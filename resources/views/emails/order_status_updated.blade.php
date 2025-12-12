<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update - {{ config('app.name') }}</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
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
        
        /* Status Update Card */
        .update-card {
            background: #fff9f0;
            border-left: 4px solid #f39c12;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .update-card::before {
            content: '📦';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 60px;
            opacity: 0.1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-processing { background: #e3f2fd; color: #1976d2; }
        .status-shipped { background: #e8f5e9; color: #388e3c; }
        .status-delivered { background: #f1f8e9; color: #689f38; }
        .status-cancelled { background: #ffebee; color: #d32f2f; }
        .status-pending { background: #fff3e0; color: #f57c00; }
        
        /* Status Timeline */
        .timeline-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .timeline-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .timeline-title::before {
            content: '📋';
            margin-right: 10px;
            font-size: 24px;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #f093fb, #f5576c);
        }
        
        .timeline-step {
            position: relative;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px dashed #e0e0e0;
        }
        
        .timeline-step:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .timeline-icon {
            position: absolute;
            left: -30px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            border: 3px solid #f093fb;
            z-index: 1;
        }
        
        .timeline-step.active .timeline-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-color: #f093fb;
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.2);
        }
        
        .timeline-step.completed .timeline-icon {
            background: #27ae60;
            border-color: #27ae60;
        }
        
        .timeline-content h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .timeline-step.active .timeline-content h4 {
            color: #f5576c;
        }
        
        .timeline-step.completed .timeline-content h4 {
            color: #27ae60;
        }
        
        .timeline-content p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .timeline-date {
            font-size: 12px;
            color: #999;
            display: flex;
            align-items: center;
        }
        
        .timeline-date::before {
            content: '🕐';
            margin-right: 5px;
        }
        
        /* Shipping Info Card */
        .shipping-card {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #667eea30;
        }
        
        .shipping-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .shipping-title::before {
            content: '🚚';
            margin-right: 10px;
            font-size: 24px;
        }
        
        .shipping-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .shipping-info {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #2c3e50;
            font-weight: 500;
        }
        
        /* Remarks Section */
        .remarks-section {
            background: #f0f7ff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid #4a90e2;
        }
        
        .remarks-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
        }
        
        .remarks-title::before {
            content: '💬';
            margin-right: 10px;
        }
        
        .remarks-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            font-style: italic;
            color: #555;
            line-height: 1.8;
            border: 1px solid #e3f2fd;
        }
        
        /* CTA Buttons */
        .cta-section {
            text-align: center;
            margin: 40px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin: 0 10px 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #555;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .btn-track {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
        
        /* Support Section */
        .support-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin-top: 30px;
        }
        
        .support-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
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
            color: #f5576c;
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
            
            .shipping-details {
                grid-template-columns: 1fr;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
            
            .timeline {
                padding-left: 20px;
            }
            
            .timeline::before {
                left: 5px;
            }
            
            .timeline-icon {
                left: -20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="header-icon">📦</div>
            <div class="logo">{{ config('app.name') }}</div>
            <h1>Order Status Update</h1>
            <p style="opacity: 0.9; margin-bottom: 20px;">Your order status has been updated</p>
            <div class="order-number">ORDER #{{ $order->id }}</div>
        </div>
        
        <!-- Main Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <p>Dear <strong>{{ $order->user->name ?? 'Customer' }}</strong>,</p>
                <p>We wanted to inform you about the latest update regarding your order. Here's what's happening with your purchase.</p>
            </div>
            
            <!-- Status Update -->
            <div class="update-card">
                @php
                    $statusName = $log->status->name ?? $order->status ?? 'updated';
                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $statusName));
                @endphp
                
                <div class="status-badge {{ $statusClass }}">
                    {{ ucfirst($statusName) }}
                </div>
                
                <h2 style="margin-bottom: 10px;">Your order status has been updated!</h2>
                <p style="color: #555; margin-bottom: 0;">
                    We're actively working on your order <strong>#{{ $order->id }}</strong>. 
                    @if($statusName === 'shipped')
                    Your items are now on their way to you!
                    @elseif($statusName === 'delivered')
                    Your package has been delivered successfully!
                    @elseif($statusName === 'processing')
                    We're currently preparing your order for shipment.
                    @endif
                </p>
            </div>
            
            <!-- Order Timeline -->
            <div class="timeline-container">
                <h3 class="timeline-title">Order Journey</h3>
                <div class="timeline">
                    <!-- Order Placed -->
                    <div class="timeline-step completed">
                        <div class="timeline-icon"></div>
                        <div class="timeline-content">
                            <h4>Order Placed</h4>
                            <p>We received your order</p>
                            <div class="timeline-date">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>
                    
                    <!-- Order Processing -->
                    <div class="timeline-step {{ in_array(strtolower($statusName), ['processing', 'shipped', 'delivered']) ? 'completed' : '' }} 
                                 {{ $statusName === 'processing' ? 'active' : '' }}">
                        <div class="timeline-icon"></div>
                        <div class="timeline-content">
                            <h4>Processing</h4>
                            <p>Preparing your items</p>
                            <div class="timeline-date">
                                @if(in_array(strtolower($statusName), ['processing', 'shipped', 'delivered']))
                                Completed
                                @else
                                Pending
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Shipped -->
                    <div class="timeline-step {{ in_array(strtolower($statusName), ['shipped', 'delivered']) ? 'completed' : '' }} 
                                 {{ $statusName === 'shipped' ? 'active' : '' }}">
                        <div class="timeline-icon"></div>
                        <div class="timeline-content">
                            <h4>Shipped</h4>
                            <p>Order is on the way</p>
                            <div class="timeline-date">
                                @if(in_array(strtolower($statusName), ['shipped', 'delivered']))
                                {{ $log->created_at->format('M d, Y') ?? 'Completed' }}
                                @else
                                Pending
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Delivered -->
                    <div class="timeline-step {{ $statusName === 'delivered' ? 'completed active' : '' }}">
                        <div class="timeline-icon"></div>
                        <div class="timeline-content">
                            <h4>Delivered</h4>
                            <p>Package delivered successfully</p>
                            <div class="timeline-date">
                                {{ $statusName === 'delivered' ? 'Completed' : 'Estimated: 2-3 business days' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Details -->
            @php
                $shippingPartner = $log->shipping_partner ?? $order->shipping_partner ?? null;
                $trackingNumber = $log->tracking_number ?? $order->tracking_number ?? null;
            @endphp
            
            @if(strtolower($statusName) === 'shipped' || $trackingNumber)
            <div class="shipping-card">
                <h3 class="shipping-title">Shipping Information</h3>
                
                <div class="shipping-details">
                    <div class="shipping-info">
                        <div class="info-row">
                            <span class="info-label">Shipping Partner</span>
                            <span class="info-value">{{ $shippingPartner ?? 'Standard Shipping' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tracking Number</span>
                            <span class="info-value" style="font-family: monospace; color: #f5576c;">
                                {{ $trackingNumber ?? 'Not available yet' }}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Estimated Delivery</span>
                            <span class="info-value">2-3 Business Days</span>
                        </div>
                    </div>
                    
                    <div class="shipping-info">
                        <div class="info-row">
                            <span class="info-label">Order Number</span>
                            <span class="info-value">#{{ $order->id }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Shipping Date</span>
                            <span class="info-value">{{ $log->created_at->format('M d, Y') ?? 'Today' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Package Status</span>
                            <span class="info-value" style="color: #27ae60; font-weight: 600;">In Transit</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Remarks -->
            @if(!empty($log->remarks))
            <div class="remarks-section">
                <h3 class="remarks-title">Update Details</h3>
                <div class="remarks-content">
                    {{ nl2br(e($log->remarks)) }}
                </div>
            </div>
            @endif
            
            <!-- CTA Buttons -->
            <div class="cta-section">
                @if($trackingNumber && in_array(strtolower($statusName), ['shipped', 'delivered']))
                    @php
                        $searchQuery = rawurlencode(($shippingPartner ?? '') . ' ' . $trackingNumber);
                        $trackUrl = "https://www.google.com/search?q=" . $searchQuery;
                    @endphp
                    <a href="{{ $trackUrl }}" target="_blank" class="btn btn-track">Track Your Shipment</a>
                @endif
                
                <a href="{{ url('/orders/' . $order->id) }}" class="btn" target="_blank">View Order Details</a>
                <a href="{{ url('/dashboard/orders') }}" class="btn btn-secondary" target="_blank">View All Orders</a>
            </div>
            
            <!-- Support Section -->
            <div class="support-section">
                <p class="support-title">Need Assistance?</p>
                <p style="color: #666; margin-bottom: 15px;">
                    If you have any questions about this update or need further assistance, 
                    our support team is here to help you.
                </p>
                <div style="font-size: 14px; color: #555;">
                    📧 <a href="mailto:support@{{ config('app.name') }}.com" style="color: #f5576c; text-decoration: none;">
                        support@{{ config('app.name') }}.com
                    </a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    📞 <a href="tel:+18001234567" style="color: #f5576c; text-decoration: none;">
                        1-800-123-4567
                    </a>
                </div>
                <p style="margin-top: 15px; font-size: 13px; color: #999;">
                    This is an automated notification. Please do not reply to this email.
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
                You are receiving this email because you placed an order with {{ config('app.name') }}.
                <a href="#" style="color: #999; text-decoration: underline;">Unsubscribe</a> from order updates.
            </div>
        </div>
    </div>
</body>
</html>