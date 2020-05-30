<?php
namespace API\Eloquent;

trait Singleton
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $this->connect();
    }

    private function __wakeup()
    {}
    private function __clone()
    {}

    private function __destructor()
    {}
}
