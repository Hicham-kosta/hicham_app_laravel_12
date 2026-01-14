<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsAttribute extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'sku',
        'price',
        'stock',
        'sort',
        'status'
    ];

    public static function productStock($product_id, $size){
        return self::where(['product_id' => $product_id, 'size' => $size, 'status' => 1])->value('stock') ?? 0;
    }

    /**
     * Attomically reduce stock by attribute ID
     * Returns true on success or false on failure
     */
    public static function reduceStockById(int $id, int $qty = 1): bool
    {
        if($qty <= 0) return false;
        $updated = self::where('id', $id)
        ->where('status', 1)
        ->whereNotNull('stock')
        ->where('stock', '>=', $qty)
        ->decrement('stock', $qty);
        return (bool)$updated;
    }

    /**
     * Attomically reduce stock by product ID + size
     * Returns true on success or false on failure
     */
    public static function reduceStockByProductAndSize(int $productId, string $size, int $qty = 1): bool
    {
        $size = trim((string)$size);
        if($qty <= 0 || $size === '') return false;
        $updated = self::where('product_id', $productId)
        ->where('size', $size)
        ->where('status', 1)
        ->whereNotNull('stock')
        ->where('stock', '>=', $qty)
        ->decrement('stock', $qty);
        return (bool)$updated;
    }
}
