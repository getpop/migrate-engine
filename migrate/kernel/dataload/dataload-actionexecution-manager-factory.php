<?php
namespace PoP\Engine;

class ActionExecutionManagerFactory
{
    protected static $instance;

    public static function setInstance(ActionExecutionManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): ActionExecutionManager
    {
        return self::$instance;
    }
}
