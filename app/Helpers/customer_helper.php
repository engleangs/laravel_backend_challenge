<?php
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
if( !function_exists('verify_customer') ) {
    function verify_customer($email, $password){
        
        $customer = Customer::where('email', $email)->get()->first();
        if($customer== null) {
            return ["success"=>false, "customer"=>null ];
        }
        if( Hash::check($password , $customer->password ) ) {
            return ["success"=>true , "customer"=> $customer];
        }
        return  ["success"=>false , "customer"=> null];
    }
}
if( !function_exists('customer_to_token') ) {
    function customer_to_token($customer) {
        $customer   = guard_customer_field( $customer );
        $token_data = jwt_token( $customer );
        return [ 'customer'=>$customer ,
                'accessToken'=> "Bearer ".$token_data['token'],
                'expire_in'=>   $token_data['expire_in'].''
        ];
    }
}
if( !function_exists('fetch_customer_to_token') ) {
    function fetch_customer_to_token( $customer_id ) {
        $customer = Customer::where('customer_id', $customer_id)->get()->first();
        return customer_to_token( $customer );
    }
}
if( !function_exists('guard_customer_field') ) {
    function guard_customer_field(Customer $customer,$fields=[]) {
        if( count( $fields) == 0) {
            $fields = [ "password"];
        }
        foreach($fields as $field) {
            $customer->makeHidden( $field);
        }
        return $customer;

    }
} 
if( !function_exists('bind_customer_isset') ) {
    function bind_customer_isset( Customer $customer, $data) {
        foreach($data as $key => $val) {
            $customer->$key = $val;
        }
        return $customer;
    }
}