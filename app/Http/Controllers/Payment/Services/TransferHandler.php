<?php

namespace App\Http\Controllers\Payment\Services;

use Auth;
use DB;
use App\Payment;
use Carbon\Carbon;

class TransferHandler extends PaymentHandler
{
    public $payment_type_id = 3;

    public function process(Payment $payment) {

        $this->payment = $payment;

        if (!$this->transferIsPending()) {
            DB::Transaction(function() {
                $this->payment->status = 2; // pending transfer
                $this->payment->transfer_comm = $this->generateCommunication();
                $this->payment->save();

                $this->payment->order->update(['payment_status' => 2]); // pending
            });
        }

        return ['success' => true, 'code' => 'transfer_validation', 'message' => 'Your transfer request has been registered. As soon as we receive your transfer, your order will be validated.'];
        // return to profile->your current order with message: "Dès réception de votre virement, votre commande sera validée."
    }

    private function transferIsPending() {
        return $this->payment->status === 2;
    }

    private function generateCommunication() {
        $communication = Auth::user()->name." ";
        $communication .= Carbon::now()->format('d-m-y')." ";
        $communication .= $this->payment->order_id;

        return $communication;
    }
}

?>