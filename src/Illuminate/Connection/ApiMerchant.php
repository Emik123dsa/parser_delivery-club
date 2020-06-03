<?php

namespace API\Illuminate\Connection;

use API\Illuminate\Connection\AbstractApiDelivery\ApiDelivery;
use API\Illuminate\Database\QueryBuilder;

class ApiMerchant extends ApiDelivery
{
    /**
     * Execute pdo query
     *
     * @return void
     */
    protected function execute_pdo($item, $value, $query = [])
    {
        $query[$item] = new QueryBuilder();

        if (array_key_exists("name", $value)) {
            $this->pdo->execute(
                $query[$item]
                    ->insert("mt_merchant")
                    ->set(["contact_name" => "vendor-" . $item,
                        "state" => "Волгоградская обл. ",
                        "post_code" => "000000",
                        "merchant_id" => ++static::$merchantStarter,
                        "restaurant_name" => isset($value["name"]) ? $value["name"] : "",
                        "restaurant_slug" => Transliter($value["name"]) . "-" . $value["id"]["primary"],
                        "restaurant_phone" => "+70000000000",
                        "username" => isset($value["chain"]["alias"]) ? $value["chain"]["alias"] : "",
                        "password" => "vendor-password" . $item,
                        "contact_phone" => "vendor-phone" . $item,
                        "contact_email" => "vendor" . $item . "@vendor.vendor",
                        "city" => "Волгоград",
                        "free_delivery" => !empty($value["delivery"]["available"]) ? $value["delivery"]["available"] : "2",
                        "country_name" => "Russia",
                        "latitude" => $value["latitude"],
                        "lontitude" => $value["longitude"],
                        "logo" => CorrectorImg($value["logo"]),
                        "delivery_estimation" => $value["id"]["primary"],
                        "delivery_minimum_order" => !empty($value["delivery"]["minOrderPrice"]["value"]) ? $value["delivery"]["minOrderPrice"]["value"] : (float) "0.00000",
                    ])->sql(),
                $query[$item]->values
            );
        }
        unset($query[$item]);
    }
    /**
     * Protected counter
     *
     * @var integer
     */
    protected static $merchantStarter = 76;
    /**
     * Iterator
     *
     * @var integer
     */
    protected static $iterator = 0;
    /**
     * Abstract protected function for data fetching
     *
     * @param [type] $vendors
     * @return void
     */
    protected function implement_init($vendors)
    {
        $vendors = sprintf($vendors, "vendors");
        $res = [];
        $items = [];
        $items_menu = [];
        $menus = [];

        foreach ($this->coordinates as $coordinate => $values) {
            if (is_array($this->ch) || isset($this->ch)) {
                $this->ch[$coordinate] = curl_init();
            }

            $this->set_init($this->ch[$coordinate],
                $vendors, $values);

            $res[$coordinate] = $this->execute_init($this->ch[$coordinate]);

            $items[$coordinate] = json_decode($res[$coordinate], true);

            if (count($items[$coordinate]) > 0 && is_array($items[$coordinate])) {
                if (array_key_exists(static::mask["v"], $items[$coordinate])) {
                    $items_menu[$coordinate][static::mask["i"]] = $items[$coordinate][static::mask["v"]][static::mask["i"]];

                    foreach ($items_menu[$coordinate][static::mask["i"]] as $item => $value) {

                        $menus[static::$iterator++] = array_merge($value, $values);
                    }

                    unset($items_menu[$coordinate][static::mask["i"]]);
                }
            }

            $this->close_init($this->ch[$coordinate]);
            unset($items[$coordinate]);
            unset($res[$coordinate]);
            unset($this->ch[$coordinate]);
        }
        $this->implement_merchant($menus);
    }

    protected function implement_merchant($menus)
    {
        $query = new QueryBuilder();

        if (is_array($menus) && count($menus) > 0) {

            $todosTemplate = array_unique(array_column($menus, "alias"));

            $todos = array_intersect_key($menus, $todosTemplate);

            $this->pdo->execute(
                $query->delete()
                    ->from("mt_merchant")
                    ->where("merchant_id", 0, ">")
                    ->sql(), $query->values
            );

            foreach (array_values($todos) as $todo => $value) {
                $this->execute_pdo($todo, $value);
            }
        }
    }
}
