<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterValue extends Model
{
    protected $fillable = ['filter_id', 'value', 'sort', 'status'];

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }
    
    // products that have this filter value
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_filter_values', 'filter_value_id', 'product_id');
    }
}
