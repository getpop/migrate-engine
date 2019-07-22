<?php
namespace PoP\Engine;

class AttachableExtensionManagerFactory
{
    protected static $instance;

    public static function setInstance(AttachableExtensionManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): AttachableExtensionManager
    {
        return self::$instance;
    }
}
