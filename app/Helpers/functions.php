<?php
if (!function_exists('app_name')) {
    function app_name()
    {
        return config('app.name');
    }
}
if( !function_exists( 'trim_product_description' ) ) {
    function trim_product_description($description, $length) {
        return substr($description, 0, $length );
    }
}
if( !function_exists('query_product_param') ) {
    function query_product_param( $request ) {
        $page               = intval( $request->input( 'page',1 ));
        $limit              = intval( $request->input( 'limit',20 ));
        $description_length = intval( $request->input( 'description_length' , 200 ) );
        return [ $page, $limit, $description_length];
    } 
}

