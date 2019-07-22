<?php
namespace PoP\Engine;

class QueryHandlerManagerFactory
{
    protected static $instance;

    public static function setInstance(QueryHandlerManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): QueryHandlerManager
    {
        return self::$instance;
    }
}
