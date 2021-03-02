<?php

namespace App\Http\Controllers\Payment\Services;
use App\Payment;
use Auth;
use Format;
use Stripe\Charge;
use Stripe\Source;

class BancontactHandler extends PaymentHandler
{
    public $payment_type_id = 2;

    public $payment;
    public $source;
    public $charge;

    public $code;
    public $success = true;
    public $error_prefix = 'bancontact';

    public $failure_states = [
        'client_security',
        'redirect_failed',
        'redirect_unknown',
        'source_failed',
        'source_canceled',
        'source_unknown',
        'charge_failed',
        'charge_unknown'
    ];

    public $end_states = [
        'charge_succeeded',
        'source_consumed'
    ];

    public function process(Payment $payment) 
    {
        $this->payment = $payment;
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $this->setSource();

        if ($this->readyForCharge()) {
            $done = $this->attemptCharge();
            if ($done) $this->succeedPayment();
        }

        if (in_array($this->code, $this->failure_states)) {
            $this->success = false;
            $this->payment->fail(); // payment->status = 6 or 7
        }

        if (in_array($this->code, $this->end_states)) {
            $this->payment->succeed(); // payment->status = 4
        }

        return $this->buildResponse();
    }

    private function setSource() 
    {
        if (empty($this->payment->stripe_source)) {
            $user = Auth::user();

            $this->source = Source::create([
                "type" => "bancontact",
                "currency" => "eur",
                "amount" => Format::priceForStripe($this->payment->amount),
                "owner" => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'address' => [
                        'postal_code' => $user->postal_code
                    ]
                ],
                "metadata" => [
                    'order_id' => $this->payment->order_id,
                    'payment_id' => $this->payment->id
                ],
                "redirect" => [
                    "return_url" => route('bancontact.land', ['user_id' => $user->id])
                ]
            ]);

            $this->payment->stripe_source = $this->source['id'];
            $this->payment->stripe_customer = $this->source['client_secret'];
            $this->payment->save();
        } else {
            $this->source = Source::retrieve($this->payment->stripe_source);
        }
    }

    private function attemptCharge()
    {
        $this->charge = Charge::create([
            'amount' => Format::priceForStripe($this->payment->amount),
            'currency' => 'eur',
            'source' => $this->source['id']
        ]);

        $this->payment->stripe_charge = $this->charge['id'];
        $this->payment->save();

        $expected_status = ['failed', 'pending', 'succeeded'];

        if (!in_array($this->charge['status'], $expected_status)) $this->code = 'charge_unknown';
        else $this->code = 'charge_'.$this->charge['status'];

        if ($this->charge['status'] === 'succeeded') return TRUE;
    }

    public function buildResponse()
    {
        $response = ['success' => $this->success, 'code' => $this->code];

        if ($this->code === 'redirect_failed') {
            $response['data'] = $this->source['redirect']['failure_reason'];
            $response['message'] = 'payment.'.$this->error_prefix.".".$this->code;
        } elseif ($this->code === 'redirect_pending') {
            $response['data'] = $this->source['redirect']['url'];
            $response['message'] = 'payment.'.$this->error_prefix.".".$this->code;
        } else {
            $response['message'] = 'Your bancontact payment has been accepted.';
        }

        // $response['message'] = 'payment.'.$this->error_prefix.".".$this->code; // payment.bancontact.redirect_pending

        return $response;
    }

    private function readyForCharge()
    {
        return ($this->redirectIsDone() && $this->sourceIsChargeable() && $this->notCharged());
    }

    protected function redirectIsDone() 
    {
        $known_status = ['failed', 'pending', 'succeeded'];

        if (!in_array($this->source['redirect']['status'], $known_status)) {
            $this->code = 'redirect_unknown';
        } else {
            $this->code = 'redirect_'.$this->source['redirect']['status']; // pending
        }
        // $this->source['redirect']['status']) == pending

        if ($this->source['redirect']['status'] === 'succeeded') {
            $this->payment->status = 2;
            $this->payment->save();

            return true;
        }
        
        return false;
    }

    private function sourceIsChargeable() 
    {
        $expected_status = ['failed', 'canceled', 'consumed', 'pending', 'chargeable'];

        if (!in_array($this->source['status'], $expected_status)) {
            $this->code = 'source_unknown';
        } else {
            $this->code = 'source_'.$this->source['status'];
        }

        if ($this->source['status'] === 'chargeable') {
            return true;
        }
            
        return false;
    }

    private function notCharged() 
    {
        if (is_null($this->payment->stripe_charge)) {
            return true;
        }

        return false;
    }
}

?>