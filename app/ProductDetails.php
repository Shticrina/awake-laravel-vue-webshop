<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetails extends Model
{    
    protected $fillable = [
        'product_id', 'price', 'units', 'size', 'color', 'image'
    ];
    
    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function all_colors() {
        return $this->where('product_id', $this->product_id)->get()->unique('color')->pluck('color')->all();
    }

    public function all_sizes() {
        return $this->where('product_id', $this->product_id)->get()->unique('size')->pluck('size')->all();
    }
}
