<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    // protected $guarded = [];

    /*protected $dispatchesEvents = [
        'created' => \App\Modules\Payment\Events\PaymentCreated::class
    ];*/
    use SoftDeletes;
    
    protected $fillable = [
        'type', 'type_name', 'amount', 'stripe_customer', 'stripe_source', 'stripe_charge', 'transfer_comm', 'status', 'order_id', 'user_id'
    ];

    public static $payment_types = [
        1 => 'credit',
        2 => 'bancontact',
        3 => 'transfer'
    ];

    public static $payment_status = [
        0 => 'error',
        1 => 'in_process',
        2 => 'pending',
        4 => 'succeeded',
        6 => 'canceled',
        7 => 'failed'
    ];

    public function order() {
        return $this->belongsTo('App\Order');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function getStatusPublicNameAttribute() {
        $names = ['Error', 'In Process', 'Pending', 'Pending', 'Validated', 'Disputed', 'Aborted', 'Failed', 'Refund'];
        return $names[$this->status];
    }

    public function getStatusNameAttribute() {
        // dd(self::$payment_status[$this->status]);
        return self::$payment_status[$this->status];
    }

    public function getTypeNameAttribute() {
        // Get the text type identifier
        $type_id = self::$payment_types[$this->type];
        return $type_id;
    }

    public function fail($code = 6) { // transfer cancelled by admin OR bancontact failed
        \DB::Transaction(function() use($code) {
            $this->status = $code;
            $this->deleted_at = \Carbon\Carbon::now();
            $this->save();

            $this->order->payment_status = 1; // failed OR transfer not yet validated
            $this->order->save();
        });
    }

    public function succeed() { // when admin validates a transfer OR bancontact succeded
        \DB::Transaction(function() {
            $this->status = 4;
            $this->save();

            $this->order->payment_status = 2; // succeded OR validated
            $this->order->save();
        });
    }

    /*
     * Scopes
     *
     */

   // The statuses that should prevent the creation of a new payment
    /*public function scopeBlocking($query) {
        return $query->whereIn('status', [1, 2, 3, 4, 5]);
    }

    public function scopeCancelable($query) {
        return $query->whereIn('status', [1, 2]);
    }

    public function scopeCredit($query) {
        return $query->where('type', 1);
    }

    public function scopeBancontact($query) {
        return $query->where('type', 2);
    }

    public function scopeTransfer($query) {
        return $query->where('type', 3);
    }*/

    public function handler()
    {
        $handlers = [
            1 => \App\Http\Controllers\Payment\Services\Card2Handler::class,
            2 => \App\Http\Controllers\Payment\Services\BancontactHandler::class,
            3 => \App\Http\Controllers\Payment\Services\TransferHandler::class
        ];

        return new $handlers[$this->type]($this);
    }

    /*public function getDisplayTypeAttribute()
    {

        return self::$payment_types[$this->type];
        // return trans('payment.types.'.self::$payment_types[$this->type]);
    }*/

    /*public function getStripeUrlAttribute()
    {
        $stripe_base = "https://dashboard.stripe.com/payments/";

        $ids = [
          0 => $this->stripe_charge,
          1 => $this->stripe_charge,
          2 => null,
          3 => $this->stripe_source
        ];

        if(!in_array($this->type, array_keys($ids))) return null;

        $id = $ids[$this->type];

        if(is_null($id) || empty($id)) return null;

        return $stripe_base.$id;
    }*/
}
