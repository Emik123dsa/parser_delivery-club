<?php

namespace API\Illuminate\ImgStealer;

use API\Config\Config;
use API\Illuminate\Connection\ApiImgSaver\ApiImgSaver;
use API\Illuminate\Database\Database;
use API\Illuminate\Database\QueryBuilder;
use ErrorException;

class ApiImg
{
    /**
     * PDO connection
     *
     * @var [type]
     */
    protected $pdo = null;
    /**
     * Connection for database
     */
    public function img_logo()
    {
        $this->pdo = Database::getInstance();

        $config = Config::group("api");

        $logos = $this->get_pdo_queries();

        $saver = [];

        foreach ($logos as $logo => $value) {
            $saver[$logo] = new ApiImgSaver();

            $saver[$logo]->get_img_value($config["essential_imgs"] . $value["logo"], $_SERVER["DOCUMENT_ROOT"] . DS . "upload" . DS . $value["logo"]);

            unset($saver[$logo]);
        }

    }

    protected function get_pdo_queries()
    {
        $query = new QueryBuilder();

        $inquiry = $query->select()
            ->from("mt_merchant")
            ->sql();

        try {
            return $this->pdo->query($inquiry, $query->values);
        } catch (ErrorException $e) {
            echo $e->getMessage();
            die();
        }
    }
}
