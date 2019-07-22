<?php
namespace PoP\Engine;
use PoP\Hooks\Facades\HooksAPIFacade;

class EngineFactory
{
    protected static $instance;

    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public static function output()
    {
        self::$instance->output();
    }
}

// // For HTML Output: call output function on the footer (it won't get called for JSON output)
// HooksAPIFacade::getInstance()->addAction(
//     'popcms:footer', 
//     array(\PoP\Engine\EngineFactory::class, 'output')
// );
