<?php

namespace API\Illuminate\Connection\ApiImgSaver;

use API\Illuminate\Connection\ApiImgSaver\ApiImgInterface\ApiImgInterface;

class ApiImgSaver implements ApiImgInterface
{
    public function get_img_value($value, $linker)
    {
        $ch = curl_init($value);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

        $content = curl_exec($ch);

        curl_close($ch);

        

        $fp = fopen($linker, 'x');
        fwrite($fp, $content);
        fclose($fp);
    }
}
