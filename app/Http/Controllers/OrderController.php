<?php

namespace App\Http\Controllers;

use App\Order;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Validator;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with(['orderItems'])->get(),200);
    }
        
    public function deliverOrder(Order $order) // for admin dashboard
    {
        $order->is_delivered = true;
        $status = $order->save();
        
        return response()->json([
            'status'    => $status,
            'data'      => $order,
            'message'   => $status ? 'Order Delivered!' : 'Error Delivering Order'
        ]);
    }

    public function show(Order $order)
    {
        $payment_status = null;

        $order= $order->load('payments', 'orderItems', 'user');
        $payment= $order->payments->first();
        $orderItems= $order->orderItems;

        foreach ($orderItems as $item) {
            $item->name = $item->product->name;
        }

        if (!$payment) {
            $payment_status = 'No payment yet';
        } elseif ($payment->type === 0) {
            $payment_status = 'Error during payment';
        } elseif ($payment->status === 1) {
            $payment_status = 'In process/Not finished';
        } elseif ($payment->type === 2 && $payment->status === 2) {
            $payment_status = 'Pending';
        } elseif ($payment->status === 4) {
            $payment_status = 'Paid';
        } elseif ($payment->status === 6 || $payment->status === 7) {
            $payment_status = 'Canceled/Failed';
        }

        return response()->json([
            'order' => $order,
            'payment' => $payment,
            'orderItems' => $orderItems,
            'payment_status' => $payment_status
        ]);
    }
    
    public function update(Request $request, Order $order) // for admin dashboard
    {
        $status = $order->update(
            $request->only(['quantity'])
        );
        
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Order Updated!' : 'Error Updating Order'
        ]);
    }

    public function updateShippingAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'nullable|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.,\_\-\']*(\w)+$/i',
            'shipping_country' => 'nullable|string|max:160',
            'shipping_city' => 'nullable|string|max:160',
            'shipping_postal_code' => 'nullable|string|min:4|max:8|regex:/^[0-9]+$/'
        ])->validate();

        if ($request->order_id && $request->user_id) {
            $order = Order::find($request->order_id);
            $user = User::find($request->user_id);

            if ($order && $user) {
                $order->shipping_address = $request->shipping_address ? $request->shipping_address : $user->address;
                $order->shipping_country = $request->shipping_country ? $request->shipping_country : $user->country;
                $order->shipping_city = $request->shipping_city ? $request->shipping_city : $user->city;
                $order->shipping_postal_code = $request->shipping_postal_code ? $request->shipping_postal_code : $user->postal_code;
                $order->save();
            }

            return response()->json([
                'order' => $order,
                'success' => $order ? 'Shipping address successfully updated.' : 'Error updating shipping address!'
            ]);
        }
        
        return response()->json([
            'error' => 'Error updating order!'
        ]);
    }

    public function destroy(Order $order) // for admin dashboard
    {
        $status = $order->delete();
        
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Order Deleted!' : 'Error Deleting Order'
        ]);
    }
}
