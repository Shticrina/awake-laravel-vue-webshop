<?php

namespace App\Http\Controllers\ShoppingCart;

use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\Product;
use App\ProductDetails;
use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ShoppingCart\ShoppingCartService;
use App\Http\Controllers\Controller;
use stdClass;

class BasketController extends Controller
{
    public function show(Order $order)
    {
        return response()->json($order,200);
    }

    public function currentShoppingCart(Request $request)
    {
        $total_price = 0;
        $order_items = [];
        $order_id = null;

        $basket = ShoppingCartService::getBasket(FALSE, $request->isLoggedIn, $request->user_id, $request->order_id);
        
        if ($basket && $basket->orderItems) {
            $basket->load('orderItems');

            foreach ($basket->orderItems as $item) {
                $item->name = $item->product->name;
                $item->details = $item->product->details->where('color', $item->color)->where('size', $item->size)->first();
                $total_price += $item->price * $item->quantity;
            }

            $basket->total_price = $total_price;
            $basket->save();

            $order_items = $basket->orderItems;
            $order_id = $basket->id;
        }
        // dd($basket, $order_items); // null, []

        return response()->json([
            'basket' => $basket,
            'order_id'   => $order_id,
            'order_items' => $order_items
        ]);
    }
    
    public function addItem(Request $request)
    {
        $userId = null;
        $total_price = 0;
        $isLoggedIn = $request->isLoggedIn == "true" ? true : false;

        // Get the user's shopping cart
        $basket = ShoppingCartService::getBasket(TRUE, $isLoggedIn, $request->user_id, $request->order_id);

        $order_item = OrderItem::where('order_id', $basket->id)
                ->where('product_id', $request->product_id)
                ->where('size', $request->size)
                ->where('color', $request->color)
                ->get()->first();
        // dd($order_item, $request->product_id, $request->all());

        if ($order_item) {
            $order_item->quantity += $request->quantity;
            $order_item->save();
        } else {
            $order_item = OrderItem::create([
                'order_id' => $basket->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1,
                'price' => $request->price,
                'size' => $request->size,
                'color' => $request->color,
                'image' => $request->image
            ]);
        }

        ProductDetails::where('product_id', $request->product_id)
                ->where('size', $request->size)
                ->where('color', $request->color)
                ->where('price', $request->price)
                ->update(['units' => $request->product_units - $request->quantity]);

        if ($basket && $basket->orderItems) {
            $basket->load('orderItems');

            foreach ($basket->orderItems as $item) {
                $item->name = $item->product->name;
                $item->details = $item->product->details->where('color', $item->color)->where('size', $item->size)->first();
                $total_price += $item->price * $item->quantity;
            }

            $basket->total_price = $total_price;
            $basket->save();
        }

        return response()->json([
            'basket'   => $basket,
            'order_id'   => $basket->id,
            'total_price'   => $basket->total_price,
            'order_items' => $basket->orderItems,
            'success' => $basket ? 'Item successfully added to shopping cart.' : 'Error creating/updating order!'
        ]);
    }

    public function updateCart(Request $request)
    {
        $total_price = 0;

        if ($request->item_id && $request->product_id && $request->order_id && $request->qty && $request->price) {
            // update item qty
            OrderItem::where('id', $request->item_id)->update(['quantity' => $request->qty]);

            // update product units
            $productDetails = ProductDetails::where('product_id', $request->product_id)
                ->where('size', $request->size)
                ->where('color', $request->color)
                ->where('price', $request->price)
                ->get()->first();
            
            if ($productDetails) {
                $productDetails->units = $productDetails->stock - $request->qty;
                $productDetails->save();
            }

            // update order total price
            $order = Order::find($request->order_id);

            if ($order && $order->orderItems) {
                $order->load('orderItems');

                foreach($order->orderItems->all() as $item) {
                    $item->name = $item->product->name;
                    $item->details = $item->product->details->where('color', $item->color)->where('size', $item->size)->first();
                    $total_price += $item->quantity * $item->price;
                }

                $order->total_price = $total_price;
                $order->save();
            }

            return response()->json([
                'total_price'   => $order->total_price,
                'order_items' => $order->orderItems,
                'order' => $order,
                'success' => $order ? 'Order successfully updated.' : 'Error updating order!'
            ]);
        }
        
        return response()->json([
            'error' => 'Error updating order!'
        ]);
    }

    public function removeItem(Request $request)
    {
        if ($request->item_id) {
            $item = OrderItem::find($request->item_id);
            $item->delete();

            // update product units
            $productDetails = ProductDetails::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->where('color', $item->color)
                ->where('price', $item->price)
                ->get()->first();
            
            if ($productDetails) {
                $productDetails->units += $item->quantity;
                $productDetails->save();
            }

            // update order total price
            $order = Order::find($item->order_id);

            if ($order && $order->orderItems) {
                $order->load('orderItems');
                $order->total_price -= $item->quantity * $item->price;
                $order->save();

                foreach($order->orderItems->all() as $item) {
                    $item->name = $item->product->name;
                    $item->details = $item->product->details->where('color', $item->color)->where('size', $item->size)->first();
                }
            }
        
            return response()->json([
                'order'   => $order,
                'total_price'   => $order->total_price,
                'order_items' => $order->orderItems,
                'success' => $order ? 'Item successfully removed.' : 'Error removing item!'
            ]);
        }

        return response()->json([
            'error' => 'Error removing item!'
        ]);
    }

    public function mergeBasket(Request $request) // when coming from logout
    {
        // dump($request->user_id, $request->order_id, $request->loggedIn);
        $order = ShoppingCartService::mergeCart($request->user_id, $request->order_id, $request->loggedIn); // 12, 17, true

        if ($order) {
            return response()->json([
                'success' => 'Basket successfully merged.',
                'order_id' => $request->order_id
            ]);
        }

        return response()->json([
            'error' => 'Error merging basket!',
            // 'order_id' => $request->order_id
        ]);
    }   
}
