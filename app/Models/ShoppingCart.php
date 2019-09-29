<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class ShoppingCart
 * @package App\Models
 * @property int $item_id
 * @property int $cart_id
 * @property int $product_id
 * @property string $attributes
 * @property int $quantity
 * @property boolean $buy_now
 * @property string $added_on
 * @property int $customer_id
 * @property \App\User $user
 */
class ShoppingCart extends Model
{
    public $timestamps = false;

    protected $table = 'shopping_cart';
    protected $primaryKey = 'item_id';
    protected $guarded = ['item_id'];

    public static function getOrCreateCartId(User $user): string
    {
        $record = ShoppingCart::where('customer_id', $user->getKey())->select('cart_id')->first();
        if($record) return $record->cart_id;

        return uniqid($user->getKey().'_');
    }
  
    public function save($option = []) {
        $this->added_on = NOW();
        return parent::save();
    }
    public static function addNewItem( $data, User $user) :ShoppingCart {
        //$data["customer_id"] = $user->getKey(); //todo check if shopping cart should add customer_id
        return self::create($data);
    }

    public static function getProductList(string $cart_id)  {
            $data = ShoppingCart::where('cart_id',$cart_id)
                    ->leftJoin('product',  'shopping_cart.product_id','=','product.product_id')
                    ->select('shopping_cart.product_id',
                            'shopping_cart.item_id',
                            'shopping_cart.cart_id',
                            'product.name',
                            'shopping_cart.attributes',
                            'product.price',
                            'product.discounted_price',
                            'shopping_cart.quantity',
                            DB::raw(' (
                                        shopping_cart.quantity * product.price - product.discounted_price 
                                    ) as subtotal'
                                )
                            )
                    ->get();
            return $data;
    }

    public static function updateQuantity(int $item_id ,int $quantity):?ShoppingCart{
        $cart = ShoppingCart::find($item_id);
        if( !is_null( $cart)) {
            $cart->quantity = $quantity;
            $cart->save();    
        }
        return $cart;
    }
    public static function emptyCart(string $cart_id):int {
        $result = DB::table('shopping_cart')->where('cart_id',$cart_id)->delete();
        return $result;
    }
    public static function removeItem( int $item_id):int {
        $result = DB::table('shopping_cart')->where('item_id',$cart_id)->delete();
        return $result;
    }
}
