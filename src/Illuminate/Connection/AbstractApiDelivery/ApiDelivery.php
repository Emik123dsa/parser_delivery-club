<?php
namespace API\Illuminate\Connection\AbstractApiDelivery;

use API\Config\Config;
use API\Illuminate\Connection\ApiAuth\ApiAuth;
use API\Illuminate\Database\Database;
use InvalidArgumentException;

abstract class ApiDelivery
{
    const mask = [
        "v" => "vendors",
        "m" => "menu",
        "c" => "count",
        "t" => "total",
        "i" => "items",
        "o" => "offset",
        "h" => "hasMore",
        "host" => CURLOPT_SSL_VERIFYHOST,
        "peer" => CURLOPT_SSL_VERIFYPEER,
    ];
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $ch = [];
    /**
     * API
     *
     * @var [type]
     */
    protected $api;
    /**
     * Coordinates
     *
     * @var [type]
     */
    protected $coordinates = [];
    /**
     * Database invoker
     *
     * @var [type]
     */
    protected $pdo = null;
    /**
     * Token auth
     *
     * @var [type]
     */
    protected $token = null;
    /**
     * ApiAuth auth
     *
     * @param ApiAuth $auth
     */
    public function __construct(ApiAuth $auth)
    {
        $config = Config::item("api", "login");

        $this->token = $auth->get_token($config);

        $this->pdo = Database::getInstance();

        $this->api = Config::item("api", "api");

        $this->coordinates = Config::group("main");

        try {
            if (filesize(path("entrance", "Middleware/token", false)) === 0) {
                $this->fp_text($this->token);
            }
            $this->implement_init($this->api);

        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
            die();
        }
    }
    protected function fp_text($token)
    {
        $fp = fopen(path("entrance", "Middleware/token", false), "wb");
        fwrite($fp, $token);
        fclose($fp);
        return;
    }

    abstract protected function implement_init($vendors);

    protected function execute_init($ch)
    {
        $response = curl_exec($ch);

        if (!$response) {
            $this->fp_text($this->token);
            trigger_error($response);
            return;
        }

        $status = json_decode($response, true);

        if ($status["status"] === 401) {
            $this->fp_text($this->token);
        } else if ($status["status"] === 1108) {
            $this->fp_text($this->token);
        }

        return $response;
    }

    protected function set_init($ch, $api, $options = [])
    {
        if (!is_array($options)) {
            throw new \ErrorException("Options is not array format");
        }
        try {
            $headers = Config::group("Middleware/auth");

            $default = [
                CURLOPT_URL => $api . '?' . http_build_query($options),
                CURLOPT_HEADER => 0,
                CURLOPT_COOKIEFILE => $_SERVER['DOCUMENT_ROOT'] . DS . "thief" . DS . "cookies.txt",
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_DNS_USE_GLOBAL_CACHE => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
            ];
            curl_setopt_array($ch, $default);
            curl_setopt($ch, static::mask["host"], 0);
            curl_setopt($ch, static::mask["peer"], 0);
        } catch (\InvalidArgumentException $e) {
            echo $e->getMessage();
            die();
        }
    }

    protected function close_init($curl)
    {
        curl_close($curl);
    }
}
