<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\Payment\Services\BancontactHandler;
use App\Http\Controllers\Payment\Services\Card2Handler;
use App\Http\Controllers\Payment\Services\TransferHandler;
use App\Payment;
use App\Order;
// use App\Modules\Commerce\Jobs\refreshOrder;

class PaymentController extends Controller
{

    public $payment;
    public $request;
    public $order;

    public $types = [
        1 => 'credit card',
        2 => 'bancontact',
        3 => 'transfer'
    ];

    function __construct() 
    {
        $this->middleware('auth');
    }

    /**
     * Makes basic verifications then dispatches to a payment handler
     * @param
     * @return array The result of the request
     */
    public function pay(Request $request) // Bancontact & transfer
    { 
        $this->request = $request;
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        // dd($request->all());

        $this->order = Order::findOrFail($this->request->input('order_id'));
        $this->validateOrder($this->order);
        $this->authorizeUser();
        $this->payment = $this->getPayment(); // $this->order->payments->first();

        if ($this->request->input('payment_method') == 2) {
            $handler = new BancontactHandler($this->request);
        } elseif ($this->request->input('payment_method') == 3) {
            $handler = new TransferHandler($this->request);
        }

        if (!$this->payment) {
            return $handler->initiate($this->order); // create payment
        } else {
            $valid_payment = $this->validatePayment($this->payment);

            if ($valid_payment !== TRUE) {
                return $valid_payment;
            }

            return $handler->process($this->payment);
        }
    }

    // Secure credit card
    public function pay2(Request $request)
    { 
        $this->request = $request;
        $shipping_price = $this->request->input('shipping_price') && $this->request->input('shipping_price') > 0 ? $this->request->input('shipping_price') : 0;
        // dd($this->request->all()); // order_id: 26, payment_method: 1, shipping_price: "60"

        // Validate the order
        $this->order = Order::with('orderItems')->findOrFail($request->input('order_id'));
        $this->validateOrder($this->order);

        // Check the user
        $this->authorizeUser();
        $this->payment = $this->getPayment(); // $this->order->payments->first();

        // Initiate the handler
        $handler = new Card2Handler;
        $handler->setUser(Auth::user());
        $handler->setOrder($this->order);

        if (!$this->payment) {
            $handler->createPayment($this->order->total_price + $shipping_price); // ok
        } else {
            $valid_payment = $this->validatePayment($this->payment);

            if ($valid_payment !== TRUE) {
                return $valid_payment;
            }

            $handler->setPayment($this->payment);
        }

        return $handler->handle(); // $this->intent->client_secret, // get paymentIntent status: "requires_payment_method"
    }

    private function validateOrder(Order $order)
    {
        // Abort if order already paid
        if ($this->order->payment_status > 1) {
            abort(400, "Order already paid");
        }

        $items_count = $this->order->orderItems->count();

        // Abort if order price is zero & no items in order
        if (bccomp($this->order->total_price, 0) === 0 && $items_count < 1) {
            abort(400, "Empty Order");
        }
    }

    private function validatePayment(Payment $payment)
    {
        if ($this->payment->type !== (int) $this->request->input('payment_method')) {
            return [
                'success' => false,
                'code' => 'payment_concurrent',
                'message' => 'A '.$this->types[$this->payment->type].' payment is already in progress. Do you want to cancel it?'
            ];
        }

        return TRUE;
    }

    /**
     * Aborts if the order is not owned by the connected user
     * @return void
     */
    public function authorizeUser()
    {
        if (!Auth::check()) abort(403, 'Please register or log in');
        if (Auth::id() !== (int) $this->order->user_id) abort(403);
    }

    /**
     * Gets the order's payment
     * Throws an exception if there are more than one payment
     * @return Payment The payment associated with the order
     */
    public function getPayment() 
    {
        $collection = $this->order->payments;

        if (count($collection) > 1) {
            throw new \Exception("Too many payments", 1);
        }

        return $collection->first();
    }

    public function cancel(Request $request)
    {
        $this->order = Order::findOrFail($request->input('order_id'));
        $this->payment = $this->getPayment(); // $this->order->payments->first();

        if (!$this->payment) abort(404);

        switch ($this->payment->type) {
            case 0:
                return ['success' => false];
                // abort(400, "This type of payment can't be canceled");
                break;
            case 1:
                if (in_array($this->payment->status, [1, 2])) {
                    $this->payment->update([
                        'status' => 6,
                        'deleted_at' => Carbon::now()
                    ]);

                    return ['success' => true];
                }

                return ['success' => false];
                break;
            case 2:
                if (in_array($this->payment->status, [1, 2])) {
                    $this->payment->update([
                        'status' => 6,
                        'deleted_at' => Carbon::now()
                    ]);

                    return ['success' => true];
                }

                return ['success' => false];
                break;
            case 3:
                return ['success' => false];
                break;
            default:
                abort(400);
                break;
        }
    }

    /*public function index() 
    {
        $payments = Auth::user()->payments->sortByDesc('id');

        return view('Payment.views.user_index', compact('payments'));
    }*/
}

?>