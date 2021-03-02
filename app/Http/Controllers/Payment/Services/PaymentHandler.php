<?php

namespace App\Http\Controllers\Payment\Services;

use App\Order;
use App\Payment;
use DB;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

abstract class PaymentHandler
{
    public $payment;
    public $code;
    public $order;
    public $request;
    public $failure_states;
    public $error_prefix;

    abstract public function process(Payment $payment);

    public function __construct(Request $request) 
    {
        $this->request = $request;
    }

    public function initiate(Order $order) 
    {
        $this->order = $order;
        $shipping_price = $this->request->input('shipping_price') && $this->request->input('shipping_price') > 0 ? $this->request->input('shipping_price') : 0;

        $payment = Payment::create([
            'type' => $this->payment_type_id,
            'type_name' => $this->getPaymentName($this->payment_type_id),
            'order_id' => $this->order->id,
            'user_id' => Auth::id(),
            'amount' => $this->order->total_price + $shipping_price,
            'status' => 1
        ]);

        return $this->process($payment);
    }

    public function failPayment() // for BancontactHandler
    {
        DB::Transaction(function() {
            $this->payment->status = 6;
            $this->payment->deleted_at = Carbon::now();
            $this->payment->save();

            $this->payment->order->update(['payment_status' => 1]);
        });
    }

    public function succeedPayment() // for BancontactHandler
    {
        DB::Transaction(function() {
            $this->payment->status = 4;
            $this->payment->save();

            $this->payment->order->update(['payment_status' => 2]);
        });
    }

    public function getPaymentName(int $payment_method) 
    {
        $payment_type = '';

        if ($payment_method == 2) {
            $payment_type = 'bancontact';
        } elseif ($payment_method == 3) {
            $payment_type = 'transfer';
        } else {
            $payment_type = 'credit';
        }

        return $payment_type;
    }
}

?>
