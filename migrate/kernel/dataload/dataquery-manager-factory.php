<?php
namespace PoP\Engine;

class DataQueryManagerFactory
{
    protected static $instance;

    public static function setInstance(DataQueryManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): DataQueryManager
    {
        return self::$instance;
    }
}
