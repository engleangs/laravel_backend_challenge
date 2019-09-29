<?php
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use App\User;
// use this lib : https://github.com/lcobucci/jwt/blob/3.3/README.md
if( !function_exists('jwt_token') ) {
    
    function jwt_token($customer) {
        $time = time();
        $expire_in = $time + env('JWT_EXPIRE');
        $token = (new Builder())->issuedBy(env('JWT_ISSUER')) // Configures the issuer (iss claim)
                                ->permittedFor(env('JWT_ISSUER')) // Configures the audience (aud claim)
                                ->identifiedBy(env('JWT_IDENTIFY'), true) // Configures the id (jti claim), replicating as a header item
                                ->issuedAt($time) // Configures the time that the token was issue (iat claim)
                                ->expiresAt( $expire_in ) // Configures the expiration time of the token (exp claim)
                                ->withClaim('items',$customer)
                                ->getToken(); // Retrieves the generated token
        return [
            'token' => $token.'',
            'expire_in'=> $expire_in
        ];
    }
    
}

if( !function_exists('jwt_validation_data') ) {
    function jwt_validation_data(){
        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer(env('JWT_ISSUER'));
        $data->setAudience( env('JWT_ISSUER'));
        $data->setId( env('JWT_IDENTIFY'));
        $time = time();
        $data->setCurrentTime($time);
        return $data;
    }
}

if( !function_exists('jwt_extract') ) {
    function jwt_extract($token) {
        try {
            $token = (new Parser())->parse((string) $token); // Parses from a string
            $validation_data  = jwt_validation_data();
            $claims = $token->getClaims()["items"]->getValue() ;// Retrieves the token claims
            $customer_id = $claims->customer_id;
            $claims = new User( ['name'=> $claims->name , 'email'=> $claims->email] );
            $claims->id = $customer_id;
            return [
                "expired"=>$token->isExpired(),
                "is_valid"=>$token->validate($validation_data),
                "claims"=> $claims
            ];
        }catch(Exception $ex) {
            return [
                "expired"=>true,
                "is_valid"=>false,
                "claims"=> null
            ];
        }
    }
    
}