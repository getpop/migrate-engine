<?php
namespace PoP\Engine;

class PoP_Module_Processor_DataQuery_RelationalFields extends PoP_Module_Processor_RelationalFieldsDataQueriesBase
{
    public const MODULE_LAYOUT_DATAQUERY_RELATIONALFIELDS = 'layout-dataquery-relationalfields';

    public function getModulesToProcess()
    {
        return array(
            [self::class, self::MODULE_LAYOUT_DATAQUERY_RELATIONALFIELDS],
        );
    }
}



