<?php
namespace API\Illuminate\Connection;

use API\Config\Config;
use API\Illuminate\Connection\AbstractApiDelivery\ApiDelivery;
use API\Illuminate\Database\QueryBuilder;

class ApiItems extends ApiDelivery
{
    const category = [
        "p" => "products",
        "m" => "menu",
    ];
    /**
     * Static function
     *
     * @var integer
     */
    protected static $initial = 5758;
    protected static $initial_menu = 545;
    /**
     * Execution of the pdo query
     *
     * @param [type] $item
     * @param [type] $value
     * @param array $query
     * @return void
     */
    protected function execute_pdo($item, $value, $middleware, $queryPDO = [])
    {

        $queryPDO[$item] = new QueryBuilder();

        $this->pdo->execute(
            $queryPDO[$item]
                ->insert("mt_item")
                ->set([
                    "merchant_id" => $item + 77,
                    "status" => "publish",
                    "item_id" => ++static::$initial,
                    "item_name" => !empty($value["name"]) ? $value["name"] : " ",
                    "photo" => !empty($value["images"]) ? clearImg($value["images"]) : " ",
                    "category" => json_encode([(string) $middleware]),
                    "price" => !empty($value["price"]["value"]) ? json_encode([(string) $value["price"]["value"]]) : " ",
                    "item_description" => !empty($value["description"]) ? $value["description"] : " ",
                    "discount" => !empty($value["discountPrice"]["value"]) ? json_encode([(string) $value["discountPrice"]["value"]]) : " ",
                    "item_massa" => MassaVolume($value),
                    "item_kolvo" => !empty($value["properties"]["calories"]) ? $value["properties"]["calories"] : " ",
                ])
                ->sql(),
            $queryPDO[$item]->values
        );

        unset($queryPDO[$item]);
    }
    protected function execute_pdo_menu($item, $value, $queryPDO = [])
    {
        $queryPDO[$item] = new QueryBuilder();

        $this->pdo->execute(
            $queryPDO[$item]
                ->insert("mt_category")
                ->set([
                    "merchant_id" => $item + 77,
                    "status" => "publish",
                    "cat_id" => ++static::$initial_menu,
                    "category_name" => isset($value["name"]) ? $value["name"] : "",
                    "category_description" => isset($value["name"]) ? $value["name"] : "",
                ])
                ->sql(),
            $queryPDO[$item]->values
        );

        unset($queryPDO[$item]);
    }
    /**
     * Implementation of the abstract protected function
     *
     * @param [type] $vendors
     * @return void
     */
    protected function implement_init($vendors)
    {
        $data_primaries = $this->get_vendor_primaries();
       
        $this->clean_preliminary_keys();

        $this->clean_category_keys();

        $todos = [];
        $res = [];
        $ans = [];
        $data = Config::group("data");
        $counter = 0;

        if (count($data_primaries) > 0 && is_array($data_primaries)) {
            foreach ($data_primaries as $primary => $value) {
                $todos[$primary] = sprintf($vendors, "vendor/" . $value . "/menu");

                if (is_array($this->ch) || isset($this->ch)) {
                    $this->ch[$primary] = curl_init();
                }

                $this->set_init(
                    $this->ch[$primary],
                    $todos[$primary],
                    $data
                );

                $res[$primary] = $this->execute_init($this->ch[$primary]);

                $ans[$primary] = json_decode($res[$primary], true);

                unset($ans[$primary]["promoActions"]);
                unset($ans[$primary]["promoActionsOld"]);
                unset($ans[$primary]["status"]);

                if (array_key_exists(static::category["p"], $ans[$primary]) && array_key_exists(static::category["m"], $ans[$primary])) {
                    foreach ($ans[$primary][static::category["p"]] as $feature => $exact) {
                        foreach ($ans[$primary][static::category["m"]] as $menu => $exact_menu) {
                            if (in_array((string) $exact["id"]["primary"], $exact_menu["productIds"])) {
                                $this->execute_pdo($primary, $exact, $menu + $counter + 545 + 1);
                            }
                        }
                    }
                }

                $counter += count($ans[$primary][static::category["m"]]);

                if (array_key_exists(static::category["m"], $ans[$primary])) {

                    foreach ($ans[$primary][static::category["m"]] as $category => $exact_menu_features) {
                        $this->execute_pdo_menu($primary, $exact_menu_features);
                    }

                }

                $this->close_init($this->ch[$primary]);

                unset($todos[$primary]);
                unset($ans[$primary]);
                unset($res[$primary]);
                unset($this->ch[$primary]);
            }
        }

    }

    protected function get_vendor_primaries()
    {
        $query = new QueryBuilder();

        $data_rebuild = [];

        $data = $this->pdo->query(
            $query->select("delivery_estimation")
                ->from("mt_merchant")
                ->sql(), $query->values);

        if (count($data) > 0 && is_array($data)) {
            foreach ($data as $key => $value) {
                $data_rebuild[$key] = $value["delivery_estimation"];
            }
            return array_unique($data_rebuild);
        }

        return [];
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    protected function clean_preliminary_keys()
    {
        $query = new QueryBuilder();

        return $this->pdo->execute($query
                ->delete()
                ->from("mt_item")
                ->sql(), $query->values);

    }
    /**
     * clean category keys
     *
     * @return void
     */
    protected function clean_category_keys()
    {
        $query = new QueryBuilder();

        return $this->pdo->execute($query
                ->delete()
                ->from("mt_category")
                ->sql(), $query->values);

    }
}
