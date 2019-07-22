<?php
namespace PoP\Engine;

class CheckpointProcessorManagerFactory
{
    protected static $instance;

    public static function setInstance(CheckpointProcessorManager $instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance(): CheckpointProcessorManager
    {
        return self::$instance;
    }
}
