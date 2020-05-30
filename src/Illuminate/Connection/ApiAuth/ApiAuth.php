<?php

namespace API\Illuminate\Connection\ApiAuth;

use API\Illuminate\Connection\ApiAuth\ApiAuthInterface\ApiAuthInterface;

class ApiAuth implements ApiAuthInterface
{
    /**
     * Simple auth
     *
     * @param [type] $ch
     * @param [type] $api
     * @param boolean $mode
     * @return void
     */
    public function get_token($api, $mode = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        $authToken = json_decode($server_output, true);

        return $authToken["token"] . "." . $authToken["secret"];

    }
}
