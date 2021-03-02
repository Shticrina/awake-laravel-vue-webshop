<?php

namespace App\Http\Controllers\ShoppingCart;

use App\Order;
use App\OrderItem;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\AuthCode;
use Laravel\Passport\PersonalAccessClient;
use Laravel\Passport\HasApiTokens;
use Illuminate\Http\Request;

class ShoppingCartService
{
    public static function getBasket($create = TRUE, $isLoggedIn, $user_id, $orderId)
    {
        if ($create) { // get the current basket or create a new one if not exist
            if ($isLoggedIn) {
                $user = User::find($user_id);

                $basket = Order::firstOrCreate([
                    'user_id' => $user_id,
                    'user_session' => null,
                    'payment_status' => FALSE,
                    'shipping_address' => $user ? $user->address : null,
                    'shipping_country' => $user ? $user->country : null,
                    'shipping_city' => $user ? $user->city : null,
                    'shipping_postal_code' => $user ? $user->postal_code : null
                ]);
            } else { // no user_id, no order_id
                $basket = Order::firstOrCreate([
                    'user_session' => Session::getId(), 
                    'payment_status' => FALSE
                ]);
            }
        } else { // just get the current basket
            // dump('here joo', $user_id);
            if ($isLoggedIn) {
                $basket = Order::where([
                    'user_id' => $user_id,
                    'user_session' => null,
                    'payment_status' => FALSE
                ])->first();
                // dd($basket);
            } else {
                if ($orderId) {
                    $basket = Order::where([
                        'id' => $orderId, 
                        'user_session' => Session::getId(), 
                        'payment_status' => FALSE
                    ])->first();
                } else {
                    $basket = Order::where([
                        'user_id' => $user_id, 
                        'user_session' => Session::getId(), 
                        'payment_status' => FALSE
                    ])->first();
                }
            }
        }
        // dump($create, $isLoggedIn, $user_id); // false, true, 1
        return $basket;
    }

    // Merge a user basket with a session basket
    public static function mergeCart($user_id, $orderId, $loggedIn) // 33 (new), null, false
    { // 12, 17, true (when logout)
        $basket = null;
        $session_user = Session::getId();
        $user = User::find($user_id);

        if ($loggedIn) {
            // if we want to logout, 'payment_status' => FALSE
            if ($orderId) {
                $basket = Order::where('user_id', $user_id)
                    ->where('id', $orderId)
                    ->where('user_session', null)
                    ->where('payment_status', FALSE)
                    ->with('orderItems')
                    ->get()->first();
            } else {
                $basket = Order::where('user_id', $user_id)
                    ->where('user_session', null)
                    ->where('payment_status', FALSE)
                    ->with('orderItems')
                    ->get()->first();
            }

            if ($basket) {
                // $basket->update(['user_id' => null, 'user_session' => $session_user]);
                $basket->update(['user_session' => $session_user]);
            }
        } else {
            // dump('here', $user_id, $session_user);
            // if we want to login, or after register
            /*$basket = Order::where('user_id', $user_id)
                ->where('payment_status', FALSE)
                ->where('user_session', '<>', null)
                ->with('orderItems')
                ->get()->first();*/

            // dump($orderId, $user_id); // 22, 20
            if ($orderId) { // we register after having already an order
                $basket = Order::where('user_session', '<>', null)
                    ->where('id', $orderId)
                    ->where('user_id', null)
                    ->where('payment_status', FALSE)
                    ->with('orderItems')
                    ->get()->first();
                // dump('$orderId && $user_id == null', $basket);
            } else {
                $basket = Order::where('user_id', $user_id)
                    ->where('payment_status', FALSE)
                    ->where('user_session', '<>', null)
                    ->with('orderItems')
                    ->get()->first();
                // dump('else', $basket);
            }
            // dump($basket); // 23, null

            if ($basket) {
                $basket->update([
                    'user_session' => null,
                    'user_id' => $user_id,
                    'shipping_address' => $user ? $user->address : null,
                    'shipping_country' => $user ? $user->country : null,
                    'shipping_city' => $user ? $user->city : null,
                    'shipping_postal_code' => $user ? $user->postal_code : null
                ]);
            }
            // dd('basket after update: ', $basket);
        }

        return $basket;
    }
}
