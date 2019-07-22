<?php
namespace PoP\Engine;

class ConvertibleFieldValueResolverResolverManagerFactory
{
    protected static $instance;

    public static function setInstance(ConvertibleFieldValueResolverResolverManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): ConvertibleFieldValueResolverResolverManager
    {
        return self::$instance;
    }
}
