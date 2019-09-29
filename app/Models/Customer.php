<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\User;

class Customer extends Model
{
    public $timestamps = false;

    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password','address_1'];

    /**
     * The override save function to store encrypted password 
     * @return boolean as parent
     */
    public function save(array $options = [])
    {  
        if( $this->isDirty( 'password' )) {
            $this->password = Hash::make( $this->password); //todo add encryption to db
        }
        $result = parent::save($options); // returns boolean
       return $result;
 
    }

    public static function getUser($user_id) {
        $customer  = self::find( $user_id);
        $user  = new User([ "name"=> $customer->name, "email"=> $customer->email , "password"=>$customer->password]);
        $user->id = $customer->customer_id;
        return $user;
    }

    public static function getOrders(int $customer_id){
        $items = Order::where('orders.customer_id', $customer_id)
                ->leftJoin('customer','orders.customer_id','customer.customer_id')
                ->select('orders.order_id',
                        'orders.total_amount',
                        'orders.created_on',
                        'orders.shipped_on',
                        'customer.name'
                )
                ->get();
        return $items;
    }

    


}
