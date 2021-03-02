<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');

Route::get('/products', 'ProductController@index')->name('home');
Route::post('/upload-file', 'ProductController@uploadFile');
Route::get('/products/{product}/{details}', 'ProductController@show')->name('product.show');
Route::post('/product/updateDetails', 'ProductController@updateProductDetails')->name('product.updateDetails');
Route::get('/product/search', 'ProductController@search');
Route::get('/byCategory/{category_id}', 'ProductController@productsByCategory');

Route::post('/currentShoppingCart', 'ShoppingCart\BasketController@currentShoppingCart');
Route::post('/basket/addItem', 'ShoppingCart\BasketController@addItem')->name('basket.item.add');
Route::post('/basket/removeItem', 'ShoppingCart\BasketController@removeItem')->name('basket.item.remove');
Route::post('/basket/update', 'ShoppingCart\BasketController@updateCart')->name('basket.update');
Route::post('/basket/merge', 'ShoppingCart\BasketController@mergeBasket');
Route::post('/order/updateShippingAddress', 'OrderController@updateShippingAddress');

// Bancontact payment redirection
Route::get('bancontact/land/{user_id}', 'Payment\BancontactPaymentController@land')->name('bancontact.land');

Route::group(['middleware' => 'auth:api'], function() {
    // User dashboard
    Route::get('/users/{user}/orders','UserController@showOrders'); // show user's orders
    Route::get('/users/{user}/payments','UserController@showPayments'); // show user's payments

    // Payment
    Route::post('pay', 'Payment\PaymentController@pay')->name('pay.dispatch'); // paymentDispatch: bancontact or tranfer
    Route::post('pay2', 'Payment\PaymentController@pay2')->name('pay2'); // payment2 (credit card)
    Route::post('pay/cancel', 'Payment\PaymentController@cancel')->name('pay.cancel');

    // Admin dashboard
    Route::get('/users','UserController@index'); // list of users
    Route::get('/users/{user}','UserController@show'); // show user's details OR update setDefaults in store
    Route::post('users/update','UserController@update'); // update user

    // Orders Admin
    Route::get('/orders','OrderController@index'); // list of orders
    Route::get('/orders/{order}','OrderController@show'); // show order's details

    // Categories Admin
    Route::get('categories', 'CategoryController@index')->name('admin.categories.index');
    Route::get('categories/{category}', 'CategoryController@edit')->name('admin.categories.edit');
    Route::post('categories/storeOrUpdate', 'CategoryController@storeOrUpdate')->name('admin.categories.update');
    Route::post('categories/delete', 'CategoryController@destroy')->name('admin.categories.delete');

    // Products Admin
    Route::get('products/{product}', 'ProductController@edit')->name('admin.products.edit');
    Route::post('products/storeOrUpdate', 'ProductController@storeOrUpdate')->name('admin.products.update');
    Route::post('products/delete', 'ProductController@destroy')->name('admin.products.delete');

    // Payments Admin
    /*Route::get('transfers', 'Modules\ShoppingCart\PaymentController@adminTransfersPage')->name('admin.transfers.list');
    Route::post('transfer/done', 'Modules\ShoppingCart\PaymentController@validateTransfer')->name('admin.transfer.done'); // validate transfer
    Route::post('payment/cancel', 'Modules\ShoppingCart\PaymentController@adminCancel')->name('admin.payment.cancel'); // refuse transfer */
    /*Route::get('payments/all', 'Modules\Admin\Payment\PaymentController@index')->name('admin.payments.index');
    Route::get('payment/{payment}', 'Modules\Admin\Payment\PaymentController@show')->name('admin.payment.show');*/
});
