<?php
require_once __DIR__ . DS . "Functions.php";
require_once path("vendor", "autoload");

use API\Illuminate\Connection\ApiMerchant;
use API\Illuminate\Connection\ApiMenu;
use API\Illuminate\Connection\ApiVendor;
use API\Illuminate\Connection\ApiItems;
use API\Illuminate\Connection\ApiAuth\ApiAuth;
use API\Illuminate\ImgStealer\ApiImg;

$auth = new ApiAuth();
//$img = new ApiImg();
//$implement_items = new ApiItems($auth);
//$implement_menu = new ApiMenu($auth);
//new ApiMerchant();

$implement_img = new ApiImg();

$implement_img->img_logo();