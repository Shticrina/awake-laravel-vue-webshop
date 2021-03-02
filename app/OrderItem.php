<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{    
    protected $fillable = [
        'order_id', 'product_id', 'price', 'quantity', 'size', 'color', 'image'
    ];
    
    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function order() {
        return $this->belongsTo(Product::class,'order_id');
    }
}
