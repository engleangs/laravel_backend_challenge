<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Order extends Model
{
    public $timestamps = false;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    protected $fillable = ['total_amount',
                        'shipping_id',
                        'tax_id',
                        'comments',
                        'status',
                        'customer_id',
                        'created_on'];

    public function details(){
        return $this->hasMany('App\Models\OrderDetail','order_id','order_id');
    }
   

    public static function addOrderDetails(array $order_details , Order $order){
        foreach($order_details as $key=>$order_detail) {
            $order_details[ $key]['order_id']  =  $order->order_id;
        }
        $result = OrderDetail::insert( $order_details );
        return $result;
    }


    /***
     * 
     * Create order instance and order details 
     * @return order_id 
     */

    public static function createOrder(string $cart_id, int $shipping_id, int $tax_id , User $user):int {
        $cart = ShoppingCart::where('cart_id', $cart_id)->get();
        $total_amount = 0;  
        $product_ids = [];
        if( count($cart)==0) {
            return -1;//exit if no cart to add
        }
        foreach($cart as $item) {
            $product_ids[ $item->product_id] = $item->product_id;
        }
        $products = Product::getProductInIds( array_values( $product_ids ));
        $order_details = [];
        foreach($cart as $item) {
            if( isset( $products[ $item->product_id])) {
                $product = $products[ $item->product_id];
                $order_detail = [
                    'unit_cost'=> $product->price,
                    'attributes' => $item->getAttribute('attributes'),
                    'product_name' => $product->name,
                    'quantity'=> $item->quantity,
                    'product_id'=> $product->product_id,
                ];
                $total_amount = $total_amount + $item->quantity * $product->price;
                $order_details[] = $order_detail;
            }
        }

        $order_array  = [
            'total_amount'=>$total_amount ,
            'shipping_id'=>$shipping_id ,
            'tax_id'=>$tax_id,
            'comments'=>'pending',
            'status'=>0,
            'customer_id'=>$user->getKey(),
            'created_on'=>NOW(),
        ];
        
        $orderObject = self::create($order_array );
        $count      = self::addOrderDetails( $order_details , $orderObject);
        ShoppingCart::where('cart_id', $cart_id)->delete();
        return  $orderObject->order_id;
    }
    public static function shortDetail( int $order_id) {

        $order = Order::where('order_id', $order_id )
                    ->leftJoin('customer','orders.customer_id','customer.customer_id')
                    ->select('orders.order_id',
                            'orders.total_amount',
                            'created_on',
                            'shipped_on',
                            'status',
                            'customer.name'
                    )
                    ->get()->first();
        $order->status = $order->status.'';
        return $order;
    }

    public static function getOrderResult( int $order_id) {
        $order = Order::find( $order_id );
        $customer = Customer::find( $order->customer_id);
        $data = ['order' =>$order,
                 'order_details'=>$order->details,
                 'shipping'=>Shipping::getShippingWithCost( $order->shipping_id),
                 'customer'=>$customer,
                 'email'=>$customer->email,
        ];
        return $data;
        
    }
}
