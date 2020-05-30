<?php
namespace API\Illuminate\Connection;

use API\Illuminate\Connection\AbstractApiDelivery\ApiDelivery;
use API\Illuminate\Database\QueryBuilder;

class ApiVendor extends ApiDelivery
{
    protected function execute_pdo($item, $value, $query = [])
    {
        $query[$item] = new QueryBuilder();

        $this->pdo->execute(
            $query[$item]
                ->update("mt_merchant")
                ->set([
                    "contact_name" => isset($value["legalData"]["name"]) ? NameCorrector($value["legalData"]["name"]) : "Vendor",
                    "street" => isset($value["address"]) ? CorrectorStreet($value["address"]) : "vendor-street",
                ])
                ->where("merchant_id", $item)
                ->sql(),
            $query[$item]->values
        );

        unset($query[$item]);
    }
    /**
     * Implement abstract functions
     *
     * @param [type] $vendors
     * @return void
     */
    protected function implement_init($vendors)
    {
        $data_primaries = $this->get_vendor_primaries();

        $todos = [];

        $menu_features = [];

        $vendors = sprintf($vendors, "vendor");

        foreach ($data_primaries as $primary => $value) {
            if (is_array($this->ch) || isset($this->ch)) {
                $this->ch[$primary] = curl_init();
            }

            $this->set_init(
                $this->ch[$primary],
                $vendors,
                $value
            );

            $res[$primary] = $this->execute_init($this->ch[$primary]);

            $todos[$primary] = json_decode($res[$primary], true);

            if (count($todos[$primary]) > 0 && is_array($todos[$primary])) {
                $menu_features[$primary] = json_decode($res[$primary], true);
            }

            $this->close_init($this->ch[$primary]);

            unset($todos[$primary]);
            unset($res[$primary]);
            unset($this->ch[$primary]);
        }

        $this->implement_merchant_keys($menu_features);
    }
    /**
     * Correct function for altering
     *
     * @param [type] $todos
     * @return void
     */
    protected function implement_merchant_keys($todos)
    {
        if (count($todos) > 0 && is_array($todos)) {
            foreach ($todos as $todo => $value) {
                $this->execute_pdo($todo, $value);
            }
        }
    }
    /**
     * get vendor primaryes keys
     *
     * @return void
     */
    protected function get_vendor_primaries()
    {
        $query = new QueryBuilder();

        $data_rebuild = [];

        $data = $this->pdo->query(
            $query->select()
                ->from("mt_merchant")
                ->sql(), $query->values);

        if (count($data) > 0 && is_array($data)) {
            foreach ($data as $key => $value) {
                $data_rebuild[$key]["latitude"] = $value["latitude"];
                $data_rebuild[$key]["longitude"] = $value["lontitude"];
                $data_rebuild[$key]["chainAlias"] = $value["username"];
            }
            return $data_rebuild;
        }

        return [];
    }
}
