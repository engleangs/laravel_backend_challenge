<?php 

function error_message_for_shopping_cart(){
    return [
        'cart_id.required'      => 'CartId is required.|CAT_02|cart_id',
        'tax_id.required'      => 'TaxId is required.|CAT_02|tax_id',
        'product_id.required'   =>'ProductId is required.|CAT_02|product_id',
        'attributes.required'   => 'Attributes is required.|CAT_02|attributes',
        'quantity.required'     => 'Quantity is required.|CAT_02|quantity',
        'quantity.numeric'      => 'Invalid quantity.|CAT_02|quantity',
        'email.unique'          => 'Email already exists.|USR_04|email',
        'email.email'           => 'Email already exists.|USR_03|email',
        'password.required'     => 'Password is required.|USR_02|email',
        'credit_card.digits_between'=>'invalid credit card .|USR_08|credit_card',
        'shipping_region_id.numeric'=>'Shipping regionID is not number.|USR_09|shipping_region_id',
        'day_phone.digits'      =>"Invalid phone number.|USER_06|day_phone",
        'eve_phone.digits'      =>"Invalid phone number.|USER_06|eve_phone",
        'mob_phone.digits'      =>"Invalid phone number.|USER_06|mob_phone",
    ];
}

function error_message_for_customer() {
    return [
        'name.required'         => 'Name is required.|USR_02|name',
        'name.alpha_spaces'     =>'Name is invalid.|USER_09|name',
        'name.max'              => 'Name is too long.|USR_07|name',
        'email.required'        => 'Email is required.|USR_02|email',
        'email.unique'          => 'Email already exists.|USR_04|email',
        'email.email'           => 'Email is invalid.|USR_03|email',
        'password.required'     => 'Password is required.|USR_02|email',
        'credit_card.digits_between'=>'Invalid credit card .|USR_08|credit_card',
        'shipping_region_id.numeric'=>'Shipping regionID is not number.|USR_09|shipping_region_id',
        'day_phone.digits'      =>"Invalid phone number.|USER_06|day_phone",
        'eve_phone.digits'      =>"Invalid phone number.|USER_06|eve_phone",
        'mob_phone.digits'      =>"Invalid phone number.|USER_06|mob_phone",
    ];
}

function error_message_for_review() {
    return [
        'customer_id.required'         => 'Customer ID is required.|REV_02|customer_id',
        'review.required'              =>'Review is required|REV_02|review',
        'rating.required'              => 'Rating is required.|REV_02|rating',
        'rating.numeric'               => 'Rating is invalid.|REV_01|rating',
        'product_id.required'          => 'Product Id is required.|REV_02|product_id',
        'customer_id.numeric'          => 'Customer ID is invalid.|REV_01|customer_id',
    ];
}
