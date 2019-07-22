<?php
namespace PoP\Engine;

class ModuleProcessorManagerFactory
{
    protected static $instance;

    public static function setInstance(ModuleProcessorManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): ModuleProcessorManager
    {
        return self::$instance;
    }
}
