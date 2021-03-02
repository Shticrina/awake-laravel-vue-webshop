<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Payment;
use App\Http\Controllers\Payment\Services\BancontactHandler;
use Illuminate\Http\Request;
use Auth;

class BancontactPaymentController extends Controller
{
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function land($user_id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        // dd($user_id, $this->request->all()); // $this->request->query('user_id') = 4, ok

        $payment = Payment::where([
            'type' => 2,
            'user_id' => $user_id,
            'stripe_customer' => $this->request->query('client_secret'),
            'stripe_source' => $this->request->query('source'),
            'status' => 1
        ])->firstOrFail();
                
        $handler = new BancontactHandler($this->request);
        $response = $handler->process($payment);
        $redirect_status = $handler->source['redirect']['status'];

        return view('land', ['result' => $redirect_status]);
    }
}

?>
