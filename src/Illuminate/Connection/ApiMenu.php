<?php
namespace API\Illuminate\Connection;

use API\Config\Config;
use API\Illuminate\Connection\AbstractApiDelivery\ApiDelivery;
use API\Illuminate\Database\QueryBuilder;

class ApiMenu extends ApiDelivery
{
    const category = [
        "m" => "menu",
    ];
    protected static $initial = 0;
    /**
     * Execution of the pdo query
     *
     * @param [type] $item
     * @param [type] $value
     * @param array $query
     * @return void
     */
    protected function execute_pdo($item, $value, $queryPDO = [])
    {

        $queryPDO[$item] = new QueryBuilder();

        $this->pdo->execute(
            $queryPDO[$item]
                ->insert("mt_category")
                ->set([
                    "merchant_id" => $item + 1,
                    "status" => "publish",
                    "cat_id" => ++static::$initial,
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

        $todos = [];
        $res = [];
        $ans = [];
        $data = Config::group("data");

        $query = new QueryBuilder();

        $this->pdo->execute($query
                ->delete()
                ->from("mt_category")
                ->sql(), $query->values);

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

                if (array_key_exists(static::category["m"], $ans[$primary])) {
                    foreach ($ans[$primary][static::category["m"]] as $category => $exact) {
                        $this->execute_pdo($primary, $exact);
                    }
                }

                $this->close_init($this->ch[$primary]);

                unset($todos[$primary]);
                unset($ans[$primary]["products"]);
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
}
