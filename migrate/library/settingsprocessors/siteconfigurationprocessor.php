<?php
namespace PoP\Engine\Settings\Impl;

class PageModuleSiteConfigurationProcessor extends \PoP\Engine\Settings\SiteConfigurationProcessorBase
{
    public function getEntryModule(): ?array
    {
        $pop_module_routemoduleprocessor_manager = \PoP\ModuleRouting\RouteModuleProcessorManagerFactory::getInstance();
        return $pop_module_routemoduleprocessor_manager->getRouteModuleByMostAllmatchingVarsProperties(POP_PAGEMODULEGROUP_ENTRYMODULE);
    }
}

/**
 * Initialization
 */
new PageModuleSiteConfigurationProcessor();
