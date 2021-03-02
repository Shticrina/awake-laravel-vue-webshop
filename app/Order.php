<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    // use SoftDeletes;
    
    protected $fillable = [
        'user_id', 'user_session', 'total_price', 'payment_status', 'is_delivered', 'shipping_address', 'shipping_country', 'shipping_city', 'shipping_postal_code'
    ];
    
    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    /*public function getItemsCountAttribute() {
        return $this->orderItems()->count();
    }*/

    public function payments() {
        return $this->hasMany(Payment::class);
    }
}
