<?php
require_once __DIR__ . DS . "Functions.php";
require_once path("vendor", "autoload");

use API\Illuminate\Connection\ApiMerchant;
use API\Illuminate\Connection\ApiMenu;
use API\Illuminate\Connection\ApiVendor;
use API\Illuminate\Connection\ApiItems;
use API\Illuminate\Connection\ApiAuth\ApiAuth;

$auth = new ApiAuth();

$implemnet = new ApiItems($auth);//new ApiMerchant();
