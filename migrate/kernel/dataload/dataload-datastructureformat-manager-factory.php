<?php
namespace PoP\Engine;

class DataStructureFormatManagerFactory
{
    protected static $instance;

    public static function setInstance(DataStructureFormatManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): DataStructureFormatManager
    {
        return self::$instance;
    }
}
