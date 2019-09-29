<?php
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
function get_error_field($message){
    $fields = explode("|", $message);
    $field_name = count($fields) >2 ? $fields[2] :'';
    $error_code = count($fields) >1 ? $fields[1] :'';
    $error_message = count($fields) >0 ?$fields[0] :'';
    return [ $error_message , $error_code , $field_name ];
}
function format_input_error($error_message){
    [ $error_message , $error_code , $field_name ]  = get_error_field( $error_message);
            $errors = construct_error( 400, $error_code , $error_message, $field_name);
    return $errors;
}
