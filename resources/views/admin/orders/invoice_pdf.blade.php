<!doctype html>
<html large="en">
    <head>
        <meta charset="UTF-8">
        <title>Invoice #INV-{{ $order->id }}</title>
        <style>
            body {font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #222; font-size: 13px; margin: 0; padding: 0;}
            .page {padding: 15mm;}
            #invoice {max-width: 800px; margin: 0 auto; background: #fff;}
            .invoice-header {display: flex; justify-content: space-between; margin-bottom: 18px;}
            .logo h3 {margin: 0 0 6px 0; font-size: 18px;}
            .invoice-meta small {display: block; line-height: 1.25;}
            .table-invoice {width: 100%; border-collapse: collapse; margin-bottom: 12px;}
            .table-invoice th,
            .table-invoice td {padding: 8px 10px; border: 1px solid #e3e3e3;}
            .table-invoice thead th {background: #f8f8f8; font-weight: 600;}
            .text-end {text-align: right;}
            .text-center {text-align: center;}
            .totals-table {width: 100%; border-collapse: collapse;}
            .totals-table td {padding: 6px 10px;}
        </style>
    </head>
    <body>
        <dive class="page">
            <div id="invoice">
                <div class="invoice-header">
                    <div class="logo">
                        <h3>{{ config('app.name', 'Your Shop') }}</h3>
                        <div class="invoice-meta">
                            <small><strong>Invoice #: </strong>INV-{{ $order->id }}</small>
                            <small><strong>Invoice Date: </strong>{{ $order->created_at->format('Y-m-d') }}</small>
                            @if(!empty($barcodeBase64))
                            <div style="margin-top: 10px;">
                                <img src="data:image/png;base64,{{ $barcodeBase64 }}" height="60"> 
                                <small>Order ID: {{ $order->id }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <strong>Status:</strong><br>
                         {{ ucfirst($order->status) }}
                    </div>
                </div>
                <div style="display-flex; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width:48%;">
                        <strong>Billing To:</strong><br>
                        {{ optional($order->user)->name ?? 'Guest User' }}<br>
                        {{ optional($order->address)->address_line1 }}<br>
                        {{ optional($order->address)->city }}, 
                        {{ optional($order->address)->state }} - {{ optional($order->address)->pincode }}<br>
                        {{ optional($order->address)->mobile }}
                    </div>
                    <div style="width:48%; text-align: left;">
                        <strong>Shipping Info:</strong><br>
                        <strong>Partner:</strong> {{ $order->shipping_partner ?? '-' }}<br>
                        <strong>Tracking number #: </strong> {{ $order->tracking_number ?? '-' }}<br>
                    </div>
                </div>
                <table class="table-invoice">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Size</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $tem->product->name ?? $item->product_name }}</td>
                            <td class="text-center">{{ $item->size ?? '-' }}</td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($item->price * $item->qty, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="text-align: right; width: 300px margin-left: auto;">
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal: </td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($order->sub_total ?? $order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td>Discount: </td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->wallet > 0)
                        <tr>
                            <td>Wallet Amount: </td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($order->wallet, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>shipping: </td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($order->shipping ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax: </td>
                            <td class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($order->tax_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total:</th>
                            <th class="text-end">{{ config('app.currency_symbol', '$') }}
                                {{ number_format($order->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
                    <p>Thank you for shopping with us!</p>
                </div>
            </div>
        </div>
    </body>
</html>