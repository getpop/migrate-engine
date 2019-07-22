<?php
namespace PoP\Engine;

class RouteModuleProcessorManager extends \PoP\ModuleRouting\AbstractRouteModuleProcessorManager
{
    public function getVars()
    {
        return Engine_Vars::getVars();
    }
}

/**
 * Initialization
 */
new RouteModuleProcessorManager();
