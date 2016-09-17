<?php

namespace acolish\config;


class CommonConfig
{
    static private $instance;

    private $config;

    public function __construct()
    {
        $this->config = require_once __DIR__ . '/../../conf/common.php';
    }

    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key) {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

}