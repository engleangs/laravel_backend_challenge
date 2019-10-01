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

Route::group(['prefix' => 'attributes'], function () {
    Route::get('/', 'AttributeController@getAllAttributes');
    Route::get('/{attribute_id}', 'AttributeController@getSingleAttribute')
            ->where('attribute_id', '[0-9]+');
    Route::get('/values/{attribute_id}', 'AttributeController@getAttributeValues')
            ->where('attribute_id', '[0-9]+');
    Route::get('/inProduct/{product_id}', 'AttributeController@getProductAttributes')
            ->where('product_id', '[0-9]+');
});
Route::post('/customers/login', 'CustomerController@login');
Route::post('/customers', 'CustomerController@create');
Route::post('/customers/facebook', 'CustomerController@facebookLogin');
Route::get('/customers', 'CustomerController@getCustomerProfile')->middleware("auth.jwt");
Route::get('/customer/token', 'CustomerController@getToken');
Route::put('/customer', 'CustomerController@apply')->middleware("auth.jwt");
Route::put('/customer/address', 'CustomerController@updateCustomerAddress')->middleware("auth.jwt");
Route::put('/customer/creditCard', 'CustomerController@updateCreditCard')->middleware("auth.jwt");



Route::group(['prefix' => 'products'], function () {
    Route::get('/', 'ProductController@getAllProducts');
    Route::get('/{product_id}', 'ProductController@getProduct')->where('product_id', '[0-9]+');
    Route::get('/{product_id}/reviews', 'ProductController@getProductReview')->where('product_id', '[0-9]+');
    Route::get('/search', 'ProductController@searchProduct');
    Route::get('/inCategory/{category_id}', 'ProductController@getProductsByCategory')->where('category_id', '[0-9]+');
    Route::get('/inDepartment/{department_id}', 'ProductController@getProductsInDepartment')
                    ->where('department_id', '[0-9]+');
    Route::post('/{product_id}/reviews', 'ProductController@postProductReview')
                    ->where('product_id', '[0-9]+')
                    ->middleware("auth.jwt");
});


Route::group(['prefix' => 'departments'], function () {
    Route::get('/', 'ProductController@getAllDepartments');
    Route::get('/{department_id}', 'ProductController@getDepartment')
            ->where('department_id', '[0-9]+');

});

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', 'ProductController@getAllCategories');
    Route::get('/{category_id}', 'ProductController@getSingleCategory')
        ->where('category_id', '[0-9]+');
    Route::get('/inDepartment/{category_id}', 'ProductController@getDepartmentCategories')
    ->where('category_id', '[0-9]+');
    Route::get('/inProduct/{product_id}', 'ProductController@getProductCategory')
        ->where('product_id', '[0-9]+');

});


Route::get('/shipping/regions', 'ShippingController@getShippingRegions');
Route::get('/shipping/regions/{shipping_region_id}', 'ShippingController@getShippingInRegion')
    ->where('shipping_region_id', '[0-9]+');



Route::group(['prefix' => 'shoppingcart'], function () {
    Route::get('/generateUniqueId', 'ShoppingCartController@generateUniqueCart');
    Route::post('/add', 'ShoppingCartController@addItemToCart')->middleware("auth.jwt");
    Route::get('/{cart_id}', 'ShoppingCartController@getCart');
    Route::put('/update/{item_id}', 'ShoppingCartController@updateCartItem')
            ->where('item_id','[0-9]+');
    Route::delete('/empty/{cart_id}', 'ShoppingCartController@emptyCart');
    Route::delete('/removeProduct/{item_id}', 'ShoppingCartController@removeItemFromCart')
        ->where('item_id','[0-9]+');
//    Route::delete('/clean', 'ShoppingCartController@clean');
});

Route::group(['prefix' => 'orders'], function () {

    Route::post('/', 'ShoppingCartController@createOrder')->middleware("auth.jwt");
    Route::get('/inCustomer', 'ShoppingCartController@getCustomerOrders')->middleware("auth.jwt");
    Route::get('/{order_id}', 'ShoppingCartController@getOrderSummary')
            ->where('order_id', '[0-9]+');
    Route::get('/shortDetail/{order_id}', 'ShoppingCartController@orderShortDetail')
            ->where('order_id', '[0-9]+');
});

Route::post('/stripe/charge', 'ShoppingCartController@processStripePayment');


Route::get('/tax', 'TaxController@getAllTax');
Route::get('/tax/{tax_id}', 'TaxController@getTaxById')
->where('tax_id', '[0-9]+');

Route::get('/email-test',function(){
        // $details  = [
        //         'email'=>'571b53c735-455857@inbox.mailtrap.io'
        // ];
        
        dispatch(new App\Jobs\SendEmailJob( 9 ));
        dd('Ok');
        // return view('order_email' )
        // ->with('data',$data);
});