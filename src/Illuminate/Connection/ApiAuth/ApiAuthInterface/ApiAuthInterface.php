<?php 

namespace API\Illuminate\Connection\ApiAuth\ApiAuthInterface; 
/**
 * Auth
 */
interface ApiAuthInterface 
{
    public function get_token($api, $mode = true); 
}


?>