<?php

class PoP_Engine_Module_EntryRouteModuleProcessor extends \PoP\ModuleRouting\AbstractEntryRouteModuleProcessor
{
    public function getModulesVarsProperties()
    {
        $ret = array();

        $ret[] = [
            'module' => [PoP_Engine_Module_Processor_Elements::class, PoP_Engine_Module_Processor_Elements::MODULE_EMPTY],
        ];

        return $ret;
    }
}

/**
 * Initialization
 */
new PoP_Engine_Module_EntryRouteModuleProcessor();
