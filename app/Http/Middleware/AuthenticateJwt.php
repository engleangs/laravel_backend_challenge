<?php
namespace App\Http\Middleware;
use Closure;
if(!defined("AUTH_HEADER")) 
{
    define("AUTH_HEADER",'USER-KEY');
}
class AuthenticateJwt{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $this->getAuthHeader( $request );
        
        if( $token == "") {
            //abort(403, 'Access denied. Empty token.');
            return response()->json([ 'error'=> construct_error(404,'AUT_01','Authorization code is empty','') ], 404);
        }
        else {
            $jwt_extraction = jwt_extract( $token );
            if($jwt_extraction["is_valid"]) {
                //bind user
                $user = $jwt_extraction['claims'] ;
                $request->merge(['user' => $user]);
                //add this
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
                return $next($request);
            }
            else {
               // abort(403, 'Access denied. Invalid Token');  
               return response()->json([ 'error'=> construct_error(403,'AUT_02','Access Unauthorized','') ], 403); 
            }
            
        }
    }

    private function getAuthHeader($request){
        $auth_header = $request->header( AUTH_HEADER ).'';
        $auth_header = str_replace("Bearer ","" ,$auth_header);
        return $auth_header;
    }

}