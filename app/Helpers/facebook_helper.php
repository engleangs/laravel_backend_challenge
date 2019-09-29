<?php
if( !function_exists('fetch_user_from_fb')) {
    /**
     * 
     * Fetch user from Facebook
     *  @param $access_token string Facebook access token
     */
    function fetch_user_from_fb( $access_token ){
        $fb = new \Facebook\Facebook([
            'app_id' => env("FB_APP_ID"),
            'app_secret' => env('FB_APP_SECRET'),
            'default_graph_version' => 'v2.10',
            //'default_access_token' => '{access-token}', // optional
          ]);
          try {
            $response = $fb->get('/me?fields=id,name,email', $access_token );
          } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            return ["success"=>false , "message"=> $e->getMessage(), "data"=>null];
          } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return ["success"=>false , "message"=> $e->getMessage(), "data"=>null];
          }
        return [
            "success"=>true,
            "data"=>$response->getGraphNode(),
            "message"=>""
        ];
    }
}