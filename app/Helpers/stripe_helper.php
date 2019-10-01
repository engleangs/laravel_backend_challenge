<?php 
use \Stripe\Stripe;
use \Stripe\ApiOperations\Create;
use \Stripe\Charge;
use \Stripe\Customer as StripeCustomer;
if(!function_exists( 'create_stripe_customer')) {
    /**
     * create stripe customer 
     * @param array $customerDetailsAry $customer array
     * expected to have some following keys :
     * customer["email"]
     * customer["source"]
     * @return StripeCustomer
     */
    function create_stripe_customer( $customerDetailsAry ){
        $customer = new StripeCustomer();
        $customerDetails = $customer->create($customerDetailsAry);
        return $customerDetails;
    }
}
if( !function_exists('stripe_payment')) {
    /**
     * stirpe payment 
     * @param array $data order array data
     * expected to have some following keys : 
     *  data["email"]
     *  data["stripeToken"]
     *  data["order_id"]
     *  data["amount"]
     *  data["description"]
     *  data["currency"]
     * @return stripe payment result
     * 
     */
    function stripe_payment( $data ) {
        $stripeApiKey = env('STRIPE_API_KEY');
        $stripeService = new \Stripe\Stripe();
        $stripeService->setVerifySslCerts(false);
        $stripeService->setApiKey( $stripeApiKey);
        $customerDetailsAry = array(
            'email' => $data['email'],
            'source' =>  $data["stripeToken"]
        );
        $customer_result = create_stripe_customer( $customerDetailsAry );
        $amount = 10;
        $stripObjeArr = [
            'amount'=> $data["amount"] *  100,
            'customer'=>$customer_result->id,
            'metadata' => [
                'order_id' =>intval($data['order_id'])
            ],
            'description'=> $data["description"],
            'currency'=> $data['currency']
        ];
    
        $stripe =  new Charge();
        $result = $stripe->create( $stripObjeArr );
        return $result;
    }
}
