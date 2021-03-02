<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Hash;
use App\User;
use App\Order;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Http\Controllers\ShoppingCart\ShoppingCartService;

class UserController extends Controller
{
    function __construct() {}

    public function index()
    {
        return response()->json(User::with(['orders'])->get(), 200);
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];
        
        $user_id = null;
        $order_items = [];
        $status = 401;
        $response = ['error' => 'Unauthorised user! Please verify your email and your password and try again.'];

        if (Auth::attempt($credentials)) {
            $status = 200;
            $response = [
                'token' => Auth::user()->createToken('bigStore')->accessToken,
                'user' => Auth::user()
            ];
            
            $user_id = Auth::id();

            // merging basket
            $order = ShoppingCartService::mergeCart($user_id, null, false);

            if ($order && $order->orderItems) {
                $order->load('orderItems');

                foreach ($order->orderItems as $item) {
                    $item->name = $item->product->name;
                    $item->details = $item->product->details->where('color', $item->color)->where('size', $item->size)->first(); // for units
                }

                $order->save();
                $order_items = $order->orderItems;
            }

            $response['merging'] = $order ? 'Basket successfully merged.' : 'No basket!'; 
            $response['order_id'] = $order ? $order->id : null;
            $response['order'] = $order;
            $response['order_items'] = $order_items;
        }
        
        return response()->json($response, $status);
    }

    public function register(Request $request)
    {
        $order_items = [];
        $order_id = $request->get('order_id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.,\_\-\']*(\w)+$/i',
            'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->whereNull('deleted_at')
                ],
            'password' => 'required|string|min:6|confirmed',
            /*'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',*/
            'phone' => 'nullable|string|min:9|max:14|regex:/^[0-9]*$/',
            'address' => 'required|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.,\_\-\']*(\w)+$/i',
            'postal_code' => 'required|string|min:4|max:8|regex:/^[0-9]+$/',
            'city' => 'required|string|max:160',
            'country' => 'required|string|max:160'
            // 'photo' => 'nullable|max:8192|mimes:jpeg,bmp,png,jpg,gif',
        ])->validate();

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'address' => $request->get('address'),
            'postal_code' => $request->get('postal_code'),
            'city' => $request->get('city'),
            'country' => $request->get('country'),
            'phone' => $request->get('phone'),
            'is_admin' => 0
        ]);

        $response = [
            'user' => $user,
            'token' => $user->createToken('bigStore')->accessToken
        ];

        // merging basket
        // if register after having already an order => $order_id != null
        // dd($user->id, $order_id, 'end register in Controller');
        $order = ShoppingCartService::mergeCart($user->id, $order_id, false);

        if ($order && $order->orderItems) {
            foreach ($order->orderItems as $item) {
                $item->name = $item->product->name;
                $item->details = $item->product->details->where('color', $item->color)->where('size', $item->size)->first(); // for units
            }

            $order->save();
            $order_items = $order->orderItems;
        }

        $response['merging'] = $order ? 'Basket successfully merged.' : 'No basket!'; 
        $response['order_id'] = $order ? $order->id : null;
        $response['order'] = $order;
        $response['order_items'] = $order_items;
        
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $order_id = $request->get('order_id') && $request->get('order_id') != "null" ? $request->get('order_id') : null;

        if ($request->get('user_id')) {
            $id = $request->input('user_id');

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.,\_\-\']*(\w)+$/i',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users')->ignore($id)->whereNull('deleted_at')
                ],
                'phone' => 'nullable|string|min:9|max:14|regex:/^[0-9]*$/',
                'address' => 'required|string|min:2|max:191|regex:/^(\w)+[a-zA-Z0-9\s.,\_\-\']*(\w)+$/i',
                'country' => 'required|string|max:160',
                'city' => 'required|string|max:160',
                'postal_code' => 'required|string|min:4|max:8|regex:/^[0-9]+$/',
                'password' => 'nullable|string|min:6|confirmed'
            ])->validate();

            $user = User::find($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->address = $request->input('address');
            $user->postal_code = $request->input('postal_code');
            $user->city = $request->input('city');
            $user->country = $request->input('country');
            $user->phone = $request->input('phone');

            $password = $request->input('password');
            $password_confirmation = $request->input('password_confirmation');

            if (!empty($password) && !empty($password_confirmation) && $password == $password_confirmation) {
                $user->password = Hash::make($password);
            }

            $user->save();
            // dd($order_id, $request->all());

            if ($order_id) {
                $order = Order::find($order_id);
                $order->shipping_address = $request->input('address');
                $order->shipping_postal_code = $request->input('postal_code');
                $order->shipping_city = $request->input('city');
                $order->shipping_country = $request->input('country');
                $order->save();
            }

            return response()->json([
                'user' => $user,
                'success' => 'User successfully updated.'
            ]);
        }

        return response()->json([
            'error' => 'Error updating user!',
            'success' => false
        ]);
    }
    
    public function show(User $user)
    {
        return response()->json($user,200);
    }
    
    public function showOrders(User $user) // for Userboard
    {
        return response()->json($user->orders()->get(),200);
    }

    public function showPayments(User $user) // for Userboard
    {
        return response()->json($user->payments()->get(),200);
    }
}