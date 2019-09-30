<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use App\Http\Requests\CustomerLoginRequest;

/**
 * Customer controller handles all requests that has to do with customer
 * Some methods needs to be implemented from scratch while others may contain one or two bugs.
 *
 *  NB: Check the BACKEND CHALLENGE TEMPLATE DOCUMENTATION in the readme of this repository to see our recommended
 *  endpoints, request body/param, and response object for each of these method
 *
 * Class CustomerController
 * @package App\Http\Controllers
 */
class CustomerController extends Controller
{


    /**
     * Allow customers to create a new account.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $data = $this->getRequestData( $request );
        $validator = Validator::make($data, [
            'name' => 'required|alpha_spaces|max:200',
            'email' => 'required|unique:customer,email|email',
            'password' => 'required'
            ],
            error_message_for_customer()
        );
        
        if($validator->fails()) {
            $errors = format_input_error( $validator->errors()->first() );
            return  response()->json(['error'=>$errors],400);       
        }
        $customer = Customer::create( $data );
        $customer_with_token = fetch_customer_to_token( $customer->customer_id );
        return response()->json( $customer_with_token , 201);
    }

    /**
     * Allow customers to login to their account.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = $this->getRequestData( $request );
        $validator = Validator::make($data, [
                'email' => 'required|email',
                'password' => 'required'
            ], 
            error_message_for_customer()
        );
        if($validator->fails()) {
            $errors = format_input_error( $validator->errors()->first() );
            return  response()->json( [ 'error'=>$errors ] , 400);       
        }
        $login_result = verify_customer( $data["email"] , $data["password"]);
        if( $login_result["success"] ) {
            $customer_with_token = customer_to_token( $login_result["customer"]);
            return response()->json( $customer_with_token );    
        } 
        return response()->json( 
                [
                    'error'=> construct_error( 403, 'USR_01', 'Invalid Email or Password','email|password')
                ],
            403);    
    }

    /**
     * Allow customers to view their profile info.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerProfile(Request $request)
    {
        $customer_id = $request->user()->getKey();//use cache like redis to enahance performance
        $customer = Customer::where('customer_id' , $customer_id)->get()->first();
        return response()->json( $customer);
    }

    /**
     * Allow customers to update their profile info like name, email, password, day_phone, eve_phone and mob_phone.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerProfile()
    {
        return response()->json(['message' => 'this works']);
    }

    /**
     * Allow customers to update their address info/
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerAddress(Request $request)
    {
        $data  = $this->getRequestData($request , ["address_1","address_2","city","region" ,"postal_code","shipping_region_id"]);
        $validator = Validator::make($data, [
            'shipping_region_id' => 'numeric',
            ],
            error_message_for_customer()
        );
        $current_customer = $request->user();
        $customer = Customer::find( intval($current_customer->getKey()));
        if( is_null( $customer) ) {
            return response()->json(['error'=>construct_error( 404,'CUS_01','Customer does not exist','customer_id')], 404);
        }
        $customer = bind_customer_isset( $customer , $data);
        $customer->save();
        return response()->json( $customer );
    }

    /**
     * Allow customers to update their credit card number.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCreditCard( Request $request )
    {
        $data =  $this->getRequestData( $request , ['credit_card']);
        $validator = Validator::make($data, [
            'credit_card' => 'required|digits_between:15,25'
            ],
            error_message_for_customer()
         );
        if($validator->fails()) {
            $errors = format_input_error( $validator->errors()->first() );
            return  response()->json(['error'=>$errors],400);          
        }
        $login_customer   = $request->user();
        $customer = Customer::find( intval(  $login_customer->getKey()) );
        if( ! is_null($customer ) ) {
            $customer->credit_card = $data['credit_card'];
            $customer->save();
            return response()->json( $customer );
        }
        else {
            return  response()->json( ['error'=>construct_error( 404,'CUS_01','Customer does not exist','customer_id')],404);       
        }
    }

    /**
     * Apply something to customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(Request $request)
    {
        $data = $this->getRequestData( $request ,['email','name','day_phone','eve_phone','mob_phone']);
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'name' => 'required|alpha_spaces',
            'day_phone'=>'digits:10',
            'eve_phone'=>'digits:10',
            'mob_phone'=>'digits:10'
        ],
        error_message_for_customer()
    );
        if($validator->fails()) {
            return  response()->json(['message'=> $validator->errors() ],400);       
        }
        $login_customer   = $request->user();
        $customer = Customer::find( intval(  $login_customer->getKey()) );
        if( ! is_null($customer ) ) {
            $customer = bind_customer_isset( $customer, $data );
            $customer->save();
            return response()->json( $customer );
        }
        else {
            return  response()->json(['error'=>construct_error( 404,'CUS_01','Customer does not exist','attribute_id') ],404);       
        }

    }
    
    /**
     * Login with facebook
     * todo: addbody of this function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookLogin(Request $request){
        $all = $request->all();
        $data = $this->getRequestData( $request );
        $validator = Validator::make($data, [
            'access_token' => 'required'
            ] , 
            error_message_for_customer()
     );
        if($validator->fails()) {
            return  response()->json(['message'=> $validator->errors() ],400);       
        }
        $fb_result = fetch_user_from_fb( $data["access_token"] );
        if( $fb_result["success"]) {
            $customer_info = $fb_result["data"];
            $customer   = Customer::where('email', $customer_info->getProperty('email') )
                        ->get()
                        ->first();
            if( is_null( $customer ) ) {
                $customer = Customer::create( [
                                    'email'=>$customer_info->getProperty('email') ,
                                    'name'=>$customer_info->getProperty('name'),
                                    'password'=>Str::random()
                            ] );
                return response()->json( fetch_customer_to_token( $customer->customer_id ) );
            }
            else {
                return response()->json( customer_to_token( $customer ) );
            }
        }
        else {
            return response()->json( ['error'=>construct_error( 403,'AUT_02','Access Unauthorized.','') ], 403 );
        }

        
    
    }

}
