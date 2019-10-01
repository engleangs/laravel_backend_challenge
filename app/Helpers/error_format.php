<?php
if( !function_exists('construct_error')) {
    /**
     * 
     * Construct error with error code and message to return to client
     * @param $status status code 
     * @param $code   error code 
     * @param $message error message
     * @param $field  filed that contain error
     * @return array 
     */
    function construct_error($status , $code , $message, $field) {
        $fields = explode("|",$field);
        if( count($fields)==1) {
            $fields = $field;
        }
        return [
            "status"=>$status,
            "code"=>$code,
            "message"=>$message,
            "field"=>$fields
        ];
    }
}
if( !function_exists('get_error_field')) {
    /**
     * get error field from message
     * @param message
     * @return array 
     */
    function get_error_field($message){
        $fields = explode("|", $message);
        $field_name = count($fields) >2 ? $fields[2] :'';
        $error_code = count($fields) >1 ? $fields[1] :'';
        $error_message = count($fields) >0 ?$fields[0] :'';
        return [ $error_message , $error_code , $field_name ];
    }
}

if( !function_exists('format_input_error')) {
    /**
     * format error message from text to error code , field and status code
     * 
     * @param $error_message
     * @return array
     * 
     */
    function format_input_error($error_message){
        [ $error_message , $error_code , $field_name ]  = get_error_field( $error_message);
                $errors = construct_error( 400, $error_code , $error_message, $field_name);
        return $errors;
    }
}

