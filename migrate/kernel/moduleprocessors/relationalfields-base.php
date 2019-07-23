<?php
namespace PoP\Engine;

abstract class PoP_Module_Processor_RelationalFieldDataloadsBase extends DataloadModuleProcessorBase
{
    protected function getInnerSubmodules(array $module)
    {
        $ret = parent::getInnerSubmodules($module);
        // The fields to retrieve are passed through module atts, so simply transfer all module atts down the line
        $ret[] = [PoP_Module_Processor_DataQuery_RelationalFields::class, PoP_Module_Processor_DataQuery_RelationalFields::MODULE_LAYOUT_DATAQUERY_RELATIONALFIELDS, $module[2]];
        return $ret;
    }

    public function getFormat(array $module)
    {
        return POP_FORMAT_FIELDS;
    }
}
