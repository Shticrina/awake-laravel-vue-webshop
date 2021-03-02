<?php

namespace App\Http\Controllers\Payment\Services;

// use App\Http\Controllers\Payment\Services\PaymentHandler;
use App\Order;
use App\Payment;
use App\User;
use Format;
use DB;

use Stripe\Stripe;
use Stripe\PaymentIntent;

/**
 *  Handles Card payments using Stripe's PaymentIntent API
 */
class Card2Handler
{
    private $order;
    private $payment;
    private $intent;
    private $user;

    public static $payment_type_id = 1;

    function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createPayment($amount) // if !payment
    {
        DB::Transaction(function() use($amount) {
          $this->payment = Payment::create([
              'type' => $this::$payment_type_id,
              'type_name' => 'credit',
              'order_id' => $this->order->id,
              'user_id' => $this->user->id,
              'amount' => $amount,
              'status' => 1
          ]);

          $this->order->update(['payment_status' => 1]); // not finished yet
        });
    }

    public function handle()
    {
        if ($this->payment->stripe_source) {
            $this->intent = PaymentIntent::retrieve($this->payment->stripe_source);
        } else {
            $this->createPaymentIntent($this->payment->amount, $this->order->id);
        }

        return $this->intent->client_secret;
    }

    private function createPaymentIntent($amount, int $order_id)
    {
        $this->intent = PaymentIntent::create([
            'amount' => Format::priceForStripe($amount),
            'currency' => 'eur',
            'metadata' => [
                'order_id' => $order_id,
                'payment_id' => $this->payment->id,
                'customer_name' => $this->user->name,
                'email' => $this->user->email,
            ]
        ]);

        $this->payment->stripe_source = $this->intent->id;
        $this->payment->save();
    }

    public function setUser(User $user) 
    {
        $this->user = $user;
    }

    public function setOrder(Order $order) {
        $this->order = $order;
    }

    public function setPayment(Payment $payment) {
        $this->payment = $payment;
    }
}

?>