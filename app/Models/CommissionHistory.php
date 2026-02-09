<?php

namespace App\Models;  // NOT App\Http\Controllers\Admin

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'commission_history';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'vendor_id',
        'product_id',
        'product_name',
        'sku',
        'size',
        'item_price',
        'quantity',
        'subtotal',
        'commission_percent',
        'commission_amount',
        'vendor_amount',
        'gst_percent',
        'gst_amount',
        'status',
        'payment_date',
        'payment_method',
        'payment_reference',
        'payment_notes',
        'processed_by',
        'settled_at',
        'cancelled_at',
        'commission_date',
        'month',
        'year',
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'settled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'commission_date' => 'date',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
    
    public function vendor()
    {
        return $this->belongsTo(Admin::class, 'vendor_id')->where('role', 'vendor');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function processor()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }
    
    // Rest of your model code...
    
    // Vendor details (through vendor)
    public function vendorDetails()
    {
        return $this->hasOneThrough(
            VendorDetail::class,
            Admin::class,
            'id', // Foreign key on Admin table
            'admin_id', // Foreign key on VendorDetail table
            'vendor_id', // Local key on CommissionHistory table
            'id' // Local key on Admin table
        );
    }

    /**
     * Scopes
     */
    
    // Scope for pending commissions
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    // Scope for paid commissions
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    // Scope for cancelled commissions
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
    
    // Scope for a specific vendor
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }
    
    // Scope for date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('commission_date', [$startDate, $endDate]);
    }
    
    // Scope for current month
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('commission_date', now()->month)
                    ->whereYear('commission_date', now()->year);
    }
    
    // Scope for last month
    public function scopeLastMonth($query)
    {
        return $query->whereMonth('commission_date', now()->subMonth()->month)
                    ->whereYear('commission_date', now()->subMonth()->year);
    }

    /**
     * Accessors
     */
    
    // Formatted commission amount
    public function getFormattedCommissionAmountAttribute()
    {
        return '₹' . number_format($this->commission_amount, 2);
    }
    
    // Formatted vendor amount
    public function getFormattedVendorAmountAttribute()
    {
        return '₹' . number_format($this->vendor_amount, 2);
    }
    
    // Formatted subtotal
    public function getFormattedSubtotalAttribute()
    {
        return '₹' . number_format($this->subtotal, 2);
    }
    
    // Status badge HTML
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'refunded' => '<span class="badge bg-info">Refunded</span>',
        ];
        
        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
    
    // Days pending calculation
    public function getDaysPendingAttribute()
    {
        if ($this->status !== 'pending') {
            return 0;
        }
        
        return now()->diffInDays($this->created_at);
    }
    
    // Get payment method name
    public function getPaymentMethodNameAttribute()
    {
        $methods = [
            'bank_transfer' => 'Bank Transfer',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'upi' => 'UPI',
        ];
        
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Business Logic Methods
     */
    
    // Mark as paid
    public function markAsPaid($paymentMethod, $reference, $notes = null, $processedBy = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
            'payment_notes' => $notes,
            'processed_by' => $processedBy ?? auth('admin')->id(),
            'settled_at' => now(),
        ]);
        
        return $this;
    }
    
    // Mark as cancelled
    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'payment_notes' => $reason,
            'cancelled_at' => now(),
        ]);
        
        return $this;
    }
    
    // Calculate and set commission
    public function calculateCommission($subtotal, $commissionPercent)
    {
        $commissionAmount = ($subtotal * $commissionPercent) / 100;
        $vendorAmount = $subtotal - $commissionAmount;
        
        $this->update([
            'subtotal' => $subtotal,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'vendor_amount' => $vendorAmount,
        ]);
        
        return $this;
    }
    
    // Get commission summary for vendor
    public static function getVendorSummary($vendorId, $startDate = null, $endDate = null)
    {
        $query = self::forVendor($vendorId);
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        $result = $query->selectRaw('
            COUNT(*) as total_items,
            SUM(subtotal) as total_sales,
            SUM(commission_amount) as total_commission,
            SUM(vendor_amount) as total_vendor_amount,
            AVG(commission_percent) as avg_commission_percent
        ')->first();
        
        return [
            'total_items' => $result->total_items ?? 0,
            'total_sales' => $result->total_sales ?? 0,
            'total_commission' => $result->total_commission ?? 0,
            'total_vendor_amount' => $result->total_vendor_amount ?? 0,
            'avg_commission_percent' => $result->avg_commission_percent ?? 0,
        ];
    }
    
    // Get pending amount for vendor
    public static function getPendingAmount($vendorId)
    {
        return self::forVendor($vendorId)
            ->pending()
            ->sum('vendor_amount');
    }
    
    // Get paid amount for vendor
    public static function getPaidAmount($vendorId)
    {
        return self::forVendor($vendorId)
            ->paid()
            ->sum('vendor_amount');
    }
    
    // Get monthly summary for vendor
    public static function getMonthlySummary($vendorId, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
        
        $query = self::forVendor($vendorId)
            ->whereYear('commission_date', $year)
            ->whereMonth('commission_date', $month);
        
        $result = $query->selectRaw('
            COUNT(*) as total_items,
            SUM(subtotal) as total_sales,
            SUM(commission_amount) as total_commission,
            SUM(vendor_amount) as total_vendor_amount,
            SUM(CASE WHEN status = "pending" THEN vendor_amount ELSE 0 END) as pending_amount,
            SUM(CASE WHEN status = "paid" THEN vendor_amount ELSE 0 END) as paid_amount
        ')->first();
        
        return [
            'total_items' => $result->total_items ?? 0,
            'total_sales' => $result->total_sales ?? 0,
            'total_commission' => $result->total_commission ?? 0,
            'total_vendor_amount' => $result->total_vendor_amount ?? 0,
            'pending_amount' => $result->pending_amount ?? 0,
            'paid_amount' => $result->paid_amount ?? 0,
        ];
    }
    
    // Get top selling products for vendor
    public static function getTopProducts($vendorId, $limit = 10)
    {
        return self::forVendor($vendorId)
            ->selectRaw('
                product_id,
                product_name,
                sku,
                COUNT(*) as times_sold,
                SUM(quantity) as total_quantity,
                SUM(subtotal) as total_sales,
                SUM(commission_amount) as total_commission,
                SUM(vendor_amount) as total_vendor_amount
            ')
            ->whereNotNull('product_id')
            ->groupBy('product_id', 'product_name', 'sku')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();
    }
}