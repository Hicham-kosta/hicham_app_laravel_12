@extends('admin.layout.layout')
@section('content')
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        color: #222;
        font-size: 13px;
        -webkit-font-smoothing: antialiased;
    }
    
    #invoice {
        width: 100%;
        box-sizing: border-box;
        background: #fff;
        padding: 18px;
    }
    
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }
    
    .invoice-header .logo h3 {
        margin: 0 0 6px 0;
        font-size: 20px;
    }
    
    .invoice-meta small {
        display: block;
        line-height: 1.25;
    }
    
    .table-invoice {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
    }
    
    .table-invoice th,
    .table-invoice td {
        padding: 8px 10px;
        border: 1px solid #e3e3e3;
        vertical-align: middle;
        font-size: 13px;
    }
    
    .table-invoice thead th {
        background: #f8f8f8;
        font-weight: 600;
    }
    
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    
    .totals-table td,
    .totals-table th {
        padding: 6px 10px;
        border: 0;
    }
    
    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }
        
        #invoice {
            max-width: 800px;
            margin: 0 auto;
            padding: 18px;
        }
        
        #printBtn {
            display: none !important;
        }
        
        body {
            margin: 0;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid py-3">
            <!-- Print button -->
            <div class="d-flex justify-content-end mb-3">
                <button id="printBtn" class="btn btn-primary" onclick="printInvoice()">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>
            
            <!-- Invoice -->
            <div id="invoice" class="card p-4">
                <div class="invoice-header">
                    <div class="logo">
                        <h3>{{ config('app.name', 'Your Shop') }}</h3>
                        <div class="invoice-meta">
                            <small><strong>Invoice #:</strong> INV {{ $order->id }}</small>
                            <small><strong>Invoice Date:</strong> {{ $order->created_at->format('Y-m-d') }}</small>
                            @if(!empty($barcodeBase64))
                            <div style="margin-top: 10px;">
                                <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Order Barcode" 
                                style="height:60px; display:block;">
                                <small>Order ID: {{ $order->id }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div><strong>Status</strong></div>
                        <div><span class="badge bg-success">{{ $order->status }}</span></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Billing To</h6>
                        <p>
                            {{ optional($order->user)->name ?? 'Guest User' }}<br>
                            {{ optional($order->address)->address_line_1 }}<br>
                            {{ optional($order->address)->city }}, 
                            {{ optional($order->address)->state }} - {{ optional($order->address)->pincode }}<br>
                            {{ optional($order->address)->mobile }}
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6>Shipping Info</h6>
                        <p>
                            <strong>Partner: </strong>{{ $order->shipping_partner ?? '-' }}<br>
                            <strong>Tracking number: </strong>{{ $order->tracking_number ?? '-' }}<br>
                            @if($order->tracking_link)
                                <a href="{{ $order->tracking_link }}" target="_blank">Track Package</a>
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Products Table -->
                <div class="table-responsive mb-3">
                    <table class="table-invoice">
                        <thead>
                            <tr>
                                <td style="width: 5%;">#</td>
                                <th style="width: 45%;">Product</th>
                                <th style="width: 15%;">Size</th>
                                <th style="width: 12%;" class="text-end">Price</th>
                                <th style="width: 8%;" class="text-center">Qty</th>
                                <th style="width: 15%;" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ optional($item->product)->name ?? $item->product_name }}</td>
                                <td class="text-center">{{ $item->size ?? '-' }}</td>
                                <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($item->price, 2) }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($item->price * $item->qty, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Totals Summary -->
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="totals-table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($order->sub_total ?? $order->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($order->shipping_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($order->tax_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <th>Total</th>
                                    <th class="text-end">{{ config('app.currency_symbol', '$') }}{{ number_format($order->total, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="small text-muted mb-0">Thank you for shopping with us!</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const orderId = {{ $order->id }};
function printInvoice() {
    try {
        const invoiceEl = document.getElementById('invoice');
        if (!invoiceEl) {
            console.error('Invoice element not found');
            return;
        }
        
        const invoiceHTML = invoiceEl.outerHTML;
        const css = `
            @page { size: A4; margin: 15mm; }
            body { font-family: Arial, Helvetica, sans-serif; color: #222; font-size: 13px; }
            #invoice { max-width: 800px; margin: 0 auto; padding: 18px; box-sizing: border-box; }
            .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; }
            .table-invoice { width: 100%; border-collapse: collapse; table-layout: fixed; word-wrap: break-word; }
            .table-invoice th, .table-invoice td { padding: 8px 10px; border: 1px solid #e3e3e3; vertical-align: middle; font-size: 13px; }
            .table-invoice thead th { background: #f8f8f8; font-weight: 600; }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            .totals-table td, .totals-table th { padding: 6px 10px; border: 0; }
            @media print { 
                #printBtn { display: none !important; } 
                body { margin: 0; } 
                #invoice { margin: 0; padding: 0; }
            }
        `;
        
        const win = window.open('', '_blank', 'width=1000,height=700,toolbar=0,location=0,menubar=0');
        
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Order Invoice #INV-${orderId}</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>${css}</style>
            </head>
            <body>
                ${invoiceHTML}
            </body>
            </html>
        `);
        
        win.document.close();
        win.focus();
        
        win.onload = function() {
            try {
                win.print();
            } catch(e) {
                console.warn('Print error', e);
            }
        };
        
        setTimeout(function() {
            if (!win.closed) {
                try {
                    win.print();
                } catch(e) {
                    // Ignore
                }
            }
        }, 800);
    } catch(err) {
        console.error('printInvoice error', err);
    }
}
</script>
@endsection