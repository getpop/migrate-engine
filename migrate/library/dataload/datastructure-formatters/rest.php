<?php
namespace PoP\Engine\Impl;
use PoP\ComponentModel\Facades\Engine\EngineFacade;
define('GD_DATALOAD_DATASTRUCTURE_REST', 'rest');

class DataStructureFormatter_REST extends DataStructureFormatter_MirrorQuery
{
    public function getName()
    {
        return GD_DATALOAD_DATASTRUCTURE_REST;
    }
    
    protected function getFields()
    {
        // Get the fields from the entry module's module atts
        $engine = EngineFacade::getInstance();
        $entryModule = $engine->getEntryModule();
        if ($moduleAtts = $entryModule[2]) {
            if ($fields = $moduleAtts['fields']) {
                return $fields;
            }
        }
        
        return parent::getFields();
    }
}
    
/**
 * Initialize
 */
new DataStructureFormatter_REST();
