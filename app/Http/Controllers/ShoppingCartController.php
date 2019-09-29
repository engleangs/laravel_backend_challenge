<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\DB;
/**
 * Check each method in the shopping cart controller and add code to implement
 * the functionality or fix any bug.
 *
 *  NB: Check the BACKEND CHALLENGE TEMPLATE DOCUMENTATION in the readme of this repository to see our recommended
 *  endpoints, request body/param, and response object for each of these method
 *
 * Class ShoppingCartController
 * @package App\Http\Controllers
 */
class ShoppingCartController extends Controller
{

   
    /**
     * To generate a unique cart id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateUniqueCart(Request $request)
    {
        // $user =  $request->user();
        //$random = ShoppingCart::getOrCreateCartId( $user);//todo modify cart table adding customer_id
        $random   = Str::random(30);
        return response()->json(['cart_id' => $random ]);
    }

    /**
     * To add new product to the cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItemToCart(Request $request)
    {
        $data = $this->getRequestData( $request  , ['cart_id','product_id','attributes','quantity']);
        $customer = $request->user();
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'product_id' => 'required',
            'attributes' => 'required',
            'quantity' => 'required',
        ]);
        //todo add validation on product id & attributes
        
        if ($validator->fails()) {
                return response()->json(['message'=> $validator->errors()]);
        }
        $result = ShoppingCart::addNewItem( $data, $customer );
        return response()->json( $result );
    }

    /**
     * Method to get list of items in a cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCart($cart_id)
    {
        $cartAndProduct = ShoppingCart::getProductList( $cart_id );
        return response()->json( $cartAndProduct);
    }

    /**
     * Update the quantity of a product in the shopping cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCartItem(Request $request, $item_id)
    {
        $item_id    = intval( $item_id);
        $data       = $this->getRequestData( $request );
        $cart       = ShoppingCart::updateQuantity( $item_id, $data["quantity"]);
        if( is_null( $cart )) {
            return response()->json(['message' => 'Cart not found'],404);    
        }
        return response()->json( $cart );
    }

    /**
     * Should be able to clear shopping cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function emptyCart($cart_id )
    {
        $total_empty = ShoppingCart::emptyCart( $cart_id );
        if( $total_empty == 0) {
            return response()->json(['message' => 'Cart not found'],404);       
        }
        return response()->json([]);
    }

    /**
     * Should delete a product from the shopping cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItemFromCart($item_id)
    {
        $item_id  = intval( $item_id );
        $total_remove = ShoppingCart::removeItem( $item_id );
        if( $total_remove ==0 ) {
            return response()->json(['message' => 'Cart not found'],404);     
        }
        return response()->json(['message' => 'successfully removed']);
    }

    /**
     * Create an order.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(Request $request)
    {
        $data = $this->getRequestData( $request , ['cart_id', 'shipping_id', 'tax_id']);
        $validator = Validator::make($data, [
            'cart_id' => 'required',
            'shipping_id' => 'required|numeric',
            'tax_id' => 'required|numeric'
            
        ]);
        $user =  $request->user();
        $result     = Order::createOrder( $data['cart_id'], intval( $data['shipping_id']), intval( $data['tax_id']), $user);
        return response()->json(['message' => 'successfully added']);
    }

    /**
     * Get all orders of a customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerOrders(Request $request )
    {
        $user  = $request->user();
        $orders = Customer::getOrders( $user->getKey());
        return response()->json( $orders );
    }

    /**
     * Get the details of an order.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderSummary($order_id)
    {
        $order_id  = intval( $order_id );
        $order     = Order::find( $order_id );
        if( !is_null( $order)) {
            $order_detail = $order->details;
            return response()->json(["order_id"=>$order->order_id , "order_items"=> $order_detail]);
        }
        else {
            return response()->json(["message"=>"Not found"] ,404);
        }
    }


    /**
     * Order short detail
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderShortDetail($order_id)
    {
        $order_id   = intval( $order_id);
        $order      = Order::shortDetail( $order_id );
        if( !is_null( $order)) {
            return response()->json( $order);
        }
        else {
            return response()->json(["message"=>"Not found"] ,404);
        }
        
    }

    /**
     * Process stripe payment.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function processStripePayment(Request $request)
    {
        $data       = $this->getRequestData( $request , ['stripeToken' , 'email', 'order_id']);
        $validator = Validator::make($data, [
            'order_id' => 'required|numeric',
            'email' => 'required|email',
            'stripeToken' => 'required'
            
        ]);
        if ($validator->fails()) {
            return response()->json(['message'=> $validator->errors()]);
        }
        $order = Order::find( intval( $data['order_id'] ));
        if( is_null( $order)) {
            return response()->json(["message"=>"Not found"] ,404);
        }
        else {
            $data['amount']         = $order->total_amount;
            $data['description']    = "process order of : ".$order->customer_id;
            $data["currency"]       = "usd";
            $result  = stripe_payment( $data );
            return response()->json(['message' => "Successfully processed payment", $result]);
        }

        
    }

    // public function clean(){
    //     $yesterday  = date("Y-m-j H:i:s", strtotime( '-1 days' ) );
    //         DB::table('shopping_cart')
    //         ->where('added_on','<', $yesterday)
    //         ->delete();
    // }

   
}
